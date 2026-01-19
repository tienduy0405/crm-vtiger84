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
 * Modified by: Kelvin Thang 
 * Date: 2018-06-26
 */
$languageStrings = array(
	// Basic Strings
	'HelpDesk' => 'Yêu cầu hỗ trợ',
	'SINGLE_HelpDesk' => 'Yêu cầu hỗ trợ',
	'LBL_ADD_RECORD' => 'Thêm Yêu cầu hỗ trợ',
	'LBL_RECORDS_LIST' => 'DS Yêu cầu hỗ trợ',

	// Blocks
	'LBL_TICKET_INFORMATION' => 'Thông tin Yêu cầu hỗ trợ',
	'LBL_TICKET_RESOLUTION' => 'Giải pháp xử lý',

	//Field Labels
	'Ticket No' => 'Mã Yêu cầu hỗ trợ',
	'Severity' => 'Mức độ nghiêm trọng',
	'Update History' => 'Lịch sử cập nhật',
	'Hours' => 'Số giờ xử lý',
	'Days' => 'Số ngày xử lý',
	'Title' => 'Tiêu đề',
	'Solution' => 'Giải pháp',
	'From Portal' => 'Từ cổng thông tin KH',
	'Related To' => 'Trường THPT',
	'Contact Name' => 'Người liên hệ',
	//Added for existing picklist entries

	'Big Problem'=>'Vấn đề lớn',
	'Small Problem'=>'Vấn đề nhỏ',
	'Other Problem'=>'Vấn đề khác',

	'Normal'=>'Bình thường',
	'High'=>'Cao',
	'Urgent'=>'Rất cao',

	'Minor'=>'Thấp',
    'Feature'=>'Trung bình',
	'Major'=>'Cao',
	'Critical'=>'Rất cao',

	'Open'=>'Mở',
	'Wait For Response'=>'Chờ phản hồi',
	'Closed'=>'Đã đóng',
	'LBL_STATUS' => 'Tình trạng',
	'LBL_SEVERITY' => 'Mức độ nghiêm trọng',
	//DetailView Actions
	'LBL_CONVERT_FAQ' => 'Chuyển thành Câu hỏi thường gặp',
	'LBL_RELATED_TO' => 'Liên quan tới',

	//added to support i18n in ticket mails
	'Ticket ID'=>'Mã Yêu cầu hỗ trợ',
	'Hi' => 'Chào',
	'Dear'=> 'Kính gửi',
	'LBL_PORTAL_BODY_MAILINFO'=> 'Yêu cầu hỗ trợ: ',
	'LBL_DETAIL' => 'chi tiết như sau :',
	'LBL_REGARDS'=> 'Trân trọng',
	'LBL_TEAM'=> 'Bộ phận CSKH',
	'LBL_TICKET_DETAILS' => 'Thông tin chi tiết',
	'LBL_SUBJECT' => 'Tiêu đề : ',
	'created' => 'đã tạo',
	'replied' => 'đã trả lời',
	'reply'=>'Thông tin phản hồi từ',
	'customer_portal' => 'trong cổng thông tin CSKH',
	'link' => 'Sử dụng đường link sau để gửi thông tin phản hồi:',
	'Thanks' => 'Cảm ơn',
	'Support_team' => 'Bộ phận CSKH',
	'The comments are' => 'Ý kiến phản hồi',
	'Ticket Title' => 'Tiêu đề yêu cầu hỗ trợ',
	'Re' => 'Lại :',

	//This label for customerportal.
	'LBL_STATUS_CLOSED' =>'Đã đóng',//Do not convert this label. This is used to check the status. If the status 'Closed' is changed in vtigerCRM server side then you have to change in customerportal language file also.
	'LBL_STATUS_UPDATE' => 'Tình trạng của Ticket đã được cập nhật thành',
	'LBL_COULDNOT_CLOSED' => 'Không thể đóng được Ticket',
	'LBL_CUSTOMER_COMMENTS' => 'Sau đây là thông tin khách hàng đã cung cấp:',
	'LBL_RESPOND'=> 'Vui lòng trả lời trong thời gian sớm nhất.',
	'LBL_SUPPORT_ADMIN' => 'Trưởng phòng CSKH',
	'LBL_RESPONDTO_TICKETID' =>'Trả lời cho Ticket ID',
	'LBL_RESPONSE_TO_TICKET_NUMBER' =>'Trả lời cho Mã Ticket',
	'LBL_TICKET_NUMBER' => 'Mã Ticket',
	'LBL_CUSTOMER_PORTAL' => ' từ Cổng thông tin CSKH - Khẩn cấp',
	'LBL_LOGIN_DETAILS' => 'Sau đây là thông tin truy cập cổng thông tin CSKH:',
	'LBL_MAIL_COULDNOT_SENT' =>'Không thể gửi mail',
	'LBL_USERNAME' => 'Tên truy cập :',
	'LBL_PASSWORD' => 'Mật khẩu :',
	'LBL_SUBJECT_PORTAL_LOGIN_DETAILS' => 'Thông tin truy cập cổng thông tin CSKH',
	'LBL_GIVE_MAILID' => 'Vui lòng cung cấp email của bạn',
	'LBL_CHECK_MAILID' => 'Hãy kiểm tra lại địa chỉ email',
	'LBL_LOGIN_REVOKED' => 'Thông tin truy cập của bạn đã bị thu hồi. Vui lòng liên hệ với người quản trị hệ thống',
	'LBL_MAIL_SENT' => 'Thông tin đăng nhập vào cổng thông tin CSKH đã được gửi cho bạn qua email',
	'LBL_ALTBODY' => 'Đây là nội dung email',
	'HelpDesk ID' => 'Mã yêu cầu hỗ trợ',
	//Portal shortcuts
	'LBL_ADD_DOCUMENT'=>"Thêm tài liệu",
	'LBL_OPEN_TICKETS'=>"Yêu cầu hỗ trợ đang mở",
	'LBL_CREATE_TICKET'=>"Tạo yêu cầu hỗ trợ",
);

$jsLanguageStrings=array(
	'LBL_ADD_DOCUMENT'=>'Thêm tài liệu',
	'LBL_OPEN_TICKETS'=>'Yêu cầu hỗ trợ đang mở',
	'LBL_CREATE_TICKET'=>'Tạo yêu cầu hỗ trợ'
);
