<?php

namespace Marvelic\Rma\Controller\Returns;

class AddLabel extends \Magento\Rma\Controller\Returns\AddLabel
{
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        return parent::__construct($context, $coreRegistry);
    }

    /**
     * Add Tracking Number action
     *
     * @return void
     */
    public function execute()
    {
        if ($this->_loadValidRma()) {
            try {
                $rma = $this->_coreRegistry->registry('current_rma');

                if (!$rma->isAvailableForPrintLabel()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Shipping Labels are not allowed.'));
                }

                $response = false;
                $number = $this->getRequest()->getPost('number');
                $number = trim(strip_tags($number));
                $carrier = $this->getRequest()->getPost('carrier');
                $carriers = $this->_objectManager->get(
                    \Magento\Rma\Helper\Data::class
                )->getShippingCarriers(
                    $rma->getStoreId()
                );

                if (empty($number)) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Please enter a valid tracking number.')
                    );
                }

                if (!isset($carriers[$carrier])) {
                    /** @var $rmaShipping \Magento\Rma\Model\Shipping */
                    $rmaShipping = $this->_objectManager->create(\Magento\Rma\Model\Shipping::class);
                    $rmaShipping->setRmaEntityId(
                        $rma->getEntityId()
                    )->setTrackNumber(
                        $number
                    )->setCarrierCode(
                        $carrier
                    )->setCarrierTitle(
                        $carrier
                    )->save();
                } else {
                    /** @var $rmaShipping \Magento\Rma\Model\Shipping */
                    $rmaShipping = $this->_objectManager->create(\Magento\Rma\Model\Shipping::class);
                    $rmaShipping->setRmaEntityId(
                        $rma->getEntityId()
                    )->setTrackNumber(
                        $number
                    )->setCarrierCode(
                        $carrier
                    )->setCarrierTitle(
                        $carriers[$carrier]
                    )->save();
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $response = ['error' => true, 'message' => $e->getMessage()];
            } catch (\Exception $e) {
                $response = ['error' => true, 'message' => __('We can\'t add a label right now.')];
            }
        } else {
            $response = ['error' => true, 'message' => __('You selected the wrong RMA.')];
        }
        if (is_array($response)) {
            $this->_objectManager->get(
                \Magento\Framework\Session\Generic::class
            )->setErrorMessage($response['message']);
        }

        $this->_view->addPageLayoutHandles();
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
        return;
    }
}
