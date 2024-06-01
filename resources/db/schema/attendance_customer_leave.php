<?php

$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['attendance_customer_leave'] = [
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
            'name' => 'FK_ATTENDANCE_CUSTOMERS_LEAVE_AOV_VID',
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
    'customer_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'customer',
            'column' => 'customer_id',
            'name' => 'FK_ATTENDANCE_CUSTOMERS_CUSTOMER_LEAVE_CID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'customer_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],
    'leave_type_id' => [
        'type' => 'int(11)',
        'default' => '0'
    ],
    'start_date' => [
        'type' => 'datetime',
        'is_null' => false
    ],
    'end_date' => [
        'type' => 'datetime',
        'is_null' => true
    ],
    'total_days' => [
        'type' => 'int(11)',
        'default' => '0'
    ],
    'comment' => [
        'type' => 'text',
        'is_null' => true
    ],
    'admin_id' => [
        'type' => 'int(11)',
        'default' => '0'
    ],
    'status' => [
        'type' => 'tinyint(11)',
        'default' => '0'
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ]
];