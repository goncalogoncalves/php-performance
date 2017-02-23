<?php

/**
* Class responsible for assisting in performance evaluation of a PHP code.
* Example:
* use Performance;
* $performance = new Performance();
* $performance->addStep("1");
* usleep(300000); // 0.30s
* $performance->addStep("2");
* usleep(100000); // 0.10s
* $performance->addStep("3");
* $steps = $performance->getSteps('time desc');
* $report = $performance->buildReport();
* $resultSave = $performance->saveReport('performance.txt');
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
        $this->steps = array();
        $processID = $this->getProcessID();
        $this->steps["process_id"] = $processID;
        $this->steps["execution_time"] = 0;
    }

    /**
    * responsible to build a report of the process
    * @return string report
    */
    public function buildReport()
    {
        $dateNow = date("Y-m-d G:i:s");

        $report = '';
        $report .= PHP_EOL.'___ NEW REPORT ___  '.$dateNow;

        $lastStep = null;

        $steps = $this->steps;

        for ($i=0; $i < sizeof($steps); $i++) {

            if (isset($steps[$i]["time"])) {

                $stepName            = isset($steps[$i]["name"]) ? $steps[$i]["name"] : "";
                $stepTime            = $steps[$i]["time"];
                $stepMemoryUsage     = $steps[$i]["memory_usage"];
                $stepMemoryUsageSize = $steps[$i]["memory_usage_size"];
                $stepMemoryPeak      = $steps[$i]["memory_peak"];
                $stepMemoryPeakSize  = $steps[$i]["memory_peak_size"];

                $report .= PHP_EOL.PHP_EOL.'NEW STEP: '.$stepName;
                $report .= PHP_EOL.'Memory (usage: '.$stepMemoryUsageSize.' / peak: '.$stepMemoryPeakSize.')';

                if ($lastStep != null) {
                    $differenceStepsDuration = $this->getDuration($lastStep["time"], $stepTime);
                    $report .= PHP_EOL.'Duration from _'. $lastStep["name"] .'_ to _'.$stepName.'_:';
                    $report .= PHP_EOL. round($differenceStepsDuration["duration"], 4).' seconds  ' .'(Minutes: '. $differenceStepsDuration["minutes"].' / Seconds: '. $differenceStepsDuration["seconds"].')';
                }

                $lastStep = $this->steps[$i];
            }
        }

        $report .= PHP_EOL.PHP_EOL;
        $report .= 'Execution time: '. round($this->steps["execution_time"], 4) .' seconds';
        $report .= PHP_EOL.PHP_EOL;

        $this->report = $report;

        return $report;
    }

    /**
    * get all the steps from the process
    * @return array steps
    */
    public function getSteps($sort = null)
    {
        $steps = $this->steps;

        if ($sort == null || $sort == "asc") {

            $steps = $steps;

        }elseif ($sort == "desc") {

            $steps = array_reverse($steps);

        }elseif ($sort == "time desc") {

            // Obtain a list of columns
            foreach ($steps as $key => $row) {
                $mid[$key]  = $row['difference_last_step']["duration"];
            }

            // Sort the data with mid descending
            // Add $steps as the last parameter, to sort by the common key
            array_multisort($mid, SORT_DESC, $steps);
        }elseif ($sort == "time asc") {

            foreach ($steps as $key => $row) {
                $mid[$key]  = $row['difference_last_step']["duration"];
            }

            array_multisort($mid, SORT_ASC, $steps);

        }else{
            $steps = $steps;
        }
        return $steps;
    }

    /**
    * add a step to the process, getting usefull information
    * @param boolean if it was well succeeded
    */
    public function addStep($name = "")
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

        /*$timeEnd = microtime(true);
        $stepDuration = $this->getDuration($timeStart, $timeEnd);
        $arrayNewStep["step_duration"] = $stepDuration;*/

        $differenceToLastStep = 0;
        // check last step time
        $lastStep = end($this->steps);
        if (isset($lastStep["time"])) {
            //$lastStepTime = $lastStep["time"];
            $differenceToLastStep = $this->getDuration($lastStep["time"], $timeStart);
        }

        $arrayNewStep["difference_last_step"] = $differenceToLastStep;

        array_push($this->steps, $arrayNewStep);

        $this->steps["execution_time"] += $differenceToLastStep["duration"];

        return true;
    }

    /**
    * responsible to save the report in a file
    * @param  string $fileName the file where the report will be saved
    * @return boolean          state of the operation
    */
    public function saveReport($fileName = null, $fileAppend = true)
    {
        if ($this->report != "") {
            if (file_exists($fileName)) {
                if ($fileAppend == true) {
                    $resultSave = file_put_contents($fileName, $this->report, FILE_APPEND);
                }else{
                    $resultSave = file_put_contents($fileName, $this->report);
                }

                if ($resultSave > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
    * get two times and return duration between
    * @param  int $timeStart inicial time
    * @param  int $timeEnd   finish time
    * @return array          duration between times
    */
    public function getDuration($timeStart = null, $timeEnd = null)
    {
        if ($timeStart != null & $timeEnd != null) {
            $duration   = $timeEnd - $timeStart;
            $hours      = (int) ($duration / 60 / 60);
            $minutes    = (int) ($duration/60) - $hours * 60;
            $seconds    = (int) $duration - $hours * 60 * 60 - $minutes * 60;

            $arrayTime = array(
                'duration' => $duration,
                'hours'    => $hours,
                'minutes'  => $minutes,
                'seconds'  => $seconds
            );

            return $arrayTime;

        }else{
            return false;
        }

    }

    /**
    * get memory usage
    * @return int memory usage
    */
    public function getMemoryUsage()
    {
        return memory_get_usage();
    }

    /**
    * get memory peak
    * @return int memory peak
    */
    public function getMemoryPeak()
    {
        return memory_get_peak_usage();
    }

    /**
    * PHP process ID
    * @return int php process id
    */
    public function getProcessID()
    {
        return getmypid();
    }

    /**
    * convert size for better reading
    * @param  string $size variable to be converted
    * @return string       converted string
    */
    function convertSize($size)
    {
        $unit = array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }



}