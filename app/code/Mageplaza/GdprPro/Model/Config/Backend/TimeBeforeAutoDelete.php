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
 * @category  Mageplaza
 * @package   Mageplaza_GdprPro
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GdprPro\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\ValidatorException;

/**
 * Class TimeBeforeAutoDelete
 * @package Mageplaza\GdprPro\Model\Config\Backend
 */
class TimeBeforeAutoDelete extends Value
{
    /**
     * @return Value|void
     * @throws ValidatorException
     */
    public function beforeSave()
    {
        $data         = $this->getData();
        $isAutoDelete = isset($data['groups']['general']['fields']['auto_delete_customer_account']['value']) ? (int) $data['groups']['general']['fields']['auto_delete_customer_account']['value'] : 0;
        $label        = $this->getData('field_config/label');
        $value        = $this->getValue();

        if ($isAutoDelete !== 0) {
            $time_auto_delete = $data['groups']['general']['fields']['time_auto_delete']['value'];
            if ($time_auto_delete === '') {
                throw new ValidatorException(__('Please set the value for Delete After Last Login For'));
            }
            if ($value > $time_auto_delete) {
                throw new ValidatorException(__(
                    'The value of field %1 must be less than or equal to %2',
                    $label,
                    $time_auto_delete
                ));
            }
        }

        $this->setValue(intval($this->getValue()));

        parent::beforeSave();
    }
}
