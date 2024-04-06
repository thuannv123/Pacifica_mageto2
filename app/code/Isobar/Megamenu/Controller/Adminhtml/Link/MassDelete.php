<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Controller\Adminhtml\Link;

use Isobar\Megamenu\Api\Data\Menu\LinkInterface;

/**
 * Class MassDelete
 * @package Isobar\Megamenu\Controller\Adminhtml\Link
 */
class MassDelete extends AbstractMassAction
{
    /**
     * @inheritdoc
     */
    protected function itemAction(LinkInterface $link)
    {
        $this->repository->deleteById($link->getEntityId());
    }

    /**
     * @inheritdoc
     */
    protected function getErrorMessage()
    {
        return __('We can\'t delete item right now. Please review the log and try again.');
    }

    /**
     * @inheritdoc
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been deleted.', $collectionSize);
        }

        return __('No records have been deleted.');
    }
}
