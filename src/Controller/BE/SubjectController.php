<?php


namespace Kommunikatisten\ContaoScheduleBundle\Controller\BE;

use Contao\CoreBundle\Controller\AbstractController;
use Contao\CoreBundle\Framework\ContaoFramework;
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

    public function __construct(TwigEnvironment $twig) {
        $this->twig = $twig;
    }

    /**
     * @return Response
     * @throws Error
     */
    public function __invoke(): Response {
        return new Response($this->twig->render(
            'backend_subjects.html.twig',
            []
        ));
    }

    public function setFramework(ContaoFramework $framework = null) {
        $this->framework = $framework;
    }
}
