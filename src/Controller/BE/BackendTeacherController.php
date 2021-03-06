<?php


namespace Kommunikatisten\ContaoScheduleBundle\Controller\BE;

use Exception;
use Kommunikatisten\ContaoScheduleBundle\Entity\Subject;
use Kommunikatisten\ContaoScheduleBundle\Entity\Teacher;
use Kommunikatisten\ContaoScheduleBundle\Repository\SubjectRepository;
use Kommunikatisten\ContaoScheduleBundle\Service\BE\BackendTeacherService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;
use Twig\Error\Error as TwigError;

/**
 * @Route("/contao/kommunikatisten/teachers",
 *     name=BackendTeacherController::class,
 *     defaults={"_scope" = "backend"}
 * )
 */
class BackendTeacherController extends AbstractBackendController {

    public const ROUTE = '/contao/kommunikatisten/teachers';
    private BackendTeacherService $service;
    private SubjectRepository $subjectRepository;


    public function __construct(TwigEnvironment $twig, BackendTeacherService $service,
                                SubjectRepository $subjectRepository) {
        parent::__construct($twig);
        $this->twig = $twig;
        $this->service = $service;
        $this->subjectRepository = $subjectRepository;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws TwigError
     * @throws Exception
     */
    protected function listEntities(Request $request): Response {
        $teachers = $this->service->findAll();
        return new Response($this->twig->render(
            '@ContaoSchedule/' . $this->last(explode('/', self::ROUTE)) . '.list.html.twig',
            ['route' => self::ROUTE, 'teachers' => $teachers]
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
        $teacher = new Teacher();
        $subjects = $this->subjectRepository->findAll();
        return new Response($this->twig->render(
            '@ContaoSchedule/' . $this->last(explode('/', self::ROUTE)) . '.form.html.twig',
            ['route' => self::ROUTE, 'rt' => $rt, 'method' => 'POST',
                'teacher' => $teacher,
                'subjects' => $subjects,
                'linked_subjects' => array()
            ]
        ));
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    protected function doAddEntity(Request $request): void {
        $this->service->save([
            'teacher_id' => 0,
            'teacher_name' => $request->get('teacher_name'),
            'teacher_subjects' => array_map(function($id) {
                return array('subject_id' => intval($id));
            }, $request->get('teacher_subjects'))
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
        $teacher = $this->service->findById($request->get('id'));
        $subjects = $this->subjectRepository->findAll();
        $this->logger->info($teacher->serialize(true), array($this));
        return new Response($this->twig->render(
            '@ContaoSchedule/' . $this->last(explode('/', self::ROUTE)) . '.form.html.twig',
            ['route' => self::ROUTE, 'rt' => $rt, 'method' => 'PUT',
                'teacher' => $teacher,
                'subjects' => $subjects,
                'linked_subjects' => array_map(function(Subject $subject){ return $subject->getId(); }, $teacher->getSubjects())
            ]
        ));
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    protected function doEditEntity(Request $request): void {
        $this->logger->info($request->get('id') . ': '. $request->get('teacher_name') . ' / ' . var_export($request->get('teacher_subjects'), true));
        $this->service->save([
            'teacher_id' => intval($request->get('id')),
            'teacher_name' => $request->get('teacher_name'),
            'teacher_subjects' => array_map(function($id) {
                return array('subject_id' => intval($id));
            }, $request->get('teacher_subjects'))
        ]);
    }

    protected function toggleEntity(Request $request): Response {
        return $this->listEntities($request);
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    protected function doDeleteEntity(Request $request): void {
        $this->service->delete(intval($request->get('id')));
    }
}
