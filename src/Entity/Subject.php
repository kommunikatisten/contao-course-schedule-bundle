<?php


namespace Kommunikatisten\ContaoScheduleBundle\Entity;

/**
 * Class Course
 * @package Kommunikatisten\ContaoScheduleBundle\Model
 */
class Subject extends AbstractEntity {

    private string $name;
    private string $description;
    private array $teachers;
    private array $courses;

    /**
     * @param array $values
     * @param bool $initRelations
     * @return Subject
     */
    public static function apply(array $values, bool $initRelations = true): Subject {
        $subject = new Subject();
        $subject->teachers = array();
        $subject->courses = array();
        if(!empty($values['subject_id'])) {
            $subject->id =  $values['subject_id'];
        }
        if(!empty($values['subject_name'])) {
            $subject->name =  $values['subject_name'];
        }
        if(!empty($values['subject_description'])) {
            $subject->description =  $values['subject_description'];
        }

        if($initRelations && !empty($values['teacher_id'])) {
            $subject->teachers[] =  Teacher::apply($values, false);
        }
        if($initRelations && !empty($values['course_id'])) {
            $subject->courses[] = Course::apply($values, false);
        }
        return $subject->fix();
    }

    /**
     * @param array $values
     */
    public function merge(array $values): void {
        if(!empty($values['subject_id'])) {
            $this->subjects[] =  Subject::apply($values, false);
        }
        $this->fix();
    }
}
