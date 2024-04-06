<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Conditions for Magento 2
 */

namespace Amasty\Conditions\Model\Rule\Condition;

use Amasty\Conditions\Model\Constants;
use Magento\Customer\Model\Customer;
use Magento\Framework\Api\CustomAttributesDataInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * @method string getAttribute()
 * @method string getIsValueParsed()
 * @method string setAttributeOption($attributes)
 * @method string setValue($value)
 * @method string setIsValueParsed($boolean)
 */
class CustomerAttributes extends \Magento\Rule\Model\Condition\AbstractCondition
{
    public const CUSTOM_ATTRIBUTES_NAME = 'custom_attributes';

    /**
     * @var array
     */
    public const NOT_ALLOWED_ATTRIBUTES = [
        'lock_expires',
        'first_failure',
        'group_id',
        'default_billing',
        'default_shipping',
        'failures_num',
        'created_in',
        'disable_auto_group_change',
        'confirmation',
    ];

    /**
     * @var array
     */
    public const AVAILABLE_TYPES = [
        'checkbox',
        'checkboxes',
        'date',
        'editablemultiselect',
        'editor',
        'fieldset',
        'file',
        'gallery',
        'image',
        'imagefile',
        'multiline',
        'multiselect',
        'radio',
        'radios',
        'select',
        'text',
        'textarea',
        'time',
    ];

    /**
     * @var array
     */
    public const INPUT_TYPES = ['string', 'numeric', 'date', 'select', 'multiselect', 'grid', 'boolean'];

    public const SECONDS_IN_DAY = 60 * 60 * 24;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer
     */
    private $resource;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Amasty\Conditions\Model\Address
     */
    private $address;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Customer\Model\ResourceModel\Customer $resource,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Amasty\Conditions\Model\Address $address,
        array $data = []
    ) {
        $this->resource = $resource;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
        $this->address = $address;
    }

    /**
     * Retrieve attribute object
     *
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    private function getAttributeObject()
    {
        return $this->resource->getAttribute($this->getAttribute());
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function loadAttributeOptions()
    {
        $customerAttributes = $this->resource
            ->loadAllAttributes()
            ->getAttributesByCode();

        $attributes = [];

        /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
        foreach ($customerAttributes as $attribute) {
            if (!($attribute->getFrontendLabel()) || !($attribute->getAttributeCode())) {
                continue;
            }

            if (in_array($attribute->getAttributeCode(), self::NOT_ALLOWED_ATTRIBUTES)) {
                continue;
            }
            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        $this->addSpecialAttributes($attributes);
        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @param array $attributes
     *
     * @return \Amasty\Conditions\Model\Rule\Condition\CustomerAttributes
     */
    private function addSpecialAttributes(array &$attributes)
    {
        $attributes['id'] = __('Customer ID');
        $attributes['membership_days'] = __('Membership Days');

        return $this;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * @return array
     */
    public function getValue()
    {
        if ($this->getInputType() == 'date' && !$this->getIsValueParsed()) {
            $value = (string)$this->getData('value');
            // date format intentionally hard-coded
            $this->setValue((new \DateTime($value))->format('Y-m-d'));
            $this->setIsValueParsed(true);
        }

        return $this->getData('value');
    }

    /**
     * This value will define which operators will be available for this condition.
     *
     * Possible values are: string, numeric, date, select, multiselect, grid, boolean
     *
     * @return string
     */
    public function getInputType()
    {
        if ($this->getAttribute() === 'entity_id' || $this->getAttribute() === 'membership_days') {
            return 'string';
        }

        $customerAttribute = $this->getAttributeObject();

        if (!$customerAttribute) {
            return parent::getInputType();
        }

        return $this->getInputTypeFromAttribute($customerAttribute);
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $customerAttribute
     *
     * @return string
     */
    private function getInputTypeFromAttribute($customerAttribute)
    {
        if (!is_object($customerAttribute)) {
            $customerAttribute = $this->getAttributeObject();
        }

        if (in_array($customerAttribute->getFrontendInput(), self::INPUT_TYPES)) {
            return $customerAttribute->getFrontendInput();
        }

        switch ($customerAttribute->getFrontendInput()) {
            case 'gallery':
            case 'media_image':
            case 'selectimg': // amasty customer attribute
                return 'select';

            case 'multiselectimg': // amasty customer attribute
                return 'multiselect';
        }

        return 'string';
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function getValueElement()
    {
        $element = parent::getValueElement();

        switch ($this->getInputType()) {
            case 'date':
                $element->setClass('hasDatepicker');
                $element->setExplicitApply(true);
                break;
        }

        return $element;
    }

    /**
     * Value element type will define renderer for condition value element
     *
     * @see \Magento\Framework\Data\Form\Element
     * @return string
     */
    public function getValueElementType()
    {
        $customerAttribute = $this->getAttributeObject();

        if ($this->getAttribute() === 'entity_id' || $this->getAttribute() === 'membership_days') {
            return 'text';
        }

        if (!is_object($customerAttribute)) {
            return parent::getValueElementType();
        }

        if (in_array($customerAttribute->getFrontendInput(), self::AVAILABLE_TYPES)) {
            return $customerAttribute->getFrontendInput();
        }

        switch ($customerAttribute->getFrontendInput()) {
            case 'selectimg':
            case 'boolean':
                return 'select';

            case 'multiselectimg':
                return 'multiselect';
        }

        return parent::getValueElementType();
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getValueSelectOptions()
    {
        $selectOptions = [];
        $attributeObject = $this->getAttributeObject();

        if (is_object($attributeObject) && $attributeObject->usesSource()) {
            $addEmptyOption = true;
            if ($attributeObject->getFrontendInput() === 'multiselect') {
                $addEmptyOption = false;
            }
            $selectOptions = $attributeObject->getSource()->getAllOptions($addEmptyOption);
        }

        if (!$this->hasData(Constants::VALUE_SELECT_OPTIONS)) {
            $this->setData(Constants::VALUE_SELECT_OPTIONS, $selectOptions);
        }

        return $this->getData(Constants::VALUE_SELECT_OPTIONS);
    }

    /**
     * Validate Address Rule Condition
     *
     * @param AbstractModel $model
     *
     * @return bool
     * @throws LocalizedException
     */
    public function validate(AbstractModel $model): bool
    {
        $customer = $model;
        if (!$customer instanceof Customer) {
            $customer = $model->getQuote()->getCustomer();
            if (!$customer->getId()) {
                $customer = $this->customerSession->getCustomer();
            }

            $attr = $this->getAttribute();
            $allAttr = $customer instanceof Customer
                ? $customer->getData()
                : $customer->__toArray();
            $this->addCustomAttributes($allAttr, $model);

            if ($attr === 'membership_days') {
                $allAttr[$attr] = $this->getMembership($customer->getCreatedAt());
            }

            if ($attr === 'created_at') {
                $allAttr[$attr] = $this->getRegistrationDate($customer->getCreatedAt());
            }

            if ($attr !== 'entity_id' && !array_key_exists($attr, $allAttr)) {
                if (isset($allAttr[self::CUSTOM_ATTRIBUTES_NAME])
                    && array_key_exists($attr, $allAttr[self::CUSTOM_ATTRIBUTES_NAME])
                ) {
                    $customAttribute = $this->resource->getAttribute($attr);
                    $attributeValue = $allAttr[self::CUSTOM_ATTRIBUTES_NAME][$attr]['value'];

                    if ($customAttribute->getFrontendInput() === 'multiselect') {
                        $attributeValue = explode(',', $attributeValue);
                    }

                    $allAttr[$attr] = $attributeValue;
                } else {
                    $address = $model->getQuote()->getBillingAddress();
                    $allAttr[$attr] = $address->getData($attr);
                }
            }

            $customer = $this->customerFactory->create()->setData($allAttr);
        }

        return parent::validate($customer);
    }

    private function addCustomAttributes(array &$allAttr, AbstractModel $model): void
    {
        $customAttributes = [];
        if ($this->address->isAdvancedConditions($model)) {
            $advancedAttributes = $model->getExtensionAttributes()->getAdvancedConditions()->getCustomAttributes();
            if (!empty($advancedAttributes)) {
                $customAttributes = array_merge($customAttributes, $advancedAttributes);
            }
        }

        if ($model instanceof CustomAttributesDataInterface && !empty($model->getCustomAttributes())) {
            $customAttributes = array_merge($customAttributes, $model->getCustomAttributes());
        }

        if (empty($customAttributes)) {
            return;
        }

        foreach ($customAttributes as $customAttribute) {
            if (!isset($allAttr[$customAttribute->getAttributeCode()])) {
                $allAttr[$customAttribute->getAttributeCode()] = $customAttribute->getValue();
            }
        }
    }

    /**
     * @param $created
     *
     * @return float
     */
    private function getMembership($created)
    {
        return round((time() - strtotime($created)) / self::SECONDS_IN_DAY);
    }

    /**
     * @param $created
     *
     * @return string
     */
    private function getRegistrationDate($created)
    {
        return date('Y-m-d', strtotime($created));
    }
}
