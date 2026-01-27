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
    'Contacts' => array(
        'LBL_CONTACT_INFORMATION' => array(
            'cf_gender' => array(
                'label' => "Giới tính",
                'table' => 'vtiger_contactscf',
                'uitype' => 15,
                'typeofdata' => 'V~O',
                'columntype' => 'VARCHAR(255)',
                'displaytype' => 1,
                'picklistvalues' => array('Nam', 'Nữ')
            ),

            'cf_lead_status' => array(
                'label' => "Tình trạng Lead",
                'table' => 'vtiger_contactscf',
                'uitype' => 15,
                'typeofdata' => 'V~O',
                'columntype' => 'VARCHAR(255)',
                'displaytype' => 1,
                'picklistvalues' => array('Đã convert', 'Khác')
            ),

            'cf_source' => array(
                'label' => "Nguồn",
                'table' => 'vtiger_contactscf',
                'uitype' => 15,
                'typeofdata' => 'V~O',
                'columntype' => 'VARCHAR(255)',
                'displaytype' => 1,
                'picklistvalues' => array('CRM', 'Khác')
            ),

            'cf_interest' => array(
                'label' => "Trạng thái quan tâm",
                'table' => 'vtiger_contactscf',
                'uitype' => 15,
                'typeofdata' => 'V~O',
                'columntype' => 'VARCHAR(255)',
                'displaytype' => 1,
                'picklistvalues' => array('Đã quan tâm', 'Khác')
            ),
        ),

        'Thông tin THPT' => array(
            'cf_city_school' => array(
                'label' => "Tỉnh/TP Trường",
                'table' => 'vtiger_contactscf',
                'uitype' => 15,
                'typeofdata' => 'V~O',
                'columntype' => 'VARCHAR(255)',
                'displaytype' => 1,
                'picklistvalues' => array('test')
            ),
        ),

    ),
);
tool_create_fields($fields);