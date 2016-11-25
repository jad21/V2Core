<?php
namespace V2\Core\Utils;

class RenderTime
{
    const PRECISION_SECOND      = 0;
    const PRECISION_MILLISECOND = 1;
    const PRECISION_MICROSECOND = 2;

    private $start;
    private $end;
    private $format = "d/m/y H:i:s";

    public function __construct($autoinit = true)
    {
        $this->start      = null;
        $this->end        = null;
        $this->start_time = null;
        $this->end_time   = null;
        if ($autoinit) {
            $this->start();
        }
    }

    public function start()
    {
        $this->start      = microtime(true);
        $this->start_time = time();
        $this->end        = null;
        return $this;
    }
    public function date($time = null)
    {
        return date($this->getFormat(), $time ?: time());
    }
    public function date_start()
    {
        is_null($this->start_time) && $this->start();
        return $this->date($this->start_time);
    }
    public function date_end()
    {
        if (is_null($this->start_time)) {
            return 'Can\'t return the render time';
        }

        is_null($this->end_time) && $this->end();
        return $this->date($this->end_time);
    }
    public function setFormat($format)
    {
        $this->format = $format;
    }
    public function getFormat()
    {
        return $this->format;
    }
    public function end()
    {
        $this->end      = microtime(true);
        $this->end_time = time();
        return $this;
    }
    public function getStarTime()
    {
        return $this->start_time;
    }
    public function duration()
    {
        if (is_null($this->start_time)) {
            return 'Can\'t return the render time';
        }
        if (is_null($this->end_time)) {
            $this->end();
        }
        $secs = $this->end_time - $this->start_time;
        $bit  = array(
            'y' => $secs / 31556926 % 12,
            'w' => $secs / 604800 % 52,
            'd' => $secs / 86400 % 7,
            'h' => $secs / 3600 % 24,
            'm' => $secs / 60 % 60,
            's' => $secs % 60
        );
        $ret = [];
        foreach ($bit as $k => $v) {
            if ($v > 0) {
                $ret[] = $v . $k;
            }
        }
        $result = join(' ', $ret);
        if(empty(trim($result))){
            return $this->getRenderTime(self::PRECISION_SECOND,4);
        }
        return $result;;
    }

    /**
     * This function return the time the code use to process
     * @param $precision the precision wanted, with const. second, millisecond and microsecond available (default PRECISION_MILLISECOND)
     * @param $floatingPrecision the number of numbers after the floating point (default 0)
     * @param $showUnit precise if the unit should be returned (default true)
     * @return the render time in the precision asked. Note that the precision is ±0.5 the precision (eq. 5s is at least 4.5s and at most 5.5s) <br/>
     * The code have an error about 2 or 3µs (time to execute the end function)
     */
    public function getRenderTime($precision = self::PRECISION_SECOND, $floatingPrecision = 2, $showUnit = true)
    {

        $test = is_int($precision) && $precision >= self::PRECISION_SECOND && $precision <= self::PRECISION_MICROSECOND &&
        is_float($this->start) && is_float($this->end) && $this->start <= $this->end &&
        is_int($floatingPrecision) && $floatingPrecision >= 0 &&
        is_bool($showUnit);

        if ($test) {
            $duration = round(($this->end - $this->start) * 10 ** ($precision * 3), $floatingPrecision);

            if ($showUnit) {
                return $duration . ' ' . self::getUnit($precision);
            } else {
                return $duration;
            }

        } else {
            return 'Can\'t return the render time';
        }
    }
    public function __toString()
    {
        return $this->duration();
    }

    private static function getUnit($precision)
    {
        switch ($precision) {
            case self::PRECISION_SECOND:
                return 's';
            case self::PRECISION_MILLISECOND:
                return 'ms';
            case self::PRECISION_MICROSECOND:
                return 'µs';
            default:
                return '(no unit)';
        }
    }
}
