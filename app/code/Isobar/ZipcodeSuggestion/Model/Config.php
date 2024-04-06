<?php

namespace Isobar\ZipcodeSuggestion\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface as StoreScopeInterface;

class Config
{
    const XML_PATH_BACKEND_ENABLE = "zipcode/general/zipcode_general_active";
    const XML_PATH_SUGGESTION_COUNTRY = "zipcode/general/show_address_suggestion_for_the_country";

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get Module status in Frontend
     *
     * @return bool|mixed
     */
    public function getModuleStatusInBackend()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_BACKEND_ENABLE, StoreScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Suggestion Country
     *
     * @return bool|mixed
     */
    public function getSuggestionCountry()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SUGGESTION_COUNTRY, StoreScopeInterface::SCOPE_STORE);
    }
}
