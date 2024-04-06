<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */

namespace Amasty\Ogrid\Plugin\Framework\View\Element\UiComponent\DataProvider\SearchResult;

use Amasty\Ogrid\Utils\MainAliasResolver;
use Magento\Framework\DB\Select;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;
use Zend_Db_Select_Exception;

class AddAliasIfNotExists
{
    /**
     * @var MainAliasResolver
     */
    private $mainAliasResolver;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        MainAliasResolver $mainAliasResolver,
        LoggerInterface $logger
    ) {
        $this->mainAliasResolver = $mainAliasResolver;
        $this->logger = $logger;
    }

    public function beforeLoad(SearchResult $subject): void
    {
        $select = $subject->getSelect();

        if (!$subject->isLoaded() && $mainAlias = $this->mainAliasResolver->resolve($select)) {
            try {
                $where = $select->getPart(Select::WHERE);
                $createdAtField = sprintf('`%s`', OrderInterface::CREATED_AT);
                $createdAtFieldWithAlias = sprintf('`%s`.`%s`', $mainAlias, OrderInterface::CREATED_AT);
                $isReset = false;

                foreach ($where as $key => $condition) {
                    if (strpos($condition, $createdAtField) !== false
                        && strpos($condition, $createdAtFieldWithAlias) === false
                    ) {
                        $where[$key] = str_replace(
                            $createdAtField,
                            $createdAtFieldWithAlias,
                            $condition
                        );

                        $isReset = true;
                    }
                }

                if ($isReset) {
                    $select->reset(Select::WHERE);
                    $select->setPart(Select::WHERE, $where);
                }
            } catch (Zend_Db_Select_Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
