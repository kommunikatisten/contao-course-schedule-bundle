<?php


namespace Kommunikatisten\ContaoScheduleBundle\Controller;


use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @FrontendModule(
 *     category="kommunikatisten",
 *     template="ctrl_schedule",
 *     renderer="forward"
 * )
 */
class ScheduleController extends AbstractFrontendModuleController
{
    public const TYPE = 'ctrl_kommunikatisten_schedule';

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response {
        if ($request->isMethod('post')) {
            if (null !== ($redirectPage = PageModel::findByPk($model->jumpTo))) {
                throw new RedirectResponseException($redirectPage->getAbsoluteUrl());
            }
        }

        $template->action = $request->getUri();

        return $template->getResponse();
    }
}
