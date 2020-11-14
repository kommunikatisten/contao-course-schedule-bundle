<?php


namespace Kommunikatisten\ContaoScheduleBundle\Entity;

/**
 * Class Course
 * @package Kommunikatisten\ContaoScheduleBundle\Model

 */
class Course extends AbstractEntity {


    private string $name;
    private string $startTime;
    private string $endTime;
    private int $dayIndex;
    private array $teachers;
    private Subject $subject;

    /**
     * @param array $values
     * @param bool $initRelations
     * @return Course
     */
    public static function apply(array $values, bool $initRelations = true): Course {
        $course = new Course();
        $course->teachers = array();
        if(!empty($values['course_id'])) {
            $course->id =  $values['course_id'];
        }
        if(!empty($values['course_name'])) {
            $course->name =  $values['course_name'];
        }
        if($initRelations && !empty($values['subject_id'])) {
            $course->subject =  Subject::apply($values, false);
        }
        if($initRelations && !empty($values['teacher_id'])) {
            $course->teachers[] =  Teacher::apply($values, false);
        }
        return $course->fix();
    }

    /**
     * @param array $values
     */
    public function merge(array $values): void {
        if(!empty($values['teacher_id'])) {
            $this->teachers[] =  Teacher::apply($values, false);
        }
        $this->fix();
    }
}
