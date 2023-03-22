<?php

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
require SIMBIO.'simbio_DB/simbio_dbop.inc.php';
require SIMBIO.'simbio_UTILS/simbio_date.inc.php';
require MDLBS.'membership/member_base_lib.inc.php';
require MDLBS.'circulation/circulation_base_lib.inc.php';

// quick return proccess
if (isset($_POST['quickReturnID']) AND $_POST['quickReturnID']) {
    // get loan data
    $loan_info_q = $dbs->query("SELECT l.*,m.member_id,m.member_name,b.title FROM loan AS l
        LEFT JOIN item AS i ON i.item_code=l.item_code
        LEFT JOIN biblio AS b ON i.biblio_id=b.biblio_id
        LEFT JOIN member AS m ON l.member_id=m.member_id
        WHERE l.item_code='".$dbs->escape_string($_POST['quickReturnID'])."' AND is_lent=1 AND is_return=0");
    if ($loan_info_q->num_rows < 1) {
        echo '<div class="errorBox">'.__('この本は借りているリストにはありません').'</div>';
    } else {
        $return_date = date('Y-m-d');
        // get data
        $loan_d = $loan_info_q->fetch_assoc();
        // create circulation object
        $circulation = new circulation($dbs, $loan_d['member_id']);

        /* modified by Indra Sutriadi */
        $circulation->ignore_holidays_fine_calc = $sysconf['ignore_holidays_fine_calc'];
        $circulation->holiday_dayname = $_SESSION['holiday_dayname'];
        $circulation->holiday_date = $_SESSION['holiday_date'];
        /* end of modification */

        // check for overdue
        $overdue = $circulation->countOverdueValue($loan_d['loan_id'], $return_date);
        // check overdue
        if ($overdue) {
            $msg = str_replace('{overdueDays}', $overdue['days'],__('OVERDUED for {overdueDays} days(s) with fines value of')); //mfc
            $loan_d['title'] .= '<div style="color: red; font-weight: bold;">'.$msg.$overdue['value'].'</div>';
        }
        // return item
        $return_status = $circulation->returnItem($loan_d['loan_id']);
        if ($return_status === ITEM_RESERVED) {
            // get reservation data
            $reserve_q = $dbs->query('SELECT r.member_id, m.member_name
                FROM reserve AS r
                LEFT JOIN member AS m ON r.member_id=m.member_id
                WHERE item_code=\''.$loan_d['item_code'].'\' ORDER BY reserve_date ASC LIMIT 1');
            $reserve_d = $reserve_q->fetch_row();
            $member = $reserve_d[1].' ('.$reserve_d[0].')';
            $reserve_msg = str_replace(array('{itemCode}', '{member}'), array($loan_d['item_code'], $member), __('Item {itemCode} is being reserved by member {member}')); //mfc
            $loan_d['title'] .= '<div>'.$reserve_msg.'</div>';
        }
        // write log
        utility::writeLogs($dbs, 'member', $loan_d['member_id'], 'circulation', $_SESSION['realname'].' return item ('.$_POST['quickReturnID'].') with title ('.$loan_d['title'].') with Quick Return method');

        ?>
        <div id="circulationLayer" style="display: block;">
        	<table width="100%" class="border s-member-account" style="margin-bottom: 15px;" cellpadding="5" cellspacing="0">
				<tbody>
					<tr>
						<td rowspan="3">
							<form id="finishForm" method="post" target="blindSubmit" action="/jjc-dev/admin/modules/circulation/circulation_action.php" onsubmit="return false;" style="display: inline;">
								<input type="button" class="btn btn-success" style="height: 110px;" accesskey="T" style="font-size: 18pt;" value="おわり" onclick="confSubmit('finishForm', 'おわり?');borrowProcess('finish',this.value);"><input type="hidden" name="finish" value="true">
							</form>
						</td>
						<td colspan="3" style="background-color: yellow;color: black;font-weight: bold;padding-left: 9px;">
							<?php echo '本・DVD '.$_POST['quickReturnID'].__('は').'&nbsp;'.$return_date; ?> 日にかえされました
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<font color="#06B1CD" style="font-size: 18pt;"><?php echo /*__('Title')*/'本・DVDのなまえ'; ?></font>
							<br>
							<?php echo $loan_d['title']; ?>
						</td>
					</tr>
					<tr>

					</tr>
					<tr>
						<td>
							<font color="#06B1CD" style="font-size: 18pt;"><?php echo /*__('Member Name')*/'なまえ'; ?></font>
							<br>
							<?php echo $loan_d['member_name']; ?>
						</td>
						<td>
							<font color="#06B1CD" style="font-size: 18pt;"><?php echo /*__('Member ID')*/'かいいん ばんごう'; ?></font>
							<br>
							<?php echo $loan_d['member_id']; ?>
						</td>
						<td>
							<font color="#06B1CD" style="font-size: 18pt;"><?php echo /*__('Loan Date')*/'かりた日'; ?></font>
							<br>
							<?php echo $loan_d['loan_date']; ?>
						</td>
						<td>
							<font color="#06B1CD" style="font-size: 18pt;"><?php echo /*__('Due Date')*/'かえす日'; ?></font>
							<br>
							<?php echo $loan_d['due_date']; ?>
						</td>
					</tr>
				</tbody>
			</table>
        </div>
        <?php
    }
    
} else {
	echo '<script type="text/javascript">';
    echo 'alert(\''.__('この本はとうろくされていません').'\');';
    echo 'location.href = \'index.php#\';';
    echo '</script>';
}
?>