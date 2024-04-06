<?php

namespace Isobar\ZipcodeSuggestion\ViewModel;

use Isobar\ZipcodeSuggestion\Model\Config;

class InjectConfig implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /** @var Config */
    protected $config;

    /**
     * InjectConfig constructor
     *
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Get Module status in Frontend
     *
     * @return int
     */
    public function getModuleStatusInBackend()
    {
        return $this->config->getModuleStatusInBackend();
    }

    /**
     * Get Suggestion Country
     *
     * @return string
     */
    public function getSuggestionCountry()
    {
        return $this->config->getSuggestionCountry();
    }
}
