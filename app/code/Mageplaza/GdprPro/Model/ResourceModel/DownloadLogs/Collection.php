<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GdprPro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GdprPro\Model\ResourceModel\DownloadLogs;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mageplaza\GdprPro\Model\DownloadLogs;
use Mageplaza\GdprPro\Model\ResourceModel\DownloadLogs as DownloadLogsResourceModel;

/**
 * Class Collection
 * @package Mageplaza\GdprPro\Model\ResourceModel\DownloadLogs
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init(DownloadLogs::class, DownloadLogsResourceModel::class);
    }
}
