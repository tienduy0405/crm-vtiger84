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
$webUI = new Vtiger_WebUI();
global $adb, $current_user;
if($current_user === null) {
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile(1, "Users");
}

$fields = array(
    'Citys' => array(
        'Thông tin chung' => array(
            'source' => array(
                'label' => "Nguồn",
                'uitype' => 1,
                'table' => 'vtiger_crmentity',
                'columntype' => 'VARCHAR(255)',
                'typeofdata' => 'V~O',
                'displaytype' => 2,
                'defaultvalue' => 'CRM',
            ),
        ),

    ),
);
tool_create_fields($fields);