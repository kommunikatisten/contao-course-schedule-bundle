<?php


namespace Kommunikatisten\ContaoScheduleBundle\Entity;

/**
 * Class Course
 * @package Kommunikatisten\ContaoScheduleBundle\Entity
 */
class Course extends AbstractEntity {


    private string $name = '';
    private string $startTime = '';
    private string $startDate = '';
    private string $endDate = '';
    private string $endTime = '';
    private int $dayIndex = 0;
    private array $teachers = [];
    private Subject $subject;
    private Room $room;

    /**
     * Course constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->subject = new Subject();
        $this->room = new Room();
    }


    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return Room|null
     */
    public function getRoom(): ?Room {
        return $this->room;
    }

    /**
     * @return string
     */
    public function getStartTime(): string {
        return $this->startTime;
    }

    /**
     * @return string
     */
    public function getStartDate(): string {
        return $this->startDate;
    }

    /**
     * @return string
     */
    public function getEndDate(): string {
        return $this->endDate;
    }

    /**
     * @return string
     */
    public function getEndTime(): string {
        return $this->endTime;
    }

    /**
     * @return int
     */
    public function getDayIndex(): int {
        return $this->dayIndex;
    }

    /**
     * @return array
     */
    public function getTeachers(): array {
        return $this->teachers;
    }

    /**
     * @return Subject|null
     */
    public function getSubject(): ?Subject {
        return $this->subject;
    }


    /**
     * @param array $values
     * @param bool $initRelations
     * @return Course
     */
    public static function apply(array $values, bool $initRelations = true): Course {
        $course = new Course();
        if (!empty($values['course_id'])) {
            $course->id = intval($values['course_id']);
        }
        if (!empty($values['course_last_modified'])) {
            $course->lastModified = $values['course_last_modified'];
        }
        if (!empty($values['course_name'])) {
            $course->name = $values['course_name'];
        }
        if (!empty($values['course_day_index'])) {
            $course->dayIndex = $values['course_day_index'];
        }
        if (!empty($values['course_start_time'])) {
            $course->startTime = substr($values['course_start_time'], 0, 5);
        }
        if (!empty($values['course_start_date'])) {
            $course->startDate = $values['course_start_date'];
        } else {
            $course->startDate = "";
        }
        if (!empty($values['course_end_date'])) {
            $course->endDate = $values['course_end_date'];
        } else {
            $course->endDate = "";
        }
        if (!empty($values['course_end_time'])) {
            $course->endTime = substr($values['course_end_time'], 0, 5);
        }
        if ($initRelations && !empty($values['room_id'])) {
            $course->room = Room::apply($values, false);
        } elseif (!empty($values['course_room_id'])) {
            $course->room = Room::apply(['room_id' => $values['course_room_id']], false);
        }
        if ($initRelations && !empty($values['subject_id'])) {
            $course->subject = Subject::apply($values, false);
        } elseif (!empty($values['course_subject_id'])) {
            $course->subject = Subject::apply(['subject_id' => $values['course_subject_id']], false);
        }
        if ($initRelations && !empty($values['teacher_id'])) {
            $course->teachers[] = Teacher::apply($values, false);
        } elseif (!empty($values['course_teachers'])) { // from form input
            $course->teachers = array();
            foreach ($values['course_teachers'] as $teacher) {
                $course->teachers[] = Teacher::apply($teacher, false);
            }
        }
        return $course;
    }

    /**
     * @param array $values
     * @param bool $cascade
     */
    public function merge(array $values, bool $cascade = true): void {
        Teacher::merging($values, $this->teachers, $cascade);
    }

    public static function merging(array $values, array &$target, bool $cascade): void {
        if ($cascade && !empty($values['course_id'])) {
            $found = array_filter($target, function (Course $entity) use ($values): bool {
                return $values['course_id'] === $entity->getId();
            });
            if (empty($found)) {
                $target[] = self::apply($values, $cascade);
            } else {
                $idx = array_keys($found)[0];
                $found[$idx]->merge($values, $cascade);
            }
        }
    }

    public function serialize(bool $encode) {
        $serialized = [
            'course_id' => $this->getId(),
            'course_name' => $this->name,
            'course_start_time' => $this->startTime,
            'course_end_time' => $this->endTime,
            'course_day_index' => $this->dayIndex,
            'course_start_date' => $this->startDate,
            'course_subject' => null == $this->subject ? null : $this->subject->serialize(false),
            'course_teachers' => parent::serializeChilds($this->teachers)
        ];
        return $encode ? json_encode($serialized) : $serialized;
    }
}
