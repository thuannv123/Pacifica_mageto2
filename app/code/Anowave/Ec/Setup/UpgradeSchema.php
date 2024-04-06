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

namespace Anowave\Ec\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Updates DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        
        if (version_compare($context->getVersion(), '101.0.0') < 0)
        {
            $sql = [];
            
            $sql[] = "SET foreign_key_checks = 0";
            
            $sql[] = "CREATE TABLE IF NOT EXISTS " . $setup->getTable('ae_ec') . " (ec_id bigint(21) NOT NULL AUTO_INCREMENT,ec_order_id bigint(21) DEFAULT NULL,ec_cookie_ga varchar(255) DEFAULT NULL,PRIMARY KEY (ec_id)) ENGINE=InnoDB DEFAULT CHARSET=latin1";
            
            $sql[] = "SET foreign_key_checks = 1";
            
            foreach ($sql as $query)
            {
                $setup->run($query);
            }
        }
        
        if (version_compare($context->getVersion(), '101.0.5') < 0)
        {
            $setup->getConnection()->addColumn(...[$setup->getTable('ae_ec'), 'ec_track',
            [
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable'  => true,
                'after'     => 'ec_id',
                'comment'   => 'Track flag'
            ]]);
        }
        
        if (version_compare($context->getVersion(), '101.0.6') < 0)
        {
            $setup->getConnection()->addColumn(...[$setup->getTable('ae_ec'), 'ec_user_agent',
            [
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable'  => true,
                'after'     => 'ec_cookie_ga',
                'comment'   => 'Track user agent'
            ]]);
        }
        
        if (version_compare($context->getVersion(), '101.0.7') < 0)
        {
            $setup->getConnection()->addColumn(...[$setup->getTable('ae_ec'), 'ec_placed_at',
            [
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                'nullable'  => true,
                'after'     => 'ec_user_agent',
                'comment'   => 'Track placed timestamp',
                'default'   => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
            ]]);
            
            $setup->getConnection()->addColumn(...[$setup->getTable('ae_ec'), 'ec_updated_at',
            [
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                'nullable'  => true,
                'after'     => 'ec_placed_at',
                'comment'   => 'Track placed timestamp',
                'default'   => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE
            ]]);
        }
        
        if (version_compare($context->getVersion(), '101.3.8') < 0)
        {
            $setup->getConnection()->addColumn(...[$setup->getTable('ae_ec'), 'ec_order_type',
            [
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable'  => false,
                'default'   => '0',
                'after'     => 'ec_order_id',
                'comment'   => 'Type'
            ]]);
        }
        
        if (version_compare($context->getVersion(), '101.4.3') < 0)
        {
            $setup->getConnection()->addIndex
            (
                $setup->getTable('ae_ec'), $setup->getIdxName('ec_order_id', ['ec_order_id']), ['ec_order_id']
            );
        }
        
        if (version_compare($context->getVersion(), '101.4.4') < 0)
        {
            $setup->getConnection()->addIndex
            (
                $setup->getTable('ae_ec'), $setup->getIdxName('ec_track', ['ec_track', 'ec_order_type']), ['ec_track','ec_order_type']
            );
        }
        
        if (version_compare($context->getVersion(), '101.5.0') < 0)
        {
            $setup->getConnection()->addIndex
            (
                $setup->getTable('ae_ec'), 'unique_order_id', ['ec_order_id'], \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            );
            
            /**
             * @todo Remove potential duplicate entries
             */
            if (false)
            {
                $sql = [];
                
                $sql[] = "SET foreign_key_checks = 0";
                
                $sql[] = "DELETE t1 FROM $setup->getTable('ae_ec') t1 INNER JOIN $setup->getTable('ae_ec') t2 WHERE  t1.ec_id < t2.ec_id AND t1.ec_order_id = t2.ec_order_id";
                
                $sql[] = "SET foreign_key_checks = 1";
            }
        }
        
        if (version_compare($context->getVersion(), '101.7.2') < 0)
        {
            $sql = [];
            
            $sql[] = "SET foreign_key_checks = 0";
            
            $sql[] = "CREATE TABLE IF NOT EXISTS {$setup->getTable('ae_ec_gdpr')} (consent_id bigint(21) NOT NULL AUTO_INCREMENT,consent_uuid varchar(255) DEFAULT NULL,consent_ip bigint(8) DEFAULT NULL,consent text,consent_type tinyint(1) NOT NULL DEFAULT '0',consent_timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (consent_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            
            $sql[] = "SET foreign_key_checks = 1";
            
            foreach ($sql as $query)
            {
                $setup->run($query);
            }
        }
        
        if (version_compare($context->getVersion(), '103.0.2') < 0)
        {
            $sql = [];
            
            $sql[] = "SET foreign_key_checks = 0";
            
            $sql[] = "CREATE TABLE IF NOT EXISTS {$setup->getTable('ae_ec_log')} (log_id bigint(21) NOT NULL AUTO_INCREMENT,log longtext,log_timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (log_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            
            $sql[] = "SET foreign_key_checks = 1";
            
            foreach ($sql as $query)
            {
                $setup->run($query);
            }
        }
        
        
        $setup->endSetup();
    }
}