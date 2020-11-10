<?php
#$GLOBALS['TL_CTE']['kommunikatisten'][''] = 'Contao\ContentComments';

use Kommunikatisten\ContaoScheduleBundle\Model\Course;

$GLOBALS['TL_MODELS']['tl_komm_course'] = Course::class;

$GLOBALS['BE_MOD']['content']['komm_subject'] = [
    'tables' => ['tl_komm_subject'],
];
