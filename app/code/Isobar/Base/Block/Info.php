<?php
/**
 * @author Isobar Team
 * @copyright Copyright (c) 2020 Isobar (https://www.isobar.com)
 * @package Isobar_Base
 */

namespace Isobar\Base\Block;

use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\App\DeploymentConfig\Reader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\State;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\Js;

// test version 3
class Info extends Fieldset
{

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var Field|null
     */
    protected $fieldRenderer;

    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        DirectoryList $directoryList,
        Reader $reader,
        ResourceConnection $resourceConnection,
        ProductMetadataInterface $productMetadata,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);
        $this->directoryList = $directoryList;
        $this->resourceConnection = $resourceConnection;
        $this->productMetadata = $productMetadata;
        $this->reader = $reader;
    }

    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $html = $this->_getHeaderHtml($element);

        $html .= $this->getMagentoMode($element);
        $html .= $this->getMagentoPathInfo($element);
        $html .= $this->getOwnerInfo($element);
        $html .= $this->getSystemTime($element);

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    private function getFieldRenderer()
    {
        if (empty($this->fieldRenderer)) {
            $this->fieldRenderer = $this->_layout->createBlock(
                Field::class
            );
        }

        return $this->fieldRenderer;
    }

    /**
     * @param AbstractElement $fieldset
     *
     * @return string
     */
    private function getMagentoMode($fieldset)
    {
        $label = __('Magento Mode');

        $env = $this->reader->load();
        $mode = isset($env[State::PARAM_MODE]) ? $env[State::PARAM_MODE] : '';

        return $this->getFieldHtml($fieldset, 'magento_mode', $label, ucfirst($mode));
    }

    /**
     * @param AbstractElement $fieldset
     *
     * @return string
     */
    private function getMagentoPathInfo($fieldset)
    {
        $label = __('Magento Path');
        $path = $this->directoryList->getRoot();

        return $this->getFieldHtml($fieldset, 'magento_path', $label, $path);
    }

    /**
     * @param AbstractElement $fieldset
     *
     * @return string
     */
    private function getOwnerInfo($fieldset)
    {
        $serverUser = __('Unknown');
        if (function_exists('get_current_user')) {
            $serverUser = get_current_user();
        }

        return $this->getFieldHtml(
            $fieldset,
            'magento_user',
            __('Server User'),
            $serverUser
        );
    }

    /**
     * @param AbstractElement $fieldset
     *
     * @return string
     */
    private function getSystemTime($fieldset)
    {
        if (version_compare($this->productMetadata->getVersion(), '2.2', '>=')) {
            $time = $this->resourceConnection->getConnection()->fetchOne('select now()');
        } else {
            $time = $this->_localeDate->date()->format('H:i:s');
        }
        return $this->getFieldHtml($fieldset, 'mysql_current_date_time', __('Current Time'), $time);
    }

    /**
     * @param AbstractElement $fieldset
     * @param string $fieldName
     * @param string $label
     * @param string $value
     *
     * @return string
     */
    protected function getFieldHtml($fieldset, $fieldName, $label = '', $value = '')
    {
        $field = $fieldset->addField($fieldName, 'label', [
            'name'  => 'dummy',
            'label' => $label,
            'after_element_html' => $value,
        ])->setRenderer($this->getFieldRenderer());

        return $field->toHtml();
    }
}
