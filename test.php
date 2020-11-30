<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'Performance.class.php';

$performance = new Devgo\Performance();

$performance->addStep("1");
usleep(300000); // 0.30s
$performance->addStep("2");
usleep(100000); // 0.10s
$performance->addStep("3");

//$duration = $performance->getDuration();

$performance->addStep("4");
usleep(200000); // 0.20s
$performance->addStep("5");
usleep(400000); // 0.40s
$performance->addStep("6");

$report = $performance->buildReport();

//$steps = $performance->getSteps('time desc');

print_r('<pre>');
var_dump($report);
//var_dump($duration);
//var_dump($steps);
print_r('</pre>');