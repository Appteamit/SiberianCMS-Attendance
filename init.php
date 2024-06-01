<?php
use Siberian\Assets;
use Siberian\Translation;
use Siberian_Module as Module;

$init = function($bootstrap) {
    
    Assets::registerScss([
        '/app/local/modules/Attendance/features/attendance/scss/attendance.scss'
    ]);
    
    Translation::registerExtractor(
        'attendance',
        'Attendance',
        '/app/local/modules/Attendance/resources/translations/default/attendance.po');

};

