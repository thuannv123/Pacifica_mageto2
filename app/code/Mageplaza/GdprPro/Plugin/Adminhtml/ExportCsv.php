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

namespace Mageplaza\GdprPro\Plugin\Adminhtml;

use Closure;
use DateTime;
use DateTimeZone;
use Exception;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Ui\Model\Export\MetadataProvider;
use Mageplaza\GdprPro\Helper\Data;

/**
 * Class ExportCsv
 * @package Mageplaza\GdprPro\Plugin\Adminhtml
 */
class ExportCsv
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var string
     */
    private $locale;

    /**
     * ExportCsv constructor.
     *
     * @param Data $helperData
     * @param RequestInterface $request
     * @param TimezoneInterface $localeDate
     * @param ResolverInterface $localeResolver
     */
    public function __construct(
        Data $helperData,
        RequestInterface $request,
        TimezoneInterface $localeDate,
        ResolverInterface $localeResolver
    ) {
        $this->helperData = $helperData;
        $this->request    = $request;
        $this->localeDate = $localeDate;
        $this->locale     = $localeResolver->getLocale();
    }

    /**
     * @param MetadataProvider $subject
     * @param Closure $proceed
     * @param DocumentInterface $document
     * @param array $fields
     * @param array $options
     *
     * @return array|mixed
     * @throws Exception
     */
    public function aroundGetRowData(
        MetadataProvider $subject,
        Closure $proceed,
        DocumentInterface $document,
        $fields,
        $options
    ) {
        $namespace  = $this->request->getParam('namespace');
        $nameSpaces = [
            'mageplaza_gdpr_download_log_listing',
            'mageplaza_gdpr_delete_account_log_listing'
        ];

        if ($this->helperData->isEnabled() && in_array($namespace, $nameSpaces, true)) {
            $row = [];
            foreach ($fields as $column) {
                $key = $document->getCustomAttribute($column)->getValue();
                if ($column === 'created_at' || $column === 'updated_at') {
                    $convertedDate = $this->localeDate->date(
                        new DateTime($key, new DateTimeZone('UTC')),
                        $this->locale,
                        true
                    );
                    $key           = $convertedDate->format('Y-m-d H:i:s');
                }
                if (is_array($key)) {
                    $row[] = implode(',', $key);
                } else {
                    $row[] = $key;
                }
            }

            return $row;
        }

        return $proceed($document, $fields, $options);
    }
}
