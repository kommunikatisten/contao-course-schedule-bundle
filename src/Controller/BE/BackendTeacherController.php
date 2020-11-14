<?php


namespace Kommunikatisten\ContaoScheduleBundle\Controller\BE;

use Kommunikatisten\ContaoScheduleBundle\Entity\Teacher;
use Kommunikatisten\ContaoScheduleBundle\Service\TeacherBackendService;
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
    private TwigEnvironment $twig;
    private TeacherBackendService $service;


    public function __construct(TwigEnvironment $twig, TeacherBackendService $service) {
        parent::__construct();
        $this->twig = $twig;
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws TwigError
     */
    protected function listEntities(Request $request): Response {
        $teachers = $this->service->findAll();
        return new Response($this->twig->render(
            'kommunikatisten/backend/' . $this->last(explode('/', self::ROUTE)) . '.list.html.twig',
            ['route' => self::ROUTE, 'token' => 'todo', 'teachers' => $teachers]
        ));
    }


    protected function addEntity(Request $request): Response {
        $teacher = new Teacher();
        return new Response($this->twig->render(
            'kommunikatisten/backend/' . $this->last(explode('/', self::ROUTE)) . '.form.html.twig',
            ['route' => self::ROUTE, 'token' => 'todo', 'teacher' => $teacher]
        ));
    }

    protected function doAddEntity(Request $request): Response {
        // TODO: Implement doAddEntity() method.
    }

    protected function editEntity(Request $request): Response {
        $teacher = $this->service->findById($request->get('id'));
        return new Response($this->twig->render(
            'kommunikatisten/backend/' . $this->last(explode('/', self::ROUTE)) . '.form.html.twig',
            ['route' => self::ROUTE, 'method' => 'PUT', 'token' => 'todo', 'teacher' => $teacher]
        ));
    }

    protected function doEditEntity(Request $request): Response {
        // TODO: Implement doEditEntity() method.
    }

    protected function toggleEntity(Request $request): Response {
        // TODO: Implement toggleEntity() method.
    }

    protected function doDeleteEntity(Request $request): Response {
        // TODO: Implement doDeleteEntity() method.
    }
}
