<?php

if (php_sapi_name() != "cli") {
    exit();
}

chdir(dirname(__FILE__) . '/../');

if (file_exists("vendor/autoload.php")) {
    include_once 'vendor/autoload.php';
}

include_once 'config.php';
include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
include_once dirname(__FILE__) . '/tool.lib.php';

// override memory_limit in config.inc.php
ini_set("memory_limit", -1);

//error_reporting(-1);
//ini_set('display_errors', 1);

$webUI = new Vtiger_WebUI();
global $adb, $current_user, $root_directory;
$user = new Users();
$current_user = $user->retrieveCurrentUserInfoFromFile(1);

tool_echo_linebreak('Start: ' . date('Y-m-d H:i:s'));
/*************************************************************************************************/

$manifest = array(
    'type' => '',
    'name' => 'Citys',
    'label' => 'Tỉnh/Thành Phố',
    'parent' => 'Sales',
    'version' => '1.0',
    'blocks' => array(
        array(
            'label' => 'Thông tin chung',
            'fields' => array(
                array(
                    'fieldname' => 'city_name',
                    'uitype' => 1,
                    'columnname' => 'city_name',
                    'tablename' => 'vtiger_citys',
                    'generatedtype' => 1,
                    'fieldlabel' => 'Tên',
                    'readonly' => 1,
                    'presence' => 0,
                    'defaultvalue' => '',
                    'sequence' => 1,
                    'maximumlength' => 255,
                    'typeofdata' => 'V~M',
                    'quickcreate' => 3,
                    'quickcreatesequence' => 0,
                    'displaytype' => 1,
                    'info_type' => 'BAS',
                    'helpinfo' => '',
                    'masseditable' => '0',
                    'summaryfield' => '1',
                    'entityidentifier' => array(
                        'entityidfield' => 'citysid',
                        'entityidcolumn' => 'citysid',
                    ),
                    'relatedmodules' => '',
                    'picklistvalues' => '',
                ),
                array(
                    'fieldname' => 'city_type',
                    'uitype' => 15,
                    'columnname' => 'city_type',
                    'tablename' => 'vtiger_citys',
                    'generatedtype' => 1,
                    'fieldlabel' => 'Loại',
                    'readonly' => 1,
                    'presence' => 0,
                    'defaultvalue' => '',
                    'sequence' => 2,
                    'maximumlength' => 100,
                    'typeofdata' => 'V~O',
                    'quickcreate' => 3,
                    'quickcreatesequence' => 0,
                    'displaytype' => 1,
                    'info_type' => 'BAS',
                    'helpinfo' => '',
                    'masseditable' => '0',
                    'summaryfield' => '1',
                    'entityidentifier' => array(),
                    'relatedmodules' => '',
                    'picklistvalues' => array('Tỉnh', 'Thành Phố')
                ),

                array(
                    'fieldname' => 'city_number',
                    'uitype' => 1,
                    'columnname' => 'city_number',
                    'tablename' => 'vtiger_citys',
                    'generatedtype' => 1,
                    'fieldlabel' => 'Mã Tỉnh/Thành Phố',
                    'readonly' => 1,
                    'presence' => 0,
                    'defaultvalue' => '',
                    'sequence' => 3,
                    'maximumlength' => 100,
                    'typeofdata' => 'V~O',
                    'quickcreate' => 2,
                    'quickcreatesequence' => 0,
                    'displaytype' => 1,
                    'info_type' => 'BAS',
                    'helpinfo' => '',
                    'masseditable' => '1',
                    'summaryfield' => '1',
                    'entityidentifier' => array(),
                    'relatedmodules' => array(),
                    'picklistvalues' => '',
                ),

                array(
                    'fieldname' => 'order_appearance',
                    'uitype' => 1,
                    'columnname' => 'order_appearance',
                    'tablename' => 'vtiger_citys',
                    'generatedtype' => 1,
                    'fieldlabel' => 'Thứ tự xuất hiện',
                    'readonly' => 1,
                    'presence' => 0,
                    'defaultvalue' => '',
                    'sequence' => 4,
                    'maximumlength' => 100,
                    'typeofdata' => 'V~O',
                    'quickcreate' => 2,
                    'quickcreatesequence' => 0,
                    'displaytype' => 1,
                    'info_type' => 'BAS',
                    'helpinfo' => '',
                    'masseditable' => '1',
                    'summaryfield' => '1',
                    'entityidentifier' => array(),
                    'relatedmodules' => array(),
                    'picklistvalues' => '',
                ),

                array(
                    'fieldname' => 'source',
                    'uitype' => 1,
                    'columnname' => 'source',
                    'tablename' => 'vtiger_crmentity',
                    'generatedtype' => 1,
                    'fieldlabel' => 'Nguồn',
                    'readonly' => 1,
                    'presence' => 0,
                    'defaultvalue' => 'CRM',
                    'sequence' => 5,
                    'maximumlength' => 100,
                    'typeofdata' => 'V~O',
                    'quickcreate' => 2,
                    'quickcreatesequence' => 0,
                    'displaytype' => 2,
                    'info_type' => 'BAS',
                    'helpinfo' => '',
                    'masseditable' => '1',
                    'summaryfield' => '1',
                    'entityidentifier' => array(),
                    'relatedmodules' => array(),
                    'picklistvalues' => '',
                ),

                array(
                    'fieldname' => 'assigned_user_id',
                    'uitype' => 53,
                    'columnname' => 'smownerid',
                    'tablename' => 'vtiger_crmentity',
                    'generatedtype' => 1,
                    'fieldlabel' => 'Giao cho',
                    'readonly' => 1,
                    'presence' => 2,
                    'defaultvalue' => '',
                    'sequence' => 30,
                    'maximumlength' => 100,
                    'typeofdata' => 'V~M',
                    'quickcreate' => 2,
                    'quickcreatesequence' => 0,
                    'displaytype' => 1,
                    'info_type' => 'BAS',
                    'helpinfo' => '',
                    'masseditable' => '0',
                    'summaryfield' => '1',
                ),
                array(
                    'fieldname' => 'createdtime',
                    'uitype' => 70,
                    'columnname' => 'createdtime',
                    'tablename' => 'vtiger_crmentity',
                    'generatedtype' => 1,
                    'fieldlabel' => 'Thời gian tạo',
                    'readonly' => 1,
                    'presence' => 0,
                    'defaultvalue' => '',
                    'sequence' => 31,
                    'maximumlength' => 100,
                    'typeofdata' => 'DT~O',
                    'quickcreate' => 2,
                    'quickcreatesequence' => 0,
                    'displaytype' => 2,
                    'info_type' => 'BAS',
                    'helpinfo' => '',
                    'masseditable' => '0',
                    'summaryfield' => '1',
                ),
                array(
                    'fieldname' => 'modifiedtime',
                    'uitype' => 70,
                    'columnname' => 'modifiedtime',
                    'tablename' => 'vtiger_crmentity',
                    'generatedtype' => 1,
                    'fieldlabel' => 'Thời gian sửa',
                    'readonly' => 1,
                    'presence' => 0,
                    'defaultvalue' => '',
                    'sequence' => 33,
                    'maximumlength' => 100,
                    'typeofdata' => 'DT~O',
                    'quickcreate' => 2,
                    'quickcreatesequence' => 0,
                    'displaytype' => 2,
                    'info_type' => 'BAS',
                    'helpinfo' => '',
                    'masseditable' => '0',
                    'summaryfield' => '0',
                ),
            ),
        ),
        array(
            'label' => 'Thông tin mô tả',
            'fields' => array(
                array(
                    'fieldname' => 'description',
                    'uitype' => 19,
                    'columnname' => 'description',
                    'tablename' => 'vtiger_citys',
                    'generatedtype' => 1,
                    'fieldlabel' => 'Mô tả',
                    'readonly' => 1,
                    'presence' => 0,
                    'defaultvalue' => '',
                    'sequence' => 1,
                    'maximumlength' => 100,
                    'typeofdata' => 'V~O',
                    'quickcreate' => 2,
                    'quickcreatesequence' => 0,
                    'displaytype' => 1,
                    'info_type' => 'BAS',
                    'helpinfo' => '',
                    'masseditable' => '1',
                    'summaryfield' => '0',
                ),
            ),
        ),
    ),
    'customviews' => array(
        array(
            'viewname' => 'All',
            'setdefault' => true,
            'setmetrics' => true,
            'fields' => array(
                'city_name', 'city_type', 'order_appearance'
            ),
        ),
    ),
    'sharingaccess' => 'public_readwritedelete',
    'relatedlists' => array(
        array(
            'relatedmodule' => 'Documents',
            'label' => 'Documents',
            'actions' => array('ADD'),
            'function' => 'get_attachments',
        ),
        array(
            'relatedmodule' => 'ModComments',
            'label' => 'ModComments',
            'actions' => array('ADD'),
            'function' => 'get_comments',
        )
    ),
);
tool_install_module_manifest($manifest, 8);

/*************************************************************************************************/
tool_echo_linebreak('End: ' . date('Y-m-d H:i:s'));