<?php

namespace Isobar\BirkenstockSetup\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddColorGroupDescIdAttributeToProduct implements DataPatchInterface
{

    private ModuleDataSetupInterface $moduleDataSetup;

    private EavSetupFactory $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(ModuleDataSetupInterface $moduleDataSetup, EavSetupFactory $eavSetupFactory)
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return AddWidthAttributeToProduct|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Laminas\Validator\Exception\ExceptionInterface
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttribute(
            Product::ENTITY,
            'color_group_desc_id',
            [
                'type' => 'varchar',
                'label' => 'Color group desc id',
                'input' => 'select',
                'source' => Table::class,
                'required' => false,
                'option' => ['values' => [
                    "Multi",
                    "Red",
                    "White",
                    "Blue",
                    "Yellow",
                    "Green",
                    "Purple",
                    "Black",
                    "Pink",
                    "Beige",
                    "Grey",
                    "Brown",
                    "Orange",
                    "Silver",
                    "Denim",
                    "Misc",
                ]]
            ]
        );

        $ATTRIBUTE_GROUP = 'General'; // Attribute Group Name
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $allAttributeSetIds = $eavSetup->getAllAttributeSetIds($entityTypeId);
        foreach ($allAttributeSetIds as $attributeSetId) {
            $groupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, $ATTRIBUTE_GROUP);
            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $groupId,
                'color_group_desc_id',
                null
            );
        }

        $this->moduleDataSetup->endSetup();
    }
}
