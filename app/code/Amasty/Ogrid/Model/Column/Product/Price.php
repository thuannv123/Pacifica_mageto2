<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */
namespace Amasty\Ogrid\Model\Column\Product;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Price extends \Amasty\Ogrid\Model\Column\Product
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceFormatter;

    /**
     * BasePrice constructor.
     * @param $fieldKey
     * @param $resourceModel
     * @param \Amasty\Base\Model\Serializer $serializer
     * @param PriceCurrencyInterface $priceFormatter
     * @param \Magento\Framework\DB\Helper $dbHelper
     * @param array $columns
     * @param string $primaryKey
     * @param string $foreignKey
     */
    public function __construct(
        $fieldKey,
        $resourceModel,
        \Amasty\Base\Model\Serializer $serializer,
        PriceCurrencyInterface $priceFormatter,
        \Magento\Framework\DB\Helper $dbHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        $columns = [],
        $primaryKey = 'entity_id',
        $foreignKey = 'entity_id'
    ) {
        $this->_priceFormatter = $priceFormatter;

        parent::__construct(
            $fieldKey,
            $resourceModel,
            $serializer,
            $dbHelper,
            $moduleManager,
            $columns,
            $primaryKey,
            $foreignKey
        );
    }

    public function modifyItem(&$item, $config = [])
    {
        parent::modifyItem($item, $config);
        $currencyCode = empty($item['order_id']) || empty($config[$item['order_id']])
            ? null
            : $config[$item['order_id']]['order_currency_code'];

        $item[$this->_alias_prefix . $this->_fieldKey] = $this->_priceFormatter->format(
            $item[$this->_alias_prefix . $this->_fieldKey],
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            null,
            $currencyCode
        );
    }
}
