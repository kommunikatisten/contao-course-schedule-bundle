<?php


namespace Kommunikatisten\ContaoScheduleBundle\Repository;


use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Kommunikatisten\ContaoScheduleBundle\Entity\Teacher;

class TeacherRepository extends AbstractRepository {


    /**
     * TeacherRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry);
    }

    /**
     * @return Teacher[]
     * @throws Exception
     */
    public function findAll(): array {
        $results = array();
        $this->executeNamedQuery(
            'select
                        *
                    from komm_sch_teachers
                    order by teacher_name',
            [],
            function (array $resultSet) use (&$results) {
                self::translate($resultSet, $results);
            });
        return $results;
    }

    /**
     * @param int $id
     * @return Teacher | null
     * @throws Exception
     */
    public function findById(int $id): ?Teacher {
        $results = array();
        $this->executeNamedQuery(
            'select
                        *
                    from komm_sch_teachers
                    where teacher_id = :teacher_id',
            ['teacher_id' => $id],
            function (array $resultSet) use (&$results) {
                self::translate($resultSet, $results);
            });
        return empty($results) ? null : parent::last($results);
    }

    /**
     * @param Teacher $teacher
     * @return int
     * @throws Exception
     */
    public function insert(Teacher $teacher): int {
        return $this->executeNamedQuery(
            'insert into komm_sch_teacher (name, last_modified)
                         values (:teacher_name, now())',
            [
                'teacher_name' => $teacher->getName()
            ]);
    }

    /**
     * @param Teacher $teacher
     * @throws Exception
     */
    public function update(Teacher $teacher) {
        $this->executeNamedQuery(
            'update komm_sch_teacher 
                         set name = :teacher_name,
                             last_modified = now()
                  where teacher_id = :teacher_id',
            [
                'teacher_name' => $teacher->getName(),
                'teacher_id' => $teacher->getId()
            ]);
    }

    /**
     * @param Teacher $teacher
     * @param int[] $ids
     * @return int
     * @throws Exception
     */
    public function unlinkSubjects(Teacher $teacher, array $ids): int {
        if(!empty($ids)) {
            return $this->executeNamedQuery(
                'delete from komm_sch_teacher_subject 
                   where subject_id in ('. join(',', array_map(function($id) { return intval($id); } , $ids)) .')
                   and teacher_id = :teacher_id',
                [
                    'teacher_id' => $teacher->getId()
                ]);
        }
        return 0;
    }

    /**
     * @param Teacher $teacher
     * @param int[] $ids
     * @return int
     * @throws Exception
     */
    public function linkSubjects(Teacher $teacher, array $ids): int {
        $count = 0;
        foreach($ids as $id) {
            $count += $this->executeNamedQuery(
                'insert into komm_sch_teacher_subject (subject_id, teacher_id) 
                       values (:subject_id, :teacher_id)',
                [
                    'teacher_id' => $teacher->getId(),
                    'subject_id' => intval($id)
                ]);
        }
        return $count;
    }

    /**
     * @param Teacher $teacher
     * @param int[] $ids
     * @return int
     * @throws Exception
     */
    public function unlinkCourses(Teacher $teacher, array $ids): int {
        if(!empty($ids)) {
            return $this->executeNamedQuery(
                'delete from komm_sch_teacher_course 
                   where course_id in ('. join(',', array_map(function($id) { return intval($id); } , $ids)) .')
                   and teacher_id = :teacher_id',
                [
                    'teacher_id' => $teacher->getId()
                ]);
        }
        return 0;
    }

    /**
     * @param Teacher $teacher
     * @param int[] $ids
     * @return int
     * @throws Exception
     */
    public function linkCourses(Teacher $teacher, array $ids): int {
        $count = 0;
        foreach($ids as $id) {
            $count += $this->executeNamedQuery(
                'insert into komm_sch_teacher_course (course_id, teacher_id) 
                       values (:course_id, :teacher_id)',
                [
                    'teacher_id' => $teacher->getId(),
                    'course_id' => intval($id)
                ]);
        }
        return $count;
    }

    /**
     * @param int $id
     * @throws Exception
     */
    public function remove(int $id) {
        $this->executeNamedQuery(
            'delete from komm_sch_teacher 
                   where teacher_id = :teacher_id',
            [
                'teacher_id' => $id
            ]);
    }


    /**
     * @param array $resultSet
     * @param array $results
     */
    private static function translate(array $resultSet, array &$results): void {
        /** @var Teacher[] $found */
        $found = array_values(array_filter($results, function (Teacher $course) use ($resultSet): bool {
            return $resultSet['teacher_id'] === $course->getId();
        }));
        if (empty($found)) {
            $results[] = Teacher::apply($resultSet);
        } else {
            $idx = array_keys($found)[0];
            $found[$idx]->merge($resultSet);
        }
    }


}
