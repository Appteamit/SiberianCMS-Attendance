<?php
/**
 *
 * Schema definition for 'attendance_messages'
 *
 * Last update: 2020-05-17
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['attendance_messages'] = [
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
            'name' => 'FK_ATTENDANCE_MESSAGES_VID',
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
    'sender_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'receiver_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'message' => [
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => false,
    ],
    'is_read' => [
        'type' => 'tinyint(1)',
        'default' => '0'
    ],
    'is_edit' => [
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