<?php

class Citys_Uninstall_Action extends Vtiger_Action_Controller
{
    function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    public function process(Vtiger_Request $request)
{
    define('DS', DIRECTORY_SEPARATOR);
    require_once('include/utils/utils.php');

    global $adb, $site_URL;

    include_once('vtlib/Vtiger/Module.php');
    $module = Vtiger_Module::getInstance('<ModuleName>');
    if ($module) {
        $module->delete();
    }

    echo "<br>&nbsp;&nbsp;<b>Uninstall <ModuleName> module</b>";


    // vtiger_links
    $table = 'vtiger_citys';
    $tablecf = 'vtiger_cityscf';
    $table_user_field = 'vtiger_citys_user_field';
    $result = $adb->pquery("DROP TABLE {$table};");
    echo "<br>&nbsp;&nbsp;- ".$table."  tables";

    if ($result) echo " - DONE"; else echo " - <b>ERROR</b>";

    $result = $adb->pquery("DROP TABLE {$tablecf};");
    echo "<br>&nbsp;&nbsp;- ".$tablecf."  tables";
    if ($result) echo " - DONE"; else echo " - <b>ERROR</b>";

    $result = $adb->pquery("DROP TABLE {$table_user_field};");
    echo "<br>&nbsp;&nbsp;- ".$table_user_field."  tables";
    if ($result) echo " - DONE"; else echo " - <b>ERROR</b>";

    $result = $adb->pquery("DELETE FROM `vtiger_field` WHERE tablename = '{$table}'");
    echo "<br>&nbsp;&nbsp;- ".$table." Failed tables";
    if ($result) echo " - DONE"; else echo " - <b>ERROR</b>";

    $result = $adb->pquery("DELETE FROM `vtiger_field` WHERE tablename = '{$tablecf}'");
    echo "<br>&nbsp;&nbsp;- ".$tablecf." Failed tables";
    if ($result) echo " - DONE"; else echo " - <b>ERROR</b>";

    $tabId = getTabid('Citys');
    $adb->pquery("DELETE FROM `vtiger_relatedlists` WHERE tabid = ?", [$tabId]);
    $adb->pquery("DELETE FROM `vtiger_relatedlists` WHERE label = ?", ['Citys']);
    echo "<br>&nbsp;&nbsp;- vtiger_relatedlists relatedlists tables";
    if ($result) echo " - DONE"; else echo " - <b>ERROR</b>";
    $name = "Citys";
    $result = $adb->pquery("DELETE FROM `vtiger_tab` WHERE `name` = '{$name}'");
    echo "<br>&nbsp;&nbsp;- Delete in vtiger_tab";
    if ($result) echo " - DONE"; else echo " - <b>ERROR</b>";


    // remove directory
    $res_template = $this->delete_directory('layouts/v7/modules/Citys');
    $res_module = $this->delete_directory('modules/Citys');

    if (!$res_module) {
        echo '
                <div style="margin-bottom: 4px;" class="helpmessagebox">
                    <b style="color: red;">ERROR</b> Can not delete the folder <b>modules/Citys</b>. Please check permission and run script again.
                </div>
            ';
    } else {
        echo "<script>
                alert('Unintall Citys module successfully');
                window.location = '$site_URL';
            </script>";
    }
}


    function delete_directory($dirname)
    {
        if (is_dir($dirname)) $dir_handle = opendir($dirname);
        if (!$dir_handle) return false;
        while ($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname . "/" . $file)) {
                    unlink($dirname . "/" . $file);
                } else {
                    $this->delete_directory($dirname . '/' . $file);
                }
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

}
