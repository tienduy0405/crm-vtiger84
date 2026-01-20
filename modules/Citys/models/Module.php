<?php
/*+***********************************************************************************
* The contents of this file are subject to the Boru, Inc. License.
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: Boru, Inc.
* The Initial Developer of the Original Code is Boru, Inc.
* Portions created by Boru, Inc. are Copyright (C) Boru, Inc.
* All Rights Reserved.
*************************************************************************************/

class Citys_Module_Model extends Vtiger_Module_Model {
    public function getModuleIcon() {
        $moduleName = $this->getName();
        $lowerModuleName = strtolower($moduleName);
        $title = vtranslate($moduleName, $moduleName);

        $moduleIcon = "<i class='vicon-$lowerModuleName' title='$title'></i>";
        if ($this->source == 'custom') {
            $moduleShortName = mb_substr(trim($title), 0, 2);
            $moduleIcon = "<span class='custom-module' title='$title'>$moduleShortName</span>";
        }

        $imageFilePath = 'layouts/'.Vtiger_Viewer::getLayoutName()."/modules/$moduleName/$moduleName.png";
        if (file_exists($imageFilePath)) {
            $moduleIcon = "<img src='$imageFilePath' title='$title' width='30'/>";
        }

        return $moduleIcon;
    }

    public function getSettingLinks()
    {
        $settingsLinks = parent::getSettingLinks();

        $settingsLinks[] = array(
            'linktype' => 'MODULESETTING',
            'linklabel' => 'Uninstall',
            'linkurl' => 'index.php?module=Citys&action=Uninstall',
            'linkicon' => ''
        );

        return $settingsLinks;
    }
    function getCalendarActivities($mode, $pagingModel, $user, $recordId = false) {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $db = PearDatabase::getInstance();

        if (!$user) {
            $user = $currentUser->getId();
        }

        $nowInUserFormat = Vtiger_Datetime_UIType::getDisplayDateTimeValue(date('Y-m-d H:i:s'));
        $nowInDBFormat = Vtiger_Datetime_UIType::getDBDateTimeValue($nowInUserFormat);
        list($currentDate, $currentTime) = explode(' ', $nowInDBFormat);

        $focus = CRMEntity::getInstance($this->getName());
        $focus->id = $recordId;

        $query = "SELECT
                    vtiger_crmentity.*,
                    vtiger_activity.*,
                    CASE
                        WHEN (vtiger_users.user_name NOT LIKE '') THEN vtiger_users.userlabel
                        ELSE vtiger_groups.groupname
                    END AS user_name,
                    vtiger_activitycf.*
                FROM
                    vtiger_activity
                INNER JOIN vtiger_crmentity ON
                    vtiger_crmentity.crmid = vtiger_activity.activityid
                INNER JOIN vtiger_crmentityrel ON
                    (vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid
                        OR vtiger_crmentityrel.crmid = vtiger_crmentity.crmid)
                LEFT JOIN vtiger_activitycf ON
                    vtiger_activitycf.activityid = vtiger_activity.activityid
                LEFT JOIN vtiger_users ON
                    vtiger_users.id = vtiger_crmentity.smownerid
                LEFT JOIN vtiger_groups ON
                    vtiger_groups.groupid = vtiger_crmentity.smownerid
                WHERE
                    vtiger_crmentity.deleted = 0
                    AND (vtiger_crmentityrel.crmid = ?
                        OR vtiger_crmentityrel.relcrmid = ?)";

        $query .= " ORDER BY date_start, time_start LIMIT " . $pagingModel->getStartIndex() . ", " . ($pagingModel->getPageLimit() + 1);

        $result = $db->pquery($query, array($recordId, $recordId));
        $numOfRows = $db->num_rows($result);

        $groupsIds = Vtiger_Util_Helper::getGroupsIdsForUsers($currentUser->getId());
        $activities = array();
        $recordsToUnset = array();
        for ($i = 0; $i < $numOfRows; $i++) {
            $newRow = $db->query_result_rowdata($result, $i);
            $model = Vtiger_Record_Model::getCleanInstance('Calendar');
            $ownerId = $newRow['smownerid'];
            $currentUser = Users_Record_Model::getCurrentUserModel();
            $visibleFields = array('activitytype', 'date_start', 'time_start', 'due_date', 'time_end', 'assigned_user_id', 'visibility', 'smownerid', 'crmid');
            $visibility = true;
            if (in_array($ownerId, $groupsIds)) {
                $visibility = false;
            } else if ($ownerId == $currentUser->getId()) {
                $visibility = false;
            }
            if (!$currentUser->isAdminUser() && $newRow['activitytype'] != 'Task' && $newRow['visibility'] == 'Private' && $ownerId && $visibility) {
                foreach ($newRow as $data => $value) {
                    if (in_array($data, $visibleFields) != -1) {
                        unset($newRow[$data]);
                    }
                }
                $newRow['subject'] = vtranslate('Busy', 'Events') . '*';
            }
            if ($newRow['activitytype'] == 'Task') {
                unset($newRow['visibility']);

                $due_date = $newRow["due_date"];
                $dayEndTime = "23:59:59";
                $EndDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($due_date . " " . $dayEndTime);
                $dueDateTimeInDbFormat = explode(' ', $EndDateTime);
                $dueTimeInDbFormat = $dueDateTimeInDbFormat[1];
                $newRow['time_end'] = $dueTimeInDbFormat;
            }

            if ($newRow['crmentity2module'] == 'Contacts') {
                $newRow['contact_id'] = $newRow['parent_id'];
                unset($newRow['parent_id']);
            }
            $model->setData($newRow);
            $model->setId($newRow['crmid']);
            $activities[$newRow['crmid']] = $model;
            if (!$currentUser->isAdminUser() && $newRow['activitytype'] == 'Task' && isToDoPermittedBySharing($newRow['crmid']) == 'no') {
                $recordsToUnset[] = $newRow['crmid'];
            }
        }

        $pagingModel->calculatePageRange($activities);
        if ($numOfRows > $pagingModel->getPageLimit()) {
            array_pop($activities);
            $pagingModel->set('nextPageExists', true);
        } else {
            $pagingModel->set('nextPageExists', false);
        }
        //after setting paging model, unsetting the records which has no permissions
        foreach ($recordsToUnset as $record) {
            unset($activities[$record]);
        }
        return $activities;
    }

    public function isCommentEnabled() {
        $enabled = true;
        return $enabled;
    }
}
