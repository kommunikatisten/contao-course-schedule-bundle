<?php


namespace Kommunikatisten\ContaoScheduleBundle\Repository;


use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Kommunikatisten\ContaoScheduleBundle\Entity\Room;

class RoomRepository extends AbstractRepository {


    /**
     * CourseRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry);
    }

    /**
     * @return Room[]
     * @throws Exception
     */
    public function findAll(): array {
        $results = array();
        $this->executeNamedQuery(
                'select
                        room_id,
                        name room_name,
                        last_modified room_last_modified
                    from komm_sch_room',
                [],
                function (array $resultSet) use (&$results) {
                    self::translate($resultSet, $results);
                });
        return $results;
    }

    /**
     * @param int $id
     * @return Room | null
     * @throws Exception
     */
    public function findById(int $id): ?Room {
        $results = array();
        $this->executeNamedQuery(
            'select
                        room_id,
                        name room_name,
                        last_modified room_last_modified
                    from komm_sch_room
                    where room_id = :room_id',
            ['room_id' => $id],
            function (array $resultSet) use (&$results) {
                self::translate($resultSet, $results);
            });
        return empty($results) ? null : parent::last($results);
    }

    /**
     * @param Room $room
     * @return int
     * @throws Exception
     */
    public function insert(Room $room): int {
        return $this->executeNamedQuery(
            'insert into komm_sch_room (name, last_modified)
                         values (:room_name, now())',
            [
                'room_name' => $room->getName()
            ]);
    }

    /**
     * @param Room $room
     * @throws Exception
     */
    public function update(Room $room) {
        $this->executeNamedQuery(
            'update komm_sch_room
                         set name = :room_name, last_modified = now()
                  where room_id = :room_id',
            [
                'room_name' => $room->getName(),
                'room_id' => $room->getId()
            ]);
    }

    /**
     * @param int $id
     * @throws Exception
     */
    public function remove(int $id) {
        $this->executeNamedQuery(
            'delete from komm_sch_room 
                   where room_id = :room_id',
            [
                'room_id' => $id
            ]);
    }


    /**
     * @param array $resultSet
     * @param Room[] $results
     */
    private static function translate(array $resultSet, array &$results): void {
        /** @var Room[] $found */
        $found = array_values(array_filter($results, function (Room $room) use ($resultSet): bool {
            return $resultSet['room_id'] === $room->getId();
        }));
        if (!empty($found)) {
            $found[0]->merge($resultSet, true);
        } else {
            $results[] = Room::apply($resultSet);
        }
    }


}
