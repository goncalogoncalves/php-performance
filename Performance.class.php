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
 * $report = $performance->buildReport();
 * 
 */
class Performance 
{
    public ?array $steps = null;
    public ?string $report = null;

    /**
     * Setup initial data
     */
    public function __construct()
    {
        $processID = $this->getProcessID();

        $this->steps = array();
        $this->steps["process_id"] = $processID ?? '';
        $this->steps["execution_time"] = 0;
        $this->steps["start_time"] = date("Y-m-d G:i:s");
    }

    /**
     * Responsible to build a report of the process
     * @return string report
     */
    public function buildReport(): string
    {
        $report = PHP_EOL . PHP_EOL . 'REPORT ' . PHP_EOL;
        $lastStep = null;

        $steps = $this->steps ?? array();

        for ($pos=0; $pos < count($steps); $pos++) {

            if (isset($steps[$pos]["time"])) {

                $stepName            = isset($steps[$pos]["name"]) ? $steps[$pos]["name"] : "";
                $stepTime            = isset($steps[$pos]["time"]) ? $steps[$pos]["time"] : "";
                $stepMemoryUsageSize = isset($steps[$pos]["memory_usage_size"]) ? $steps[$pos]["memory_usage_size"] : "";
                $stepMemoryPeakSize  = isset($steps[$pos]["memory_peak_size"]) ? $steps[$pos]["memory_peak_size"] : "";

                if ($lastStep != null) {
                    $diffStepsDuration = $this->getStepDuration($lastStep["time"], $stepTime);
                    $report .= PHP_EOL . 'FROM ' . $lastStep["name"] . ' to ' . $stepName . ':  ';
                    $report .= $diffStepsDuration['duration'] . " seconds  \t(minutes: " . $diffStepsDuration['minutes'] . ")";
                    $report .= '  (memory: '.$stepMemoryUsageSize.'  peak: '.$stepMemoryPeakSize.')';
                }

                $lastStep = $this->steps[$pos];
            }
        }

        $executionTime = isset($this->steps["execution_time"]) ? round($this->steps["execution_time"], 2) : 0;
        $executionTimeMinutes = round($executionTime / 60, 2);
        $report .= PHP_EOL . PHP_EOL . 'Execution time: ' . $executionTime . ' seconds (minutes: ' . $executionTimeMinutes . ')';
        $report .= PHP_EOL . 'Start: ' . $this->steps["start_time"];
        $report .= PHP_EOL . 'End: ' . date("Y-m-d G:i:s") . '' . PHP_EOL . PHP_EOL;

        $this->report = $report;

        return $report;
    }

    /**
     * Get all the steps from the process
     * @param string type of sort
     * @return array steps
     */
    public function getSteps(string $sort = ""): array
    {
        $steps = $this->steps;

        if ($sort == "desc") {

            $steps = array_reverse($steps);

        }else if ($sort == "time desc") {

            // Obtain a list of columns
            foreach ($steps as $key => $row) {
                $mid[$key]  = isset($row['difference_last_step']["duration"]) ? $row['difference_last_step']["duration"] : '';
            }
            // Sort the data with mid descending
            // Add $steps as the last parameter, to sort by the common key
            array_multisort($mid, SORT_DESC, $steps);

        }else if ($sort == "time asc") {

            foreach ($steps as $key => $row) {
                $mid[$key]  =
                    isset($row['difference_last_step']["duration"]) ? $row['difference_last_step']["duration"] : '';
            }

            array_multisort($mid, SORT_ASC, $steps);
        }

        return $steps;
    }

    /**
     * Add a step to the process, getting usefull information
     * @param string name of the step
     * @return boolean if it was well succeeded
     */
    public function addStep(string $name = ""): bool
    {
        $timeStart         = microtime(true);
        $nowMemoryUsage    = $this->getMemoryUsage();
        $nowMemoryPeak     = $this->getMemoryPeak();

        $memoryUsageSize   = $this->convertSize($nowMemoryUsage);
        $nowMemoryPeakSize = $this->convertSize($nowMemoryPeak);

        $arrayNewStep                      = array();
        $arrayNewStep["name"]              = $name;
        $arrayNewStep["time"]              = $timeStart;
        $arrayNewStep["memory_usage"]      = $nowMemoryUsage;
        $arrayNewStep["memory_usage_size"] = $memoryUsageSize;
        $arrayNewStep["memory_peak"]       = $nowMemoryPeak;
        $arrayNewStep["memory_peak_size"]  = $nowMemoryPeakSize;

        $differenceToLastStep = 0;

        // check last step time
        $lastStep = end($this->steps);
        $lastStepTime = $lastStep["time"] ?? null;
        if (isset($lastStep["time"])) {
            $differenceToLastStep = $this->getStepDuration($lastStepTime, $timeStart);
        }

        $arrayNewStep["difference_last_step"] = $differenceToLastStep;

        array_push($this->steps, $arrayNewStep);

        $duration = isset($differenceToLastStep["duration"]) ? (float) $differenceToLastStep["duration"] : 0;
        $this->steps["execution_time"] += $duration;

        return true;
    }

    /**
     * Responsible to get the duration of the process until this point
     *
     * @return float
     */
    public function getDuration(): float
    {
        return isset($this->steps["execution_time"]) ? round($this->steps["execution_time"], 2) : 0;
    }

    /**
     * Get two times and return duration between them
     * @param  $timeStart inicial time
     * @param  $timeEnd   finish time
     * @return array          duration between times
     */
    private function getStepDuration($timeStart = null, $timeEnd = null): array
    {
        $duration = null;
        $hours = null;
        $minutes = null;
        $seconds = null;

        if ($timeStart != null && $timeEnd != null) {
            $duration   = $timeEnd - $timeStart;
            $hours      = (int) ($duration / 60 / 60);
            $minutes    = (int) ($duration/60) - $hours * 60;
            $seconds    = (int) $duration - $hours * 60 * 60 - $minutes * 60;

            $duration = round($duration, 2);
        }

        return array(
            'duration' => $duration,
            'hours'    => $hours,
            'minutes'  => $minutes,
            'seconds'  => $seconds
        );
    }

    /**
     * Get memory usage
     * @return int memory usage
     */
    private function getMemoryUsage(): int
    {
        return memory_get_usage();
    }

    /**
     * Get memory peak
     * @return int memory peak
     */
    private function getMemoryPeak(): int
    {
        return memory_get_peak_usage();
    }

    /**
    * PHP process ID
    * @return int php process id
    */
    private function getProcessID(): int
    {
        return getmypid();
    }

    /**
     * Convert size for better reading
     * @param  int $size variable to be converted
     * @return string       converted string
     */
    private function convertSize(int $size = 0): string
    {
        $unit = array('b','Kb','Mb','Gb','Tb','Pb');
        return @round($size/pow(1024,($pos=floor(log($size,1024)))),2).' '.$unit[$pos];
    }
}