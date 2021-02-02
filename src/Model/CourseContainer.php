<?php


namespace Kommunikatisten\ContaoScheduleBundle\Model;




use Kommunikatisten\ContaoScheduleBundle\Entity\Course;

class CourseContainer {

    public Course $course;
    public int $courseTimeMinutes;
    public int $rowMinutes;
    private string $title;
    private string $css;
    private int $duration;
    private string $start;
    private string $end;
    private bool $isFuture;
    private string $startDate;
    private string $room;

    /**
     * CourseContainer constructor.
     * @param Course $course
     * @param string $title
     * @param string $room
     * @param string $css
     * @param int $duration
     * @param string $start
     * @param string $end
     * @param bool $isFuture
     * @param string $startDate
     */
    public function __construct(
        Course $course, int $courseTimeMinutes, int $rowMinutes,
        string $title, string $room, string $css, int $duration, string $start, string $end, bool $isFuture, string $startDate) {
        $this->course = $course;
        $this->courseTimeMinutes = $courseTimeMinutes; $this->rowMinutes = $rowMinutes;
        $this->title = $title;
        $this->room = $room;
        $this->css = $css;
        $this->duration = $duration;
        $this->start = $start;
        $this->end = $end;
        $this->isFuture = $isFuture;
        $this->startDate = $startDate;
    }

    /**
     * @return Course
     */
    public function getCourse(): Course {
        return $this->course;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getRoom(): string {
        return $this->room;
    }

    /**
     * @return string
     */
    public function getCss(): string {
        return $this->css;
    }

    /**
     * @return int
     */
    public function getDuration(): int {
        return $this->duration;
    }

    /**
     * @return string
     */
    public function getStart(): string {
        return $this->start;
    }

    /**
     * @return string
     */
    public function getEnd(): string {
        return $this->end;
    }

    /**
     * @return bool
     */
    public function isFuture(): bool {
        return $this->isFuture;
    }

    /**
     * @return string
     */
    public function getStartDate(): string {
        return $this->startDate;
    }
}
