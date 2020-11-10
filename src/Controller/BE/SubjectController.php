<?php


namespace Kommunikatisten\ContaoScheduleBundle\Controller\BE;

use Contao\CoreBundle\Controller\AbstractController;
use Contao\CoreBundle\Framework\ContaoFramework;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Error\Error;

/**
 * @Route("/contao/kommunikatisten/subjects",
 *     name=SubjectController::class,
 *     defaults={"_scope" = "backend"}
 * )
 */
class SubjectController extends AbstractController implements FrameworkAwareInterface {

    private ?ContaoFramework $framework;
    private TwigEnvironment $twig;
    private LoggerInterface $logger;

    public function __construct(TwigEnvironment $twig, LoggerInterface $logger) {
        $this->twig = $twig;
        $this->logger = $logger;
    }

    /**
     * @return Response
     * @throws Error
     */
    public function __invoke(): Response {
        foreach ($this->twig->getGlobals() as $k => $gl) {
            $this->logger->info($k + ': ' + $gl);
        }
        return new Response('<p>it s me </p>');
        /*
        return new Response($this->twig->render(
            '../vendor/kommunikatisten/Resources/views/backend_subjects.html.twig',
            []
        ));
        */
    }

    public function setFramework(ContaoFramework $framework = null) {
        $this->framework = $framework;
    }
}
