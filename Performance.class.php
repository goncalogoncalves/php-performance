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
* usleep(100000); // 0.10s
* $performance->addStep("3");
* $steps = $performance->getSteps('time desc');
* $report = $performance->buildReport();
* 
*/
class Performance 
{
    public $steps;
    public $report;

    /**
    * setup initial data
    */
    public function __construct()
    {
        $processID = $this->getProcessID();

        $this->steps = array();
        $this->steps["process_id"] = $processID;
        $this->steps["execution_time"] = 0;
    }

    /**
    * responsible to build a report of the process
    * @return string report
    */
    public function buildReport(): string
    {
        $report = PHP_EOL.'-------------------------------------------------------';
        $report .= PHP_EOL.PHP_EOL.'REPORT'.PHP_EOL;

        $lastStep = null;

        $steps = $this->steps;

        for ($pos=0; $pos < count($steps); $pos++) {

            if (isset($steps[$pos]["time"])) {

                $stepName            = isset($steps[$pos]["name"]) ? $steps[$pos]["name"] : "";
                $stepTime            = $steps[$pos]["time"];
                $stepMemoryUsageSize = $steps[$pos]["memory_usage_size"];
                $stepMemoryPeakSize  = $steps[$pos]["memory_peak_size"];

                if ($lastStep != null) {
                    $diffStepsDuration = $this->getDuration($lastStep["time"], $stepTime);
                    $report .= PHP_EOL.'FROM '. $lastStep["name"] .' to '.$stepName.':  ';
                    $report .= $diffStepsDuration["duration"]. ' seconds  (minutes: '. $diffStepsDuration["minutes"].'  seconds: '. $diffStepsDuration["seconds"].')';
                    $report .= '  (memory: '.$stepMemoryUsageSize.'  peak: '.$stepMemoryPeakSize.')';
                }

                $lastStep = $this->steps[$pos];
            }
        }

        $report .= PHP_EOL.PHP_EOL.'Execution time: '. round($this->steps["execution_time"], 4) .' seconds  ('.date("Y-m-d G:i:s").')'.PHP_EOL;

        $this->report = $report;

        return $report;
    }

    /**
    * get all the steps from the process
    * @param string type of sort
    * @return array steps
    */
    public function getSteps($sort = ""): array
    {
        $steps = $this->steps;

        if ($sort == "desc") {

            $steps = array_reverse($steps);

        }else if ($sort == "time desc") {

            // Obtain a list of columns
            foreach ($steps as $key => $row) {
                $mid[$key]  = $row['difference_last_step']["duration"];
            }
            // Sort the data with mid descending
            // Add $steps as the last parameter, to sort by the common key
            array_multisort($mid, SORT_DESC, $steps);

        }else if ($sort == "time asc") {

            foreach ($steps as $key => $row) {
                $mid[$key]  = $row['difference_last_step']["duration"];
            }

            array_multisort($mid, SORT_ASC, $steps);
        }

        return $steps;
    }

    /**
    * add a step to the process, getting usefull information
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
        if (isset($lastStep["time"])) {
            $differenceToLastStep = $this->getDuration($lastStep["time"], $timeStart);
        }

        $arrayNewStep["difference_last_step"] = $differenceToLastStep;

        array_push($this->steps, $arrayNewStep);

        $this->steps["execution_time"] += $differenceToLastStep["duration"];

        return true;
    }

    /**
    * get two times and return duration between
    * @param  int $timeStart inicial time
    * @param  int $timeEnd   finish time
    * @return array          duration between times
    */
    public function getDuration($timeStart = null, $timeEnd = null): array
    {
        if ($timeStart != null & $timeEnd != null) {
            $duration   = $timeEnd - $timeStart;
            $hours      = (int) ($duration / 60 / 60);
            $minutes    = (int) ($duration/60) - $hours * 60;
            $seconds    = (int) $duration - $hours * 60 * 60 - $minutes * 60;

            $duration = round($duration, 4);

            return array(
                'duration' => $duration,
                'hours'    => $hours,
                'minutes'  => $minutes,
                'seconds'  => $seconds
            );
        }

        return array();
    }

    /**
    * get memory usage
    * @return int memory usage
    */
    public function getMemoryUsage(): int
    {
        return memory_get_usage();
    }

    /**
    * get memory peak
    * @return int memory peak
    */
    public function getMemoryPeak(): int
    {
        return memory_get_peak_usage();
    }

    /**
    * PHP process ID
    * @return int php process id
    */
    public function getProcessID(): int
    {
        return getmypid();
    }

    /**
    * convert size for better reading
    * @param  int $size variable to be converted
    * @return string       converted string
    */
    function convertSize(int $size = 0): string
    {
        $unit = array('b','Kb','Mb','Gb','Tb','Pb');
        return @round($size/pow(1024,($pos=floor(log($size,1024)))),2).' '.$unit[$pos];
    }

}
