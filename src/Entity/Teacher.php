<?php


namespace Kommunikatisten\ContaoScheduleBundle\Entity;

/**
 * Class Teacher
 * @package Kommunikatisten\ContaoScheduleBundle\Model
 */
class Teacher extends AbstractEntity {

    private string $name;
    private array $subjects;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getSubjects(): array {
        return $this->subjects;
    }




    /**
     * @param array $values
     * @param bool $initRelations
     * @return Teacher
     */
    public static function apply(array $values, bool $initRelations = true): Teacher {
        $teacher = new Teacher();
        $teacher->subjects = array();
        if(!empty($values['teacher_id'])) {
            $teacher->id =  $values['teacher_id'];
        }
        if(!empty($values['teacher_name'])) {
            $teacher->name =  $values['teacher_name'];
        }

        if($initRelations && !empty($values['subject_id'])) {
            $teacher->subjects[] =  Subject::apply($values, false);
        }
        return $teacher->fix();
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
