<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Ui\DataProvider\Form\Link\Modifier;

use Isobar\Megamenu\Api\Data\Menu\ItemInterface;
use Isobar\Megamenu\Api\Data\Menu\LinkInterface;
use Isobar\Megamenu\Api\ItemRepositoryInterface;
use Isobar\Megamenu\Model\Provider\FieldsByStore;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class UseDefault
 * @package Isobar\Megamenu\Ui\DataProvider\Form\Link\Modifier
 */
class UseDefault implements ModifierInterface
{
    /**
     * @var int
     */
    private $entityId;

    /**
     * @var string
     */
    private $type = '';

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var ItemRepositoryInterface
     */
    private $itemRepository;

    /**
     * @var FieldsByStore
     */
    private $fieldsByStore;

    /**
     * UseDefault constructor.
     * @param Registry $registry
     * @param RequestInterface $request
     * @param ItemRepositoryInterface $itemRepository
     * @param FieldsByStore $fieldsByStore
     */
    public function __construct(
        Registry $registry,
        RequestInterface $request,
        ItemRepositoryInterface $itemRepository,
        FieldsByStore $fieldsByStore
    ) {
        $this->itemRepository = $itemRepository;
        $this->storeId = (int)$request->getParam('store', 0);
        $this->fieldsByStore = $fieldsByStore;
        if ($registry->registry(LinkInterface::PERSIST_NAME)) {
            $this->entityId = $registry->registry(LinkInterface::PERSIST_NAME)->getEntityId();
            $this->type = 'custom';
        } else {
            $this->entityId = $registry->registry('current_category')
                ? $registry->registry('current_category')->getId()
                : $request->getParam('id');
            $this->type = 'category';
        }
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        $defaultData = $this->getItem() ? $this->getItem()->getData() : [];
        $perStoreData = [];
        if ($this->storeId) {
            $perStoreData = $this->getItem($this->storeId) ? $this->getItem($this->storeId)->getData() : [];
        }
        $notnull = function ($var) {
            return $var !== null;
        };
        $changedData = array_merge(
            array_filter($defaultData, $notnull),
            array_filter($perStoreData, $notnull)
        );

        $fieldsToUse = $this->fieldsByStore->getCategoryFields()['isobar_mega_menu_fieldset'];
        foreach ($changedData as $field => $value) {
            if (in_array($field, $fieldsToUse)) {
                $data[$changedData['entity_id']][$field] = $value;
            }
        }

        $fieldsToUse = $this->fieldsByStore->getCustomFields()['general'];
        // config edit content 
        $fieldsToUse['content'] = 'content';
        foreach ($changedData as $field => $value) {
            if (in_array($field, $fieldsToUse)) {
                $data[$changedData['entity_id']][$field] = $value;
            }
        }

        if ($this->storeId) {
            reset($data);
            $data[key($data)]['store_id'] = $this->storeId;
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        if ($this->isShowDefaultCheckbox()) {
            $fieldsByStore = $this->type == 'category' ?
                $this->fieldsByStore->getCategoryFields() :
                $this->fieldsByStore->getCustomFields();
            foreach ($fieldsByStore as $fieldSetCode => $fieldSet) {
                foreach ($fieldSet as $field) {
                    $meta[$fieldSetCode]['children'][$field]['arguments']['data']['config']['service'] =
                        [
                            'template' => 'ui/form/element/helper/service'
                        ];
                    if (!$this->getItem($this->storeId)
                        || $this->getItem($this->storeId)->getData($field) === null
                    ) {
                        $meta[$fieldSetCode]['children'][$field]['arguments']['data']['config']['disabled'] = true;
                    }
                }
            }
        }

        return $meta;
    }

    /**
     * @return bool
     */
    private function isShowDefaultCheckbox()
    {
        return (bool)$this->storeId;
    }

    /**
     * @param int $storeId
     *
     * @return ItemInterface
     */
    private function getItem($storeId = 0)
    {
        $item = $this->itemRepository->getByEntityId($this->entityId, $storeId, $this->type);
        if ($item && $item->getType() === 'category' && $item->getContent() === null) {
            $item->setContent('{{child_categories_content}}');
        }

        return $item;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->entity = $category;

        return $this;
    }
}
