<?php


namespace Kommunikatisten\ContaoScheduleBundle\Controller\BE;


use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;
use Twig\Error\Error as TwigError;

/**
 * @Route("/contao/backend/kommunikatisten/courses",
 *     name=BackendCourseController::class,
 *     defaults={"_scope" = "backend"}
 * )
 */
class BackendCourseController extends AbstractController implements FrameworkAwareInterface{

    public const TYPE = "ce_course";

    private TwigEnvironment $twig;

    public function __construct(TwigEnvironment $twig) {
        $this->twig = $twig;
    }

    /**
     * @return Response
     * #throws TwigError
     */
    public function __invoke(): Response {
        $env = '';
        foreach ($_ENV as $k => $v) {
            $env .= "<p>$k: $v</p>";
        }
        return new Response($env);


        /*
        return new Response($this->twig->render(
            'my_backend_route.html.twig',
            []
        ));
        */
    }

    public function setFramework(ContaoFramework $framework = null) {
       if(null != $framework) {
           $framework->initialize();
       }
    }

}
