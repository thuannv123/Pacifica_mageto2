<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * https://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2023 Anowave (https://www.anowave.com/)
 * @license  	https://www.anowave.com/license-agreement/
 */

declare(strict_types=1);

namespace Anowave\Ec\Ui\Component\Listing\Column;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;

class Track extends Column
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteria;
    
    /**
     * @var \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory
     */
    protected $transactionsFactory;
    
    /**
     * Constructor 
     * 
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $criteria
     * @param \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionsFactory
     * @param array $components
     * @param array $data
     */
    public function __construct
    (
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteria,
        \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionsFactory,
        array $components = [],
        array $data = []
    ) 
    {
        /**
         * Set OrderRepositoryInterface
         * 
         * @var OrderRepositoryInterface $orderRepository
         */
        $this->orderRepository = $orderRepository;
        
        /**
         * Set SearchCriteriaBuilder
         * 
         * @var SearchCriteriaBuilder $searchCriteria
         */
        $this->searchCriteria  = $criteria;
        
        /**
         * Set transactions factory
         * 
         * @var \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionsFactory
         */
        $this->transactionsFactory = $transactionsFactory;
        
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    
    /**
     * Prepare data source
     * 
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource) : array
    {
        if (isset($dataSource['data']['items'])) 
        {
            foreach ($dataSource['data']['items'] as & $item) 
            {
                if (isset($item[$this->getData('name')]))
                {
                    if (is_null($item[$this->getData('name')]))
                    {
                        $item[$this->getData('name')] = __('-');
                    }
                    else
                    {
                        $item[$this->getData('name')] = 1 === (int) $item['ec_track'] ? __('Yes') : __('No');
                    }
                }
                else 
                {
                    $item[$this->getData('name')] = __('-');
                }
            }
        }
        
        return $dataSource;
    }
    
    /**
     * Apply sorting
     */
    protected function applySorting()
    {
        $sorting = $this->getContext()->getRequestParam('sorting');
        
        if (!empty($sorting['field']) && !empty($sorting['direction']) && $sorting['field'] === $this->getName() && in_array(strtoupper($sorting['direction']), ['ASC', 'DESC'], true))
        {
            $this->getContext()->getDataProvider()->addOrder('ec_track',strtoupper($sorting['direction']));
        }
    }
}