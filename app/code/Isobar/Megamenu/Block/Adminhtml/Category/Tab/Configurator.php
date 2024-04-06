<?php
namespace Isobar\Megamenu\Block\Adminhtml\Category\Tab;

use Isobar\Megamenu\Helper\Data;

class Configurator extends \Magento\Backend\Block\Template
{
    const CONFIGURATOR_TEMPLATE = 'Isobar_Megamenu::category/tab/configurator.phtml';

    const FILED_NAME = 'mm_configurator';

    protected $_staticBlocksSource;

    protected $registry;

    protected $_request;

    /**
     * Configurator constructor.
     *
     * @param \Magento\Backend\Block\Template\Context               $context
     * @param \Magento\Catalog\Model\Category\Attribute\Source\Page $staticBlocksSource
     * @param \Magento\Framework\App\Request\Http                   $request
     * @param \Magento\Framework\Registry                           $registry
     * @param array                                                 $data
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\Category\Attribute\Source\Page $staticBlocksSource,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->_request = $request;
        $this->_staticBlocksSource = $staticBlocksSource;
    }

    public function getTemplate()
    {
        return self::CONFIGURATOR_TEMPLATE;
    }

    public function toHtml()
    {
        return $this->_toHtml();
    }

    /**
     * name admin
     *
     * @return null|string
     */
    public function getFrontName()
    {
        return $this->_request->getFrontName();
    }

    public function getFieldName()
    {
        return self::FILED_NAME;
    }

    public function getCategory()
    {
        return $this->registry->registry('category');
    }

    public function convertString($str)
    {
        if($str != NULL){
            $str1 = str_replace('\\"', '\\\\"', $str);
            if (is_string($str1)) {
                $unicode = [
                    '\\"' => '\"',
                ];
                foreach ($unicode as $key => $value) {
                    $str1 = preg_replace("/($value)/", $key, $str1);
                }
                return $str1;
            }
        }
        return null;
    }

    public function getStaticBlocksJson()
    {
        $blocks = $this->_staticBlocksSource->getAllOptions();
        array_shift($blocks);
        return $this->convertString(\Laminas\Json\Json::encode($blocks));
    }

    public function getConfiguredValue()
    {
        $mmConfigurator = $this->getCategory()->getMmConfigurator();
        return $this->convertString($mmConfigurator);
    }


    public function getCategoryLevel()
    {
        return $this->getCategory()->getLevel();
    }
}
