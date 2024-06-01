<?php

$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['attendance_settings'] = [
    'id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'app_id' => [
        'type' => 'int(11)',
        'is_null' => false
    ],
    'value_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'application_option_value',
            'column' => 'value_id',
            'name' => 'FK_ATTENDANCE_SETTINGS_VID_AOV_VID',
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
    'check_in_history_enable'  => [
        'type' => 'int(11) unsigned',
        'default' => '1'
    ],
    'auto_checkout_time'  => [
        'type' => 'int(11) unsigned',
        'default' => '15'
    ],
    'min_working_hours'  => [
        'type' => 'int(11) unsigned',
        'default' => '8'
    ],  
    'financial_year' => [
        'type' => 'int(11)',
        'default' => '1'
    ],
    'admin_email' => [
        'type' => 'varchar(255)',
    ],
    'leave_enable' => [
        'type' => 'int(11)',
        'default' => '1'
    ],
    'check_in_out_enable' => [
        'type' => 'int(11)',
        'default' => '1'
    ],
    'fixed_working_hours' => [
        'type' => 'int(11)',
        'default' => '0'
    ],
    'is_message_admin' => [
        'type' => 'int(11)',
        'default' => '1'
    ],    
    'business_hour_start' => [
        'type' => 'varchar(50)',
        'is_null' => true,
    ],
    'business_hour_end' => [
        'type' => 'varchar(50)',
        'is_null' => true,
    ],
    'late_count_time' => [
        'type' => 'int(11)',
        'default' => '60',
        'is_null' => true,
    ],    
    'leave_apply_enable' => [
        'type' => 'int(11)',
        'default' => '1'
    ],
    'total_leave_bar_color' => [
        'type' => 'varchar(50)',
        'is_null' => false,
        'default' => '#ef5350'
    ],
    'inprogress_leave_bar_color' => [
        'type' => 'varchar(50)',
        'is_null' => false,
        'default' => '#2196f3'
    ],
    'remaining_leave_bar_color' => [
        'type' => 'varchar(50)',
        'is_null' => false,
        'default' => '#66bb6a'
    ],
    'date_format' => [
        'type' => 'varchar(50)',
        'default' => 'd-m-Y',
    ],
    'time_format' => [
        'type' => 'varchar(50)',
        'default' => 'h:m A',
    ],
    'qr_scan_enable' => [
        'type' => 'int(11)',
        'default' => '0'
    ],
    'project_tracking_enable' => [
        'type' => 'int(11)',
        'default' => '0'
    ],
    'holiday_enable' => [
        'type' => 'int(11)',
        'default' => '1'
    ],
    'is_checkout_enable' => [
        'type' => 'int(11)',
        'default' => '1'
    ],
    'qr_checkin_note' => [
        'type' => 'text',
        'is_null' => true,
    ],
    'timezone' => [
        'type' => 'varchar(100)',
        'is_null' => true,
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ]
];