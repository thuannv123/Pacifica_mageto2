<?php
/**
 * @author Isobar Team
 * @copyright Copyright (c) 2020 Isobar (https://www.isobar.com)
 * @package Isobar_Base
 */


namespace Isobar\Base\Plugin\Backend\Model\Menu;

use Isobar\Base\Model\ConfigProvider;
use Magento\Backend\Model\Menu;
use Magento\Backend\Model\Menu\Config;
use Magento\Backend\Model\Menu\Filter\IteratorFactory;
use Magento\Backend\Model\Menu\ItemFactory;
use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\Config\Structure\Element\Iterator;
use Magento\Config\Model\Config\Structure\Element\Tab;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Framework\Module\ModuleListInterface;
use Psr\Log\LoggerInterface;

class Builder
{
    const BASE_MENU = 'MenuIsobar_Base::menu';

    const PAYMENTS_MODULE = [
        'Isobar_GMOPayment' => [
            'id' => 'payment',
            'label' => 'GMO Setting'
        ]
    ];
    /**
     * @var Config
     */
    private $menuConfig;

    /**
     * @var array|null
     */
    private $isobarItems = null;

    /**
     * @var IteratorFactory
     */
    private $iteratorFactory;

    /**
     * @var ItemFactory
     */
    private $itemFactory;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var Structure
     */
    private $configStructure;

    /**
     * @var ObjectFactory
     */
    private $objectFactory;

    /**
     * @var ProductMetadataInterface
     */
    private $metadata;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ConfigProvider
     */
    private $configProvider;


    public function __construct(
        Config $menuConfig,
        IteratorFactory $iteratorFactory,
        ItemFactory $itemFactory,
        ModuleListInterface $moduleList,
        Structure $configStructure,
        ProductMetadataInterface $metadata,
        ObjectFactory $objectFactory,
        ScopeConfigInterface $scopeConfig,
        ConfigProvider $configProvider,
        LoggerInterface $logger
    ) {
        $this->menuConfig = $menuConfig;
        $this->iteratorFactory = $iteratorFactory;
        $this->itemFactory = $itemFactory;
        $this->moduleList = $moduleList;
        $this->configStructure = $configStructure;
        $this->objectFactory = $objectFactory;
        $this->metadata = $metadata;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->configProvider = $configProvider;
    }

    /**
     * @param \Magento\Backend\Model\Menu\Builder $subject
     * @param Menu $menu
     *
     * @return Menu
     */
    public function afterGetResult($subject, Menu $menu)
    {
        try {
            $menu = $this->observeMenu($menu);
            //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
        } catch (\Exception $ex) {
            //do nothing - do not show our menu
            $this->logger->error($ex->getMessage());
        }

        return $menu;
    }

    /**
     * @param Menu $menu
     *
     * @return Menu
     *
     * @throws \Exception
     */
    private function observeMenu(Menu $menu)
    {
        $item = $menu->get(self::BASE_MENU);
        if (!$item) {
            return $menu;
        }

        $origMenu = $this->menuConfig->getMenu();
        $menuItems = $this->getMenuItems($origMenu);
        $configItems = $this->getConfigItems();

        foreach ($this->getInstalledModules($configItems) as $title => $installedModule) {
            $moduleInfo = $this->configProvider->getModuleInfo($installedModule);

            if (isset($menuItems[$installedModule])) {
                $itemsToAdd = $this->cloneMenuItems($menuItems[$installedModule], $menu);
            } else {
                $itemsToAdd = [];
            }

            if (isset($configItems[$installedModule]['id'])) {
                $isobarItem = $this->generateMenuItem(
                    $installedModule . '::menuconfig',
                    $installedModule,
                    self::BASE_MENU,
                    'adminhtml/system_config/edit/section/' . $configItems[$installedModule]['id'],
                    __('Configuration')->render()
                );

                if ($isobarItem) {
                    $itemsToAdd[] = $isobarItem;
                }
            }

            if (isset($moduleInfo['guide']) && $moduleInfo['guide']) {
                $isobarItem = $this->generateMenuItem(
                    $installedModule . '::menuguide',
                    $installedModule,
                    self::BASE_MENU,
                    'adminhtml/system_config/edit/section/ambase',
                    __('User Guide')->render()
                );

                if ($isobarItem) {
                    $itemsToAdd[] = $isobarItem;
                }
            }

            $parentNodeResource = '';
            foreach ($itemsToAdd as $key => $itemToAdd) {
                $itemToAdd = $itemToAdd->toArray();
                if (empty($itemToAdd['action'])) {
                    $parentNodeResource = $itemToAdd['resource'];
                    unset($itemsToAdd[$key]);
                }
            }

            if ($itemsToAdd) {
                $itemId = $installedModule . '::container';
                /** @var \Magento\Backend\Model\Menu\Item $module */
                $module = $this->itemFactory->create(
                    [
                        'data' => [
                            'id'       => $itemId,
                            'title'    => $this->normalizeTitle($title),
                            'module'   => $installedModule,
                            'resource' => $this->getValidResource($installedModule, $parentNodeResource)
                        ]
                    ]
                );
                $menu->add($module, self::BASE_MENU, 1);
                foreach ($itemsToAdd as $copy) {
                    if ($copy) {
                        $menu->add($copy, $itemId, null);
                    }
                }
            }
        }

        return $menu;
    }

    /**
     * According to default validation rules, title can't be longer than 50 characters
     * @param string $title
     * @return string
     */
    private function normalizeTitle(string $title): string
    {
        if (mb_strlen($title) > 50) {
            $title = mb_substr($title, 0, 47) . '...';
        }

        return $title;
    }

    /**
     * @param $installedModule
     * @param $parentNodeResource
     * @return string
     */
    private function getValidResource($installedModule, $parentNodeResource)
    {
        if (!empty($parentNodeResource)) {
            return $parentNodeResource;
        }
        return $installedModule . "::config";
    }

    /**
     * @param $menuItems
     * @param Menu $menu
     * @return array
     */
    private function cloneMenuItems($menuItems, Menu $menu)
    {
        $itemsToAdd = [];
        foreach ($menuItems as $link) {
            $isobarItem = $menu->get($link);
            if ($isobarItem) {
                $itemData = $isobarItem->toArray();
                if (isset($itemData['id'], $itemData['resource'], $itemData['title'])) {
                    $itemToAdd = $this->generateMenuItem(
                        $itemData['id'] . 'menu',
                        $this->getModuleFullName($itemData),
                        $itemData['resource'],
                        $itemData['action'],
                        $itemData['title']
                    );

                    if ($itemToAdd) {
                        $itemsToAdd[] = $itemToAdd;
                    }
                }
            }
        }
        return $itemsToAdd;
    }

    /**
     * @param $itemData
     *
     * @return string
     */
    private function getModuleFullName($itemData)
    {
        if (isset($itemData['module'])) {
            return $itemData['module'];
        } else {
            return current(explode('::', $itemData['resource']));
        }
    }

    /**
     * @param $id
     * @param $installedModule
     * @param $resource
     * @param $url
     * @param $title
     *
     * @return bool|Menu\Item
     */
    private function generateMenuItem($id, $installedModule, $resource, $url, $title)
    {
        try {
            $item = $this->itemFactory->create(
                [
                    'data' => [
                        'id'           => $id,
                        'title'        => $title,
                        'module'       => $installedModule,
                        'action'       => $url,
                        'resource'     => $resource
                    ]
                ]
            );
        } catch (\Exception $ex) {
            $this->logger->warning($ex);
            $item = false;
        }

        return $item;
    }

    /**
     * @param $configItems
     *
     * @return array
     */
    private function getInstalledModules($configItems)
    {
        $installed = [];
        $modules = $this->moduleList->getNames();
        $dispatchResult = $this->objectFactory->create(['data' => $modules]);
        $modules = $dispatchResult->toArray();

        foreach ($modules as $moduleName) {
            if ($moduleName === 'Isobar_Base'
                || strpos($moduleName, 'Isobar_') === false
            ) {
                continue;
            }

            $title = (isset($configItems[$moduleName]['label']) && $configItems[$moduleName]['label'])
                ? $configItems[$moduleName]['label']
                : $moduleName;

            $installed[$title] = $moduleName;
        }
        ksort($installed);

        return $installed;
    }

    /**
     * @param Menu $menu
     *
     * @return array|null
     */
    private function getMenuItems(Menu $menu)
    {
        if ($this->isobarItems === null) {
            $all = $this->generateIsobarItems($menu);
            $this->isobarItems = [];
            foreach ($all as $item) {
                $name = explode('::', $item);
                $name = $name[0];
                if (!isset($this->isobarItems[$name])) {
                    $this->isobarItems[$name] = [];
                }
                $this->isobarItems[$name][] = $item;
            }
        }

        return $this->isobarItems;
    }

    /**
     * @return array
     */
    private function getConfigItems()
    {
        $configItems = [];
        $config = $this->generateConfigItems();
        foreach ($config as $item => $section) {
            $name = explode('::', $item);
            $name = $name[0];
            $configItems[$name] = $section;
        }

        foreach (self::PAYMENTS_MODULE as $moduleName => $value) {
            if ($this->moduleList->has($moduleName)) {
                $configItems[$moduleName] = $value;
            }
        }

        return $configItems;
    }

    /**
     * @return array
     */
    private function generateIsobarItems($menu)
    {
        $isobar = [];
        foreach ($this->getMenuIterator($menu) as $menuItem) {
            if ($this->isCollectedNode($menuItem)) {
                $isobar[] = $menuItem->getId();
            }
            if ($menuItem->hasChildren()) {
                foreach ($this->generateIsobarItems($menuItem->getChildren()) as $menuChild) {
                    $isobar[] = $menuChild;
                }
            }
        }

        return $isobar;
    }

    /**
     * @param $menuItem
     *
     * @return bool
     */
    private function isCollectedNode($menuItem)
    {
        if (strpos($menuItem->getId(), 'Isobar') === false
            || strpos($menuItem->getId(), 'Isobar_Base') !== false) {
            return false;
        }

        if (empty($menuItem->getAction()) || (strpos($menuItem->getAction(), 'system_config') === false)) {
            return true;
        }

        return false;
    }

    /**
     * Get menu filter iterator
     *
     * @param \Magento\Backend\Model\Menu $menu
     *
     * @return \Magento\Backend\Model\Menu\Filter\Iterator
     */
    private function getMenuIterator($menu)
    {
        return $this->iteratorFactory->create(['iterator' => $menu->getIterator()]);
    }

    private function generateConfigItems()
    {
        $result = [];
        $configTabs = $this->configStructure->getTabs();
        $config = $this->findResourceChildren($configTabs, 'isobar');

        if ($config) {
            foreach ($config as $item) {
                $data = $item->getData('resource');
                if (isset($data['resource'], $data['id']) && $data['id']) {
                    $result[$data['resource']] = $data;
                }
            }
        }

        return $result;
    }

    /**
     * @param Iterator $config
     * @param string   $name
     *
     * @return Iterator|null
     */
    private function findResourceChildren($config, $name)
    {
        /** @var Tab|null $currentNode */
        $currentNode = null;
        foreach ($config as $node) {
            if ($node->getId() === $name) {
                $currentNode = $node;
                break;
            }
        }

        if ($currentNode) {
            return $currentNode->getChildren();
        }

        return null;
    }
}
