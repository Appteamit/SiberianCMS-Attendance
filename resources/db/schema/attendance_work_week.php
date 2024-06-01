<?php

$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['attendance_work_week'] = [
    'id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'value_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'application_option_value',
            'column' => 'value_id',
            'name' => 'FK_ATTENDANCE_WORK_WEEK_VID_AOV_VID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'value_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ]
    ],
    'monday' => [
        'type' => 'tinyint(1)',
        'default' => '0'
    ],
    'tuesday' => [
        'type' => 'tinyint(1)',
        'default' => '0'
    ],
    'wednesday' => [
        'type' => 'tinyint(1)',
        'default' => '0'
    ],
    'thursday' => [
        'type' => 'tinyint(1)',
        'default' => '0'
    ],
    'friday' => [
        'type' => 'tinyint(1)',
        'default' => '0'
    ],
    'saturday' => [
        'type' => 'tinyint(1)',
        'default' => '0'
    ],
    'sunday' => [
        'type' => 'tinyint(1)',
        'default' => '0'
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ]
];