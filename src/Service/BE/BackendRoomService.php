<?php


namespace Kommunikatisten\ContaoScheduleBundle\Service\BE;


use Exception;
use Kommunikatisten\ContaoScheduleBundle\Entity\Room;
use Kommunikatisten\ContaoScheduleBundle\Entity\Subject;
use Kommunikatisten\ContaoScheduleBundle\Repository\RoomRepository;

class BackendRoomService extends AbstractBackendService {


    private RoomRepository $repository;

    /**
     * TeacherBackendService constructor.
     * @param RoomRepository $repository
     */
    public function __construct(RoomRepository $repository) {
        parent::__construct();
        $this->repository = $repository;
    }

    /**
     * @return Room[]
     * @throws Exception
     */
    public function findAll(): array {
        return $this->repository->findAll();
    }

    /**
     * @param int $id
     * @return Room|null
     * @throws Exception
     */
    public function findById(int $id): ?Room {
        return $this->repository->findById($id);
    }

    /**
     * @param array $values
     * @throws Exception
     */
    public function save(array $values) {
        $room = Room::apply($values);
        $current = null;
        if($room->getId()) {
            $current = $this->findById($room->getId());
            if(!$current->equals($room)) {
                $this->repository->update($room);
            }
        } else {
            if($id = $this->repository->insert($room)) {
                Room::applyAfterInsert($room, $id);
            }
        }
    }

    /**
     * @param int $id
     * @throws Exception
     */
    public function delete(int $id) {
        $this->repository->remove($id);
    }

}
