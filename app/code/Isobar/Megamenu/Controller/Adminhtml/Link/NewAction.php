<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Controller\Adminhtml\Link;

/**
 * Class NewAction
 * @package Isobar\Megamenu\Controller\Adminhtml\Link
 */
class NewAction extends Edit
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Isobar_Megamenu::menu_links';
}
