<?php
declare(strict_types = 1);

namespace Isobar\Megamenu\Model\Provider;

/**
 * Class FieldsByStore
 * @package Isobar\Megamenu\Model\Provider
 */
class FieldsByStore
{
    /**
     * @var array
     */
    private $fieldsByStoreCustom;

    /**
     * @var array
     */
    private $fieldsByStoreCategory;

    /**
     * FieldsByStore constructor.
     * @param array $fieldsByStoreCustom
     * @param array $fieldsByStoreCategory
     */
    public function __construct(
        array $fieldsByStoreCustom = [],
        array $fieldsByStoreCategory = []
    ) {
        $this->fieldsByStoreCustom = $fieldsByStoreCustom;
        $this->fieldsByStoreCategory = $fieldsByStoreCategory;
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        return $this->fieldsByStoreCustom;
    }

    /**
     * @return array
     */
    public function getCategoryFields()
    {
        return $this->fieldsByStoreCategory;
    }
}
