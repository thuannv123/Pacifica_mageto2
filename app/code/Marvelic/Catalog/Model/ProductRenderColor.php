<?php

namespace Marvelic\Catalog\Model;

use Marvelic\Catalog\Api\Data\ProductRenderColorInterface;

/**
 * Class ProductRenderColor
 */
class ProductRenderColor implements ProductRenderColorInterface
{
    /**
     * @var string|null
     */
    private $color;

    /**
     * @var string|null
     */
    private $optionId;

    /**
     * @var string|null
     */
    private $optionValue;

    /**
     * @var string|null
     */
    private $optionProductId;

    /**
     * @var string|null
     */
    private $optionThumb;

    /**
     * @var int|null
     */
    private $optionType;

    /**
     * @var int|null
     */
    private $optionLimit;

    /**
     * Get the color value
     *
     * @return string|null
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set the color value
     *
     * @param int $color
     * @return $this
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Get the option ID value
     *
     * @return int|null
     */
    public function getOptionId()
    {
        return $this->optionId;
    }

    /**
     * Set the option ID value
     *
     * @param string $optionId
     * @return $this
     */
    public function setOptionId($optionId)
    {
        $this->optionId = $optionId;
        return $this;
    }

    /**
     * Get the option ID value
     *
     * @return string|null
     */
    public function getOptionValue()
    {
        return $this->optionValue;
    }

    /**
     * Set the option value
     *
     * @param string $optionValue
     * @return $this
     */
    public function setOptionValue($optionValue)
    {
        $this->optionValue = $optionValue;
        return $this;
    }

    /**
     * Set the option product id
     *
     * @param string $optionProductId
     * @return $this
     */
    public function setOptionProductId($optionProductId)
    {
        $this->optionProductId = $optionProductId;
        return $this;
    }

    /**
     * Get the option product id
     *
     * @return string|null
     */
    public function getOptionProductId()
    {
        return $this->optionProductId;
    }

    /**
     * Set the option thumb
     *
     * @param string $optionThumb
     * @return $this
     */
    public function setOptionThumb($optionThumb)
    {
        $this->optionThumb = $optionThumb;
        return $this;
    }

    /**
     * Get the option thumb
     *
     * @return string|null
     */
    public function getOptionThumb()
    {
        return $this->optionThumb;
    }

    /**
     * Set the option type
     *
     * @param int $optionType
     * @return $this
     */
    public function setOptionType($optionType)
    {
        $this->optionType = $optionType;
        return $this;
    }

    /**
     * Get the option type
     *
     * @return int|null
     */
    public function getOptionType()
    {
        return $this->optionType;
    }
}
