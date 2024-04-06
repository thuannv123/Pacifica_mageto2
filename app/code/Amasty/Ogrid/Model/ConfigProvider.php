<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */

namespace Amasty\Ogrid\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Magento\Catalog\Model\Config\Source\Product\Thumbnail;

class ConfigProvider extends ConfigProviderAbstract
{
    /**
     * @var string
     */
    protected $pathPrefix = 'amasty_ogrid/';

    public const PRODUCT_IMAGE = 'general/product_image';

    public function isUseChildThumbnail(): bool
    {
        return $this->getValue(self::PRODUCT_IMAGE) === Thumbnail::OPTION_USE_OWN_IMAGE;
    }
}
