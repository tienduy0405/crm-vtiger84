<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/*
 *	Modified by: Hieu Nguyen
 *	Date: 2018-07-03
 */
 
$languageStrings = array(
	//Basic Field Names
	'LBL_NEW' => 'Mới',
	'LBL_WORKFLOW' => 'Workflow',
	'LBL_CREATING_WORKFLOW' => 'Đang tạo WorkFlow',
	'LBL_EDITING_WORKFLOW' => 'Sửa Workflow',
	'LBL_ADD_RECORD' => 'Thêm Workflow',

	//Edit view
	'LBL_STEP_1' => 'Bước 1',
	'LBL_ENTER_BASIC_DETAILS_OF_THE_WORKFLOW' => 'Nhập thông tin cơ bản của Workflow',
	'LBL_SPECIFY_WHEN_TO_EXECUTE' => 'Xác đinh thời điểm chạy cho Workflow này',
	'ON_FIRST_SAVE' => 'Khi thêm mới dữ liệu',
	'ONCE' => 'Lần đầu tiên thỏa mãn điều kiện',
	'ON_EVERY_SAVE' => 'Khi lưu dữ liệu',
	'ON_MODIFY' => 'Khi sửa dữ liệu',
	'ON_SCHEDULE' => 'Theo lịch',
	'MANUAL' => 'Hệ thống',
	'SCHEDULE_WORKFLOW' => 'Lập lịch cho Workflow',
	'ADD_CONDITIONS' => 'Thêm điều kiện',
	'ADD_TASKS' => 'Thêm nhiệm vụ',

	//Step2 edit view
	'LBL_EXPRESSION' => 'Biểu thức',
	'LBL_FIELD_NAME' => 'Trường dữ liệu',
	'LBL_SET_VALUE' => 'đặt giá trị',
	'LBL_USE_FIELD' => 'Sử dụng trường',
	'LBL_USE_FUNCTION' => 'Sử dụng chức năng',
	'LBL_RAW_TEXT' => 'văn bản',
	'LBL_ENABLE_TO_CREATE_FILTERS' => 'Bật để tạo bộ lọc',
	'LBL_CREATED_IN_OLD_LOOK_CANNOT_BE_EDITED' => 'Bạn không thể sửa điều kiện của các workflow hệ thống. Bạn có thể tạo lại hoặc sử dụng điều kiện đã tồn tại và không được phép thay đổi.',
	'LBL_USE_EXISTING_CONDITIONS' => 'Sử dụng điều kiện đã có',
	'LBL_RECREATE_CONDITIONS' => 'Tạo lại điều kiện',
	'LBL_SAVE_AND_CONTINUE' => 'Lưu và tiếp tục',

	//Step3 edit view
	'LBL_ACTIVE' => 'Kích hoạt',
	'LBL_TASK_TYPE' => 'Loại tác vụ',
	'LBL_TASK_TITLE' => 'Tên tác vụ',
	'LBL_ADD_TASKS_FOR_WORKFLOW' => 'Thêm tác vụ cho Workflow',
	'LBL_EXECUTE_TASK' => 'Thực hiện tác vụ',
	'LBL_SELECT_OPTIONS' => 'Chọn giá trị',
	'LBL_ADD_FIELD' => 'Thêm trường dữ liệu',
	'LBL_ADD_TIME' => 'Thêm thời gian',
	'LBL_TITLE' => 'Tiêu đề',
	'LBL_PRIORITY' => 'Mức độ ưu tiên',
	'LBL_ASSIGNED_TO' => 'Giao cho',
	'LBL_TIME' => 'Thời gian',
	'LBL_DUE_DATE' => 'Ngày kết thúc',
	'LBL_THE_SAME_VALUE_IS_USED_FOR_START_DATE' => 'Ngày bắt đầu không được giống nhau',
	'LBL_EVENT_NAME' => 'Tên hoạt động',
	'LBL_TYPE' => 'Loại',
	'LBL_METHOD_NAME' => 'Tên phương thức',
	'LBL_RECEPIENTS' => 'Người nhận',
	'LBL_ADD_FIELDS' => 'Chèn biến',
	'LBL_SMS_TEXT' => 'Nội dung SMS',
	'LBL_SET_FIELD_VALUES' => 'Đặt giá trị cho trường',
	'LBL_IN_ACTIVE' => 'Trong quá trình hoạt động',
	'LBL_SEND_NOTIFICATION' => 'Gởi thông báo',
	'LBL_START_TIME' => 'Thời gian bắt đầu',
	'LBL_START_DATE' => 'Ngày bắt dầu',
	'LBL_END_TIME' => 'Thời gian kết thúc',
	'LBL_END_DATE' => 'Ngày kết thúc',
	'LBL_ENABLE_REPEAT' => 'Kích hoạt chế độ lặp lại',
	'LBL_NO_METHOD_IS_AVAILABLE_FOR_THIS_MODULE' => 'Không có phương thức nào sẵn sàng cho module này',
	
	'LBL_NO_TASKS_ADDED' => 'Không có tác vụ nào',
	'LBL_CANNOT_DELETE_DEFAULT_WORKFLOW' => 'Bạn không thể xóa Workflow mặc định',
	'LBL_MODULES_TO_CREATE_RECORD' => 'Tạo một bản ghi trong',
	'LBL_EXAMPLE_EXPRESSION' => 'Biểu thức',
	'LBL_EXAMPLE_RAWTEXT' => 'Văn bản',
	'LBL_VTIGER' => 'OnlineCRM',
	'LBL_EXAMPLE_FIELD_NAME' => 'Trường dữ liệu',
	'LBL_NOTIFY_OWNER' => 'thông báo cho người quản lý',
	'LBL_ANNUAL_REVENUE' => 'doanh thu hàng năm',
	'LBL_EXPRESSION_EXAMPLE2' => "if mailingcountry == 'Vietnam' then concat(firstname,' ',lastname) else concat(lastname,' ',firstname) end",
	'LBL_FROM' => 'Từ',
	'LBL_RUN_WORKFLOW' => 'Chạy Workflow',
	'LBL_AT_TIME' => 'Vào thời gian',
	'LBL_HOURLY' => 'Hàng giờ',
	'Optional' => 'Không bắt buộc',
	'ENTER_FROM_EMAIL_ADDRESS'=> 'Hãy nhập địa chỉ Email',
	'LBL_ADD_TASK' => 'Thêm nhiệm vụ',
    'Portal Pdf Url' =>'Đường dẫn File PDF trên Cổng thông tin CSKH',

	'LBL_DAILY' => 'Hàng ngày',
	'LBL_WEEKLY' => 'Hàng tuần',
	'LBL_ON_THESE_DAYS' => 'Vào những ngày sau',
	'LBL_MONTHLY_BY_DATE' => 'Hàng tháng theo ngày',
	'LBL_MONTHLY_BY_WEEKDAY' => 'Hàng tháng theo tuần',
	'LBL_YEARLY' => 'Hàng năm',
	'LBL_SPECIFIC_DATE' => 'Vào ngày chỉ định',
	'LBL_CHOOSE_DATE' => 'Chọn ngày',
	'LBL_SELECT_MONTH_AND_DAY' => 'Chọn ngày tháng',
	'LBL_SELECTED_DATES' => 'Đã chọn ngày',
	'LBL_EXCEEDING_MAXIMUM_LIMIT' => 'Vượt quá giới hạn cho phép',
	'LBL_NEXT_TRIGGER_TIME' => 'Lần chạy tiếp theo',
    'LBL_ADD_TEMPLATE' => 'Thêm mẫu',
    'LBL_LINEITEM_BLOCK_GROUP' => 'Thông tin hàng hóa cho thuế theo nhóm',
    'LBL_LINEITEM_BLOCK_INDIVIDUAL' => 'Thông tin hàng hóa cho thuế theo mặt hàng',
	'LBL_MESSAGE' => 'Tin nhắn',
    'LBL_ADD_PDF' => 'Thêm File PDF',
	
	//Translation for module
	'Calendar' => 'Công việc',
	'Send Mail' => 'Gởi Email',
	'Invoke Custom Function' => 'Gọi hàm tự định nghĩa',
	'Create Todo' => 'Tạo mới tác vụ',
	'Create Event' => 'Thêm hoạt động',
	'Update Fields' => 'Cập nhật trường dữ liệu',
	'Create Entity' => 'Tạo mới bản ghi',
	'SMS Task' => 'Gởi tin nhắn SMS',
	'Mobile Push Notification' => 'Thông báo đẩy',
    
    // v7 translations
    'LBL_WORKFLOW_NAME' => 'Tên Workflow',
    'LBL_TARGET_MODULE' => 'Áp dụng cho Module',
    'LBL_WORKFLOW_TRIGGER' => 'Kích hoạt Workflow',
    'LBL_TRIGGER_WORKFLOW_ON' => 'Kích hoạt Workflow trên',
    'LBL_RECORD_CREATION' => 'Tạo bản ghi',
    'LBL_RECORD_UPDATE' => 'Cập nhật bản ghi',
    'LBL_TIME_INTERVAL' => 'Khoảng thời gian',
    'LBL_RECURRENCE' => 'Lặp lại',
    'LBL_FIRST_TIME_CONDITION_MET' => 'Chỉ lần đầu thỏa mãn điều kiện (chạy duy nhất lần)',
    'LBL_EVERY_TIME_CONDITION_MET' => 'Mỗi khi thỏa mãn điều kiện (lặp lại liên tục)',
    'LBL_WORKFLOW_CONDITION' => 'Điều kiện của Workflow',
    'LBL_WORKFLOW_ACTIONS' => 'Nhiệm vụ của Workflow',
    'LBL_DELAY_ACTION' => 'Hành động chậm',
    'LBL_FREQUENCY' => 'Tần suất',
    'LBL_SELECT_FIELDS' => 'Chọn trường',
    'LBL_INCLUDES_CREATION' => 'Bao gồm thêm mới',
    'LBL_ACTION_FOR_WORKFLOW' => 'Nhiệm vụ của Workflow',
    'LBL_WORKFLOW_SEARCH' => 'Tìm theo tên',
	'LBL_ACTION_TYPE' => 'Loại hành động',
	'LBL_VTEmailTask' => 'Email',
    'LBL_VTEntityMethodTask' => 'Chức năng do người dùng định nghĩa',
    'LBL_VTCreateTodoTask' => 'Công việc',
    'LBL_VTCreateEventTask' => 'Hoạt động',
    'LBL_VTUpdateFieldsTask' => 'Cập nhật trường dữ liệu',
    'LBL_VTSMSTask' => 'SMS', 
    'LBL_VTPushNotificationTask' => 'Thông báo di động',
	'LBL_VTCreateEntityTask' => 'Tạo bản ghi',
	
	// Added by Hieu Nguyen on 2018-07-03
	'Workflow Name' => 'Tên Workflow',
	'Trigger' => 'Điều kiện kích hoạt',
	'Conditions' => 'Điều kiện Logic',
	'InActive' => 'Không hoạt động'
);

$jsLanguageStrings = array(
	'JS_STATUS_CHANGED_SUCCESSFULLY' => 'Đã thay đổi tình trạng',
	'JS_TASK_DELETED_SUCCESSFULLY' => 'Đã xóa tác vụ',
	'JS_SAME_FIELDS_SELECTED_MORE_THAN_ONCE' => 'Trường dữ liệu đã chọn nhiều hơn 1 lần',
	'JS_WORKFLOW_SAVED_SUCCESSFULLY' => 'Đã lưu Workflow',
    'JS_CHECK_START_AND_END_DATE'=>'Thời gian kết thúc phải lớn hơn thời gian bắt đầu',
    'JS_TASK_STATUS_CHANGED' => 'Đã thay đổi tình trạng tác vụ.',
    'JS_WORKFLOWS_STATUS_CHANGED' => 'Đã thay đổi tình trạng Workflow.',
    'VTEmailTask' => 'Gởi Email',
    'VTEntityMethodTask' => 'Chức năng do người dùng định nghĩa',
    'VTCreateTodoTask' => 'Thêm tác vụ',
    'VTCreateEventTask' => 'Thêm hoạt động',
    'VTUpdateFieldsTask' => 'Cập nhật trường dữ liệu',
    'VTSMSTask' => 'Gởi SMS',
    'VTPushNotificationTask' => 'Thông báo đẩy',
    'VTCreateEntityTask' => 'Tạo bản ghi',
    'LBL_EXPRESSION_INVALID' => 'Biểu thức không hợp lệ'
);

