<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Ui\DataProvider\Form\Link;

use Isobar\Megamenu\Api\Data\Menu\LinkInterface;
use Isobar\Megamenu\Model\Backend\DataProvider\Pool;
use Isobar\Megamenu\Model\Menu\Link;
use Isobar\Megamenu\Model\ResourceModel\Menu\Link\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * Class DataProvider
 * @package Isobar\Megamenu\Ui\DataProvider\Form\Link
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Pool
     */
    private $dataModifier;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param DataPersistorInterface $dataPersistor
     * @param CollectionFactory $collectionFactory
     * @param Registry $coreRegistry
     * @param PoolInterface $pool
     * @param RequestInterface $request
     * @param Pool $dataModifier
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        DataPersistorInterface $dataPersistor,
        CollectionFactory $collectionFactory,
        Registry $coreRegistry,
        PoolInterface $pool,
        RequestInterface $request,
        Pool $dataModifier,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->coreRegistry = $coreRegistry;
        $this->pool = $pool;
        $this->request = $request;
        $this->dataModifier = $dataModifier;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function getData()
    {
        /** @var Link $current */
        $current = $this->coreRegistry->registry(LinkInterface::PERSIST_NAME);
        if ($current && $current->getEntityId()) {
            $data = $current->getData();
            $storeId = (int)$this->request->getParam('store', Store::DEFAULT_STORE_ID);
            $entityId = (int)$current->getEntityId();
            $result[$entityId] = $this->dataModifier->execute($data, $storeId, $entityId);
        } else {
            $data = $this->dataPersistor->get(LinkInterface::PERSIST_NAME);
            if (!empty($data)) {
                /** @var Link $link */
                $link = $this->collection->getNewEmptyItem();
                $link->setData($data);
                $data = $link->getData();
                $result[$link->getEntityId()] = $data;
                $this->dataPersistor->clear(LinkInterface::PERSIST_NAME);
            }
        }

        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $result = $modifier->modifyData($result ?? []);
        }

        return $result ?? [];
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
