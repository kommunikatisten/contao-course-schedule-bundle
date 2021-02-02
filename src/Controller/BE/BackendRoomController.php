<?php


namespace Kommunikatisten\ContaoScheduleBundle\Controller\BE;

use Exception;
use Kommunikatisten\ContaoScheduleBundle\Entity\Room;
use Kommunikatisten\ContaoScheduleBundle\Service\BE\BackendRoomService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;
use Twig\Error\Error as TwigError;
use Twig\Loader\FilesystemLoader;

/**
 * @Route("/contao/kommunikatisten/rooms",
 *     name=BackendRoomController::class,
 *     defaults={"_scope" = "backend"}
 * )
 */
class BackendRoomController extends AbstractBackendController {

    public const ROUTE = '/contao/kommunikatisten/rooms';
    private BackendRoomService $service;


    public function __construct(TwigEnvironment $twig, BackendRoomService $service) {
        parent::__construct($twig);
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws TwigError
     * @throws Exception
     */
    protected function listEntities(Request $request): Response {
        $rooms = $this->service->findAll();
        //$this->twig->setLoader(new FilesystemLoader(["/vendor/kommunikatisten/contao-schedule-bundle/src/Resources/views"]));
        return new Response($this->twig->render(
            '@ContaoSchedule/' . $this->last(explode('/', self::ROUTE)) . '.list.html.twig',
            ['route' => self::ROUTE, 'rooms' => $rooms]
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
        $room = new Room();
        return new Response($this->twig->render(
            '@ContaoSchedule/' . $this->last(explode('/', self::ROUTE)) . '.form.html.twig',
            ['route' => self::ROUTE, 'rt' => $rt, 'method' => 'POST',
                'room' => $room
            ]
        ));
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    protected function doAddEntity(Request $request): void {
        $this->service->save([
            'room_id' => 0,
            'room_name' => $request->get('room_name')
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
        $room = $this->service->findById($request->get('id'));
        return new Response($this->twig->render(
            '@ContaoSchedule/' . $this->last(explode('/', self::ROUTE)) . '.form.html.twig',
            ['route' => self::ROUTE, 'rt' => $rt, 'method' => 'PUT',
                'room' => $room
            ]
        ));
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    protected function doEditEntity(Request $request): void {
        $this->service->save([
            'room_id' => intval($request->get('id')),
            'room_name' => $request->get('room_name')
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
