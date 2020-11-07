<?php
use Kommunikatisten\ContaoScheduleBundle\Controller\TestController;

$GLOBALS['TL_DCA']['tl_module']['palettes']['schedule'] = '{title_legend},name,headline,type;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes'][TestController::TYPE] = '{title_legend},title,alias;{teaser_legend},description;{publish_legend},published';
