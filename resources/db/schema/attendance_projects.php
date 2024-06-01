<?php
/**
 *
 * Schema definition for 'attendance_projects'
 *
 * Last update: 2020-04-07
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['attendance_projects'] = [
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
            'name' => 'FK_ATTENDANCE_PROJECTS_VID',
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
    'title' => [
        'type' => 'varchar(255)',
        'is_null' => false,
    ],
    'description' => [
        'type' => 'text',
        'is_null' => true,
    ],
    'address' => [
        'type' => 'varchar(255)',
        'is_null' => true,
    ],
    'start_date' => [
        'type' => 'varchar(120)',
        'is_null' => false,
    ],
    'end_date' => [
        'type' => 'varchar(120)',
        'is_null' => true,
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
    ],
];