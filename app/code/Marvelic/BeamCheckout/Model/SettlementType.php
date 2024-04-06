<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Model;

class SettlementType extends \Magento\Payment\Model\Method\AbstractMethod
{
	public function toOptionArray()
	{
		return [['value' => 'authorize', 'label' => __('Manual')], ['value' => 'authorize_capture', 'label' => __('Auto (Default)')],];
	}
}
