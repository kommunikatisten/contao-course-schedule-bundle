<?php


namespace Kommunikatisten\ContaoScheduleBundle\EventListener;

use Contao\CoreBundle\Event\MenuEvent;

use Kommunikatisten\ContaoScheduleBundle\Controller\BE\BackendCourseController;
use Kommunikatisten\ContaoScheduleBundle\Controller\BE\BackendSubjectController;
use Kommunikatisten\ContaoScheduleBundle\Controller\BE\BackendTeacherController;
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


        $contentNode->addChild($factory
            ->createItem('teacher')->setUri($this->router->generate(BackendTeacherController::class))
            ->setLinkAttribute('class', 'teacher')
            ->setLabel('Kursleiter')
            ->setLinkAttribute('title', 'Title')
            ->setCurrent($this->requestStack->getCurrentRequest()->get('_controller') === BackendTeacherController::class));
        $contentNode->addChild($factory
            ->createItem('subject')->setUri($this->router->generate(BackendSubjectController::class))
            ->setLinkAttribute('class', 'subject')
            ->setLabel('Kursefächer')
            ->setLinkAttribute('title', 'Title')
            ->setCurrent($this->requestStack->getCurrentRequest()->get('_controller') === BackendSubjectController::class));
        $contentNode->addChild($factory
            ->createItem('course')->setUri($this->router->generate(BackendCourseController::class))
            ->setLinkAttribute('class', 'course')
            ->setLabel('Kurse')
            ->setLinkAttribute('title', 'Title')
            ->setCurrent($this->requestStack->getCurrentRequest()->get('_controller') === BackendCourseController::class));


    }
}
