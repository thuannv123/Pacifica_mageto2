<?php

namespace Isobar\ZipcodeSuggestion\Block\Checkout\LayoutProcessor;

use Isobar\ZipcodeSuggestion\Model\Config;

class ValidationCustom
{
    /**
     * @var Config
     */
    protected $zipcodeConfig;

    /**
     * ValidationCustom constructor.
     *
     * @param Config $zipcodeConfig
     */
    public function __construct(
        Config $zipcodeConfig
    ) {
        $this->zipcodeConfig = $zipcodeConfig;
    }

    /**
     * AfterProcess
     *
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
        if ($this->zipcodeConfig->getModuleStatusInBackend() == 1) {
            // add rule for shipping checkout
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['validation']['post-code'] = 1;

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id']['sortOrder'] = 80;

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['sortOrder'] = 90;

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['region']['sortOrder'] = 100;

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['sortOrder'] = 110;

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['sortOrder'] = 130;

            if ($this->zipcodeConfig->getSuggestionCountry() == 'TH') {
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['label'] = __('District');

                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['shipping-address-fieldset']['children']['subdistrict'] = [
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        // customScope is used to group elements within a single form (e.g. they can be validated separately)
                        'customScope' => 'shippingAddress.custom_attributes',
                        'customEntry' => null,
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input',
                    ],
                    'dataScope' => 'shippingAddress.custom_attributes.subdistrict',
                    'label' => 'Sub District',
                    'provider' => 'checkoutProvider',
                    'sortOrder' => 120,
                    'validation' => [
                        'required-entry' => true
                    ],
                    'options' => [],
                    'additionalClasses' => 'subdistrict',
                    'filterBy' => null,
                    'customEntry' => null,
                    'visible' => true,
                ];
            }

            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'])) {
                foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                         ['payment']['children']['payments-list']['children'] as $key => $payment) {
                    if (isset($payment['children']['form-fields']['children']['country_id'])) {
                        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                        ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                        ['country_id']['sortOrder'] = 80;
                    }

                    if (isset($payment['children']['form-fields']['children']['country_id'])) {
                        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                        ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                        ['postcode']['sortOrder'] = 90;
                    }

                    if (isset($payment['children']['form-fields']['children']['country_id'])) {
                        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                        ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                        ['region']['sortOrder'] = 100;
                    }

                    if (isset($payment['children']['form-fields']['children']['country_id'])) {
                        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                        ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                        ['city']['sortOrder'] = 110;
                    }

                    if (isset($payment['children']['form-fields']['children']['country_id'])) {
                        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                        ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                        ['subdistrict']['sortOrder'] = 120;
                    }

                    if (isset($payment['children']['form-fields']['children']['country_id'])) {
                        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                        ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                        ['telephone']['sortOrder'] = 130;
                    }

                    if ($this->zipcodeConfig->getSuggestionCountry() == 'TH') {
                        if (isset($payment['children']['form-fields']['children']['country_id'])) {
                            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                            ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                            ['city']['label'] = __('District');
                        }
                    }
                }
            }
        }

        return $jsLayout;
    }
}
