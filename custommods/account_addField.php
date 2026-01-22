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
    'Accounts' => array(
        'LBL_ACCOUNT_INFORMATION' => array(
            'cf_city' => array(
                'label' => "Tỉnh/Thành Phố",
                'table' => 'vtiger_accountscf',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'columntype' => 'INT(19)',
                'displaytype' => 1,
                'relatedModules' => array('Citys')
            ),

            'cf_ward' => array(
                'label' => "Xã/Phường",
                'table' => 'vtiger_accountscf',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'columntype' => 'INT(19)',
                'displaytype' => 1,
                'relatedModules' => array('Wards')
            ),
        ),

    ),
);
tool_create_fields($fields);