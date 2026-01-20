<?php

/* @TODO: READ FILE TYPE WITH CALLBACK */

function tool_read_xls_callback($file, $sheet_index = 0, $construct = array(), $callback = ''){
    if(!file_exists($file)){
        return false;
    }
    include_once 'libraries/PHPExcel/PHPExcel.php';
    /* @var $excelReader PHPExcel_Reader_Excel2007 */
    $excelReader = PHPExcel_IOFactory::createReaderForFile($file);
    $objPHPExcel = $excelReader->load($file);
    /* @var $worksheet PHPExcel_Worksheet */
    $sheet = $objPHPExcel->getSheet($sheet_index);
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();
    $rowIndex = tool_build_xls_column_index($highestColumn);
    for ($row = 1; $row <= $highestRow; $row++){
        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
            NULL,
            TRUE,
            FALSE);
        $rowData = $rowData[0];
        foreach($construct as $r => $t){
            $v = '';
            if($t == 'calculated'){
                $v = $sheet->getCell($r . $row)->getCalculatedValue();
            } else if($t == 'formatted'){
                $v = $sheet->getCell($r . $row)->getFormattedValue();
            }
            $rowData[$rowIndex[$r]] = $v;
        }
        if($callback){
            $result = $callback($rowData, $row);
            if($result === false){
                break 1;
            }
        }
    }
    return true;
}

function tool_read_csv_callback($file, $callback = ''){
    if(!file_exists($file)){
        return false;
    }
    $fp = fopen($file, "r");
    $position = 0;
    while (($data = fgetcsv($fp)) !== FALSE) {
        $position++;
        if($callback){
            $result = $callback($data, $position);
            if($result === false){
                break 1;
            }
        }
    }
    return true;
}

function tool_query_batch_callback($query, $limit = 100, $callback = ''){
    global $adb;
    $query .= ' LIMIT ?, ?';
    $run = true;
    $start = 0;
    $position = 0;
    while($run){
        $res = $adb->pquery($query, array($start, $limit));
        if($adb->num_rows($res)){
            while($row = $adb->fetchByAssoc($res, -1, false)){
                $position++;
                if($callback){
                    $result = $callback($row, $position);
                    if($result === false){
                        $run = false;
                        break 2;
                    }
                }
            }
            $start += $limit;
        } else {
            $run = false;
        }
    }
}

function tool_query_batch_id_callback($query, $id_field, $id_start = 0, $callback = ''){
    global $adb;
    $run = true;
    $position = 0;
    while($run){
        $res = $adb->pquery($query, array($id_start));
        if($adb->num_rows($res)){
            while($row = $adb->fetchByAssoc($res, -1, false)){
                $position++;
                if($callback){
                    $result = $callback($row, $position);
                    if($result === false){
                        $run = false;
                        break 2;
                    }
                }
                $id_start = $row[$id_field];
            }
        } else {
            $run = false;
        }
    }
}

function tool_query_batch_delete_callback($query, $limit = 100, $callback = '', $delete_callback = ''){
    global $adb;
    $query .= ' LIMIT ?';
    $run = true;
    $position = 0;
    while($run){
        $res = $adb->pquery($query, array($limit));
        if($adb->num_rows($res)){
            while($row = $adb->fetchByAssoc($res, -1, false)){
                $position++;
                if($callback){
                    $result = $callback($row, $position);
                    if($delete_callback){
                        $delete_callback($row, $position);
                    }
                    if($result === false){
                        $run = false;
                        break 2;
                    }
                }
            }
        } else {
            $run = false;
        }
    }
}

function tool_build_xls_column_index($max_column){
    $start = 'A';
    $position = 0;
    $run = true;
    $data = array(
        $start => $position,
    );
    while($run){
        $start++;
        $position++;
        $data[$start] = $position;
        if($start == $max_column){
            $run = false;
        }
    }
    return $data;
}

function tool_scrap_mail_callback($mailConfig, $scrapConfig = array(), $callback = ''){
    if(!function_exists('imap_open')){
        return false;
    }
    $mailConfigDef = array(
        'host' => '',
        'port' => '',
        'user' => '',
        'pass' => '',
        'ssl' => 1,
        'host_type' => 'imap',
    );
    $scrapConfigDef = array(
        'mailbox' => 'INBOX',
        'search' => 'UNSEEN',
        'download_attachments' => false,
        'folder_attachments' => dirname(__FILE__) . '/email_scraper/'
    );
    $mailConfig = array_merge($mailConfigDef, $mailConfig);
    $scrapConfig = array_merge($scrapConfigDef, $scrapConfig);
    if(!$mailConfig['host'] || !$mailConfig['user'] || !$mailConfig['pass']){
        return false;
    }
    $hostname = '{' . $mailConfig['host'];
    if($mailConfig['port']){
        $hostname .= ':' . $mailConfig['port'];
    }
    $hostname .= '/' . $mailConfig['host_type'];
    if ($mailConfig["ssl"] == 1) {
        $hostname .= "/ssl/novalidate-cert";
    } else {
        $hostname .= "/notls";
    }
    $hostname .= '}' . $scrapConfig['mailbox'];
    $mailbox = imap_open($hostname, $mailConfig['user'], $mailConfig['pass']);
    if(!$mailbox){
        return false;
    }
    $emails = imap_search($mailbox, $scrapConfig['search']);
    if (!$emails) {
        return true;
    }
    $folder_attachments = $scrapConfig['folder_attachments'];
    $download_attachments = $scrapConfig['download_attachments'];
    if($download_attachments){
        @mkdir($folder_attachments, 0777, true);
    }
    foreach ($emails as $email_number) {
        $header = imap_header($mailbox, $email_number);
        $body = imap_fetchbody($mailbox, $email_number, 1);
        $email_original = imap_fetchbody($mailbox, $email_number, "");
        $attachments = array();
        if($download_attachments){
            $structure = imap_fetchstructure($mailbox, $email_number);
            if(isset($structure->parts) && count($structure->parts)){
                for($i = 0; $i < count($structure->parts); $i++){
                    $attachments[$i] = array(
                        'is_attachment' => false,
                        'filename' => '',
                        'name' => '',
                        'attachment' => ''
                    );

                    if($structure->parts[$i]->ifdparameters){
                        foreach($structure->parts[$i]->dparameters as $object){
                            if(strtolower($object->attribute) == 'filename'){
                                $attachments[$i]['is_attachment'] = true;
                                $attachments[$i]['filename'] = $object->value;
                            }
                        }
                    }

                    if($structure->parts[$i]->ifparameters){
                        foreach($structure->parts[$i]->parameters as $object){
                            if(strtolower($object->attribute) == 'name'){
                                $attachments[$i]['is_attachment'] = true;
                                $attachments[$i]['name'] = $object->value;
                            }
                        }
                    }

                    if($attachments[$i]['is_attachment']){
                        $attachments[$i]['attachment'] = imap_fetchbody($mailbox, $email_number, $i+1);
                        if($structure->parts[$i]->encoding == 3){ /* 3 = BASE64 encoding */
                            $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                        } elseif($structure->parts[$i]->encoding == 4){ /* 4 = QUOTED-PRINTABLE encoding */
                            $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                        }
                    }
                }
            }

            foreach($attachments as $k => $attachment){
                if($attachment['is_attachment'] == 1){
                    $filename = $attachment['name'];
                    if(empty($filename)) $filename = $attachment['filename'];
                    if(empty($filename)) $filename = "blank.dat";
                    $download_path = $folder_attachments . microtime(true) . '_' . $filename;
                    $attachment['download_path'] = $download_path;
                    $fp = fopen($download_path, "w+");
                    fwrite($fp, $attachment['attachment']);
                    fclose($fp);
                    $attachment['attachment'] = '';
                    $attachments[$k] = $attachment;
                }
            }
        }
        $data = $header;
        $data->email_body = $body;
        $data->email_original = $email_original;
        $data->attachments = $attachments;
        if($callback){
            $result = $callback($mailbox, $email_number, $data);
            if($result === false){
                imap_close($mailbox);
                return true;
            }
        }
    }
    imap_close($mailbox);
    return true;
}

function tool_mine_parse_email($message_full_body, $email_number = '') {
    require_once 'include/maillibs/mime_parser.php';
    require_once 'include/maillibs/rfc822_addresses.php';
    $mime = new mime_parser_class;
    $mime->mbox = 0;
    $mime->decode_bodies = 1;
    $mime->ignore_syntax_errors = 1;
    $mime->track_lines = 1;
    $bodystring = $filename = $messageid = '';
    $parameters = array(
        'Data' => $message_full_body,
        'SaveBody' => null, // Ezri ::: 08 29 2011 ::: Added to match borucrm version
        'SkipBody' => 0
    );
    $decoded = array();
    $results = array();
    if (!$mime->Decode($parameters, $decoded)) {
        return false;
    } else {
        $analyze_results = array();
        for ($message = 0; $message < count($decoded); $message++) {
            if ($mime->decode_bodies) {
                if ($mime->Analyze($decoded[$message], $results))
                    $analyze_results = $results;
            }
            $messageid = $decoded[0]["Headers"]["message-id:"];
            if (empty($messageid))
                $messageid = $decoded[0]["Headers"]["message_id:"];
            if (empty($messageid))
                $messageid = '<' . md5($email_number) . (isset($cgf_array['user']) ? $cgf_array['user'] : "") . '>';
        }
        if(!$analyze_results){
            return false;
        }
        $email_date = $analyze_results['Date'];

        for ($warning = 0, Reset($mime->warnings); $warning < count($mime->warnings); Next($mime->warnings), $warning++) {
            $w = Key($mime->warnings);
        }
    }

    $subject = $analyze_results["Subject"];
    if ($subject == "") {
        $subject = "<<<BLANK SUBJECT>>>";
    } else {
        $filename = ($subject);
    }

    $ar_emailto = Array();
    foreach ($analyze_results["To"] as $k => $v) {
        $ar_emailto[] = $v["address"];
    }
    $ar_cc = Array();
    if (isset($analyze_results["Cc"]) && is_array($analyze_results["Cc"])) {
        foreach ($analyze_results["Cc"] as $k => $v) {
            $ar_cc[] = $v["address"];
        }
    }
    $ar_bcc = array(); // Ezri 08 29 2011 ::: Added to match borucrm version
    if (isset($analyze_results["Bcc"]) && sizeof($analyze_results["Bcc"]) > 0) {
        foreach ($analyze_results["Bcc"] as $k => $v) {
            $ar_bcc[] = $v["address"];
        }
    }
    $from = $analyze_results["From"][0]["address"];

    if (isset($analyze_results["DataFile"])) {
        if (strtolower($analyze_results["Type"]) == "html" || strtolower($analyze_results["Type"]) == "text") {
            $newcontents = file_get_contents($analyze_results["DataFile"]);
            $bodystring = $newcontents;
        }
    }

    if (isset($analyze_results["Data"])) {
        if (strtolower($analyze_results["Type"]) == "html" || strtolower($analyze_results["Type"]) == "text") {
            $bodystring = $analyze_results["Data"];
        }
    }

    if ($analyze_results["Type"] == "text") {
        $bodystring = nl2br($bodystring);
    }

    $emailReturn = array();
    if (trim($bodystring) != '') {
        $emailReturn["subject"] = $subject;
        $emailReturn["from"] = $from;
        $emailReturn["to"] = $ar_emailto;
        $emailReturn["cc"] = $ar_cc;
        $emailReturn["bcc"] = $ar_bcc;
        $emailReturn["date"] = $email_date;
        //$emailReturn["body"] = preg_replace('/[^(\x20-\x7F)]*/','', $bodystring);
        $emailReturn["body"] = $bodystring;
        $emailReturn["attach_file"] = $filename;
        $emailReturn["messageid"] = $messageid;
    } else {

    }
    return $emailReturn;
}

/* @TODO: CREATE FILE TYPE */

function tool_create_excel_file($file_path, $excelData){
    require_once dirname(__FILE__) . '/../libraries/PHPExcel/PHPExcel.php';
    $objPHPExcel = new PHPExcel();
    foreach($excelData as $tab => $sheetData){
        if($tab > 0){
            $objPHPExcel->createSheet();
        }
        $objPHPExcel->setActiveSheetIndex($tab);
        $objPHPExcel->getActiveSheet()->setTitle($sheetData['title']);

        $position = 1;
        foreach($sheetData['data'] as $row){
            $column = 'A';
            foreach($row as $item){
                $objPHPExcel->getActiveSheet()->SetCellValue($column . $position, $item);
                $column++;
            }
            $position++;
        }
        foreach (range('A', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
            $objPHPExcel->getActiveSheet()
                ->getColumnDimension($col)
                ->setAutoSize(true);
        }
    }
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save($file_path);
    return true;
}

function tool_create_csv_from_query($query, $file, $callback = '', $batch_limit = 100){
    @unlink($file);
    $file_fp = fopen($file, 'w+');
    tool_query_batch_callback($query, $batch_limit, function($row, $position) use ($file_fp, $callback) {
        if($position == 1){
            $headers = array_keys($row);
            if($callback){
                $is_header = true;
                $headers = $callback($headers, $is_header);
            }
            if($headers){
                fputcsv($file_fp, $headers);
            }
        }
        if($callback){
            $is_header = false;
            $row = $callback($row, $is_header);
        }
        if($row){
            fputcsv($file_fp, $row);
        }
    });
}

/* @TODO: ADD FUNCTIONS */

function tool_add_picklist_values($moduleName, $field, $values){
    global $adb;
    $tabId = getTabid($moduleName);
    $res = $adb->pquery("SELECT * FROM vtiger_field WHERE tabid = ? AND fieldname = ?", array($tabId, $field));
    if(!$adb->num_rows($res)){
        return false;
    }
    $table = 'vtiger_' . $field;
    $value_res = $adb->pquery("SELECT * FROM {$table}");
    $currentValue = array();
    while($row = $adb->fetchByAssoc($value_res)){
        $currentValue[] = $row[$field];
    }
    foreach($values as $k => $value){
        if(in_array($value, $currentValue)){
            unset($values[$k]);
        }
    }
    $module= Vtiger_Module::getInstance($moduleName);
    $fieldModel = Vtiger_Field_Model::getInstance($field, $module);
    if(!$fieldModel || !$fieldModel->getId()){
        return false;
    }
    if($values){
        $fieldModel->setPicklistValues($values);
    }
    return true;
}

function tool_create_inventory($moduleName, $data, $items, $order_total = null, $generate_group_tax = false, $requestData = array()){
    return tool_save_inventory($moduleName, null, $data, $items, $order_total, $generate_group_tax, $requestData);
}

function tool_save_inventory($moduleName, $recordId, $data, $items, $order_total = null, $generate_group_tax = false, $requestData = array(), $reset = false){
    $total = 0;
    if($order_total)
        $total = $order_total;
    unset($_REQUEST);
    unset($_POST);
    unset($_GET);
    $count = 0;
    $itemDataDef = array(
        'deleted' => 0,
        'hidtax_row_no' => null,
        'lineItemType' => 'Products',
        'subproduct_ids' => null,
        'comment' => '',
        'discount_type' => 'amount',
        'discount_percentage' => 0,
        'discount' => 'off',
        'discount_amount' => 0,
    );
    foreach($items as $item){
        $count++;
        $productRequest = array();
        foreach($itemDataDef as $k => $v){
            $productRequest[$k . $count] = $v;
        }
        foreach($item as $k => $v){
            if($k == 'name'){
                $k = 'productName';
            } else if($k == 'id'){
                $k = 'hdnProductId';
            } else if($k == 'type'){
                $k = 'lineItemType';
            } else if($k == 'price'){
                $k = 'listPrice';
            }
            $productRequest[$k . $count] = $v;
        }
        foreach($productRequest as $request_key => $request_value){
            $_REQUEST[$request_key] = $request_value;
        }
        if(!$order_total)
            $total += $item['total'];
    }

    $orderTotal = array(
        'discount_type_final' => 'amount',
        'discount_percentage_final' => 0,
        'discount_final' => 'on',
        'discount_amount_final' => 0,
        'tax1_group_percentage' => 0,
        'tax1_group_amount' => 0,
        'tax2_group_percentage' => 0,
        'tax2_group_amount' => 0,
        'tax3_group_percentage' => 0,
        'tax3_group_amount' => 0,
        'shipping_handling_charge' => 0,
        'shtax1_sh_percent' => 0.00,
        'shtax1_sh_amount' => 0.00,
        'shtax2_sh_percent' => 0.00,
        'shtax2_sh_amount' => 0.00,
        'shtax3_sh_percent' => 0.00,
        'shtax3_sh_amount' => 0.00,
        'adjustmentType' => '+',
        'adjustment' => 0,
        'totalProductCount' => $count,
        'subtotal' => $total,
        'total' => $total,
    );
    if($generate_group_tax){
        $taxInfo = tool_generate_inventory_group_tax($total);
        $orderTotal = array_merge($orderTotal, $taxInfo['data']);
        $orderTotal['total'] = $orderTotal['total'] + $taxInfo['total'];
        $orderTotal['taxtype'] = 'group';
    }
    foreach($orderTotal as $total_key => $total_value){
        $_REQUEST[$total_key] = $total_value;
    }
    foreach($requestData as $k => $v){
        $_REQUEST[$k] = $v;
    }
    if(!$recordId){
        $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
    } else {
        if($reset){
            if(!function_exists('vtws_getWebserviceEntityId')){
                include_once 'include/Webservices/Utils.php';
            }
            $wsRecordId = vtws_getWebserviceEntityId($moduleName, $recordId);
            VTEntityCache::unsetCachedEntity($wsRecordId);
        }
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        $recordModel->setId($recordId);
        $recordModel->set('mode', 'edit');
    }
    foreach($data as $k => $v){
        $recordModel->set($k, $v);
    }
    $recordModel->save();

    return $recordModel->getId();
}

function tool_save_inventory_ajax($moduleName, $recordId, $data, $reset = false){
    if($recordId && !isRecordExists($recordId)){
        return false;
    }
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : false;
    $_REQUEST['action'] = 'SaveAjax';
    if(!$recordId){
        $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
    } else {
        if($reset){
            if(!function_exists('vtws_getWebserviceEntityId')){
                include_once 'include/Webservices/Utils.php';
            }
            $wsRecordId = vtws_getWebserviceEntityId($moduleName, $recordId);
            VTEntityCache::unsetCachedEntity($wsRecordId);
        }
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        $recordModel->setId($recordId);
        $recordModel->set('mode', 'edit');
    }
    foreach($data as $k => $v){
        $recordModel->set($k, $v);
    }
    $recordModel->save();
    if($action){
        $_REQUEST['action'] = $action;
    }
    return $recordModel->getId();
}

function tool_create_record($moduleName, $data){
    return tool_save_record($moduleName, null, $data);
}

function tool_save_record($moduleName, $recordId, $data, $reset = false){
    if($recordId && !isRecordExists($recordId)){
        return false;
    }
    if(!$recordId){
        $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
    } else {
        if($reset){
            if(!function_exists('vtws_getWebserviceEntityId')){
                include_once 'include/Webservices/Utils.php';
            }
            $wsRecordId = vtws_getWebserviceEntityId($moduleName, $recordId);
            VTEntityCache::unsetCachedEntity($wsRecordId);
        }
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        $recordModel->setId($recordId);
        $recordModel->set('mode', 'edit');
    }
    foreach($data as $k => $v){
        $recordModel->set($k, $v);
    }
    $recordModel->save();
    return $recordModel->getId();
}

function tool_generate_inventory_group_tax($total){
    global $adb;
    $res = $adb->pquery("SELECT * FROM vtiger_inventorytaxinfo WHERE deleted = 0");
    $data = array();
    $total_tax = 0;
    while($row = $adb->fetchByAssoc($res, -1, false)){
        $amount = $row['percentage'] * $total / 100;
        $data[$row['taxname'] . '_group_percentage'] = $row['percentage'];
        $data[$row['taxname'] . '_group_amount'] = $amount;
        $total_tax += $amount;
    }
    return array(
        'total' => $total_tax,
        'data' => $data,
    );
}

function tool_related_module($moduleName, $relatedModuleName, $label, $action, $related_function, $related_field = null){
    $moduleInstance = Vtiger_Module::getInstance($moduleName);
    $relatedListModule = Vtiger_Module::getInstance($relatedModuleName);
    $fieldId = null;
    if($related_field){
        $field = tool_get_field_data($relatedModuleName, $related_field);
        if($field){
            $fieldId = $field['fieldid'];
        }
    }
    $moduleInstance->setRelatedList($relatedListModule, $label, $action, $related_function, $fieldId);
}

function tool_create_document_from_file($data, $file){
    global $adb, $root_directory;
    $filename = basename($file);
    $filetype = mime_content_type($file);
    $filesize = filesize($file);
    $documentModel = Vtiger_Record_Model::getCleanInstance('Documents');
    $documentModel->set('filename', $filename);
    $documentModel->set('filetype', $filetype);
    $documentModel->set('filesize', $filesize);
    $documentModel->set('filelocationtype', 'I');
    $documentModel->set('filestatus', 1);
    foreach($data as $k => $v){
        $documentModel->set($k, $v);
    }
    $documentModel->save();
    $document_id = $documentModel->getId();

    $now = date('Y-m-d h:i:s');
    $storageFolder = Vtiger_Functions::initStorageFileDirectory();
    $attachment_id = $adb->getUniqueID("vtiger_crmentity");
    $path = $root_directory . $storageFolder . $attachment_id . '_' . $filename;
    if (copy($file, $path)){
        $adb->pquery("INSERT INTO `vtiger_crmentity` (`crmid`, `smcreatorid`, `smownerid`, `modifiedby`, `setype`, `description`, `createdtime`, `modifiedtime`, `viewedtime`, `status`, `version`, `presence`, `deleted`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", array($attachment_id, 1, 1, 0, 'Documents Attachment', NULL, $adb->formatDate($now, true), $adb->formatDate($now, true), NULL, NULL, 0, 1, 0));
        $adb->pquery("INSERT INTO `vtiger_attachments` (`attachmentsid`, `name`, `type`, `path`) VALUES (?, ?, ?, ?)", array($attachment_id, $filename, $filetype, $storageFolder));
        $adb->pquery("INSERT INTO `vtiger_seattachmentsrel` (`crmid`, `attachmentsid`) VALUES (?, ?)", array($document_id, $attachment_id));
    }
    return $document_id;
}

function tool_create_document_from_upload($data, $file){
    global $adb, $root_directory;
    $filename = $file['name'];
    $filetype = $file['type'];
    $filesize = $file['size'];
    $documentModel = Vtiger_Record_Model::getCleanInstance('Documents');
    $documentModel->set('filename', $filename);
    $documentModel->set('filetype', $filetype);
    $documentModel->set('filesize', $filesize);
    $documentModel->set('filelocationtype', 'I');
    $documentModel->set('filestatus', 1);
    foreach($data as $k => $v){
        $documentModel->set($k, $v);
    }
    $documentModel->save();
    $document_id = $documentModel->getId();

    $now = date('Y-m-d h:i:s');
    $storageFolder = Vtiger_Functions::initStorageFileDirectory();
    $attachment_id = $adb->getUniqueID("vtiger_crmentity");
    $path = $root_directory . $storageFolder . $attachment_id . '_' . $filename;
    if (move_uploaded_file($file['tmp_name'], $path)){
        $adb->pquery("INSERT INTO `vtiger_crmentity` (`crmid`, `smcreatorid`, `smownerid`, `modifiedby`, `setype`, `description`, `createdtime`, `modifiedtime`, `viewedtime`, `status`, `version`, `presence`, `deleted`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", array($attachment_id, 1, 1, 0, 'Documents Attachment', NULL, $adb->formatDate($now, true), $adb->formatDate($now, true), NULL, NULL, 0, 1, 0));
        $adb->pquery("INSERT INTO `vtiger_attachments` (`attachmentsid`, `name`, `type`, `path`) VALUES (?, ?, ?, ?)", array($attachment_id, $filename, $filetype, $storageFolder));
        $adb->pquery("INSERT INTO `vtiger_seattachmentsrel` (`crmid`, `attachmentsid`) VALUES (?, ?)", array($document_id, $attachment_id));
    }
    return $document_id;
}

function tool_create_setting_page($linkName, $linkURL, $blockName){
    $success = true;
    $db = PearDatabase::getInstance();
    $result = $db->pquery('SELECT 1 FROM vtiger_settings_field WHERE name=?', array($linkName));
    if ($result && !$db->num_rows($result)) {
        $blockId = 0;
        if ($blockName) {
            $blockId = getSettingsBlockId($blockName);
        }
        if (!$blockId) {
            $blockName = 'LBL_OTHER_SETTINGS';
            $blockId = getSettingsBlockId($blockName);
        }
        if (!$blockId) {
            $blockSeqResult = $db->pquery('SELECT MAX(sequence) AS sequence FROM vtiger_settings_blocks', array());
            if ($db->num_rows($blockSeqResult)) {
                $blockId = $db->getUniqueID('vtiger_settings_blocks');
                $blockSequence = $db->query_result($blockSeqResult, 0, 'sequence');
                $db->pquery('INSERT INTO vtiger_settings_blocks(blockid, label, sequence) VALUES(?,?,?)', array($blockId, 'LBL_OTHER_SETTINGS', $blockSequence++));
            }
        }
        if ($blockId) {
            $fieldSeqResult = $db->pquery('SELECT MAX(sequence) AS sequence FROM vtiger_settings_field WHERE blockid=?', array($blockId));
            if ($db->num_rows($fieldSeqResult)) {
                $fieldId = $db->getUniqueID('vtiger_settings_field');
                $linkURL = ($linkURL) ? $linkURL : '';
                $fieldSequence = $db->query_result($fieldSeqResult, 0, 'sequence');
                $db->pquery('INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence, active, pinned) VALUES(?,?,?,?,?,?,?,?,?)', array($fieldId, $blockId, $linkName, '', $linkName, $linkURL, $fieldSequence++, 0, 0));
            }
        } else {
            $success = false;
        }
    }
    return $success;
}

function tool_create_fields($fields){
    /*
    $fields = array(
        'module_name' => array(
            'block_name' => array(
                'field_name' => array(
                    'label' => '',
                    'uitype' => 1,
                    'table' => '',
                    'typeofdata' => 'V~O',
                    'displaytype' => 1,
                    'picklistvalues' => array(),
                    'relatedModules' => array(),
                ),
            ),
        ),
    );
    */
    $defaultData = array(
        'label' => false,
        'table' => false,
        'column' => false,
        'columntype' => false,
        'helpinfo' => '',
        'summaryfield' => 0,
        'masseditable' => 1,
        'uitype' => 1,
        'typeofdata' => 'V~O',
        'displaytype' => 1,
        'generatedtype' => 2,
        'readonly' => 1,
        'presence' => 2,
        'defaultvalue' => '',
        'maximumlength' => 100,
        'sequence' => false,
        'quickcreate' => 1,
        'info_type' => 'BAS',
        'isunique' => false,
        'headerfield' => 0,
    );
    foreach ($fields as $moduleName => $blocks) {
        $module = Vtiger_Module::getInstance($moduleName);
        if ($module) {
            foreach ($blocks as $blockName => $blockField) {
                $block = Vtiger_Block::getInstance($blockName, $module);
                if (!$block) {
                    $block        = new Vtiger_Block();
                    $block->label = $blockName;
                    $block->__create($module);
                }
                foreach ($blockField as $fieldName => $fieldData) {
                    $fieldData = array_merge($defaultData, $fieldData);
                    $field = Vtiger_Field::getInstance($fieldName, $module);
                    if (!$field) {
                        $field             = new Vtiger_Field();
                        $field->name       = $fieldName;
                        foreach($fieldData as $k => $v){
                            $field->$k = $v;
                        }
                        $field->__create($block);
                        if ($fieldData['uitype'] == 15 || $fieldData['uitype'] == 16 || $fieldData == 33) {
                            $field->setPicklistValues($fieldData['picklistvalues']);
                        }
                        if ($fieldData['uitype'] == 10) {
                            $field->setRelatedModules($fieldData['relatedModules']);
                        }
                        tool_echo_linebreak("Field " . $fieldData['label'] . " is created!");
                    } else {
                        tool_echo_linebreak("Field " . $fieldData['label'] . " exist!");
                    }
                }
            }
        }
    }
}

function tool_add_event_handler($eventHandlers){
    /*
    $eventHandlers = array(
        'module_name' => array(
            array(
                'eventname' => '',
                'classname' => '',
                'filename' => '',
            ),
        ),
    );
    */
    foreach ($eventHandlers as $moduleName => $eventHandler) {
        $moduleInstance = Vtiger_Module::getInstance($moduleName);
        foreach ($eventHandler as $handler) {
            Vtiger_Event::register($moduleInstance, $handler['eventname'], $handler['classname'], $handler['filename']);
            tool_echo_linebreak('Handler ' . $handler['classname'] . ' added.');
        }
    }
}

function tool_active_workflow_task_type($task_type, $module){
    global $adb;
    $res = $adb->pquery("SELECT * FROM com_vtiger_workflow_tasktypes WHERE tasktypename = ? ORDER BY id ASC LIMIT 1", array($task_type));
    if(!$adb->num_rows($res)){
        return;
    }
    $row = $adb->raw_query_result_rowdata($res, 0);
    $modules = $row['modules'];
    $modules = json_decode($modules, 1);
    $moduleInclude = $modules['include'];
    if(!in_array($module, $moduleInclude)){
        $moduleInclude[] = $module;
    }
    $modules['include'] = $moduleInclude;
    $adb->pquery("UPDATE com_vtiger_workflow_tasktypes SET modules = ? WHERE id = ? ", array(json_encode($modules), $row['id']));
}

function tool_add_workflow_custom_method($moduleName, $methodName, $functionPath, $functionName){
    global $adb;
    $check_res = $adb->pquery("SELECT * FROM com_vtiger_workflowtasks_entitymethod WHERE function_name = ?", array($functionName));
    if($adb->num_rows($check_res)){
        return;
    }
    include_once "modules/com_vtiger_workflow/include.inc";
    include_once "modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc";
    include_once "modules/com_vtiger_workflow/VTEntityMethodManager.inc";
    $entityMethodManager = new VTEntityMethodManager($adb);
    $entityMethodManager->addEntityMethod($moduleName, $methodName, $functionPath, $functionName);
}

function tool_add_workflow_custom_methods($methods){
    /*
    $methods = array(
        'module_name' => array(
            array(
                'method_name' => '',
                'function_path' => '',
                'function_name' => '',
            )
        ),
    );
    */
    foreach ($methods as $moduleName => $moduleMethod){
        foreach ($moduleMethod as $module_method){
            tool_add_workflow_custom_method($moduleName, $module_method['method_name'], $module_method['function_path'], $module_method['function_name']);
        }
    }
}

/* @TODO: GET FUNCTIONS */

function tool_get_field_data($moduleName, $fieldName){
    global $adb;
    $tabId = getTabid($moduleName);
    if(!$tabId){
        return false;
    }
    $res = $adb->pquery("SELECT * FROM vtiger_field WHERE tabid = ? AND fieldname = ?", array($tabId, $fieldName));
    if(!$adb->num_rows($res)){
        return false;
    }
    $data = $adb->raw_query_result_rowdata($res, 0);
    return $data;
}

function tool_get_product_by_name($product_name, $fetch_one = false, $like = false){
    $product_name = trim($product_name);
    global $adb;
    $query = "SELECT * FROM vtiger_products p
                INNER JOIN vtiger_productcf f ON f.productid = p.productid
                INNER JOIN vtiger_crmentity vc ON vc.crmid = p.productid
              WHERE vc.deleted = 0";
    $binds = array();
    if($like){
        $query .= ' AND p.productname LIKE ? ';
        $binds[] = '%' . $product_name . '%';
    } else {
        $query .= ' AND p.productname = ? ';
        $binds[] = $product_name;
    }
    $res = $adb->pquery($query, $binds);
    $data = array();
    if(!$adb->num_rows($res)){
        return $data;
    }
    if(!$fetch_one){
        while($row = $adb->fetchByAssoc($res, -1, false)){
            $data[] = $row;
        }
    } else {
        $data = $adb->raw_query_result_rowdata($res, 0);
    }
    return $data;
}

function tool_get_account_by_name($account_name, $fetch_one = false, $like = false){
    $account_name = trim($account_name);
    global $adb;
    $query = "SELECT * FROM vtiger_account a
                INNER JOIN vtiger_accountscf f ON f.accountid = a.accountid
                INNER JOIN vtiger_crmentity vc ON vc.crmid = a.accountid
              WHERE vc.deleted = 0";
    $binds = array();
    if($like){
        $query .= ' AND a.accountname LIKE ? ';
        $binds[] = '%' . $account_name . '%';
    } else {
        $query .= ' AND a.accountname = ? ';
        $binds[] = $account_name;
    }
    $res = $adb->pquery($query, $binds);
    $data = array();
    if(!$adb->num_rows($res)){
        return $data;
    }
    if(!$fetch_one){
        while($row = $adb->fetchByAssoc($res, -1, false)){
            $data[] = $row;
        }
    } else {
        $data = $adb->raw_query_result_rowdata($res, 0);
    }
    return $data;
}

function tool_get_vendor_by_name($vendor_name, $fetch_one = false, $like = false){
    $vendor_name = trim($vendor_name);
    global $adb;
    $query = "SELECT * FROM vtiger_vendor v
                INNER JOIN vtiger_vendorcf f ON f.vendorid = v.vendorid
                INNER JOIN vtiger_crmentity vc ON vc.crmid = v.vendorid
              WHERE vc.deleted = 0";
    $binds = array();
    if($like){
        $query .= ' AND v.vendorname LIKE ? ';
        $binds[] = '%' . $vendor_name . '%';
    } else {
        $query .= ' AND v.vendorname = ? ';
        $binds[] = $vendor_name;
    }
    $res = $adb->pquery($query, $binds);
    $data = array();
    if(!$adb->num_rows($res)){
        return $data;
    }
    if(!$fetch_one){
        while($row = $adb->fetchByAssoc($res, -1, false)){
            $data[] = $row;
        }
    } else {
        $data = $adb->raw_query_result_rowdata($res, 0);
    }
    return $data;
}

function tool_get_project_by_name($project_name, $fetch_one = false, $like = false){
    $project_name = trim($project_name);
    global $adb;
    $query = "SELECT * FROM vtiger_project p
                INNER JOIN vtiger_projectcf f ON f.projectid = p.projectid
                INNER JOIN vtiger_crmentity vc ON vc.crmid = p.projectid
                WHERE vc.deleted = 0 ";
    $binds = array();
    if($like){
        $query .= ' AND p.projectname LIKE ? ';
        $binds[] = '%' . $project_name . '%';
    } else {
        $query .= ' AND p.projectname = ? ';
        $binds[] = $project_name;
    }
    $res = $adb->pquery($query, $binds);
    $data = array();
    if(!$adb->num_rows($res)){
        return $data;
    }
    if(!$fetch_one){
        while($row = $adb->fetchByAssoc($res, -1, false)){
            $data[] = $row;
        }
    } else {
        $data = $adb->raw_query_result_rowdata($res, 0);
    }
    return $data;
}

function tool_get_user_by_username($username){
    global $adb;
    $res = $adb->pquery("SELECT * FROM vtiger_users WHERE user_name = ?", array($username));
    if(!$adb->num_rows($res)){
        return false;
    }
    return $adb->query_result_rowdata($res, 0);
}

function tool_get_user_by_id($id){
    global $adb;
    $res = $adb->pquery("SELECT * FROM vtiger_users WHERE id = ?", array($id));
    if(!$adb->num_rows($res)){
        return false;
    }
    return $adb->query_result_rowdata($res, 0);
}

function tool_get_account_by_id($id){
    global $adb;
    $query = "SELECT * FROM vtiger_account a
                INNER JOIN vtiger_accountscf f ON f.accountid = a.accountid
                INNER JOIN vtiger_crmentity vc ON vc.crmid = a.accountid
              WHERE vc.deleted = 0 AND a.accountid = ?";
    $binds = array($id);
    $res = $adb->pquery($query, $binds);
    $data = array();
    if(!$adb->num_rows($res)){
        return $data;
    }
    $data = $adb->raw_query_result_rowdata($res, 0);
    return $data;
}

function tool_get_picklist_values($moduleName, $fieldName){
    global $adb;
    $tabId = getTabid($moduleName);
    $res = $adb->pquery("SELECT * FROM vtiger_field WHERE tabid = ? AND fieldname = ?", array($tabId, $fieldName));
    if(!$adb->num_rows($res)){
        return false;
    }
    $table = 'vtiger_' . $fieldName;
    $value_res = $adb->pquery("SELECT * FROM {$table}");
    $values = array();
    while($row = $adb->fetchByAssoc($value_res)){
        $values[] = $row[$fieldName];
    }
    return $values;
}

function tool_get_block($blockId){
    global $adb;
    $res = $adb->pquery("SELECT * FROM vtiger_blocks WHERE blockid = ?", array($blockId));
    if(!$adb->num_rows($res)){
        return false;
    }
    return $adb->raw_query_result_rowdata($res, 0);
}

function tool_get_block_by_name($blockName, $moduleName){
    global $adb;
    $tabId = getTabid($moduleName);
    $res = $adb->pquery("SELECT * FROM vtiger_blocks WHERE tabid = ? AND blocklabel = ?", array($tabId, $blockName));
    if(!$adb->num_rows($res)){
        return false;
    }
    return $adb->raw_query_result_rowdata($res, 0);
}

function tool_get_field_block($moduleName, $fieldName){
    $field = tool_get_field_data($moduleName, $fieldName);
    if(!$field){
        return false;
    }
    return tool_get_block($field['block']);
}

/* @TODO: DELETE */

function tool_delete_entity_by_query($crmId){
    global $adb;
    $adb->pquery("UPDATE vtiger_crmentity SET deleted = 1 WHERE crmid = ?", array($crmId));
}

function tool_delete_fields($fields){
    /*$fields = array(
        'module_name' => array('field_name1', 'field_name2'),
    );*/
    foreach ($fields as $moduleName => $moduleField){
        $module = Vtiger_Module::getInstance($moduleName);
        if(!$module){
            continue;
        }
        foreach ($moduleField as $fieldName){
            $field = Vtiger_Field::getInstance($fieldName, $module);
            if($field){
                $field->delete();
                tool_echo_linebreak("Field " . $fieldName . " deleted!");
            }
        }
    }
}

function tool_delete_field($moduleName, $fieldName){
    $module = Vtiger_Module::getInstance($moduleName);
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if($field){
        $field->delete();
        tool_echo_linebreak("Field " . $fieldName . " deleted!");
    }
}

function tool_delete_picklist_values($moduleName, $field, $values){
    global $adb;
    $tabId = getTabid($moduleName);
    if(!$tabId){
        return false;
    }
    $res = $adb->pquery("SELECT * FROM vtiger_field WHERE tabid = ? AND fieldname = ?", array($tabId, $field));
    if(!$adb->num_rows($res)){
        return false;
    }
    $table = 'vtiger_' . $field;
    foreach ($values as $value){
        $adb->pquery("DELETE FROM {$table} WHERE $field = ?", array($value));
    }
    return true;
}

/* @TODO:UPDATE FUNCTION */

function tool_move_module_menu_tab($moduleName, $menuTab){
    global $adb;
    $tabId = getTabid($moduleName);
    $res = $adb->pquery("SELECT max(sequence) AS sequence FROM vtiger_app2tab WHERE appname = ?", array($menuTab));
    $sequence = 0;
    if($adb->num_rows($res)){
        $sequence = $adb->query_result($res, 0, 'sequence');
    }
    if(!$sequence){
        $sequence = 0;
    }
    $sequence = $sequence + 1;
    $adb->pquery("UPDATE vtiger_app2tab SET appname = ?, sequence = ? WHERE tabid = ?", array($menuTab, $sequence, $tabId));
}

/* @TODO: REMOVE */

function tool_remove_module_records($moduleName, $debug = false){
    $module = CRMEntity::getInstance($moduleName);
    if(!$module){
        return;
    }
    global $adb;
    $ignoreTable = array('vtiger_crmentity', 'vtiger_crmentity_user_field');
    $adb->setDebug($debug);
    foreach ($module->tab_name as $table){
        if(in_array($table, $ignoreTable)){
            continue;
        }
        $adb->pquery("DELETE FROM {$table}");
    }
    $adb->pquery("DELETE FROM vtiger_crmentity WHERE setype = ?", array($moduleName));
    $adb->setDebug(0);
}

/* @TODO: CUSTOMER PORTAL */

function tool_active_customer_portal_modules($modules){
    global $adb;
    $sequence = 0;
    $res = $adb->pquery("SELECT * FROM vtiger_customerportal_tabs ORDER BY sequence DESC LIMIT 1");
    if($adb->num_rows($res)){
        $sequence = $adb->query_result($res, 0, 'sequence');
    }
    $sequence = $sequence + 1;
    foreach($modules as $module){
        $tab_id = getTabid($module);
        if(!$tab_id){
            continue;
        }
        $check = $adb->pquery("SELECT * FROM vtiger_customerportal_tabs WHERE tabid = ?", array($tab_id));
        if(!$adb->num_rows($check)){
            $adb->pquery("INSERT INTO vtiger_customerportal_tabs (`tabid`, `visible`, `sequence`, `createrecord`, `editrecord`) VALUES (?, ?, ?, ?, ?)", array($tab_id, 1, $sequence, 0, 0));
            $sequence++;
        }
    }
    return true;
}

function tool_active_customer_portal_related_module($module, $relatedModule){
    $tabId = getTabid($module);
    if(!$tabId){
        return;
    }
    global $adb;
    $res = $adb->pquery("SELECT * FROM vtiger_customerportal_relatedmoduleinfo WHERE tabid = ?", array($tabId));
    $activatedModule = array();
    $currentActiveModule = array();
    $exists = false;
    if($adb->num_rows($res)){
        $row = $adb->raw_query_result_rowdata($res, 0);
        $current_data = $row['relatedmodules'];
        $currentActiveModule = @json_decode($current_data, 1);
        foreach($currentActiveModule as $k => $item){
            $activatedModule[$item['name']] = $k;
        }
        $exists = true;
    }
    foreach($relatedModule as $related_module){
        if(isset($activatedModule[$related_module])){
            $k = $activatedModule[$related_module];
            $currentActiveModule[$k]['value'] = 1;
        } else {
            $currentActiveModule[] = array(
                'name' => $related_module,
                'value' => 1,
            );
        }
    }
    if($exists){
        $adb->pquery("UPDATE vtiger_customerportal_relatedmoduleinfo SET relatedmodules = ? WHERE tabid = ?", array(json_encode($currentActiveModule), $tabId));
    } else {
        $adb->pquery("INSERT INTO vtiger_customerportal_relatedmoduleinfo (tabid, relatedmodules) VALUES (?, ?)", array($tabId, json_encode($currentActiveModule)));
    }
}

/* @TODO: INSTALL/UNINSTALL MODULE */

function tool_install_module($module_zip){
    if(!file_exists($module_zip)){
        return false;
    }
    try {
        $package = new Vtiger_Package();
        $package->import($module_zip, true);
    } catch (Exception $e){
        return false;
    }
    return true;
}

function tool_uninstall_module($moduleName){
    $module = Vtiger_Module::getInstance($moduleName);
    if ($module){
        try{
            $module->delete();
            return true;
        }catch (Exception $e){
            return false;
        }
    }
    return true;
}

function tool_install_module_manifest($manifest, $vtiger_version = 6){
    /*$manifest = array(
        'type' => '',
        'name' => '',
        'label' => '',
        'parent' => '',
        'version' => '',
        'tables' => array(
            array(
                'name' => '',
                'sql' => '',
            ),
        ),
        'blocks' => array(
            array(
                'label' => '',
                'fields' => array(
                    array(
                        'fieldname' => '',
                        'uitype' => '',
                        'columnname' => '', // blank if generate columns
                        'tablename' => '',
                        'generatedtype' => '',
                        'fieldlabel' => '',
                        'readonly' => '',
                        'presence' => '',
                        'defaultvalue' => '',
                        'sequence' => '',
                        'maximumlength' => '',
                        'typeofdata' => '',
                        'quickcreate' => '',
                        'quickcreatesequence' => '',
                        'displaytype' => '',
                        'info_type' => '',
                        'helpinfo' => '',
                        'masseditable' => '',
                        'summaryfield' => '',
                        'entityidentifier' => array(
                            'entityidfield' => '',
                            'entityidcolumn' => '',
                        ),
                        'relatedmodules' => array(),
                        'picklistvalues' => array(),
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

                ),
            ),
        ),
        'sharingaccess' => 'public_readwritedelete',
        'events' => array(
            array(
                'eventname' => '',
                'classname' => '',
                'filename' => '',
            )
        ),
        'actions' => array(
            array(
                'name' => '',
                'status' => '',
            ),
        ),
        'relatedlists' => array(
            array(
                'relatedmodule' => '',
                'label' => '',
                'actions' => array(),
                'function' => '',
            ),
        ),
        'customlinks' => array(
            array(
                'linktype' => '',
                'linklabel' => '',
                'linkurl' => '',
                'linkicon' => '',
                'sequence' => '',
                'handler_path' => '',
                'handler_class' => '',
                'handler' => '',
            ),
        ),
        'crons' => array(
            array(
                'name' => '',
                'handler' => '',
                'frequency' => '',
                'status' => '',
                'sequence' => '',
                'description' => '',
            ),
        ),
    );*/
    $moduleName = $manifest['name'];
    global $adb;
    $check = $adb->pquery("SELECT * FROM vtiger_tab WHERE `name` = ?", array($moduleName));
    if($adb->num_rows($check)){
        return false;
    }
    $cache = array();

    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $manifest['name'];
    $moduleInstance->label= $manifest['label'];
    $moduleInstance->parent = $manifest['parent'];
    $moduleInstance->isentitytype = !$manifest['type'];
    $moduleInstance->version = $manifest['version'];
    $moduleInstance->minversion = false;
    $moduleInstance->maxversion = false;
    $moduleInstance->save();

    if(!empty($manifest['parent'])) {
        $menuInstance = Vtiger_Menu::getInstance($manifest['parent']);
        $menuInstance->addModule($moduleInstance);
    }

    if($manifest['tables']){
        $tables = $manifest['tables'];
        foreach($tables as $table){
            $table_name = $table['name'];
            $table_sql  = $table['sql'];
            if(Vtiger_Utils::IsCreateSql($table_sql)) {
                if(!Vtiger_Utils::checkTable($table_name)) {
                    Vtiger_Utils::ExecuteQuery($table_sql);
                }
            } else {
                if(Vtiger_Utils::IsDestructiveSql($table_sql)) {

                } else {
                    Vtiger_Utils::ExecuteQuery($table_sql);
                }
            }
        }
    } else {
        $moduleInstance->initTables();
    }

    $blocks = $manifest['blocks'];
    foreach($blocks as $block){
        $block_label = $block['label'];
        $block_fields = $block['fields'];

        $blockInstance = new Vtiger_Block();
        $blockInstance->label = $block_label;
        $moduleInstance->addBlock($blockInstance);

        foreach($block_fields as $block_field){
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name         = $block_field['fieldname'];
            $fieldInstance->label        = $block_field['fieldlabel'];
            $fieldInstance->table        = $block_field['tablename'];
            $fieldInstance->column       = $block_field['columnname'];
            $fieldInstance->uitype       = $block_field['uitype'];
            $fieldInstance->generatedtype= $block_field['generatedtype'];
            $fieldInstance->readonly     = $block_field['readonly'];
            $fieldInstance->presence     = $block_field['presence'];
            $fieldInstance->defaultvalue = $block_field['defaultvalue'];
            $fieldInstance->maximumlength= $block_field['maximumlength'];
            $fieldInstance->sequence     = $block_field['sequence'];
            $fieldInstance->quickcreate  = $block_field['quickcreate'];
            $fieldInstance->quicksequence= $block_field['quickcreatesequence'];
            $fieldInstance->typeofdata   = $block_field['typeofdata'];
            $fieldInstance->displaytype  = $block_field['displaytype'];
            $fieldInstance->info_type    = $block_field['info_type'];

            if(!empty($block_field['helpinfo'])){
                $fieldInstance->helpinfo = $block_field['helpinfo'];
            }

            if(isset($block_field['masseditable'])){
                $fieldInstance->masseditable = $block_field['masseditable'];
            }

            if(isset($block_field['columntype']) && !empty($block_field['columntype'])){
                $fieldInstance->columntype = $block_field['columntype'];
            }

            $blockInstance->addField($fieldInstance);

            // Set the field as entity identifier if marked.
            if(!empty($block_field['entityidentifier'])) {
                $moduleInstance->entityidfield = $block_field['entityidentifier']['entityidfield'];
                $moduleInstance->entityidcolumn= $block_field['entityidentifier']['entityidcolumn'];
                $moduleInstance->setEntityIdentifier($fieldInstance);
            }

            // Check picklist values associated with field if any.
            if(!empty($block_field['picklistvalues'])) {
                $fieldInstance->setPicklistValues($block_field['picklistvalues']);
            }

            // Check related modules associated with this field
            if(!empty($block_field['relatedmodules'])) {
                $fieldInstance->setRelatedModules($block_field['relatedmodules']);
            }

            // Set summary field if marked in xml
            if(!empty($block_field['summaryfield'])) {
                $fieldInstance->setSummaryField($block_field['summaryfield']);
            }
            $cache['fields'][$block_field['fieldname']] = $fieldInstance;
        }
    }

    if(isset($manifest['customviews']) && $manifest['customviews']){
        try {
            foreach($manifest['customviews'] as $customview){
                $filterInstance = new Vtiger_Filter();
                $filterInstance->name = $customview['viewname'];
                $filterInstance->isdefault = $customview['setdefault'];
                $filterInstance->inmetrics = $customview['setmetrics'];
                $moduleInstance->addFilter($filterInstance);

                foreach($customview['fields'] as $key => $customview_field){
                    $fieldInstance = $cache['fields'][$customview_field];
                    $filterInstance->addField($fieldInstance, $key);
                }
            }
        } catch(Exception $e) {

        }
    }

    if(isset($manifest['events']) && $manifest['events']){
        try {
            foreach($manifest['events'] as $event){
                $event_condition = '';
                $event_dependent = '[]';
                if(isset($event['condition'])) $event_condition = $event['condition'];
                if(isset($event['condition'])) $event_dependent =$event['dependent'];
                Vtiger_Event::register($moduleInstance, $event['eventname'], $event['classname'], $event['filename'], $event_condition, $event_dependent);
            }
        } catch(Exception $e) {

        }
    }

    if(isset($manifest['actions']) && $manifest['actions']){
        try {
            foreach($manifest['actions'] as $action){
                if($action['status'] == 'enabled'){
                    $moduleInstance->enableTools($action['name']);
                } else {
                    $moduleInstance->disableTools($action['name']);
                }
            }
        } catch(Exception $e) {

        }
    }

    if(isset($manifest['relatedlists']) && $manifest['relatedlists']){
        try {
            foreach($manifest['relatedlists'] as $relatedlist){
                $relModuleInstance = Vtiger_Module::getInstance($relatedlist['relatedmodule']);
                if(!$relModuleInstance) {
                    continue;
                }
                $moduleInstance->setRelatedList($relModuleInstance, $relatedlist['label'], $relatedlist['actions'], $relatedlist['function']);
            }
        } catch(Exception $e) {

        }
    }

    if(isset($manifest['customlinks']) && $manifest['customlinks']){
        try {
            foreach($manifest['customlinks'] as $customlink){
                $handlerInfo = null;
                if(!empty($customlink['handler_path'])) {
                    $handlerInfo = array(
                        'path' => $customlink['handler_path'],
                        'class' => $customlink['handler_class'],
                        'method' => $customlink['handler']
                    );
                }
                $moduleInstance->addLink($customlink['linktype'], $customlink['linklabel'], $customlink['linkurl'], $customlink['linkicon'], $customlink['sequence'], $handlerInfo);
            }
        } catch(Exception $e) {

        }
    }

    if(isset($manifest['crons']) && $manifest['crons']){
        try {
            foreach($manifest['crons'] as $cron){
                if(empty($cron['status'])){
                    $cron['status'] = Vtiger_Cron::$STATUS_DISABLED;
                } else {
                    $cron['status'] = Vtiger_Cron::$STATUS_ENABLED;
                }
                if((empty($cron['sequence']))){
                    $cron['sequence'] = Vtiger_Cron::nextSequence();
                }
                Vtiger_Cron::register($cron['name'], $cron['handler'], $cron['frequency'], $moduleName, $cron['status'], $cron['sequence'], $cron['description']);
            }
        } catch(Exception $e) {

        }
    }

    if($vtiger_version == 7){
        $parentTabs = explode(',', $manifest['parent']);
        foreach($parentTabs as $parentTabName) {
            Settings_MenuEditor_Module_Model::addModuleToApp($moduleName, $parentTabName);
        }
    }

    if(file_exists("modules/{$manifest['name']}/{$manifest['name']}.php" )){
        try {
            Vtiger_Module::fireEvent($moduleName, Vtiger_Module::EVENT_MODULE_POSTINSTALL);
        } catch(Exception $e) {

        }
    }
    try {
        $moduleInstance->initWebservice();
    } catch(Exception $e) {

    }

    $sql = "select * from `vtiger_ws_entity` where `name`='$moduleName'";
    $res = $adb->pquery($sql, array());
    if ($adb->num_rows($res) <= 0) {
        $sql = "insert into vtiger_ws_entity SET `name`='$moduleName',`handler_path`='include/Webservices/VtigerModuleOperation.php',`handler_class`='VtigerModuleOperation',`ismodule`='1'";
        $adb->pquery($sql, array());
    }

    if(isset($manifest['sharingaccess']) && $manifest['sharingaccess']){
        try {
            $moduleInstance->setDefaultSharing($manifest['sharingaccess']);
        } catch(Exception $e) {

        }
    }

    return true;
}

function tool_export_module($moduleName){
    $module = Vtiger_Module::getInstance($moduleName);
    if($module){
        $package = new Vtiger_PackageExport();
        try {
            $fileName = $moduleName . '.zip';
            $filePath = 'test/' . $fileName;
            $package->export(
                $module,
                $filePath,
                $fileName,
                false
            );
            return $filePath;
        } catch (Exception $e) {

        }
    }
    return false;
}

function tool_export_module_folder($moduleName, $to_install_package = true){
    global $root_directory;
    $moduleInstance = Vtiger_Module::getInstance($moduleName);
    if(!$moduleInstance){
        return false;
    }
    $export_folder = $root_directory. 'test/' . $moduleName . '_' . time() . "/";
    @mkdir($export_folder, 0777, true);

    $module = $moduleName;
    $package = new Vtiger_PackageExport();
    $package->__initExport($moduleName, $moduleInstance);
    $package->export_Module($moduleInstance);
    $package->__finishExport();
    $manifest = $package->__getManifestFilePath();
    tool_copy_file($root_directory . $manifest, $export_folder . '/manifest.xml');
    tool_copy_directory($root_directory . "modules/$module",
        $export_folder . "modules/$module");
    if(is_dir($root_directory . "modules/Settings/$module")){
        if($to_install_package){
            tool_copy_directory($root_directory . "modules/Settings/$module",
                $export_folder . "settings");
        } else {
            tool_copy_directory($root_directory . "modules/Settings/$module",
                $export_folder . "modules/Settings/$module");
        }
    }
    if(is_dir($root_directory . "cron/modules/$module")){
        if($to_install_package){
            tool_copy_directory($root_directory . "cron/modules/$module",
                $export_folder . "cron");
        } else {
            tool_copy_directory($root_directory . "cron/modules/$module",
                $export_folder . "cron/modules/$module");
        }
    }
    if(is_dir($root_directory . "layouts/vlayout/modules/$module")){
        if($to_install_package){
            tool_copy_directory($root_directory . "layouts/vlayout/modules/$module",
                $export_folder . "templates");
        } else {
            tool_copy_directory($root_directory . "layouts/vlayout/modules/$module",
                $export_folder . "layouts/vlayout/modules/$module");
        }
    }
    if(is_dir($root_directory . "layouts/vlayout/modules/Settings/$module")){
        if($to_install_package){
            tool_copy_directory($root_directory . "layouts/vlayout/modules/Settings/$module",
                $export_folder . "settings/templates");
        } else {
            tool_copy_directory($root_directory . "layouts/vlayout/modules/Settings/$module",
                $export_folder . "layouts/vlayout/modules/Settings/$module");
        }
    }
    if(is_dir($root_directory . "layouts/vlayout/skins/images/$module")){
        if($to_install_package){
            tool_copy_directory($root_directory . "layouts/vlayout/skins/images/$module",
                $export_folder . "images");
        } else {
            tool_copy_directory($root_directory . "layouts/vlayout/skins/images/$module",
                $export_folder . "layouts/vlayout/skins/images/$module");
        }
    }
    if(is_dir($root_directory . "layouts/v7/modules/$module")){
        tool_copy_directory($root_directory . "layouts/v7/modules/$module",
            $export_folder . "layouts/v7/modules/$module");
    }
    if(is_dir($root_directory . "layouts/v7/modules/Settings/$module")){
        tool_copy_directory($root_directory . "layouts/v7/modules/Settings/$module",
            $export_folder . "layouts/v7/modules/Settings/$module");
    }

    //Copy language files
    $languageFolder = "languages";
    if($dir = @opendir($languageFolder)) {
        while (($langName = readdir($dir)) !== false) {
            if ($langName != ".." && $langName != "." && is_dir($languageFolder."/".$langName)) {
                $langDir = @opendir($languageFolder. '/'.$langName);
                while(($moduleLangFile = readdir($langDir))  !== false) {
                    $langFilePath = $languageFolder.'/'.$langName.'/'.$moduleLangFile;
                    if(is_file($langFilePath) && $moduleLangFile === $module.'.php') {
                        tool_copy_file($root_directory . $langFilePath, $export_folder . $langFilePath);
                    } else if(is_dir($langFilePath) && $moduleLangFile == 'Settings') {
                        $settingsLangDir = @opendir($langFilePath);
                        while($settingLangFileName = readdir($settingsLangDir)) {
                            $settingsLangFilePath = $languageFolder.'/'.$langName.'/'.$moduleLangFile.'/'.$settingLangFileName;
                            if(is_file($settingsLangFilePath) && $settingLangFileName === $module.'.php') {
                                tool_copy_file($root_directory .  $settingsLangFilePath,
                                    $export_folder . $settingsLangFilePath);
                            }
                        }
                        closedir($settingsLangDir);
                    }
                }
                closedir($langDir);
            }
        }
        closedir($dir);
    }
    return $export_folder;
}

/* @TODO: EMAIL */

function tool_send_mail($module, $to_email, $from_name, $from_email, $subject, $contents, $cc='', $bcc='', $attachments=array(), $emailid='', $logo='', $useGivenFromEmailAddress=false, $useSignature = 'Yes', $inReplyToMessageId='')
{
    //clone from send_mail function and modify the attachments
    include_once 'modules/Emails/mail.php';
    global $adb, $log;
    global $root_directory;
    global $HELPDESK_SUPPORT_EMAIL_ID, $HELPDESK_SUPPORT_NAME;

    //Get the email id of assigned_to user -- pass the value and name, name must be "user_name" or "id"(field names of vtiger_users vtiger_table)
    //$to_email = getUserEmailId('id',$assigned_user_id);

    //if module is HelpDesk then from_email will come based on support email id
    if($from_email == '') {
        //if from email is not defined, then use the useremailid as the from address
        $from_email = getUserEmailId('user_name',$from_name);
    }

    //if the newly defined from email field is set, then use this email address as the from address
    //and use the username as the reply-to address
    $cachedFromEmail = VTCacheUtils::getOutgoingMailFromEmailAddress();
    if($cachedFromEmail === null) {
        $query = "select from_email_field from vtiger_systems where server_type=?";
        $params = array('email');
        $result = $adb->pquery($query,$params);
        $from_email_field = $adb->query_result($result,0,'from_email_field');
        VTCacheUtils::setOutgoingMailFromEmailAddress($from_email_field);
    }

    if(isUserInitiated()) {
        $replyToEmail = $from_email;
    } else {
        $replyToEmail = $from_email_field;
    }
    if(isset($from_email_field) && $from_email_field!='' && !$useGivenFromEmailAddress){
        //setting from _email to the defined email address in the outgoing server configuration
        $from_email = $from_email_field;
    }

    if($module != "Calendar"){
        if($useSignature == 'Yes'){
            $contents = addSignature($contents,$from_name,$from_email);
        }
    }
    $mail = new PHPMailer();

    setMailerProperties($mail,$subject,$contents,$from_email,$from_name,trim($to_email,","), '',$emailid,$module,$logo);
    setCCAddress($mail,'cc',$cc);
    setCCAddress($mail,'bcc',$bcc);
    if(!empty($replyToEmail)) {
        $mail->AddReplyTo($replyToEmail);
    }

    if (!empty($inReplyToMessageId)) {
        $mail->AddCustomHeader("In-Reply-To", $inReplyToMessageId);
    }
    // vtmailscanner customization: If Support Reply to is defined use it.
    global $HELPDESK_SUPPORT_EMAIL_REPLY_ID;
    if($HELPDESK_SUPPORT_EMAIL_REPLY_ID && $HELPDESK_SUPPORT_EMAIL_ID != $HELPDESK_SUPPORT_EMAIL_REPLY_ID) {
        $mail->AddReplyTo($HELPDESK_SUPPORT_EMAIL_REPLY_ID);
    }
    // END

    if($attachments){
        foreach($attachments as $attachment){
            $mail->AddAttachment($attachment);
        }
    }

    // Fix: Return immediately if Outgoing server not configured
    if(empty($mail->Host)) {
        return 0;
    }
    // END

    $mail_status = MailSend($mail);

    if($mail_status != 1)
    {
        $mail_error = getMailError($mail,$mail_status, $to_email);
    }
    else
    {
        $mail_error = $mail_status;
    }

    return $mail_error;
}

/* @TODO: SCRIPT */

function tool_script_run_check($lock_file)
{
    # If lock file exists, check if stale.  If exists and is not stale, return TRUE
    # Else, create lock file and return FALSE.

    if( file_exists( $lock_file ) )
    {
        # check if it's stale
        $lockingPID = trim( file_get_contents( $lock_file ) );

        # Get all active PIDs.
        $pids = explode( "\n", trim( `ps -e | awk '{print $1}'` ) );

        # If PID is still active, return true
        if( in_array( $lockingPID, $pids ) )  return true;

        # Lock-file is stale, so kill it.  Then move on to re-creating it.
        echo "Removing stale lock file.\n";
        @unlink( $lock_file );
    }

    file_put_contents($lock_file, getmypid() . "\n" );
    return false;
}

function tool_script_run_remove($lock_file){
    @unlink( $lock_file );
}

/* @TODO: DUMP */

function tool_dump_field_to_create($moduleName, $fieldName){
    global $adb;
    $tabId = getTabid($moduleName);
    $res = $adb->pquery("SELECT * FROM vtiger_field WHERE tabid = ? AND fieldname = ?", array($tabId, $fieldName));
    if(!$adb->num_rows($res)){
        return false;
    }
    $row = $adb->raw_query_result_rowdata($res, 0);
    $field = array(
        'label' => $row['fieldlabel'],
        'table' => $row['tablename'],
        'helpinfo' => $row['helpinfo'],
        'summaryfield' => $row['summaryfield'],
        'masseditable' => $row['masseditable'],
        'uitype' => $row['uitype'],
        'typeofdata' => $row['typeofdata'],
        'displaytype' => $row['displaytype'],
        'generatedtype' => $row['generatedtype'],
        'readonly' => $row['readonly'],
        'presence' => $row['presence'],
        'defaultvalue' => $row['defaultvalue'],
        'maximumlength' => $row['maximumlength'],
        'sequence' => false,
        'quickcreate' => $row['quickcreate'],
        'info_type' => $row['info_type'],
        'headerfield' => $row['headerfield'],
    );
    if($row['uitype'] == 15 || $row['uitype'] == 16 || $row['uitype'] == 33){
        $field['picklistvalues'] = tool_get_picklist_values($moduleName, $fieldName);
    }
    return $field;
}

function tool_compare_dump_fields($dump_table){
    // dump table create from query: SELECT f.*, t.`name` AS module_name, b.blocklabel FROM vtiger_field f INNER JOIN vtiger_tab t ON t.tabid = f.tabid INNER JOIN vtiger_blocks b ON b.blockid = f.block
    global $adb;
    $res = $adb->pquery("SELECT * FROM `{$dump_table}` ORDER BY tabid ASC");
    $missField = array();
    while ($row = $adb->fetchByAssoc($res, -1, false)){
        $fieldName = $row['fieldname'];
        $moduleName = $row['module_name'];
        $blockName = $row['blocklabel'];
        $exists_res = $adb->pquery("SELECT * FROM vtiger_field f INNER JOIN vtiger_tab t ON t.tabid = f.tabid WHERE f.fieldname = ? AND t.`name` = ?", array($fieldName, $moduleName));
        if(!$adb->num_rows($exists_res)){
            if(!isset($missField[$moduleName])){
                $missField[$moduleName] = array();
            }
            if(!isset($missField[$moduleName][$blockName])){
                $missField[$moduleName][$blockName] = array();
            }
            $missField[$moduleName][$blockName][$fieldName] = $row['fieldlabel'];
        }
    }
    return $missField;
}

function tool_dump_create_module_fields($fieldDump){
    $fieldSchema = array();
    foreach ($fieldDump as $moduleName => $blockField){
        if(!isset($fieldSchema[$moduleName])){
            $fieldSchema[$moduleName] = array();
        }
        foreach ($blockField as $blockName => $fields){
            if(!isset($fieldSchema[$moduleName][$blockName])){
                $fieldSchema[$moduleName][$blockName] = array();
            }
            foreach ($fields as $fieldName => $fieldLabel){
                $field_schema = tool_dump_field_to_create($moduleName, $fieldName);
                $fieldSchema[$moduleName][$blockName][$fieldName] = $field_schema;
            }
        }
    }
    return $fieldSchema;
}

/* @TODO: UTILS */

function tool_put_log($file, $message){
    if(!$file){
        return;
    }
    @file_put_contents($file, $message . "\n", FILE_APPEND);
}

function tool_put_log_csv_format($file, $array){
    $message = implode(',' , $array);
    tool_put_log($file, $message);
}

function tool_put_log_date($location, $message, $type = 'info'){
    $log_file = $location . '/' . $type . '.' . date('Y.m.d') . '.log';
    if(!is_dir($location)){
        @mkdir($location, 0777, true);
    }
    tool_put_log($log_file, $message);
}

function tool_convert_date_format($date, $from_format, $to_format, $return = true){
    if(!$date){
        if(!$return){
            return false;
        }
        return date($to_format);
    }
    $obj = date_create_from_format($from_format, $date);
    if(!$obj){
        if(!$return){
            return false;
        }
        return date($to_format);
    }
    if(array_sum($obj::getLastErrors())){
        if(!$return){
            return false;
        }
        return date($to_format);
    }
    $convert = $obj->format($to_format);
    if(!$convert){
        $convert = date($to_format);
    }
    return $convert;
}

function tool_build_filename($filename){
    $basename = basename($filename);
    $name = preg_replace('/[^A-Za-z0-9\.\-]/', '_', $basename);
    return $name;
}

function tool_build_update_query($table, $data, $where = array(), $combine = 'AND'){
    $set_condition = $where_condition = $binds = array();
    foreach($data as $k => $v){
        $set_condition[] = " `{$k}` = ? ";
        $binds[] = $v;
    }
    foreach($where as $k => $v){
        $where_condition[] = " `{$k}` = ? ";
        $binds[] = $v;
    }
    $query = "UPDATE `{$table}` SET ";
    $query .= implode(',', $set_condition);
    $query .= ' ';
    if($where_condition){
        $combine = ' ' . $combine . ' ';
        $query .= ' WHERE ';
        $query .= implode($combine, $where_condition);
    }
    return array(
        'query' => $query,
        'binds' => $binds,
    );
}

function tool_echo_linebreak($text){
    $is_cli = php_sapi_name() == "cli";
    if($is_cli){
        $break = "\r\n";
    } else {
        $break = '<br />';
    }
    echo $text . $break;
}

function tool_map_header_data($header, $data){
    $result = array();
    foreach ($header as $k => $v){
        $v1 = $data[$k];
        $result[$v] = $v1;
    }
    return $result;
}

function tool_copy_directory($from_path, $to_path){
    if(!is_dir($from_path)){
        return false;
    }
    $from_path = rtrim($from_path, "/\\");
    $to_path = rtrim($to_path, "/\\");
    if(!is_dir($to_path)){
        @mkdir($to_path, 0777, true);
    }
    $dir = opendir($from_path);
    while($item = readdir($dir)) {
        if(($item == '.') || ($item == '..')){
            continue;
        }
        $from_path_item = $from_path . '/' . $item;
        $to_path_item = $to_path . '/' . $item;
        if (is_dir($from_path_item)){
            tool_copy_directory($from_path_item, $to_path_item);
        } else {
            tool_copy_file($from_path_item, $to_path_item);
        }
    }
    closedir($dir);
    return true;
}

function tool_copy_file($from_path, $to_path){
    if(!file_exists($from_path)){
        return false;
    }
    $parent = dirname($to_path);
    if(!is_dir($parent)){
        @mkdir($parent, 0777, true);
    }
    return copy($from_path, $to_path);
}

function tool_print_array_as_php_variable($array, $indent = 0){
    $space = str_repeat("    ", $indent);
    $spaceNext = str_repeat("    ", $indent + 1);

    $out = "array(\n";
    foreach ($array as $key => $value) {
        $out .= $spaceNext;
        $out .= is_int($key) ? $key . " => " : "'" . addslashes($key) . "' => ";
        if (is_array($value)) {
            if (empty($value)) {
                $out .= "array()";
            } else {
                $out .= array_to_php_array($value, $indent + 1);
            }
        } elseif (is_string($value)) {
            $out .= "'" . addslashes($value) . "'";
        } elseif (is_bool($value)) {
            $out .= $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            $out .= 'null';
        } else {
            $out .= $value;
        }
        $out .= ",\n";
    }
    $out .= $space . ")";
    return $out;
}
