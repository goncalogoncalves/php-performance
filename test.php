<?php

require_once 'Performance.class.php';

$performance = new Devgo\Performance();
$performance->addStep("1");
usleep(300000); // 0.30s
$performance->addStep("2");
usleep(100000); // 0.10s
$performance->addStep("3");
$steps = $performance->getSteps('time desc');
$report = $performance->buildReport();

print_r('<pre>');
var_dump($report);
print_r('</pre>');
