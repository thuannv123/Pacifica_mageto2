<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\Menu\Content;

use Magento\Framework\Data\Tree\Node;
use Magento\Framework\View\LayoutInterface;

/**
 * Class Resolver
 * @package Isobar\Megamenu\Model\Menu\Content
 */
class Resolver
{
    /**
     * @var string
     */
    const CHILD_CATEGORIES = '{{child_categories_content}}';

    /**
     * @var string
     */
    const CHILD_CATEGORIES_PAGE_BUILDER = '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div data-content-type="mega_menu_widget" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;">{{child_categories_content}}</div></div></div>';

    /**
     * @var array
     */
    private $categoriesHtml = [];

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * Resolver constructor.
     * @param LayoutInterface $layout
     */
    public function __construct(
        LayoutInterface $layout
    ) {
        $this->layout = $layout;
    }

    /**
     * @param Node $node
     * @return string|null
     */
    public function resolve(Node $node): ?string
    {
        if ($node->getIsCategory()) {
            $content = $this->parseVariables($node, $this->getDefaultContent());
        }

        return $content ?? null;
    }

    /**
     * @return string
     */
    private function getDefaultContent(): string
    {
        return self::CHILD_CATEGORIES;
    }

    /**
     * @param Node $node
     * @param string|null $content
     * @return string
     */
    protected function parseVariables(Node $node, ?string $content): string
    {
        preg_match_all('@\{{(.+?)\}}@', $content, $matches);

        if (isset($matches[1]) && !empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $result = '';

                switch ($match) {
                    case 'child_categories_content':
                        $result = $node->hasChildren() ? $this->getChildCategoriesContent() : '';
                        break;
                }

                $content = str_replace('{{' . $match . '}}', $result, $content);
            }
        }

        return $content;
    }

    /**
     * @return string
     */
    private function getChildCategoriesContent(): string
    {
        return '<!-- ko scope: "index = tree_wrapper" --> '
            . '<!-- ko template: getTemplate() --><!-- /ko --> '
            . '<!-- /ko -->';
    }
}
