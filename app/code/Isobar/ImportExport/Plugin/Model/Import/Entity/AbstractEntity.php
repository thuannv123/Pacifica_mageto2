<?php

namespace Isobar\ImportExport\Plugin\Model\Import\Entity;

use Firebear\ImportExport\Model\Import\Order;
use Firebear\ImportExport\Model\ResourceModel\Import\Data as ResourceModelData;
use Isobar\ImportExport\Helper\UpdateStatus;

class AbstractEntity
{
    /**
     * @var ResourceModelData
     */
    private $dataSourceModel;

    /**
     * @var UpdateStatus
     */
    private $updateStatus;
    /**
     * AbstractEntity constructor.
     * @param ResourceModelData $dataSourceModel
     * @param UpdateStatus $updateStatus
     */
    public function __construct(
        ResourceModelData $dataSourceModel,
        UpdateStatus $updateStatus
    ) {
        $this->dataSourceModel = $dataSourceModel;
        $this->updateStatus = $updateStatus;
    }
    public function aroundImportData(
        $subject,
        \Closure $proceed
    ) {
        if ($subject instanceof Order) {
            $data = $this->dataSourceModel->getNextBunch();
            if ($data && count($data) > 0) {
                if (isset($data[0]['increment_id']) && isset($data[0]['status']) && isset($data[0]['tracking_number'])) {
                    foreach ($data as $item) {
                        $this->updateStatus->execute($item);
                    }
                    return true;
                }
            }
            $this->dataSourceModel->getIterator();
            return $proceed();
        } else {
            return $proceed();
        }
    }
}
