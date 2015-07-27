<?php
/**
 * Measuring tool to verify the script execution time and memory consumption
 *
 *
 * @package ResourceMeter
 * @author     Paweł Sikora <pascal.sikora@gmail.com>
 * @version 1.0
 */
class ResourceMeter{
    /**
     * @var array
     */
    private static $times = array(
      'hour'   => 3600000,
      'minute' => 60000,
      'second' => 1000
    );

    /**
     * @var float
     */
   private $startPartTime;

    /**
     * @var float
     */
    private $requestTime;

    /**
     * Constructor
     */
    public function __construct(){

        $this->start();
    }


    public function start(){

        if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            $this->requestTime = $_SERVER['REQUEST_TIME_FLOAT'];
            $this->startPartTime = $_SERVER['REQUEST_TIME_FLOAT'];
        }else {
            $this->requestTime = microtime(TRUE);
            $this->startPartTime = microtime(TRUE);
        }
    }


    /**
     * Starts the timer.
     */
    public function startPart(){

         $this->startPartTime = microtime(TRUE);
         return true;
    }


    /**
     * Formats the elapsed time as a string.
     *
     * @param  float $time
     * @return string
     */
    public function secondsToTimeString($time){

        $ms = round($time * 1000);

        foreach (self::$times as $unit => $value) {
            if ($ms >= $value) {
                $time = floor($ms / $value * 100.0) / 100.0;
                return $time . ' ' . ($time == 1 ? $unit : $unit . 's');
            }
        }

        return $ms . ' ms';
    }

    /**
     * Return human redable memory size
     * 
     * @param integer $size
     */
    private function memoryToString($size){

        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];

    }

    /**
     * Formats the elapsed time since the start of the request as a string.
     *
     * @return string
     */
    public function timeSincePartOfRequest(){

        return $this->secondsToTimeString(microtime(TRUE) - $this->startPartTime);
    }

    /**
     * Formats the elapsed time since the start of the request as a string.
     *
     * @return string
     */
    public function timeSinceStartOfRequest(){

        return $this->secondsToTimeString(microtime(TRUE) - $this->requestTime);
    }


    /**
     * Returns the resources (time, memory) of the request as a string.
     * - stop measuring, set timer to next part
     *
     * @return string
     */
    public function resourcePartUsage(){

        $toReturn = sprintf(
          'Part time: %s, Memory: %4.2fMb, Memory in peak: %4.2fMb',
          $this->timeSincePartOfRequest(),
          $this->memoryToString(memory_get_usage(TRUE)),
          $this->memoryToString(memory_get_peak_usage(TRUE))
        );

        $this->startPart();

        return $toReturn;

    }

    /**
     * Returns the resources (time, memory) of the request as a string.
     *
     * @return string
     */
    public function resourceUsage(){

        return sprintf(
          'Time: %s, Memory: %4.2fMb, Memory in peak: %4.2fMb',
          $this->timeSinceStartOfRequest(),
          $this->memoryToString(memory_get_usage(TRUE)),
          $this->memoryToString(memory_get_peak_usage(TRUE))
        );

    }

    
    
}
