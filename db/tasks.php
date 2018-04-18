<?php // $Id: tasks.php

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
    'classname' => '\mod_multichoice\task\multichoice_scheduled',                                                          
    'blocking' => 0,                                                                                             
    'minute' => '*/5',
    'hour' => '*',
    'day' => '*',
    'dayofweek' => '*',
    'month' => '*'
    )
);
