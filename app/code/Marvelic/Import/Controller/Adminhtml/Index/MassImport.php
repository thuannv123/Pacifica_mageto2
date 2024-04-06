<?php
namespace Marvelic\Import\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\Exception\FileSystemException;
use function Safe\file_get_contents;

class MassImport extends \Magento\Customer\Controller\Adminhtml\Index\AbstractMassAction
{
    protected $customerRepository;
    protected $eavConfig;
   
    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csv;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $file;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    protected $_customerFactory;
    /**
     * @var Reader
     */
    protected $_reader;
    /**
     * @var string
     */
    private $viewSourceBasePath;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Config $eavConfig,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\File\Csv $csv,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $filter, $collectionFactory);
        $this->collectionFactory = $collectionFactory;
        $this->eavConfig = $eavConfig;
        $this->dataPersistor = $dataPersistor;
        $this->connection = $resource->getConnection();
        $this->file = $file;
        $this->csv = $csv;
        $this->directoryList = $directoryList;
        $this->_customerFactory = $customerFactory;
        $this->resource = $resource;
        $this->storeManager = $storeManager;    
    }

    protected function massAction(AbstractCollection $collection)
    {
        if( $this->importCSV()){
            $this->messageManager->addSuccess(_('Import done'));
        }else{
            $this->messageManager->addError(__('Csv file not exist'));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }

    public function getFilteredCustomerCollection($email) {
        return $this->_customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("email", $email)
                ->addAttributeToFilter("website_id",4 )
                ->getData();
    }

    public function getCustomerNewsletter($email) {
        $dbConnection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('newsletter_subscriber'); 
        //Select Data from table
        $sql = "Select * FROM " . $tableName ." where subscriber_email = '". $email."'";
        return $dbConnection->fetchAll($sql);
    }

    public function randomSequence($length = 32)
    {
        $id = '';
        $par = [];
        $char = array_merge(range('a', 'z'), range(0, 9));
        $charLen = count($char) - 1;
        for ($i = 0; $i < $length; $i++) {
            $disc = \Magento\Framework\Math\Random::getRandomNumber(0, $charLen);
            $par[$i] = $char[$disc];
            $id = $id . $char[$disc];
        }
        return $id;
    }
    
    public function readFileList($file_name)
    {
        $pathFile = $this->viewSourceBasePath . $file_name;
        if (!file_exists($pathFile)) {
            return false;
        }
        return $pathFile;
    }

    private function insertData($result, $email,$store_id,$customer_id = 0)  {
        $dbConnection = $this->connection;
        if ($result == []) {
            
            $insertData = [
                'store_id' => 4,
                'change_status_at' => date('Y-m-d H:i:s'),
                'customer_id'=> $customer_id,
                'subscriber_email'=> $email,
                'subscriber_status'=> 1,
                'subscriber_confirm_code'=> $this->randomSequence(),
            ];
            $dbConnection->insertOnDuplicate('newsletter_subscriber', $insertData);
        }else{
            foreach ($result as $key => $value) {
                if($result[$key]['store_id']== 4){
                    $updatedata = [
                        'change_status_at' => date('Y-m-d H:i:s'),
                        'customer_id'=> $customer_id,
                        'subscriber_status'=> 1,
                    ];
                    $where =    ['subscriber_id = ?' => $result[0]['subscriber_id']];
                    $dbConnection->update('newsletter_subscriber',$updatedata,$where); 
                }else{
                    $insertData = [
                        'store_id' => 4,
                        'change_status_at' => date('Y-m-d H:i:s'),
                        'customer_id'=> $customer_id,
                        'subscriber_email'=> $email,
                        'subscriber_status'=> 1,
                        'subscriber_confirm_code'=> $this->randomSequence(),
                    ];
                    $dbConnection->insertOnDuplicate('newsletter_subscriber', $insertData);
                }
            }
        }
    }
    private function getStores($stores) {
        $arr = [];
        foreach ($stores as $key => $value) {
            if($value->getWebsiteId() == 4){
                array_push($arr, $value);
            }
        }
        return $arr;
    }
    private function importCSV(){

        $stores = $this->getStores($this->storeManager->getStores());
        $rootDirectory = $this->directoryList->getRoot();
        $csvFile = $rootDirectory . "/var/tmp/Birkenstock_import_customer_final-v_2402_2023.csv";
        try {
            if ($this->file->isExists($csvFile)) {               
                //set delimiter, for tab pass "\t"
                $this->csv->setDelimiter(',');
                $this->csv->setEnclosure('"');
                //get data as an array
                $a = file_get_contents($csvFile);
                $b = str_replace('\\', '', $a);
                file_put_contents($csvFile, $b);
                $data = $this->csv->getData($csvFile);

                if (!empty($data)) {
                    foreach (array_slice($data, 1) as $rowNum => $rowData) {  
                        $customer = $this->getFilteredCustomerCollection($rowData[0]);
                        // get store_id of birkenstock
                        foreach ($stores as $key => $store) {
                            if($store->getCode() == $rowData[2]){
                                $store_id = $store->getId();
                            }
                        }
                        $result = $this->getCustomerNewsletter($rowData[0]);
                        ($customer != []) ? 
                        // If you have an account, you will insert or update according to the account's store_id
                            $this->insertData($result,$rowData[0],$customer[0]['store_id'],$customer[0]['entity_id']) :
                        // If you don't have an account, you will insert or update by default store_id is store eng
                            $this->insertData($result,$rowData[0],$store_id,0);
                    }    
                } 
                $this->file->deleteFile($csvFile);  
                return true;             
            } else {
                return false;
                // return __('Csv file not exist');
            }
        } catch (FileSystemException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }
}
    