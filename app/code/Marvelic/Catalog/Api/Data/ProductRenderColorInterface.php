<?php

namespace Marvelic\Catalog\Api\Data;

/**
 * Interface ProductRenderColorInterface
 * @api
 */
interface ProductRenderColorInterface
{
    /**
     * Get the color value
     *
     * @return string|null
     */
    public function getColor();

    /**
     * Set the color value
     *
     * @param string $color
     * @return $this
     */
    public function setColor($color);

    /**
     * Get the option ID value
     *
     * @return int|null
     */
    public function getOptionId();

    /**
     * Set the option ID value
     *
     * @param int $optionId
     * @return $this
     */
    public function setOptionId($optionId);

    /**
     * Get the option value
     *
     * @return string|null
     */
    public function getOptionValue();

    /**
     * Set the option value
     *
     * @param string $optionValue
     * @return $this
     */
    public function setOptionValue($optionValue);

    /**
     * Get the option product id
     *
     * @return string|null
     */
    public function getOptionProductId();

    /**
     * Set the option product id
     *
     * @param string $optionProductId
     * @return $this
     */
    public function setOptionProductId($optionProductId);

    /**
     * Get the option thumb
     *
     * @return string|null
     */
    public function getOptionThumb();

    /**
     * Set the option thumb
     *
     * @param string $optionThumb
     * @return $this
     */
    public function setOptionThumb($optionThumb);

    /**
     * Get the option type
     *
     * @return int|null
     */
    public function getOptionType();

    /**
     * Set the option type
     *
     * @param int $optionType
     * @return $this
     */
    public function setOptionType($optionType);
}
