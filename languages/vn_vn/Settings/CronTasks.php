<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 *	Modified by: Hieu Nguyen
 *	Date: 2018-07-03
 */

 $languageStrings = array(
	'CronTasks' => 'Lập lịch hệ thống',

	//Basic Field Names
	'Id' => 'Id',
	'Cron Job' => 'Tác vụ chạy nền',
	'Frequency' => 'Tần suất',
	'Status' => 'Tình trạng',
	'Last Start' => 'Thời điểm bắt đầu quét gần nhất',
	'Last End' => 'Thời điểm kết thúc quét gần nhất',
	'Sequence' => 'Thứ tự chạy',

	//Actions
	'LBL_COMPLETED' => 'Hoàn tất',
	'LBL_RUNNING' => 'Đang chạy',
	'LBL_ACTIVE' => 'Có hiệu lực',
	'LBL_INACTIVE' => 'Đã vô hiệu',

	// Added by Hieu Nguyen on 2018-07-02
	'Frequency(H:M)' => 'Tần suất (Giờ:Phút)',
	'Recommended frequency for RecurringInvoice is 12 hours' => 'Tần suất lặp lại tối ưu cho RecurringInvoice là 12 tiếng 1 lần',
	'Recommended frequency for Workflow is 15 mins' => 'Tần suất lặp lại tối ưu cho Workflow là 15 phút 1 lần',
	'Recommended frequency for SendReminder is 15 mins' => 'Tần suất lặp lại tối ưu cho SendReminder là 15 phút 1 lần',
	'Recommended frequency for MailScanner is 15 mins' => 'Tần suất lặp lại tối ưu cho MailScanner là 15 phút 1 lần',
	'Recommended frequency for ScheduleImport is 15 mins' => 'Tần suất lặp lại tối ưu cho ScheduleImport là 15 phút 1 lần',
	'Recommended frequency for ScheduleReports is 15 mins' => 'Tần suất lặp lại tối ưu cho ScheduleReports là 15 phút 1 lần',
	'LBL_BTN_RESET_TITLE' => 'Chạy lại',
);

$jsLanguageStrings = array(
	// Added by Hieu Nguyen on 2018-08-18
	'JS_RESET_CONFIRM_MSG' => "Hành động này sẽ cho phép Tác vụ chạy nền này được khởi động lại từ đầu.\nLưu ý: Chỉ thực hiện hành động này nếu Tác vụ chạy nền bị tắc, nếu không bạn sẽ gặp rắc rối vì tác vụ sẽ chạy lại dẫn đến logic bị lặp lại!",
	'JS_RESET_SUCCESS_MSG' => 'Khởi động lại Tác vụ chạy nền thành công!',
	'JS_RESET_ERROR_MSG' => 'Không thể khởi động lại Tác vụ chạy nền. Bạn vui lòng thử lại!',
);
