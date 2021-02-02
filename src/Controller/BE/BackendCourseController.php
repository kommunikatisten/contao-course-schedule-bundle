<?php


namespace Kommunikatisten\ContaoScheduleBundle\Controller\BE;


use Exception;
use Kommunikatisten\ContaoScheduleBundle\Entity\Course;
use Kommunikatisten\ContaoScheduleBundle\Entity\Teacher;
use Kommunikatisten\ContaoScheduleBundle\Repository\RoomRepository;
use Kommunikatisten\ContaoScheduleBundle\Repository\SubjectRepository;
use Kommunikatisten\ContaoScheduleBundle\Repository\TeacherRepository;
use Kommunikatisten\ContaoScheduleBundle\Service\BE\BackendCourseService;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;
use Twig\Error\Error as TwigError;

/**
 * @Route("/contao/kommunikatisten/courses",
 *     name=BackendCourseController::class,
 *     defaults={"_scope" = "backend"}
 * )
 */
class BackendCourseController extends AbstractBackendController {

    public const ROUTE = '/contao/kommunikatisten/courses';
    private static $days = [1 => 'Montag', 2 => 'Dienstag', 3 => 'Mittwoch', 4 => 'Donnerstag', 5 => 'Freitag', 6 => 'Samstag', 7 => 'Sonntag'];

    private BackendCourseService $service;
    private SubjectRepository $subjectRepository;
    private TeacherRepository $teacherRepository;
    private RoomRepository $roomRepository;

    public function __construct(TwigEnvironment $twig,
                                BackendCourseService $service,
                                SubjectRepository $subjectRepository,
                                TeacherRepository $teacherRepository,
                                RoomRepository $roomRepository
        ) {
        parent::__construct($twig);
        $this->service = $service;
        $this->subjectRepository = $subjectRepository;
        $this->teacherRepository = $teacherRepository;
        $this->roomRepository = $roomRepository;
    }


    protected function listEntities(Request $request): Response {
        $courses = $this->service->findAllValid();
        return new Response($this->twig->render(
            '@ContaoSchedule/' . $this->last(explode('/', self::ROUTE)) . '.list.html.twig',
            ['route' => self::ROUTE,
                'courses' => $courses,
                'days' => self::$days
            ]
        ));
    }

    /**
     * @param Request $request
     * @return Response
     * @throws TwigError
     * @throws Exception
     */
    protected function addEntity(Request $request): Response {
        $rt = $request->cookies->get('csrf_contao_csrf_token');
        $course = new Course();
        $teachers = $this->teacherRepository->findAll();
        $subjects = $this->subjectRepository->findAll();
        $rooms = $this->roomRepository->findAll();
        return new Response($this->twig->render(
            '@ContaoSchedule/' . $this->last(explode('/', self::ROUTE)) . '.form.html.twig',
            ['route' => self::ROUTE, 'rt' => $rt, 'method' => 'POST',
                'course' => $course,
                'teachers' => $teachers,
                'subjects' => $subjects,
                'rooms' => $rooms,
                'days' => self::$days,
                'linked_teachers' => array_map(function(Teacher $teacher){ return $teacher->getId(); }, $course->getTeachers())
            ]
        ));
    }

    protected function doAddEntity(Request $request): void {
        $this->service->save([
            'course_id' => 0,
            'course_name' => $request->get('course_name'),
            'course_day_index' => $request->get('course_day_index'),
            'course_start_time' => $request->get('course_start_time'),
            'course_end_time' => $request->get('course_end_time'),
            'course_start_date' => $request->get('course_start_date'),
            'course_end_date' => $request->get('course_end_date'),
            'course_subject_id' => $request->get('course_subject_id'),
            'course_room_id' => $request->get('course_room_id'),

            'course_teachers' => array_map(function($id) {
                return array('teacher_id' => intval($id));
            }, $request->get('course_teachers'))
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws TwigError
     * @throws Exception
     */
    protected function editEntity(Request $request): Response {
        $rt = $request->cookies->get('csrf_contao_csrf_token');
        $course = $this->service->findById($request->get('id'));
        $teachers = $this->teacherRepository->findAll();
        $subjects = $this->subjectRepository->findAll();
        $rooms = $this->roomRepository->findAll();
        return new Response($this->twig->render(
            '@ContaoSchedule/' . $this->last(explode('/', self::ROUTE)) . '.form.html.twig',
            ['route' => self::ROUTE, 'rt' => $rt, 'method' => 'PUT',
                'course' => $course,
                'teachers' => $teachers,
                'subjects' => $subjects,
                'rooms' => $rooms,
                'days' => self::$days,
                'linked_teachers' => array_map(function(Teacher $teacher){ return $teacher->getId(); }, $course->getTeachers())
            ]
        ));
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    protected function doEditEntity(Request $request): void {
        $this->service->save([
            'course_id' => intval($request->get('id')),
            'course_name' => $request->get('course_name'),
            'course_day_index' => $request->get('course_day_index'),
            'course_start_time' => $request->get('course_start_time'),
            'course_end_time' => $request->get('course_end_time'),
            'course_start_date' => $request->get('course_start_date'),
            'course_end_date' => $request->get('course_end_date'),
            'course_subject_id' => $request->get('course_subject_id'),
            'course_room_id' => $request->get('course_room_id'),

            'course_teachers' => array_map(function($id) {
                return array('teacher_id' => intval($id));
            }, $request->get('course_teachers'))
        ]);
    }

    protected function toggleEntity(Request $request): Response {
        // TODO: Implement toggleEntity() method.
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    protected function doDeleteEntity(Request $request): void {
        $this->service->delete(intval($request->get('id')));
    }
}
