<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Plugin\Block;

use Isobar\Megamenu\Model\Menu\TreeResolver;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\Category;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Block\Html\Topmenu as BaseTopmenu;

/**
 * Class Topmenu
 * @package Isobar\Megamenu\Plugin\Block
 */
class Topmenu
{
    /**
     * @var TreeResolver
     */
    protected $treeResolver;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Topmenu constructor.
     * @param TreeResolver $treeResolver
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        TreeResolver $treeResolver,
        StoreManagerInterface $storeManager
    ) {
        $this->treeResolver = $treeResolver;
        $this->storeManager = $storeManager;
    }

    /**
     * Get menu object.
     *
     * @param BaseTopmenu $subject
     * @param Node $result
     * @return Node
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterGetMenu(BaseTopmenu $subject, Node $result)
    {
        return $this->treeResolver->get($this->getStoreId());
    }

    /**
     * Add list of associated identities to the top menu block for caching purposes.
     *
     * @param BaseTopmenu $subject
     * @return void
     * @throws LocalizedException
     */
    public function beforeGetIdentities(BaseTopmenu $subject)
    {
        $subject->addIdentity(Category::CACHE_TAG);
        $storeId = $this->getStoreId();
        $rootId = $this->treeResolver->getCategoryRootId($storeId);
        /** @var CategoryCollection $collection */
        $collection = $this->treeResolver->getCategoryCollection($storeId);
        $mapping = [$rootId => $subject->getMenu()];
        foreach ($collection as $category) {
            if (!isset($mapping[$category->getParentId()])) {
                continue;
            }
            $subject->addIdentity(Category::CACHE_TAG . '_' . $category->getId());
        }
    }

    /**
     * Add active
     *
     * @param BaseTopmenu $subject
     * @param string[] $result
     * @return string[]
     */
    public function afterGetCacheKeyInfo(BaseTopmenu $subject, array $result)
    {
        $activeCategory = $this->treeResolver->getCurrentCategory();
        if ($activeCategory) {
            $result[] = Category::CACHE_TAG . '_' . $activeCategory->getId();
        }

        return $result;
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    private function getStoreId()
    {
        return (int)$this->storeManager->getStore()->getId();
    }
}
