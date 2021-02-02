<?php


namespace Kommunikatisten\ContaoScheduleBundle\Controller;


use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\System;
use Contao\Template;
use Kommunikatisten\ContaoScheduleBundle\Error\InitializationException;
use Kommunikatisten\ContaoScheduleBundle\Service\CourseScheduleService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @FrontendModule(
 *     category="kommunikatisten",
 *     template="ctrl_course_schedule",
 *     renderer="forward"
 * )
 */
class CourseScheduleController extends AbstractFrontendModuleController
{
    public const TYPE = 'ctrl_kommunikatisten_schedule';

    private CourseScheduleService $service;

    /**
     * CourseListController constructor.
     * @param CourseScheduleService|null $service
     * @throws InitializationException
     */
    public function __construct(CourseScheduleService $service = null) {
        if($service == null) {
            $this->service = System::getContainer()->get('Kommunikatisten\ContaoScheduleBundle\Service\CourseScheduleService');
        } else {
            $this->service = $service;
        }
        if($this->service == null) {
            throw new InitializationException("Fehler bei der Initialisierung: Service nicht erreichbar");
        }
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response {
        if ($request->isMethod('post')) {
            if (null !== ($redirectPage = PageModel::findByPk($model->jumpTo))) {
                throw new RedirectResponseException($redirectPage->getAbsoluteUrl());
            }
        }

        try {
            $template->setData($this->service->getSchedule());
        } catch (\Exception $e) {
            $template->setData(['error' => $e]);
        }

        return $template->getResponse();
    }
}
