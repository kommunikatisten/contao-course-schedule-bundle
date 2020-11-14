<?php


namespace Kommunikatisten\ContaoScheduleBundle\Service;


use Exception;
use Kommunikatisten\ContaoScheduleBundle\Entity\Teacher;
use Kommunikatisten\ContaoScheduleBundle\Repository\TeacherRepository;

class TeacherBackendService {

    private TeacherRepository $repository;

    /**
     * TeacherBackendService constructor.
     * @param TeacherRepository $repository
     */
    public function __construct(TeacherRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * @return Teacher[]
     * @throws Exception
     */
    public function findAll(): array {
        return $this->repository->findAll();
    }

    /**
     * @param int $id
     * @return Teacher|null
     * @throws Exception
     */
    public function findById(int $id): ?Teacher {
        return $this->repository->findById($id);
    }

}
