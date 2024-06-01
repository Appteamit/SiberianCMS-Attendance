<?php
/**
 *
 * Schema definition for 'attendance_locations'
 *
 * Last update: 2020-04-07
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['attendance_locations'] = [
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
            'name' => 'FK_ATTENDANCE_LOCATIONS_VID',
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
    'qr_code' => [
        'type' => 'text',
        'is_null' => false,
    ],
    'address' => [
        'type' => 'text',
        'is_null' => true,
    ],
    'status' => [
        'type' => 'tinyint(1)',
        'default' => '0'
    ],
    'allow_scan' => [
        'type' => 'varchar(120)',
        'default' => 'all'
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];