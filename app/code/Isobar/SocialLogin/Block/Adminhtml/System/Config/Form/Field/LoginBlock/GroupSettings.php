<?php
namespace Isobar\SocialLogin\Block\Adminhtml\System\Config\Form\Field\LoginBlock;

use Isobar\SocialLogin\Model\Config\LoginBlock;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

/**
 * Class GroupSettings
 */
class GroupSettings extends AbstractFieldArray
{
    /**
     * @var bool
     */
    protected $_addAfter = false;

    /**
     * @var string
     */
    protected $_template = 'Isobar_SocialLogin::system/config/form/field/array.phtml';

    /**
     * @var array
     */
    protected $rendererList = [];

    /**
     * @var LoginBlock
     */
    protected $loginBlockConfig;

    /**
     * @param Context $context
     * @param LoginBlock $loginBlockConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        LoginBlock $loginBlockConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->loginBlockConfig = $loginBlockConfig;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn('group', [
            'label' => __('Block'),
            'renderer' => $this->getGroupRenderer()
        ]);
        $this->addColumn('is_visible', [
            'label' => __('Visibility'),
            'renderer' => $this->getVisibilityRenderer()
        ]);
        $this->addColumn('template', [
            'label' => __('Template'),
            'renderer' => $this->getTemplateRenderer()
        ]);
    }

    /**
     * Get group renderer
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    protected function getGroupRenderer()
    {
        return $this->getRenderer(Group::class);
    }

    /**
     * Get visibility renderer
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    protected function getVisibilityRenderer()
    {
        return $this->getRenderer(Visibility::class);
    }

    /**
     * Get template renderer
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    protected function getTemplateRenderer()
    {
        return $this->getRenderer(Template::class);
    }

    /**
     * Get renderer
     *
     * @param string $blockType
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getRenderer($blockType)
    {
        if (!isset($this->rendererList[$blockType])) {
            $this->rendererList[$blockType] = $this->getLayout()->createBlock(
                $blockType,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->rendererList[$blockType];
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $row->setData('option_extra_attrs', []);
    }

    /**
     * {@inheritdoc}
     */
    public function getArrayRows()
    {
        $result = [];
        $groupsSettings = $this->getGroupsSettings();
        foreach ($groupsSettings as $setting) {
            $rowId = $setting['_id'];
            $setting['is_group_editable'] = false;
            $rowColumnValues = [];
            foreach ($setting as $key => $value) {
                $row[$key] = $this->escapeHtml($value);
                $rowColumnValues[$this->_getCellInputElementId($rowId, $key)] = $row[$key];
            }
            $row['column_values'] = $rowColumnValues;
            $result[$rowId] = new DataObject($row);
            $this->_prepareArrayRow($result[$rowId]);
        }
        return $result;
    }

    /**
     * Get groups settings
     *
     * @return array
     */
    protected function getGroupsSettings()
    {
        $elementValue = $this->getElement()->getValue();
        if (is_array($elementValue)) {
            $elementValue = $this->loginBlockConfig->prepareCustomGroupsSettings($elementValue);
        }
        $groupsSettings = $this->loginBlockConfig->getGroupsSettings($elementValue);
        foreach ($groupsSettings as $group => &$setting) {
            $isDefaultGroup = in_array($group, $this->loginBlockConfig->getDefaultGroups());
            $setting['is_default'] = $isDefaultGroup;
            $setting['_id'] = isset($setting['_id']) ? $setting['_id'] : $this->generateRowId();
        }

        return $groupsSettings;
    }

    /**
     * Generate row id
     *
     * @return string
     */
    protected function generateRowId()
    {
        return microtime(true) . '_' . rand();
    }
}
