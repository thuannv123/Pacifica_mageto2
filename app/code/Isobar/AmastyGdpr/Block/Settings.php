<?php

namespace Isobar\AmastyGdpr\Block;

use Amasty\Gdpr\Block\AccountCheckbox;
use Amasty\Gdpr\Model\Config;
use Amasty\Gdpr\Model\Consent\DataProvider\PrivacySettingsDataProviderFactory;
use Amasty\Gdpr\Model\ConsentLogger;
use Magento\Framework\Data\Form\FormKey as FormKey;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;

class Settings extends \Amasty\Gdpr\Block\Settings
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Gdpr::settings.phtml';

    /**
     * @var FormKey
     */
    protected $formKey;
    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var PrivacySettingsDataProviderFactory
     */
    private $privacySettingsDataProviderFactory;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        FormKey $formKey,
        Config $configProvider,
        Manager $moduleManager,
        PrivacySettingsDataProviderFactory $privacySettingsDataProviderFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formKey, $configProvider, $moduleManager, $privacySettingsDataProviderFactory, $data);
        $this->formKey = $formKey;
        $this->registry = $registry;
        $this->configProvider = $configProvider;
        $this->moduleManager = $moduleManager;
        $this->privacySettingsDataProviderFactory = $privacySettingsDataProviderFactory;
    }

    /**
     * @return array
     */
    public function getPrivacySettings()
    {
        return $this->getAvailableBlocks();
    }

    /**
     * @return array
     */
    private function getPrivacyBlocks()
    {
        $result = [];

        if ($this->configProvider->isModuleEnabled()) {
            $result[self::POLICIES_TEXT_NAME] = [
                'title' => __('Policies text'),
                'policiesText' => true,
                'isCookieEnabled' => $this->moduleManager->isEnabled('Amasty_GdprCookie')
            ];

            if ($this->configProvider->isAllowed(Config::DOWNLOAD) && $this->isVisible()) {
                $downloadContent = $this->isOrder()
                    ? __(
                        'Here you can download a copy of your personal '
                        . 'data which we store for your order in CSV format.'
                    )
                    : __(
                        'Here you can download a copy of your personal '
                        . 'data which we store for your account in CSV format.'
                    );
                $result[self::DOWNLOAD_DATA_BLOCK_SHORT_NAME] = [
                    'title' => __('Download personal data'),
                    'content' => $downloadContent,
                    'hasCheckbox' => false,
                    'checkboxText' => '',
                    'hidePassword' => false,
                    'needPassword' => $this->isNeedPassword(),
                    'submitText' => __('Download'),
                    'action' => $this->getUrl('gdpr/customer/downloadCsv'),
                    'actionCode' => Config::DOWNLOAD,
                ];
            }

            if ($this->configProvider->isAllowed(Config::ANONYMIZE) && $this->isVisible()) {
                $result[self::ANONYMISE_DATA_BLOCK_SHORT_NAME] = [
                    'title' => __('Anonymise personal data'),
                    'content' => __(
                        'Anonymising your personal data means that it will be replaced with non-personal anonymous information and before that you will get your new login to your e-mail address (your password will stay the same). After this process, your e-mail address and all other personal data will be removed from the website.'
                    ),
                    'hasCheckbox' => true,
                    'checkboxText' => __('I agree and I want to proceed'),
                    'hidePassword' => true,
                    'needPassword' => $this->isNeedPassword(),
                    'submitText' => __('Proceed'),
                    'action' => $this->getUrl('gdpr/customer/anonymise'),
                    'actionCode' => Config::ANONYMIZE,
                ];
            }

            if ($this->configProvider->isAllowed(Config::DELETE)) {
                $result[self::DELETE_ACCOUT_BLOCK_SHORT_NAME] = [
                    'title' => __('Delete account'),
                    'content' => __(
                        'Request to remove your account, together with all your personal data, will be processed by our staff.<br>Deleting your account will remove all the purchase history, discounts, orders, invoices and all other information that might be related to your account or your purchases.<br>All your orders and similar information will be lost. You will not be able to restore access to your account after we approve your removal request.'
                    ),
                    'checked' => true,
                    'hasCheckbox' => true,
                    'checkboxText' => __('I understand and I want to delete my account'),
                    'hidePassword' => true,
                    'needPassword' => $this->isNeedPassword(),
                    'submitText' => __('Submit request'),
                    'action' => $this->getUrl('gdpr/customer/addDeleteRequest'),
                    'actionCode' => Config::DELETE,
                ];
            }
            $privacySettingsProvider = $this->privacySettingsDataProviderFactory->create();

            if ($this->configProvider->isAllowed(Config::CONSENT_OPTING)
                && count($privacySettingsProvider->getData(ConsentLogger::FROM_PRIVACY_SETTINGS)) > 0
            ) {
                $checkboxBlock = $this->getLayout()->createBlock(
                    AccountCheckbox::class,
                    'opting_consents_block',
                    [
                        'dataProvider' => $privacySettingsProvider,
                        'scope' => ConsentLogger::FROM_PRIVACY_SETTINGS
                    ]
                );
                $result[self::CONSENT_OPTING_BLOCK_SHORT_NAME] = [
                    'title' => __('Given Consent'),
                    'content' => __('Here you can opt in or opt out from consents that were previously given.'),
                    'hasCheckbox' => false,
                    'checkboxText' => '',
                    'additionalBlock' => $checkboxBlock,
                    'hidePassword' => true,
                    'needPassword' => false,
                    'submitText' => __('Save'),
                    'action' => $this->getUrl('gdpr/customer/saveConsentChanges'),
                    'actionCode' => Config::GIVEN_CONSENTS,
                ];
            }

            if ($this->configProvider->isDisplayDpoInfo()) {
                $result[self::DISPLAY_DPO_INFO_BLOCK_SHORT_NAME] = [
                    'title' => $this->configProvider->getDpoSectionName(),
                    'content' => $this->configProvider->getDpoInfo()
                ];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getAvailableBlocks()
    {
        $result = [];
        $allBlocks = $this->getPrivacyBlocks();
        $visibleBlocks = $this->getData(self::VISIBLE_BLOCK_LAYOUT_VARIABLE_NAME)
            ? explode(',', $this->getData(self::VISIBLE_BLOCK_LAYOUT_VARIABLE_NAME)) : [];

        if (!$visibleBlocks) {
            return $allBlocks;
        }

        foreach ($visibleBlocks as $blockName) {
            if (array_key_exists($blockName, $allBlocks)) {
                $result[$blockName] = $allBlocks[$blockName];
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    private function isOrder()
    {
        return (bool)$this->getData(self::IS_ORDER_LAYOUT_VARIABLE_NAME);
    }

    /**
     * @return bool
     */
    private function isCustomerGuest()
    {
        $currentOrder = $this->registry->registry('current_order');

        return $currentOrder ? $currentOrder->getCustomerIsGuest() : false;
    }

    /**
     * @return bool
     */
    private function isVisible()
    {
        return ($this->isOrder() && $this->isCustomerGuest()) || !$this->isOrder();
    }
}
