<?php


namespace Kommunikatisten\ContaoScheduleBundle\Controller\BE;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DoctrineException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
class BackendSubjectController extends AbstractController implements FrameworkAwareInterface {

    private TwigEnvironment $twig;
    private Connection $connection;

    public function __construct(TwigEnvironment $twig, ManagerRegistry $managerRegistry) {
        $this->twig = $twig;
        $this->connection = $managerRegistry->getConnection();
    }

    /**
     * @return Response
     * #throws TwigError
     * @throws DoctrineException
     */
    public function __invoke(): Response {
        /*
        $this->connection->createQueryBuilder()->s('
                                        SELECT * FROM tl_komm_course JOIN tl_komm_subject
                                        USING (subject_id)
                                        ');
        */
        return new Response($this->twig->render(
            'kommunikatisten/backend/subjects.html.twig',
            []
        ));

    }

    public function setFramework(ContaoFramework $framework = null) {
        if(null != $framework) {
            $framework->initialize();
        }
    }

}
