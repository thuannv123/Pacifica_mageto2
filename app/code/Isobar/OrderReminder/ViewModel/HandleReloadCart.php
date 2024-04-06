<?php

namespace Isobar\OrderReminder\ViewModel;

use Isobar\OrderReminder\Model\HandleReloadCartCookie;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class HandleReloadCart implements ArgumentInterface
{
    private HandleReloadCartCookie $handleReloadCartCookie;

    /**
     * @param HandleReloadCartCookie $handleReloadCartCookie
     */
    public function __construct(HandleReloadCartCookie $handleReloadCartCookie)
    {
        $this->handleReloadCartCookie = $handleReloadCartCookie;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function isMustReloadCartSection()
    {
       if ($this->handleReloadCartCookie->readReloadCartCookie()) {
           $this->handleReloadCartCookie->removeReloadCartCookie();
           return true;
       }

       return false;
    }

}
