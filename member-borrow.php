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
require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';

// transaction is started
if (isset($_POST['memberID']) OR isset($_SESSION['memberID'])) {
    // create member object
    // if there is already member ID session
    if (isset($_SESSION['memberID'])) {
        $memberID = trim($_SESSION['memberID']);
    } else {
        // new transaction proccess
        // clear previous sessions
        $_SESSION['temp_loan'] = array();
        $memberID = trim(preg_replace('@\s*(<.+)$@i', '', $_POST['memberID']));
        // write log
        utility::writeLogs($dbs, 'member', $memberID, 'circulation', $_SESSION['realname'].' start transaction with member ('.$memberID.')');
    }
    $member = new member($dbs, $memberID);
    if (!$member->valid()) {
        # echo '<div class="errorBox">Member ID '.$memberID.' not valid (unregistered in database)</div>';
        echo '<div class="errorBox">'.__('とうろくがありません。').'</div><center><a href="index.php">もういちど入力して下さい。</a></center>';exit; //mfc
    } else {
        // get member information
        $member_type_d = $member->getMemberTypeProp();
        // member type ID
        $_SESSION['memberTypeID'] = $member->member_type_id;
        // save member ID to the sessions
        $_SESSION['memberID'] = $member->member_id;
        // create renewed/reborrow session array
        $_SESSION['reborrowed'] = array();
        // check membership expire
        $_SESSION['is_expire'] = $member->isExpired();
        // check if membership is blacklisted
        $_SESSION['is_pending'] = $member->isPending();
        // print record
        $_SESSION['receipt_record'] = array();
        // set HTML buttons disable flag
        $disabled = '';
        $add_style = '';
        // check for expire date and pending state
        if ($_SESSION['is_expire'] OR $_SESSION['is_pending']) {
            $disabled = ' disabled ';
            $add_style = ' disabled';
        }

        $fines_alert = FALSE;
        $total_unpaid_fines = 0;
        $_unpaid_fines = $dbs->query('SELECT * FROM fines WHERE member_id=\''.$_SESSION['memberID'].'\' AND debet > credit');
        $unpaid_fines = $_unpaid_fines->fetch_row();
        #var_dump($unpaid_fines);
        if (!empty($unpaid_fines)) {
            foreach ($unpaid_fines as $key => $value) {
                $total_unpaid_fines = $total_unpaid_fines + $value['3'];
            }
        }
        if ($total_unpaid_fines > 0) {
            $fines_alert = TRUE;
        }

    }
    // exit();
    // print_r($_SESSION);

$expire_msg = '';
if ($_SESSION['is_expire']) {
    $expire_msg .= '<span class="error">('.__('Membership Already Expired').')</span>';
}
echo $expire_msg;
?>

<table width="100%" class="border s-member-account" style="margin-bottom: 5px;" cellpadding="5" cellspacing="0">
	<tbody>
		<tr>
			<td rowspan="3">
				<form id="finishForm" method="post" target="blindSubmit" action="/jjc-dev/admin/modules/circulation/circulation_action.php" onsubmit="return false;" style="display: inline;">
					<input type="button" class="btn btn-success" style="height: 110px; font-size: 20pt;" accesskey="T" value="おわり" onclick="return doConfirmFinish('おわり?');"><input type="hidden" name="finish" value="true">
				</form>
			</td>
			<td>
				<font color="#06B1CD" style="font-size: 18pt;">なまえ</font>
				<br>
				<?php echo $member->member_name; ?>
				<input type="hidden" name="memeberID" id="menuTabberMemberID" value="<?php echo $member->member_id; ?>">
				<!--input type="text" name="tempSessionLoan" id="tempSessionLoan"-->
			</td>
			<td>
				<font color="#06B1CD" style="font-size: 18pt;">メールアドレス</font>
				<br>
				<?php echo $member->member_email; ?>
			</td>
			<td>
				<font color="#06B1CD" style="font-size: 18pt;">とうろく日</font>
				<br>
				<?php echo $member->register_date; ?>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<font color="#06B1CD" style="font-size: 18pt;">かいいん ばんごう</font>
				<br>
				<?php echo $member->member_id; ?>
			</td>
			<td>
				<!--font color="#06B1CD" style="font-size: 18pt;">かいいん たいぷ</font-->
				<br>
				<?php /*echo $member->member_type_name; */ ?>
			</td>
			<td>
				<font color="#06B1CD" style="font-size: 18pt;">かりるきげん</font>
				<br>
				<?php echo $member->expire_date; ?>
			</td>
		</tr>
	</tbody>
</table>
<div style="border-bottom: solid 1px black;"></div>
<div id="borrowPageContent">
	<ul class="nav nav-tabs nav-justified circ-action-btn">
		<!-- <li class="active">
			<a accesskey="L" class="tab notAJAX" id="borrowPageInput" href="#" onclick="return false;" target="listsFrame">本・DVDをかりる</a>
		</li> -->
		<li class="active">
			<a accesskey="C" class="tab notAJAX" id="borrowPageList" href="#" onclick="return false;" target="listsFrame">かえしていない本・DVDのリスト</a>
		</li>
		<li class="">
			<a accesskey="H" class="tab notAJAX" id="borrowPageHistory" href="#" onclick="return false;" target="listsFrame">今までにかりた本・DVD</a>
		</li>
	</ul>
	<br>

    <!--item loan form-->
    <div class="loanItemCodeInput">
        <form name="itemLoan" id="loanForm" action="circulation_action.php" method="post" onsubmit="return false;" style="display: inline;">
            ISBN/バーコードをスキャンして下さい :
            <input type="text" id="tempLoanID" name="tempLoanID" style="height: 35px;">
            <input type="submit" style="font-size: 20pt;" value="かりる" onclick="borrowAdd('add',$('#tempLoanID').val(),$('#menuTabberMemberID').val());" class="btn btn-warning button">
        </form>
    </div>
    <script type="text/javascript">$('#tempLoanID').focus();</script>
    <!--item loan form end-->
    <br>
    <?php

    // check if there is member ID
	if (isset($_SESSION['memberID'])) {
	    $memberID = trim($_SESSION['memberID']);
	    
	    // make a list of temporary loan if there is any
	    if (count($_SESSION['temp_loan']) > 0) {
	        // create table object
	        $temp_loan_list = new simbio_table();
	        $temp_loan_list->table_attr = "align='center' style='width: 100%;' cellpadding='3' cellspacing='0'";
	        $temp_loan_list->table_header_attr = 'class="dataListHeader" style="font-weight: bold;"';
	        $temp_loan_list->highlight_row = true;
	        // table header
	        $headers = array(__('Remove'),  __('Item Code'), __('Title'), __('Loan Date'), __('Due Date'));
	        $temp_loan_list->setHeader($headers);
	        // row number init
	        $row = 1;
	        foreach ($_SESSION['temp_loan'] as $_loan_ID => $temp_loan_list_d) {
	            // alternate the row color
	            $row_class = ($row%2 == 0)?'alterCell':'alterCell2';

	            // remove link
	            $remove_link = '<a href="circulation_action.php?removeID='.$temp_loan_list_d['item_code'].'" title="Remove this item" class="trashLink">&nbsp;</a>';

	            // check if manually changes loan and due date allowed
	            if ($sysconf['allow_loan_date_change']) {
	                $loan_date = '<a href="#" title="'.__('Click To Change Loan Date').'" class="dateChange loan notAJAX" data="'.$_loan_ID.'" id="loanDate'.$row.'">'.$temp_loan_list_d['loan_date'].'</a>';
	                $due_date = '<a href="#" title="'.__('Click To Change Due Date').'" class="dateChange due notAJAX" data="'.$_loan_ID.'" id="dueDate'.$row.'">'.$temp_loan_list_d['due_date'].'</a>';
	            } else {
	                $loan_date = $temp_loan_list_d['loan_date'];
	                $due_date = $temp_loan_list_d['due_date'];
	            }

	            // row colums array
	            $fields = array(
	                $remove_link, $temp_loan_list_d['item_code'],
	                $temp_loan_list_d['title'], $loan_date, $due_date);

	            // append data to table row
	            $temp_loan_list->appendTableRow($fields);
	            // set the HTML attributes
	            $temp_loan_list->setCellAttr($row, null, 'class="'.$row_class.'"');
	            $temp_loan_list->setCellAttr($row, 0, 'valign="top" align="center" style="width: 5%;"');
	            $temp_loan_list->setCellAttr($row, 1, 'valign="top" style="width: 10%;"');
	            $temp_loan_list->setCellAttr($row, 2, 'valign="top" style="width: 60%;"');

	            $row++;
	        }
	        echo '<div style="max-height:280px;overflow:auto;">';	
	        echo $temp_loan_list->printTable();
	        echo '</div>';
	    }

	}

    ?>
    
</div>
<script type="text/javascript">
	$('#borrowPageInput').click(function(){
		var memberID = $('#menuTabberMemberID').val();
	  	$.ajax({
			url:'borrow-page-input.php',
			type: 'POST',
			data: {'memberID':memberID},
			async: true,
			success:function(response){
				$('#borrowPageContent').html(response);
				console.log(response);
			}
		});
	  });

	  $('#borrowPageList').click(function(){
	  	var memberID = $('#menuTabberMemberID').val();
	  	$.ajax({
			url:'borrow-page-list.php',
			type: 'POST',
			data: {'memberID':memberID},
			async: true,
			success:function(response){
				$('#borrowPageContent').html(response);
				console.log(response);
			}
		});
	  });

	  $('#borrowPageHistory').click(function(){
	  	var memberID = $('#menuTabberMemberID').val();
	  	$.ajax({
			url:'borrow-page-history.php',
			type: 'POST',
			data: {'memberID':memberID},
			async: true,
			success:function(response){
				$('#borrowPageContent').html(response);
				console.log(response);
			}
		});
	  });

	  function doConfirmFinish(textConfirm){
	  	var showText = confirm(textConfirm);
	  	if(showText){
	  		/* return true; */
	  		borrowProcess('finish','おわり');
	  	} else {
	  		return false;
	  	}
	  }

</script>

<?php
}
?>