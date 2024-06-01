<?php

$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['attendance_holidays'] = [
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
            'name' => 'FK_ATTENDANCE_HOLIDAYS_VID_AOV_VID',
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
    'name' => [
        'type' => 'varchar(255)',
        'is_null' => false,
    ],
    'valid_from' => [
        'type' => 'date',
        'is_null' => false,
    ],
    'valid_until' => [
        'type' => 'date',
        'is_null' => false,
    ],
    'description'=> [
        'type' => 'text',
        'is_null' => true,
    ],
    'total_days'=> [
        'type' => 'int(11)',
        'default' => 0,
    ],
    'status'=> [
        'type' => 'tinyint(11)',
        'is_null' => true,
        'default' => 1,
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ]
];