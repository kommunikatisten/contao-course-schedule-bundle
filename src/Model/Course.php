<?php


namespace Kommunikatisten\ContaoScheduleBundle\Model;

use Contao\Model;

/**
 * Class Course
 * @package Kommunikatisten\ContaoScheduleBundle\Model
 *
 * @property integer $id
 * @property integer $subject_id
 * @property integer $tstamp
 * @property string  $alias
 * @property boolean $published
 */
class Course extends Model{

    /**
     * @var string
     */
    protected static $strTable = 'tl_komm_course';

    public string $customTpl = '';
}
