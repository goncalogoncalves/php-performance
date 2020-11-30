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
$performance->addStep("4");
usleep(200000); // 0.20s
$performance->addStep("5");
usleep(400000); // 0.40s
$performance->addStep("6");

$report = $performance->buildReport();

print_r("<pre>");
var_dump($report);
print_r("</pre>");

```

#### Output:

```
REPORT

FROM 1 to 2:  0.3 seconds       (minutes: 0)  (memory: 425.1 Kb  peak: 
474.7 Kb)
FROM 2 to 3:  0.1 seconds       (minutes: 0)  (memory: 425.91 Kb  peak: 474.7 Kb)
FROM 3 to 4:  0 seconds         (minutes: 0)  (memory: 426.73 Kb  peak: 474.7 Kb)
FROM 4 to 5:  0.2 seconds       (minutes: 0)  (memory: 427.54 Kb  peak: 474.7 Kb)
FROM 5 to 6:  0.4 seconds       (minutes: 0)  (memory: 428.35 Kb  peak: 474.7 Kb)

Execution time: 1 seconds (minutes: 0.02)
Start: 2020-11-30 16:00:26
End: 2020-11-30 16:00:27
```

#### Install with composer:

```
composer require devgo/php-performance
```
