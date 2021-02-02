<?php


namespace Kommunikatisten\ContaoScheduleBundle\Service\BE;


use Exception;
use Kommunikatisten\ContaoScheduleBundle\Entity\Course;
use Kommunikatisten\ContaoScheduleBundle\Entity\Subject;
use Kommunikatisten\ContaoScheduleBundle\Entity\Teacher;
use Kommunikatisten\ContaoScheduleBundle\Repository\TeacherRepository;

class BackendTeacherService extends AbstractBackendService {


    private TeacherRepository $repository;

    /**
     * TeacherBackendService constructor.
     * @param TeacherRepository $repository
     */
    public function __construct(TeacherRepository $repository) {
        parent::__construct();
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

    /**
     * @param array $values
     * @throws Exception
     */
    public function save(array $values) {
        $teacher = Teacher::apply($values);
        $current = null;
        if($teacher->getId()) {
            $current = $this->findById($teacher->getId());
            if(!$current->equals($teacher)) {
                $this->repository->update($teacher);
            }
        } else {
            if($id = $this->repository->insert($teacher)) {
                Teacher::applyAfterInsert($teacher, $id);
            }
        }
        if($teacher->getId() > 0) {
            $this->updateSubjects($teacher, $current);
        }
    }

    /**
     * @param int $id
     * @throws Exception
     */
    public function delete(int $id) {
        $this->repository->remove($id);
    }

    /**
     * @param Teacher $teacher
     * @param Teacher|null $current
     * @throws Exception
     */
    private function updateSubjects(Teacher $teacher, ?Teacher $current) {
        $currentIds = $current == null ? array() : array_map(function(Subject $subject) { return $subject->getId(); }, $current->getSubjects());
        $actualIds = array_map(function(Subject $subject) { return $subject->getId(); }, $teacher->getSubjects());

        $toBeAddedIds = parent::toBeAdded($actualIds, $currentIds);
        $toBeDeletedIds = parent::toBeDeleted($actualIds, $currentIds);

        if(!empty($toBeAddedIds)) {
            $this->repository->linkSubjects($teacher, $toBeAddedIds);
        }
        if(!empty($toBeDeletedIds)) {
            $this->repository->unlinkSubjects($teacher, $toBeDeletedIds);
        }
    }

}
