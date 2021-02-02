<?php


namespace Kommunikatisten\ContaoScheduleBundle\Repository;


use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Kommunikatisten\ContaoScheduleBundle\Entity\Subject;

class SubjectRepository extends AbstractRepository {


    /**
     * CourseRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry);
    }

    /**
     * @return Subject[]
     * @throws Exception
     */
    public function findAll(): array {
        $results = array();
        $this->executeNamedQuery(
                'select
                        *
                    from komm_sch_subjects',
                [],
                function (array $resultSet) use (&$results) {
                    self::translate($resultSet, $results);
                });
        return $results;
    }

    /**
     * @return Subject[]
     * @throws Exception
     */
    public function findAllValid(): array {
        $results = array();
        $this->executeNamedQuery(
            'select
                        *
                    from komm_sch_subjects',
            [],
            function (array $resultSet) use (&$results) {
                self::translate($resultSet, $results);
            });
        return $results;
    }

    /**
     * @param int $id
     * @return Subject | null
     * @throws Exception
     */
    public function findById(int $id): ?Subject {
        $results = array();
        $this->executeNamedQuery(
            'select
                        *
                    from komm_sch_subjects
                    where subject_id = :subject_id',
            ['subject_id' => $id],
            function (array $resultSet) use (&$results) {
                self::translate($resultSet, $results);
            });
        return empty($results) ? null : parent::last($results);
    }

    /**
     * @param Subject $subject
     * @return int
     * @throws Exception
     */
    public function insert(Subject $subject): int {
        return $this->executeNamedQuery(
            'insert into komm_sch_subject (name, description, css_class, last_modified)
                         values (:subject_name, :subject_description, :subject_css_class ,now())',
            [
                'subject_name' => $subject->getName(),
                'subject_css_class' => $subject->getCssClass(),
                'subject_description' => $subject->getDescription()
            ]);
    }

    /**
     * @param Subject $subject
     * @throws Exception
     */
    public function update(Subject $subject) {
        $this->executeNamedQuery(
            'update komm_sch_subject
                         set name = :subject_name,
                             description = :subject_description,
                             css_class = :subject_css_class,
                             last_modified = now()
                  where subject_id = :subject_id',
            [
                'subject_name' => $subject->getName(),
                'subject_description' => $subject->getDescription(),
                'subject_css_class' => $subject->getCssClass(),
                'subject_id' => $subject->getId(),
            ]);
    }

    /**
     * @param Subject $subject
     * @param array $ids
     * @return int
     * @throws Exception
     */
    public function linkTeachers(Subject $subject, array $ids): int {
        $count = 0;
        foreach($ids as $id) {
            $count += $this->executeNamedQuery(
                'insert into komm_sch_teacher_subject (subject_id, teacher_id) 
                       values (:subject_id, :teacher_id)',
                [
                    'subject_id' => $subject->getId(),
                    'teacher_id' => intval($id)
                ]);
        }
        return $count;
    }

    /**
     * @param Subject $subject
     * @param array $ids
     * @return int
     * @throws Exception
     */
    public function unlinkTeacher(Subject $subject, array $ids): int {
        if(!empty($ids)) {
            return $this->executeNamedQuery(
                'delete from komm_sch_teacher_subject 
                   where teacher_id in ('. join(',', array_map(function($id) { return intval($id); } , $ids)) .')
                   and subject_id = :subject_id',
                [
                    'subject_id' => $subject->getId()
                ]);
        }
        return 0;
    }

    /**
     * @param int $id
     * @throws Exception
     */
    public function remove(int $id) {
        $this->executeNamedQuery(
            'delete from komm_sch_subject 
                   where subject_id = :subject_id',
            [
                'subject_id' => $id
            ]);
    }


    /**
     * @param array $resultSet
     * @param Subject[] $results
     */
    private static function translate(array $resultSet, array &$results): void {
        /** @var Subject[] $found */
        $found = array_values(array_filter($results, function (Subject $subject) use ($resultSet): bool {
            return $resultSet['subject_id'] === $subject->getId();
        }));
        if (!empty($found)) {
            $found[0]->merge($resultSet, true);
        } else {
            $results[] = Subject::apply($resultSet);
        }
    }


}
