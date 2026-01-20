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
include_once 'includes/main/WebUI.php';
include_once dirname(__FILE__) . '/tool.lib.php';

// override memory_limit in config.inc.php
ini_set("memory_limit", -1);

error_reporting(E_ERROR);
ini_set('display_errors', -1);
global $adb;
$adb->setDebug(1);
$webUI = new Vtiger_WebUI();
global $adb, $current_user, $root_directory;
$user = new Users();
$current_user = $user->retrieveCurrentUserInfoFromFile(1);
$moduleName = 'Citys';
tool_related_module($moduleName, 'Wards', 'Xã/Phường', array('add','select'), 'get_dependents_list', 'city_id');