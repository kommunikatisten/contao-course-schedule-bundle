<?php


namespace Kommunikatisten\ContaoScheduleBundle\Service\BE;


use Exception;
use Kommunikatisten\ContaoScheduleBundle\Entity\Subject;
use Kommunikatisten\ContaoScheduleBundle\Entity\Teacher;
use Kommunikatisten\ContaoScheduleBundle\Repository\CourseRepository;
use Kommunikatisten\ContaoScheduleBundle\Repository\SubjectRepository;

class BackendSubjectService extends AbstractBackendService {


    private SubjectRepository $repository;

    /**
     * TeacherBackendService constructor.
     * @param SubjectRepository $repository
     */
    public function __construct(SubjectRepository $repository) {
        parent::__construct();
        $this->repository = $repository;
    }

    /**
     * @return Subject[]
     * @throws Exception
     */
    public function findAll(): array {
        return $this->repository->findAll();
    }

    /**
     * @param int $id
     * @return Subject|null
     * @throws Exception
     */
    public function findById(int $id): ?Subject {
        return $this->repository->findById($id);
    }

    /**
     * @param array $values
     * @throws Exception
     */
    public function save(array $values) {
        $subject = Subject::apply($values);
        $current = null;
        if($subject->getId()) {
            $current = $this->findById($subject->getId());
            if(!$current->equals($subject)) {
                $this->repository->update($subject);
            }
        } else {
            if($id = $this->repository->insert($subject)) {
                Subject::applyAfterInsert($subject, $id);
            }
        }
        if($subject->getId() > 0) {
            $this->updateTeachers($subject, $current);
        }
    }

    /**
     * @param Subject $subject
     * @param Subject|null $current
     * @throws Exception
     */
    private function updateTeachers(Subject $subject, ?Subject $current) {
        $currentIds = $current == null ? array() : array_map(function(Teacher $teacher) { return $teacher->getId(); }, $current->getTeachers());
        $actualIds = array_map(function(Teacher $teacher) { return $teacher->getId(); }, $subject->getTeachers());

        $toBeAddedIds = parent::toBeAdded($actualIds, $currentIds);
        $toBeDeletedIds = parent::toBeDeleted($actualIds, $currentIds);

        if(!empty($toBeAddedIds)) {
            $this->repository->linkTeachers($subject, $toBeAddedIds);
        }
        if(!empty($toBeDeletedIds)) {
            $this->repository->unlinkTeacher($subject, $toBeDeletedIds);
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
