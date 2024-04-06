<?php
 
namespace Isobar\Megamenu\Model;
 
use Isobar\Megamenu\Api\AjaxInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Store\Model\StoreManagerInterface;
use Isobar\Megamenu\Block\Html\Topmenu;

class CustomApi implements AjaxInterface
{
    protected $httpContext;
    protected $storeManager;
    protected $resultPageFactory;
    protected $urlModel;
    protected $resultJsonFactory;
    protected $resultLayoutFactory;
    protected $categoryRepository;
    protected $treeResolver;
    protected $storeManagerInterface;
    public $_layoutFactory;

    public $menu;
 
    public function __construct(
        HttpContext $httpContext,
        StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Isobar\Megamenu\Model\Menu\TreeResolver $treeResolver,
        Topmenu $menu
    ) {
        $this->httpContext = $httpContext;
        $this->storeManager = $storeManager;
         $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->_layoutFactory = $layoutFactory;
        $this->categoryRepository = $categoryRepository;
        $this->treeResolver = $treeResolver;
        $this->menu = $menu;
    }
 
    public function getMenuMobile($param)
    {
        $html = '';
        $category_id = $param;
        $menu = $this->treeResolver->get((int)$this->storeManager->getStore()->getId());

        $children = $menu->getChildren();
        $parentLevel = $menu->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;

        foreach ($children as $child) {
            $categoryId = explode("-",$child->getId())[2];
            if($category_id == $categoryId){
                $category_name = $child->getName();
                $category_url  = $child->getUrl();
               
                $child->setLevel($childLevel);

                $turnOnMega = $child->getMmTurnOn();
               
                if ($turnOnMega && ($childLevel == 0)) {
                    $block = $this->_layoutFactory->create()->createBlock(
                        'Isobar\Megamenu\Block\Html\Topmenu\Block',
                        '',
                        ['data' => ['mode' => $child->getMmMenuType()]]
                    );
                    $child->setMenuLevel(1);
                    $block->setNode($child);
                    if(strpos($child->getId(),'custom-node') !== false){
                        $html = $block->renderRows();
                    
                    }else{
                        $html = $block->renderBlock();
                    }
                    
                } else {
                    
                    $html = $this->menu->_addSubMenu(
                        $child,
                        $childLevel,
                        'submenu',
                        '3'
                    );
                }

            }
        }
        $data = [
            'html' => $html,
            'category_name' => $category_name,
            'category_url' => $category_url,
        ];
        return $data;
    }

}