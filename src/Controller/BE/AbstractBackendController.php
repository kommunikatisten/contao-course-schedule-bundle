<?php


namespace Kommunikatisten\ContaoScheduleBundle\Controller\BE;


use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\System;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\Error as TwigError;

abstract class AbstractBackendController extends AbstractController implements FrameworkAwareInterface {

    protected LoggerInterface $logger;

    /**
     * AbstractBackendController constructor.
     */
    public function __construct() {
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
        if($request->get('id') == null) {
            switch ($request->getMethod()) {
                case 'POST': // do add and return to list
                    return $this->doAddEntity($request);
                case 'DELETE': // do delete and return to list
                    return $this->doDeleteEntity($request);
                default:
                    return $this->listEntities($request);
            }
        } else {
            switch ($request->getMethod()) {
                case 'PUT': // do edit and return to list
                    return $this->doEditEntity($request);
                default:
                    return $this->editEntity($request);
            }
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
     * @return Response
     * @throws TwigError
     */
    abstract protected function doAddEntity(Request $request): Response;


    /**
     * @param Request $request
     * @return Response
     * @throws TwigError
     */
    abstract protected function doEditEntity(Request $request): Response;

    /**
     * @param Request $request
     * @return Response
     * @throws TwigError
     */
    abstract protected function toggleEntity(Request $request): Response;

    /**
     * @param Request $request
     * @return Response
     * @throws TwigError
     */
    abstract protected function doDeleteEntity(Request $request): Response;

    /**
     * @param array $array
     * @return mixed|null
     */
    protected static function last(array $array) {
        if(null == $array || empty($array)) return null;
        return $array[count($array)-1];
    }
}
