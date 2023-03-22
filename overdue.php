<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// key to authenticate
define('INDEX_AUTH', '1');

// key to get full database access
@define('DB_ACCESS', 'fa');

// main system configuration
require 'sysconfig.inc.php';

// IP based access limitation
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-circulation');

require SIMBIO.'simbio_UTILS/simbio_date.inc.php';
require MDLBS.'membership/member_base_lib.inc.php';
require LIB.'phpmailer/class.phpmailer.php';

$iGetOverduedMember = $dbs->query("SELECT m.member_id AS member_id FROM member AS m LEFT JOIN loan AS l ON m.member_id=l.member_id WHERE  (l.is_lent=1 AND l.is_return=0 AND TO_DAYS(due_date) < TO_DAYS('".date('Y-m-d', strtotime('+ 2 days'))."')) GROUP BY m.member_id ORDER BY MAX(l.due_date) DESC");
$iGetOverduedMemberRow = $iGetOverduedMember->fetch_all();

for ($i=0; $i < sizeof($iGetOverduedMemberRow); $i++) { 
	
	$memberID = $dbs->escape_string(trim($iGetOverduedMemberRow[$i][0]));

	// create member Instance
	$member = new member($dbs, $memberID);
	
	// send e-mail
	$status = $member->sendOverdueNotice();

}

?>