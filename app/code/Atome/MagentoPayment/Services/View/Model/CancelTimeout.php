<?php

namespace Atome\MagentoPayment\Services\View\Model;

use Atome\MagentoPayment\Services\Config\Atome;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\ValidatorException;

class CancelTimeout extends Value
{

    public function beforeSave()
    {
        $value = intval($this->getValue());

        if ($value < ATOME::CANCEL_TIMEOUT_MINIMUM_MINUTES || $value > ATOME::CANCEL_TIMEOUT_MAXIMUM_MINUTES) {
            throw new ValidatorException(__("`{$this->getData('field_config/label')}` " . "is not between '" . ATOME::CANCEL_TIMEOUT_MINIMUM_MINUTES . "' and '" . ATOME::CANCEL_TIMEOUT_MAXIMUM_MINUTES . "', inclusively"));
        }

        $this->setValue($value);

        parent::beforeSave();
    }


}
