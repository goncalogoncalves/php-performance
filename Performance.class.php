<?php

namespace Devgo;

/**
 * Class responsible for assisting in performance evaluation of a PHP code.
 *
 * Example:
 * $performance = new Devgo\Performance();
 * $performance->addStep("1");
 * usleep(300000); // 0.30s
 * $performance->addStep("2");
 * var_dump($performance->buildReport());
 */
class Performance
{
    public array $steps = [];

    public string $report = '';

    /**
     * Setup initial data
     */
    public function __construct()
    {
        $this->steps['process_id'] = getmypid();
        $this->steps['start_time'] = date('Y-m-d G:i:s');
        $this->steps['execution_time'] = 0;
    }

    /**
     * Responsible to build a report of the process
     *
     * @return string report
     */
    public function buildReport(): string
    {
        $report = PHP_EOL.PHP_EOL.'REPORT PERFORMANCE'.PHP_EOL;
        $lastStep = [];

        for ($pos = 0; $pos < count($this->steps) - 3; $pos++) {

            if (! empty($lastStep)) {
                $diffStepsDuration = $this->getStepDuration(
                    $lastStep['time'],
                    $this->steps[$pos]['time']
                );
                $report .= PHP_EOL.'FROM '.$lastStep['name'].' to '.$this->steps[$pos]['name'].':  ';
                $report .= $diffStepsDuration['duration'].' seconds';
                $report .= "\t (memory: ".$this->steps[$pos]['memory_usage_size'];
                $report .= ' / peak: '.$this->steps[$pos]['memory_peak_size'].')';
            }

            $lastStep = $this->steps[$pos];
        }

        $executionTime = round($this->steps['execution_time'], 2);
        $executionTimeMinutes = round($executionTime / 60, 2);

        $report .= PHP_EOL.PHP_EOL.'Execution time: '.$executionTime.' seconds (minutes: '.$executionTimeMinutes.')';
        $report .= PHP_EOL.'Start: '.$this->steps['start_time'];
        $report .= PHP_EOL.'End: '.date('Y-m-d G:i:s').PHP_EOL;

        $this->report = $report;

        return $report;
    }

    /**
     * Add a step to the process, getting usefull information
     *
     * @param  string  $name name of the step
     * @return bool if it was well succeeded
     */
    public function addStep(string $name = ''): bool
    {
        $timeStart = microtime(true);
        $nowMemoryUsage = memory_get_usage();
        $nowMemoryPeak = memory_get_peak_usage();

        $arrayNewStep = [];
        $arrayNewStep['name'] = $name;
        $arrayNewStep['time'] = $timeStart;
        $arrayNewStep['memory_usage'] = $nowMemoryUsage;
        $arrayNewStep['memory_usage_size'] = $this->convertSize($nowMemoryUsage);
        $arrayNewStep['memory_peak'] = $nowMemoryPeak;
        $arrayNewStep['memory_peak_size'] = $this->convertSize($nowMemoryPeak);

        // check last step time
        $lastStep = end($this->steps);

        $differenceToLastStep = [];
        if (isset($lastStep['time'])) {
            $differenceToLastStep = $this->getStepDuration($lastStep['time'], $timeStart);
        }

        $this->steps[] = $arrayNewStep;
        $this->steps['execution_time'] += $differenceToLastStep['duration'] ?? 0;

        return true;
    }

    /**
     * Get two times and return duration between them
     *
     * @return array     duration between times
     */
    private function getStepDuration($timeStart = null, $timeEnd = null): array
    {
        $duration = 0;
        $hours = 0;
        $minutes = 0;
        $seconds = 0;

        if ($timeStart !== null && $timeEnd !== null) {
            $duration = $timeEnd - $timeStart;
            $hours = (int) ($duration / 60 / 60);
            $minutes = (int) ($duration / 60) - $hours * 60;
            $seconds = $duration - $hours * 60 * 60 - $minutes * 60;
        }

        return [
            'duration' => round($duration, 2),
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds,
        ];
    }

    /**
     * Convert size for better reading
     *
     * @param  int  $size variable to be converted
     * @return string converted string
     */
    private function convertSize(int $size = 0): string
    {
        $unit = ['b', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb'];

        return @round($size / pow(1024, ($pos = floor(log($size, 1024)))), 2).' '.$unit[$pos];
    }
}
