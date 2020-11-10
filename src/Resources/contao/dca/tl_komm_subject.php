<?php
$GLOBALS['TL_DCA']['tl_komm_subject']['config'] = array(
    'dataContainer' => 'Table',
    'sql' => array (
        'keys' => array (
            'id' => 'primary',
            'subject' => 'index',
        )
    )
);
$GLOBALS['TL_DCA']['tl_komm_subject']['list'] = array(
    'sorting' => array(
        'mode'                    => 1,
        'fields'                  => array('subject','published'),
        'headerFields'            => array('tstamp'),
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
            'showInHeader'        => true
        ),
        'show' => array (
            'href'                => 'act=show',
            'icon'                => 'show.svg'
        ),
    ),
    'label' => array(
        'fields' => ['id','subject','published'],
    ),
);
$GLOBALS['TL_DCA']['tl_komm_subject']['fields'] = array(
    'id' => [
        'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
    ],
    'tstamp' => [
        'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0]
    ],
    'subject' => [
        'sql' => ['type' => 'string', 'length' => 255, 'notnull' => true],
        'inputType' => 'text',
    ],
    'description' => [
        'sql' => ['type' => 'string', 'length' => 1024, 'notnull' => true],
        'inputType' => 'textarea',
    ],
    'published' => [
        'inputType' => 'checkbox',
        'sql' => ['type' => 'boolean', 'notnull' => true, 'default' => false],
    ],

);
$GLOBALS['TL_DCA']['tl_komm_subject']['palettes'] = array(
    'default' => '{title_legend},subject;'
);

