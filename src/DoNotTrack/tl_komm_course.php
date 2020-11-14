<?php

$GLOBALS['TL_DCA']['tl_komm_course']['config'] = array(
    'dataContainer' => 'Table',
    'ptable' => 'tl_komm_subject.subject_id',
    'sql' => array (
        'keys' => array (
            'id' => 'primary',
            'alias' => 'index',
        )
    )
);
$GLOBALS['TL_DCA']['tl_komm_course']['list'] = array(
    'sorting' => array(
        'mode'                    => 4,
        'fields'                  => array('alias','published'),
        'headerFields'            => array('subject'),
        'panelLayout'             => 'filter;sort,search,limit',
#        'child_record_callback'   => array('listEvents')
    ),
    'global_operations' => array(
        'all' => array(
            'href'                => 'act=select',
            'class'               => 'header_edit_all',
            'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
        )
    ),
    'operations' => array(
        'edit' => array (
            'href'                => 'table=tl_content',
            'icon'                => 'edit.svg'
        ),
        'editheader' => [
            'href'                => 'act=edit',
            'icon'                => 'header.svg',
        ],
        'delete' => array (
            'href'                => 'act=delete',
            'icon'                => 'delete.svg',
            'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
        ),
        'toggle' => array (
            'icon'                => 'visible.svg',
            'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
            'button_callback'     => array('tl_calendar_events', 'toggleIcon'),
            'showInHeader'        => true
        ),
        'show' => array (
            'href'                => 'act=show',
            'icon'                => 'show.svg'
        ),
    ),
);
$GLOBALS['TL_DCA']['tl_komm_course']['fields'] = array(
    'id' => [
        'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
    ],
    'tstamp' => [
        'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0]
    ],
    'subject_id' => [
        'foreignKey' => 'tl_komm_subject.id',
        'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        'relation' => ['type'=>'belongsTo', 'load'=>'eager', 'field' => 'id', 'table' => 'tl_komm_subject'],
        'inputType' => 'select',
    ],
    'alias' => [
        'sql' => ['type' => 'string', 'length' => 255, 'notnull' => true],
        'inputType' => 'text',
    ],
    'published' => [
        'inputType' => 'checkbox',
        'sql' => ['type' => 'boolean', 'notnull' => true, 'default' => false],
    ],

);
$GLOBALS['TL_DCA']['tl_komm_course']['palettes'] = array(
    'default' => '{title_legend},alias;'
);
