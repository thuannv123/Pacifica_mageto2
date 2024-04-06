<?php

namespace Marvelic\Catalog\Plugin\ProductRender;

use Magento\Catalog\Api\Data\ProductRender\ImageExtensionInterfaceFactory;

class Image
{
    /**
     * @var ImageExtensionInterfaceFactory
     */
    protected $extensionAttributes;

    /**
     * Image type constructor.
     * @param ImageExtensionInterfaceFactory $extensionFactory
     */
    public function __construct(
        ImageExtensionInterfaceFactory $extensionAttributes
    ) {
        $this->extensionAttributes = $extensionAttributes;
    }

    /**
     * @param $subject
     * @param $result
     * @param $rateModel
     * @return mixed
     */
    public function afterGetExtensionAttributes($subject, $result)
    {
        if (null === $result) {
            $result = $this->extensionAttributes->create();
        }
        $result->setImageType($subject->getImageType());
        return $result;
    }
}
