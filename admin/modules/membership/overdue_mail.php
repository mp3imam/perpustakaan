<?php
/**
 * Copyright (C) 2007,2008  Arie Nugraha (dicarve@yahoo.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

// key to authenticate
define('INDEX_AUTH', '1');

require '../../../sysconfig.inc.php';
// IP based access limitation
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-membership');
require SB.'admin/default/session.inc.php';

// privileges checking
$can_read = utility::havePrivilege('membership', 'r');
if (!$can_read) { die(); }

require SIMBIO.'simbio_UTILS/simbio_date.inc.php';
require MDLBS.'membership/member_base_lib.inc.php';
require LIB.'phpmailer/class.phpmailer.php';

// // get data
// $memberID = $dbs->escape_string(trim($_POST['memberID']));
// // create member Instance
// $member = new member($dbs, $memberID);
// // send e-mail
// $status = $member->sendOverdueNotice();
// // get message
// echo $status['message'];

$memberID = trim($_POST['memberID']);

if($memberID == 'sendEmailToAllOverdue'){
    
    $iGetOverduedMember = $dbs->query("SELECT m.member_id AS member_id FROM member AS m LEFT JOIN loan AS l ON m.member_id=l.member_id WHERE  (l.is_lent=1 AND l.is_return=0 AND TO_DAYS(due_date) < TO_DAYS('".date('Y-m-d', strtotime('+ 2 days'))."')) GROUP BY m.member_id ORDER BY MAX(l.due_date) DESC");
    $iGetOverduedMemberRow = $iGetOverduedMember->fetch_all();

	for ($i=0; $i < sizeof($iGetOverduedMemberRow); $i++) { 
		
		$memberID = $dbs->escape_string(trim($iGetOverduedMemberRow[$i][0]));
		// $memberID = $dbs->escape_string('22513B');
		
		// create member Instance
    	$member = new member($dbs, $memberID);
    	// send e-mail
    	$status = $member->sendOverdueNotice();

	}
	$sendEmailToAllStatus = '<font color="#4AC49B">Email reminder has sent to all overdued loan.</font>';
	echo $sendEmailToAllStatus;

// print_r($iGetOverduedMemberRow);

} elseif((isset($_POST['memberID'])) AND ($_POST['memberID'] != '')) {

	// get data
	$memberID = $dbs->escape_string(trim($_POST['memberID']));
	
	// create member Instance
	$member = new member($dbs, $memberID);
	// send e-mail
	$status = $member->sendOverdueNotice();
	// get message
	echo $status['message'];

}
