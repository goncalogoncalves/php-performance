# php-performance
Class responsible for assisting in performance evaluation of a PHP code

Example:
```php
use Performance;
$performance = new Performance();
$performance->addStep("1");
usleep(300000); // 0.30s
$performance->addStep("2");
usleep(100000); // 0.10s
$performance->addStep("3");
$steps = $performance->getSteps('time desc');
$report = $performance->buildReport();
$resultSave = $performance->saveReport('performance.txt');
```
