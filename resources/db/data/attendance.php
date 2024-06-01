<?php

use Siberian\Feature;

 $module = (new Installer_Model_Installer_Module())
        ->prepare('Attendance');

#Code to add report link
Feature::installCronjob(
    "Attendance auto checkout",
    "Attendance_Model_Attendance::Autocheckout",
    -1,
    -1,
    -1,
    -1,
    -1,
    true,
    5,
    false,
    $module->getId()
);