<?php

$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['attendance_customers'] = [
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
            'name' => 'FK_ATTENDANCE_CUSTOMERS_VID_AOV_VID',
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
            'name' => 'FK_ATTENDANCE_CUSTOMERS_CUSTOMER_CID',
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
    'attendance_date' => [
        'type' => 'varchar(100)',
        'is_null' => false
    ],
    'startdate' => [
        'type' => 'varchar(100)',
    ],
    'enddate' => [
        'type' => 'varchar(100)',
        'is_null' => true,
    ],
    'startlatlong' => [
        'type' => 'varchar(100)',
        'is_null' => true,
        'default' => '0,0'
    ],
    'endlatlong' => [
        'type' => 'varchar(100)',
        'is_null' => true,
        'default' => '0,0'
    ],
    'totaltime' => [
        'type' => 'varchar(100)',
        'is_null' => true,
        'default' => '00:00:00'
    ],
    'startaddress' => [
        'type' => 'varchar(255)',
        'is_null' => true
    ],
    'endaddress' => [
        'type' => 'varchar(255)',
        'is_null' => true
    ],
    'status' => [
        'type' => 'tinyint(1)',
        'default' => '1'
    ],
    'location_id' => [
        'type' => 'int(11)',
        'default' => '0',
        'is_null' => true
    ],
    'project_id' => [
        'type' => 'int(11)',
        'default' => '0',
        'is_null' => true
    ],
    'note' => [
        'type' => 'text',
        'is_null' => true
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ]
];