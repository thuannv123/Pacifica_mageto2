<?php
namespace Marvelic\Export\Controller\Adminhtml\Export\Job;

class Save extends \Firebear\ImportExport\Controller\Adminhtml\Export\Job\Save
{
    protected $additionalFields = [
        'enable_last_entity_id',
        'last_entity_id',
        'language',
        'divided_additional',
        'use_api',
        'only_admin',
        'cron_groups',
        'email_type',
        'template',
        'receiver',
        'sender',
        'copy',
        'copy_method',
        'is_attach',
        'behavior_field_source_code'
    ];
}
