<?php

namespace Isobar\FreeShippingProcessor\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;

class Freeshipping extends \Magento\OfflineShipping\Model\Carrier\Freeshipping
{
	/**
	 * Check subtotal for allowed free shipping
	 *
	 * @param RateRequest $request
	 *
	 * @return bool
	 */
	private function isFreeShippingRequired(RateRequest $request): bool
	{
		$minSubtotal = $request->getPackageValueWithDiscount();
		if ($request->getBaseSubtotalWithDiscountInclTax()
			&& $this->getConfigFlag('tax_including')) {
			$minSubtotal = $request->getBaseSubtotalWithDiscountInclTax();
		}

        // Temporary workaround for amasty free gift issue
		if ($minSubtotal < 0) {
			return true;
		}

		return $minSubtotal >= $this->getConfigData('free_shipping_subtotal');
	}

	/**
	 * FreeShipping Rates Collector
	 *
	 * @param RateRequest $request
	 * @return \Magento\Shipping\Model\Rate\Result|bool
	 */
	public function collectRates(RateRequest $request)
	{
		if (!$this->getConfigFlag('active')) {
			return false;
		}

		/** @var \Magento\Shipping\Model\Rate\Result $result */
		$result = $this->_rateResultFactory->create();

		$this->_updateFreeMethodQuote($request);

		if ($request->getFreeShipping() || $this->isFreeShippingRequired($request)) {
			/** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
			$method = $this->_rateMethodFactory->create();

			$method->setCarrier('freeshipping');
			$method->setCarrierTitle($this->getConfigData('title'));

			$method->setMethod('freeshipping');
			$method->setMethodTitle($this->getConfigData('name'));

			$method->setPrice('0.00');
			$method->setCost('0.00');

			$result->append($method);
		} elseif ($this->getConfigData('showmethod')) {
			$error = $this->_rateErrorFactory->create();
			$error->setCarrier($this->_code);
			$error->setCarrierTitle($this->getConfigData('title'));
			$errorMsg = $this->getConfigData('specificerrmsg');
			$error->setErrorMessage(
				$errorMsg ? $errorMsg : __(
					'Sorry, but we can\'t deliver to the destination country with this shipping module.'
				)
			);
			return $error;
		}
		return $result;
	}
}
