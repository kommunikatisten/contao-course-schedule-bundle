<?php


namespace Kommunikatisten\ContaoScheduleBundle\Entity;

/**
 * Class Subject
 * @package Kommunikatisten\ContaoScheduleBundle\Entity
 */
class Subject extends AbstractEntity {

    private string $name = '';
    private string $description = '';
    private string $cssClass = '';
    private array $teachers = [];
    private array $courses = [];

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getCssClass(): string {
        return $this->cssClass;
    }

    /**
     * @return Teacher[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }

    /**
     * @return String[]
     */
    public function getCourses(): array {
        return $this->courses;
    }

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
        if(!empty($values['subject_css_class'])) {
            $subject->cssClass =  $values['subject_css_class'];
        }
        if(!empty($values['subject_description'])) {
            $subject->description =  $values['subject_description'];
        }

        if($initRelations && !empty($values['teacher_id'])) {
            $subject->teachers[] =  Teacher::apply($values, false);
        } elseif (!empty($values['subject_teachers'])) { // from form input
            $subject->teachers = array();
            foreach ($values['subject_teachers'] as $teacher) {
                $subject->teachers[] = Teacher::apply($teacher, false);
            }
        }
        if($initRelations && !empty($values['course_id'])) {
            $subject->courses[] = Course::apply($values, false);
        }
        return $subject;
    }

    /**
     * @param array $values
     * @param bool $cascade
     */
    public function merge(array $values, bool $cascade = true): void {
        Course::merging($values, $this->courses, $cascade);
        Teacher::merging($values, $this->teachers, $cascade);
    }

    public static function merging(array $values, array &$target, bool $cascade) : void {
        if($cascade && !empty($values['subject_id'])) {
            $found = array_filter($target, function (Subject $entity) use ($values): bool {
                return intval($values['subject_id']) === $entity->getId();
            });
            if(empty($found)) {
                $target[] = Subject::apply($values, $cascade);
            } else {
                $idx = array_keys($found)[0];
                $found[$idx]->merge($values, $cascade);
            }
        }
    }

    public function serialize(bool $encode) {
        $serialized = [
            'subject_id' => $this->getId(),
            'subject_name' => $this->name,
            'subject_description' => $this->description,
            'subject_css_class' => $this->cssClass,
            'subject_teachers' => parent::serializeChilds($this->teachers),
            'subject_courses' => parent::serializeChilds($this->courses)
        ];
        error_log('subject serialized: ' . var_export($serialized, true));
        return $encode ? json_encode($serialized) : $serialized;
    }
}
