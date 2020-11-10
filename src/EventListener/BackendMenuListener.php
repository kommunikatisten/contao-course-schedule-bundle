<?php


namespace Kommunikatisten\EventListener;

use Contao\CoreBundle\Event\MenuEvent;
use Kommunikatisten\ContaoScheduleBundle\Controller\BE\SubjectController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTag;

/**
 * @ServiceTag("kernel.event_listener", event="contao.backend_menu_build", priority=-255)
 */
class BackendMenuListener {

    protected RouterInterface $router;
    protected RequestStack $requestStack;

    public function __construct(RouterInterface $router, RequestStack $requestStack) {
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    public function __invoke(MenuEvent $event): void
    {
        $factory = $event->getFactory();
        $tree = $event->getTree();

        if ('mainMenu' !== $tree->getName()) {
            return;
        }

        $contentNode = $tree->getChild('content');

        $node = $factory
            ->createItem('subject')
            ->setUri($this->router->generate(SubjectController::class))
            ->setLabel('Kursfächer')
            ->setLinkAttribute('title', 'Title')
            ->setLinkAttribute('class', 'subject')
            ->setCurrent($this->requestStack->getCurrentRequest()->get('_controller') === SubjectController::class)
        ;

        $contentNode->addChild($node);
    }
}
