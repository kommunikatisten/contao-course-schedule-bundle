<?php


namespace Kommunikatisten\ContaoScheduleBundle\Controller;


use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @FrontendModule(
 *     category="texts",
 *     template="mod_kom_test",
 *     renderer="forward"
 * )
 */
class TestController extends AbstractFrontendModuleController
{
    public const TYPE = 'kom_test';

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