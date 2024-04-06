<?php

namespace Isobar\LineBot\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SendMessageToCustomer implements ObserverInterface
{
    /**
     * @var \Isobar\LineBot\Service\Messages
     */
    private $messages;
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * SendMessageToCustomer constructor.
     * @param \Isobar\LineBot\Service\Messages $messages
     * @param Session $customerSession
     */
    public function __construct(\Isobar\LineBot\Service\Messages $messages, Session $customerSession)
    {
        $this->messages = $messages;
        $this->customerSession = $customerSession;
    }

    /**
     * @param Observer $observer
     * @return bool
     */
    public function execute(Observer $observer)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getData('order');
        $incrementId = "#" . $order->getIncrementId();
        $userID = $this->customerSession->getSocialId();
        $message = "Thanks for order our shop! \n Your order is $incrementId. \n If you have questions about your order, you can email us at support@example.com.";
        $this->messages->pushMessage($userID, $message);
        return true;
    }
}
