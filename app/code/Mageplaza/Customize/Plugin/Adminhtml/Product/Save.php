<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CurrencyFormatter
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Customize\Plugin\Adminhtml\Product;

use Mageplaza\CurrencyFormatter\Helper\Data;

class Save
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Save constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param Data $helper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        Data                                    $helper
    ) {
        $this->request = $request;
        $this->helper = $helper;
    }

    /**
     * Save product action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */

    public function beforeExecute(\Magento\Catalog\Controller\Adminhtml\Product\Save $subject)
    {
        if ($this->helper->isEnabled()) {
            $data = $this->request->getPostValue('product');
            if (!strpos($data['price'], '.')) {
                $data['price'] = $data['price'] . '.';
            }
            $this->request->setPostValue('product', $data);
        }
    }
}
