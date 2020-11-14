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
                        teacher_id,
                        name  teacher_name,
                        last_modified  teacher_last_modified
                    from komm_schb_teacher
                    order by name',
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
                        teacher_id,
                        name  teacher_name,
                        last_modified  teacher_last_modified
                    from komm_schb_teacher
                    where teacher_id = :teacher_id',
            ['teacher_id' => $id],
            function (array $resultSet) use (&$results) {
                self::translate($resultSet, $results);
            });
        return empty($results) ? null : parent::last($results);
    }

    /**
     * @param array $resultSet
     * @param array $results
     */
    private static function translate(array $resultSet, array &$results): void {
        /** @var Teacher[] $found */
        $found = array_filter($results, function (Teacher $course) use ($resultSet): bool {
            return $resultSet['teacher_id'] === $course->getId();
        });
        if (!empty($found)) {
            $found[0]->merge($resultSet);
        } else {
            $results[] = Teacher::apply($resultSet);
        }
    }


}
