[![Open Source Love](https://badges.frapsoft.com/os/v2/open-source.svg?v=103)](https://github.com/ellerbrock/open-source-badges/)
[![MIT Licence](https://badges.frapsoft.com/os/mit/mit.svg?v=103)](https://opensource.org/licenses/mit-license.php)

# php-performance

Class responsible for assisting in performance evaluation of a PHP code

#### Example:

```php
use Devgo\Performance;

$performance = new Performance();

$performance->addStep('1');
usleep(200000); // 0.20s
$performance->addStep('2');
usleep(100000); // 0.10s
$performance->addStep('3');
usleep(400000); // 0.40s
$performance->addStep('4');
usleep(300000); // 0.30s
$performance->addStep('5');

var_dump($performance->buildReport());

```

#### Output:

```
REPORT PERFORMANCE

FROM 1 to 2:  0.2 seconds        (memory: 420.66 Kb / peak: 468.52 Kb)
FROM 2 to 3:  0.11 seconds       (memory: 421.11 Kb / peak: 468.52 Kb)
FROM 3 to 4:  0.4 seconds        (memory: 421.55 Kb / peak: 468.52 Kb)
FROM 4 to 5:  0.31 seconds       (memory: 422 Kb / peak: 468.52 Kb)

Execution time: 1.02 seconds (minutes: 0.02)
Start: 2024-02-14 14:34:20
End: 2024-02-14 14:34:21
```

#### Install with composer:

```
composer require devgo/php-performance
```
