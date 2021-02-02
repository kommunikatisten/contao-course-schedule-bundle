<?php


namespace Kommunikatisten\ContaoScheduleBundle\Service\BE;


use Exception;
use Kommunikatisten\ContaoScheduleBundle\Entity\Course;
use Kommunikatisten\ContaoScheduleBundle\Entity\Teacher;
use Kommunikatisten\ContaoScheduleBundle\Repository\CourseRepository;

class BackendCourseService extends AbstractBackendService {


    private CourseRepository $repository;

    /**
     * TeacherBackendService constructor.
     * @param CourseRepository $repository
     */
    public function __construct(CourseRepository $repository) {
        parent::__construct();
        $this->repository = $repository;
    }

    /**
     * @return Course[]
     * @throws Exception
     */
    public function findAll(): array {
        return $this->repository->findAll();
    }

    /**
     * @return String[]
     * @throws Exception
     */
    public function findAllValid(): array {
        return $this->repository->findAllValidUntil();
    }

    /**
     * @param int $id
     * @return Course|null
     * @throws Exception
     */
    public function findById(int $id): ?Course {
        return $this->repository->findById($id);
    }

    /**
     * @param array $values
     * @throws Exception
     */
    public function save(array $values) {
        $course = Course::apply($values);
        if($course->getId()) {
            $current = $this->findById($course->getId());
            if(!$current->equals($course)) {
                $this->repository->update($course);
            }
        } elseif($id = $this->repository->insert($course)){
            Course::applyAfterInsert($course, $id);
        }
        if($course->getId() > 0) {
            $this->updateTeachers($course, $current);
        }
    }


    /**
     * @param Course $course
     * @param Course|null $current
     * @throws Exception
     */
    private function updateTeachers(Course $course, ?Course $current) {
        $currentIds = $current == null ? array() : array_map(function(Teacher $teacher) { return $teacher->getId(); }, $current->getTeachers());
        $actualIds = array_map(function(Teacher $teacher) { return $teacher->getId(); }, $course->getTeachers());

        $toBeAddedIds = parent::toBeAdded($actualIds, $currentIds);
        $toBeDeletedIds = parent::toBeDeleted($actualIds, $currentIds);

        if(!empty($toBeAddedIds)) {
            $this->repository->linkTeachers($course, $toBeAddedIds);
        }
        if(!empty($toBeDeletedIds)) {
            $this->repository->unlinkTeacher($course, $toBeDeletedIds);
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
