<?php


namespace Kommunikatisten\ContaoScheduleBundle\Controller\BE;


use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\System;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;
use Twig\Error\Error as TwigError;

abstract class AbstractBackendController extends AbstractController implements FrameworkAwareInterface {

    protected LoggerInterface $logger;
    protected TwigEnvironment $twig;

    /**
     * AbstractBackendController constructor.
     * @param TwigEnvironment $twig
     */
    public function __construct(TwigEnvironment $twig) {
        $this->twig = $twig;
        $this->logger = System::getContainer()->get('monolog.logger.contao');
    }

    public function setFramework(ContaoFramework $framework = null) {
        if (null != $framework) {
            $framework->initialize();
        }
    }

    /**
     * @param Request $request
     * @return Response
     * @throws TwigError
     */
    public function __invoke(Request $request): Response {
        $this->logger->info(self::class . ' invoked ' . $request->getMethod() );
        switch ($request->getMethod()) {
            case 'POST': {
                try {
                    if ($request->get('_method') == 'POST') {
                        $this->doAddEntity($request);
                    } elseif ($request->get('_method') == 'PUT') {
                        $this->doEditEntity($request);
                    }
                    return $this->listEntities($request);
                } catch (Exception $e) {
                    return $this->showError($e);
                }
            }
            case 'GET': {
                if($request->get('_method') == 'POST') {
                    return $this->addEntity($request);
                } elseif($request->get('_method') == 'PUT') {
                    return $this->editEntity($request);
                } elseif($request->get('_method') == 'DELETE') {
                    $this->doDeleteEntity($request);
                }
                return $this->listEntities($request);
            }
            case 'PUT': {
                $this->doEditEntity($request);
                return $this->listEntities($request);
            }
            default: return $this->showError(new Exception("unexpected RequestMethod: " . $request->getMethod()));
        }
    }

    /**
     * @param Request $request
     * @return Response
     * @throws TwigError
     */
    abstract protected function listEntities(Request $request): Response;

    /**
     * @param Request $request
     * @return Response
     * @throws TwigError
     */
    abstract protected function addEntity(Request $request): Response;


    /**
     * @param Request $request
     * @return void
     * @throws TwigError
     */
    abstract protected function doAddEntity(Request $request): void;


    /**
     * @param Request $request
     * @return void
     * @throws TwigError
     */
    abstract protected function doEditEntity(Request $request): void;

    /**
     * @param Request $request
     * @return Response
     * @throws TwigError
     */
    abstract protected function toggleEntity(Request $request): Response;

    /**
     * @param Request $request
     * @throws TwigError
     */
    abstract protected function doDeleteEntity(Request $request): void;

    /**
     * @param array $array
     * @return mixed|null
     */
    protected static function last(array $array) {
        if(null == $array || empty($array)) return null;
        return $array[count($array)-1];
    }

    protected function showError(Exception $exception): Response {
        return new Response($this->twig->render(
            'kommunikatisten/backend/error.html.twig',
            ['message' => $exception->getMessage()]
        ));
    }
}
