<?php

class Citys extends CRMEntity
{
    var $table_name = 'vtiger_citys';
    var $table_index = 'citysid';

    var $customFieldTable = array('vtiger_cityscf', 'citysid');

    var $tab_name = array('vtiger_crmentity', 'vtiger_citys', 'vtiger_cityscf');

    var $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_citys' => 'citysid',
        'vtiger_cityscf' => 'citysid');

    var $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Assigned To' => array('crmentity', 'smownerid'),
        'Tên' => array('citys', 'city_name'),

    );
    var $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Assigned To' => 'assigned_user_id',
        'Tên' => 'city_name',
    );

    // Make the field link to detail view
    // var $list_link_field = 'exampleamount';

    // For Popup listview and UI type support
    var $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Tên' => array('vtiger_citys', 'city_name'),
        'Assigned To' => array('vtiger_crmentity', 'assigned_user_id'),
    );
    var $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Tên' => 'city_name',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    var $popup_fields = array('city_name');

    // For Alphabetical search
    var $def_basicsearch_col = 'city_name';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'city_name';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = array('city_name', 'assigned_user_id');

    var $default_order_by = 'city_name';
    var $default_sort_order = 'ASC';

    public function __construct()
    {
        if (class_exists('LoggerManager')) {
            $this->log = LoggerManager::getLogger('Citys');
        } else {
            $this->log = Logger::getLogger('Citys');
        }
        $this->db = PearDatabase::getInstance();
        $this->column_fields = getColumnFields('Citys');
    }

    function save_module($module)
    {

    }

    function vtlib_handler($moduleName, $event_type)
    {
        if ($event_type == 'module.postinstall') {
            self::updateWSEntity($moduleName);
            self::enableModTracker($moduleName);
            self::updateAutoGenerateField($moduleName, 'CITY');
        } else if ($event_type == 'module.disabled') {
            // TODO Handle actions when this module is disabled.
        } else if ($event_type == 'module.enabled') {
            // TODO Handle actions when this module is enabled.
        } else if ($event_type == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
        } else if ($event_type == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } else if ($event_type == 'module.postupdate') {
            // TODO Handle actions after this module is updated.
        }
    }

    public static function enableModTracker($moduleName)
    {
        include_once 'vtlib/Vtiger/Module.php';
        include_once 'modules/ModTracker/ModTracker.php';

        //Enable ModTracker for the module
        $moduleInstance = Vtiger_Module::getInstance($moduleName);
        ModTracker::enableTrackingForModule($moduleInstance->getId());
    }

    public static function updateWSEntity($moduleName)
    {
        global $adb;
        $sql = "select * from `vtiger_ws_entity` where `name`='$moduleName'";
        $res = $adb->query($sql);
        if ($adb->num_rows($res) <= 0) {
            $sql = "insert into vtiger_ws_entity SET `name`='$moduleName',`handler_path`='include/Webservices/VtigerModuleOperation.php',`handler_class`='VtigerModuleOperation',`ismodule`='1'";
            $adb->query($sql);
        }
    }

    public static function updateAutoGenerateField($moduleName, $prefix = 'CITY')
    {
        global $adb;
        $sql = "SELECT * FROM vtiger_modentity_num WHERE semodule=? LIMIT 1";
        $res = $adb->pquery($sql, array($moduleName));
        if ($adb->num_rows($res) == 0) {
            $res = $adb->pquery("SELECT MAX(num_id) num_id FROM `vtiger_modentity_num`;", array());
            $num_id = $adb->query_result($res, 0, 'num_id');
            $num_id++;
            $adb->pquery("INSERT INTO `vtiger_modentity_num` (`num_id`, `semodule`, `prefix`, `start_id`, `cur_id`, `active`) VALUES ('$num_id', '{$moduleName}', '{$prefix}', '1', '1', '1')", array());
            $adb->pquery("UPDATE `vtiger_modentity_num_seq` SET `id`='$num_id'", array());
        }
    }

    function save_related_module($module, $crmid, $with_module, $with_crmids, $otherParams = array())
    {
        $adb = PearDatabase::getInstance();

        if (!is_array($with_crmids)) $with_crmids = array($with_crmids);
        foreach ($with_crmids as $with_crmid) {
            if ($with_module == 'SalesOrder') { //When we select contact from potential related list
                $sql = "update vtiger_salesorder set sitesid = ? where salesorderid = ?";
                $adb->pquery($sql, array($crmid, $with_crmid));
            } else {
                parent::save_related_module($module, $crmid, $with_module, $with_crmid);
            }
        }
    }
}