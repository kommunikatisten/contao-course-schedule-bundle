<?php


namespace Kommunikatisten\ContaoScheduleBundle\Entity;

/**
 * Class Teacher
 * @package Kommunikatisten\ContaoScheduleBundle\Entity
 */
class Teacher extends AbstractEntity {

    private ?string $name = null;
    private array $subjects = [];
    private array $courses = [];

    /**
     * @return string | null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getSubjects(): array {
        return $this->subjects;
    }

    /**
     * @return array
     */
    public function getCourses(): array {
        return $this->courses;
    }

    /**
     * @param array $values
     * @param bool $initRelations
     * @return Teacher
     */
    public static function apply(array $values, bool $initRelations = true): Teacher {
        $teacher = new Teacher();
        if(!empty($values['teacher_id'])) {
            $teacher->id =  $values['teacher_id'];
        }
        if(!empty($values['teacher_name'])) {
            $teacher->name =  $values['teacher_name'];
        }

        if($initRelations && !empty($values['subject_id'])) {
            $teacher->subjects[] = Subject::apply($values, false);
        } elseif (!empty($values['teacher_subjects'])) {
            $teacher->subjects = array();
            foreach ($values['teacher_subjects'] as $subject) {
                $teacher->subjects[] = Subject::apply($subject, false);
            }
        }
        return $teacher;
    }

    /**
     * @param array $values
     * @param bool $cascade
     */
    public function merge(array $values, bool $cascade = true): void {
        Subject::merging($values, $this->subjects, $cascade);
        Course::merging($values, $this->courses, $cascade);
    }

    public static function merging(array $values, array &$target, bool $cascade) : void {
        if($cascade && !empty($values['teacher_id'])) {
            $found = array_filter($target, function (Teacher $entity) use ($values): bool {
                return $values['teacher_id'] === $entity->getId();
            });
            if(empty($found)) {
                $target[] = Teacher::apply($values, $cascade);
            } else {
                $idx = array_keys($found)[0];
                $found[$idx]->merge($values, $cascade);
            }
        }
    }


    /**
     * @param bool $encode
     * @return array|string
     */
    public function serialize(bool $encode) {
        $serialized = [
            'teacher_id' => $this->getId(),
            'teacher_name' => $this->name,
            'teacher_subjects' => parent::serializeChilds($this->subjects),
            'teacher_courses' => parent::serializeChilds($this->courses)
        ];
        return $encode ? json_encode($serialized, JSON_UNESCAPED_LINE_TERMINATORS) : $serialized;
    }
}
