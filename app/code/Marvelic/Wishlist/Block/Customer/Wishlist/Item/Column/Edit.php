<?php

namespace Marvelic\Wishlist\Block\Customer\Wishlist\Item\Column;


class Edit extends \Magento\Wishlist\Block\Customer\Wishlist\Item\Column\Edit
{
    public function _getHelper()
    {
        return $this->_wishlistHelper;
    }

}
