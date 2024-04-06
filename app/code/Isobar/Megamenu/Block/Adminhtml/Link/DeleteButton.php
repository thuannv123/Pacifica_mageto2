<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Block\Adminhtml\Link;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 * @package Isobar\Megamenu\Block\Adminhtml\Link
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        $linkId = $this->getLinkId();
        if ($linkId) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to delete this?'
                ) . '\', \'' . $this->getUrlBuilder()->getUrl('*/*/delete', ['id' => $linkId]) . '\')',
                'sort_order' => 20,
            ];
        }

        return $data;
    }
}
