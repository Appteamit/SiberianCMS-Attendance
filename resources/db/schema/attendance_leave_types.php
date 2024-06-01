<?php

$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['attendance_leave_types'] = [
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
            'name' => 'FK_ATTENDANCE_LEAVE_TYPES_VID_AOV_VID',
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
    'entitlement' => [
        'type' => 'varchar(255)',
        'is_null' => false,
    ],
    'is_carry_forword' => [
        'type' => 'int(11)',
        'is_null' => false,
        'default' => 0
    ],
    'status' => [
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