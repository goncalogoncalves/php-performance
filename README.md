# php-performance
Class responsible for assisting in performance evaluation of a PHP code

#### Example:
```php
use Performance;
$performance = new Performance();
$performance->addStep("1");
usleep(300000); // 0.30s
$performance->addStep("2");
usleep(100000); // 0.10s
$performance->addStep("3");
$steps = $performance->getSteps();
$report = $performance->buildReport();
//$resultSave = $performance->saveReport('performance.txt');

print_r("<pre>");
var_dump($report);
print_r("</pre>");

```
#### Output:
```
___ NEW REPORT ___  2017-02-23 18:40:38

NEW STEP: 1
Memory (usage: 14.8 mb / peak: 14.84 mb)

NEW STEP: 2
Memory (usage: 14.8 mb / peak: 14.84 mb)
Duration from _1_ to _2_:
0.3005 seconds  (Minutes: 0 / Seconds: 0)

NEW STEP: 3
Memory (usage: 14.8 mb / peak: 14.84 mb)
Duration from _2_ to _3_:
0.1006 seconds  (Minutes: 0 / Seconds: 0)

Execution time: 0.401 seconds
```

#### Install with composer:
```
composer require devgo/php-performance
```
