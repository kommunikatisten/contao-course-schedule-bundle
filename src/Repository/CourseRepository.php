<?php


namespace Kommunikatisten\ContaoScheduleBundle\Repository;


use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Kommunikatisten\ContaoScheduleBundle\Entity\Course;

class CourseRepository extends AbstractRepository {


    /**
     * CourseRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry);
    }

    /**
     * @return Course[]
     * @throws Exception
     */
    public function findAll(): array {
        $results = array();
        $this->executeNamedQuery(
            'select
                        *
                    from komm_sch_courses
                    order by course_start_date, course_day_index, course_start_time',
            [],
            function (array $resultSet) use (&$results) {
                self::translate($resultSet, $results);
            });
        return $results;
    }

    /**
     * @param string $validDate
     * @return Course[]
     * @throws Exception
     */
    public function findAllValid(string $validDate): array {
        $results = array();
        $this->executeNamedQuery(
            'select
                        *
                    from komm_sch_courses
                    where (course_start_date is null or DATE_ADD(course_start_date, interval -1 month) < ' . $validDate . ')   
                    and (course_end_date is null or DATE_ADD(course_end_date, interval 1 week) > ' . $validDate . ')
                    order by course_day_index, course_start_time',
            [],
            function (array $resultSet) use (&$results) {
                if(is_array($resultSet)) {
                    self::translate($resultSet, $results);
                }
            });
        return $results;
    }

    /**
     * @return Course[]
     * @throws Exception
     */
    public function findAllValidUntil(): array {
        $results = array();
        $this->executeNamedQuery(
            'select
                        *
                    from komm_sch_courses
                    where course_end_date is null or DATE_ADD(course_end_date, interval 1 MONTH) > CURRENT_DATE()
                    order by course_day_index, course_start_time',
            [],
            function (array $resultSet) use (&$results) {
                if(is_array($resultSet)) {
                    self::translate($resultSet, $results);
                }
            });
        return $results;
    }

    /**
     * @param int $id
     * @return Course | null
     * @throws Exception
     */
    public function findById(int $id): ?Course {
        $results = array();
        $this->executeNamedQuery(
            'select
                        *
                    from komm_sch_courses
                    where course_id = :course_id',
            ['course_id' => $id],
            function (array $resultSet) use (&$results) {
                self::translate($resultSet, $results);
            });
        return empty($results) ? null : parent::last($results);
    }

    /**
     * @param Course $course
     * @return int
     * @throws Exception
     */
    public function insert(Course $course): int {
        return $this->executeNamedQuery(
            'insert into komm_sch_course (name, start_date, end_date, start_time, end_time, day_index, subject_id, room_id, last_modified)
                         values (:course_name, :course_start_date, :course_end_date, :course_start_time, :course_end_time, :course_day_index, :subject_id, :room_id, now())',
            [
                'course_name' => $course->getName(),
                'course_start_date' => empty($course->getStartDate()) ? null : $course->getStartDate(),
                'course_end_date' => empty($course->getEndDate()) ? null : $course->getEndDate(),
                'course_start_time' => $course->getStartTime(),
                'course_end_time' => $course->getEndTime(),
                'course_day_index' => $course->getDayIndex(),
                'subject_id' => $course->getSubject()->getId(),
                'room_id' => $course->getRoom()->getId(),
            ]);
    }

    /**
     * @param Course $course
     * @throws Exception
     */
    public function update(Course $course) {
        $this->executeNamedQuery(
            'update komm_sch_course
                         set name = :course_name,
                             start_date = :course_start_date, 
                             end_date = :course_end_date, 
                             start_time = :course_start_time, 
                             end_time = :course_end_time, 
                             day_index = :course_day_index, 
                             subject_id = :subject_id,
                             room_id = :room_id,
                             last_modified = now()
                  where course_id = :course_id',
            [
                'course_id' => $course->getId(),
                'course_name' => $course->getName(),
                'course_start_date' => empty($course->getStartDate()) ? null : $course->getStartDate(),
                'course_end_date' => empty($course->getEndDate()) ? null : $course->getEndDate(),
                'course_start_time' => $course->getStartTime(),
                'course_end_time' => $course->getEndTime(),
                'course_day_index' => $course->getDayIndex(),
                'subject_id' => $course->getSubject()->getId(),
                'room_id' => $course->getRoom()->getId(),
            ]);
    }

    /**
     * @param Course $course
     * @param array $ids
     * @return int
     * @throws Exception
     */
    public function linkTeachers(Course $course, array $ids): int {
        $count = 0;
        foreach($ids as $id) {
            $count += $this->executeNamedQuery(
                'insert into komm_sch_teacher_course (course_id, teacher_id) 
                       values (:course_id, :teacher_id)',
                [
                    'course_id' => $course->getId(),
                    'teacher_id' => intval($id)
                ]);
        }
        return $count;
    }

    /**
     * @param Course $course
     * @param array $ids
     * @return int
     * @throws Exception
     */
    public function unlinkTeacher(Course $course, array $ids): int {
        if(!empty($ids)) {
            return $this->executeNamedQuery(
                'delete from komm_sch_teacher_course 
                   where teacher_id in ('. join(',', array_map(function($id) { return intval($id); } , $ids)) .')
                   and course_id = :course_id',
                [
                    'course_id' => $course->getId()
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
            'delete from komm_sch_course 
                   where course_id = :course_id',
            [
                'course_id' => $id
            ]);
    }


    /**
     * @param array $resultSet
     * @param Course[] $results
     */
    private static function translate(array $resultSet, array &$results): void {
        /** @var Course[] $found */
        $found = array_values(array_filter($results, function (Course $course) use ($resultSet): bool {
            return intval($resultSet['course_id']) === $course->getId();
        }));
        if (!empty($found)) {
            $found[0]->merge($resultSet, true);
        } else {
            $results[] = Course::apply($resultSet);
        }
    }


}
