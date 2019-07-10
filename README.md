[![Open Source Love](https://badges.frapsoft.com/os/v2/open-source.svg?v=103)](https://github.com/ellerbrock/open-source-badges/)
[![MIT Licence](https://badges.frapsoft.com/os/mit/mit.svg?v=103)](https://opensource.org/licenses/mit-license.php)

# php-performance

Class responsible for assisting in performance evaluation of a PHP code

#### Example:

```php
use Devgo\Performance;
$performance = new Performance();
$performance->addStep("1");
usleep(300000); // 0.30s
$performance->addStep("2");
usleep(100000); // 0.10s
$performance->addStep("3");
$steps = $performance->getSteps();
$report = $performance->buildReport();

print_r("<pre>");
var_dump($report);
print_r("</pre>");

```

#### Output:

```
-------------------------------------------------------
REPORT

FROM 1 to 2:  0.3006 seconds  (minutes: 0  seconds: 0)  (memory: 396.36 Kb  peak: 396.36 Kb)
FROM 2 to 3:  0.1009 seconds  (minutes: 0  seconds: 0)  (memory: 397.17 Kb  peak: 397.17 Kb)

Execution time: 0.4015 seconds  (2019-07-10 11:25:37)
-------------------------------------------------------
```

#### Install with composer:

```
composer require devgo/php-performance
```
