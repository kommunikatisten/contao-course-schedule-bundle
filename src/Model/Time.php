<?php


namespace Kommunikatisten\ContaoScheduleBundle\Model;


class Time {

    public const SECONDS = 0;
    public const MINUTES = 1;
    public const HOURS = 2;


    private int $hour = 0;
    private int $min = 0;
    private int $sec = 0;

    /**
     * Time constructor.
     * @param int $hour
     * @param int $min
     * @param int $sec
     */
    public function __construct(int $hour = 0, int $min = 0, int $sec = 0) {
        $this->hour = $hour ;
        $this->min = $min;
        $this->sec = $sec;
    }

    /**
     * @param string $asString
     * @return Time
     */
    public static function fromString(string $asString): Time {
        list($hour, $min, $sec) = explode(":", $asString);
        return new Time($hour ? $hour : 0, $min ? $min : 0, $sec ? $sec : 0);
    }

    /**
     * @param Time $time
     * @return int
     */
    public function compare(Time $time): int {
        return $this->format(self::SECONDS) - $time->format(self::SECONDS);
    }

    public function format(int $type) : int {
        switch ($type) {
            case self::SECONDS: return $this->hour * 60 * 60 + $this->min * 60 + $this->sec;
            case self::MINUTES: return $this->hour * 60 + $this->min + ($this->sec > 30 ? 1 : 0);
            case self::HOURS: return $this->hour + ($this->min > 30 ? 1 : 0);
        }
    }



}
