<?php


namespace Kommunikatisten\ContaoScheduleBundle\Controller\BE;


use Exception;
use Kommunikatisten\ContaoScheduleBundle\Entity\Subject;
use Kommunikatisten\ContaoScheduleBundle\Entity\Teacher;
use Kommunikatisten\ContaoScheduleBundle\Repository\CourseRepository;
use Kommunikatisten\ContaoScheduleBundle\Repository\TeacherRepository;
use Kommunikatisten\ContaoScheduleBundle\Service\BE\BackendSubjectService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;
use Twig\Error\Error as TwigError;

/**
 * @Route("/contao/kommunikatisten/subjects",
 *     name=BackendSubjectController::class,
 *     defaults={"_scope" = "backend"}
 * )
 */
class BackendSubjectController extends AbstractBackendController {

    public const ROUTE = '/contao/kommunikatisten/subjects';
    private BackendSubjectService $service;
    private TeacherRepository $teacherRepository;

    public function __construct(TwigEnvironment $twig,
                                BackendSubjectService $service,
                                TeacherRepository $teacherRepository) {

        parent::__construct($twig);
        $this->service = $service;
        $this->teacherRepository = $teacherRepository;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    protected function listEntities(Request $request): Response {
        $subjects = $this->service->findAll();
        return new Response($this->twig->render(
            '@ContaoSchedule/' . $this->last(explode('/', self::ROUTE)) . '.list.html.twig',
            ['route' => self::ROUTE, 'subjects' => $subjects]
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
        $subject = new Subject();
        $teachers = $this->teacherRepository->findAll();
        return new Response($this->twig->render(
            '@ContaoSchedule' . $this->last(explode('/', self::ROUTE)) . '.form.html.twig',
            ['route' => self::ROUTE, 'rt' => $rt, 'method' => 'POST',
                'subject' => $subject,
                'teachers' => $teachers,
                'linked_teachers' => array()
            ]
        ));
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    protected function doAddEntity(Request $request): void {
        $this->service->save([
            'subject_id' => 0,
            'subject_name' => $request->get('subject_name'),
            'subject_description' => $request->get('subject_description'),
            'subject_teachers' => array_map(function($id) {
                return array('teacher_id' => intval($id));
            }, $request->get('subject_teachers'))
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
        $subject = $this->service->findById($request->get('id'));
        $teachers = $this->teacherRepository->findAll();
        $this->logger->info($subject->serialize(true), array($this));
        return new Response($this->twig->render(
            '@ContaoSchedule/' . $this->last(explode('/', self::ROUTE)) . '.form.html.twig',
            ['route' => self::ROUTE, 'rt' => $rt, 'method' => 'PUT',
                'subject' => $subject,
                'teachers' => $teachers,
                'linked_teachers' => array_map(function(Teacher $teacher){ return $teacher->getId(); }, $subject->getTeachers())]
        ));
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    protected function doEditEntity(Request $request): void {
        $this->service->save([
            'subject_id' => intval($request->get('id')),
            'subject_name' => $request->get('subject_name'),
            'subject_description' => $request->get('subject_description'),
            'subject_teachers' => array_map(function($id) {
                return array('teacher_id' => intval($id));
            }, $request->get('subject_teachers'))
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
