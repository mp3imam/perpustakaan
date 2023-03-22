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

// print_r($_SESSION);exit;
if (!isset($_SESSION['memberID'])) { /*die();*/ $_SESSION['memberID'] = $_POST['memberID']; }
require SIMBIO.'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_GUI/paging/simbio_paging.inc.php';


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
        // $headers = array(__('Remove'),  __('Item Code'), __('Title'), __('Loan Date'), __('Due Date'));
        $headers = array( 'ISBN/バーコード', '本・DVDのなまえ', 'かりた日', 'かえす日');
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
                /*$remove_link,*/ $temp_loan_list_d['item_code'],
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
    }











    $circulation = new circulation($dbs, $memberID);
    $loan_list_query = $dbs->query(sprintf("SELECT L.loan_id, b.title, ct.coll_type_name,
        i.item_code, L.loan_date, L.due_date, L.return_date, L.renewed,
        IF(lr.reborrow_limit IS NULL, IF(L.renewed>=mt.reborrow_limit, 1, 0), IF(L.renewed>=lr.reborrow_limit, 1, 0)) AS extend
        FROM loan AS L
        LEFT JOIN item AS i ON L.item_code=i.item_code
        LEFT JOIN mst_coll_type AS ct ON i.coll_type_id=ct.coll_type_id
        LEFT JOIN member AS m ON L.member_id=m.member_id
        LEFT JOIN mst_member_type AS mt ON m.member_type_id=mt.member_type_id
        LEFT JOIN mst_loan_rules AS lr ON mt.member_type_id=lr.member_type_id AND i.coll_type_id = lr.coll_type_id
        LEFT JOIN biblio AS b ON i.biblio_id=b.biblio_id
        WHERE L.is_lent=1 AND L.is_return=0 AND L.member_id='%s' order by L.loan_id desc", $memberID)); // query modified by Indra Sutriadi

    // create table object
    $loan_list = new simbio_table();
    $loan_list->table_attr = 'align="center" width="100%" cellpadding="3" cellspacing="0"';
    $loan_list->table_header_attr = 'class="dataListHeader" style="font-weight: bold;font-size:15px;"';
    $loan_list->highlight_row = true;
    // table header
    // $headers = array( __('Item Code'), __('Title'), __('Loan Date'), __('Due Date'));
    $headers = array( 'ISBN/バーコード', '本・DVDのなまえ', 'かりた日', 'かえす日');
    $loan_list->setHeader($headers);
    // row number init
    $row = 1;
    $is_overdue = false;
    /* modified by Indra Sutriadi */
    $circulation->ignore_holidays_fine_calc = $sysconf['ignore_holidays_fine_calc'];
    $circulation->holiday_dayname = $_SESSION['holiday_dayname'];
    $circulation->holiday_date = $_SESSION['holiday_date'];
    /* end of modification */
    $_total_temp_fines = 0; #newly added
    while ($loan_list_data = $loan_list_query->fetch_assoc()) {
        // alternate the row color
        $row_class = ($row%2 == 0)?'alterCell':'alterCell2';

        // return link
        $return_link = '<a href="#" onclick="confirmProcess('.$loan_list_data['loan_id'].', \''.$loan_list_data['item_code'].'\', \'return\')" title="'.__('Return this item').'" class="returnLink">&nbsp;</a>';
        // extend link
        // check if membership already expired
        if ($_SESSION['is_expire']) {
            $extend_link = '<span class="noExtendLink" title="'.__('No Extend').'">&nbsp;</span>';
        } else {
            // check if this loan just already renewed
            if ($loan_list_data['return_date'] == date('Y-m-d') || in_array($loan_list_data['loan_id'], $_SESSION['reborrowed']) || $loan_list_data['extend'] == 1) {
                $extend_link = '<span class="noExtendLink" title="'.__('No Extend').'">&nbsp;</span>';
            } else {
                $extend_link = '<a href="#" onclick="confirmProcess('.$loan_list_data['loan_id'].', \''.$loan_list_data['item_code'].'\', \'extend\')" title="'.__('Extend loan for this item').'" class="extendLink">&nbsp;</a>';
            }
        }
        // renewed flag
        if ($loan_list_data['renewed'] > 0) {
            $loan_list_data['title'] = $loan_list_data['title'].' - <strong style="color: blue;">'.__('Extended').'</strong>';
        }
        
        // row colums array
        $fields = array(
            $loan_list_data['item_code'],
            $loan_list_data['title'],
            $loan_list_data['loan_date'],
            $loan_list_data['due_date']
            );

        // append data to table row
        $loan_list->appendTableRow($fields);
        // set the HTML attributes
        $loan_list->setCellAttr($row, 0, "valign='top' align='center' class='$row_class' style='width: 20%;'");
        $loan_list->setCellAttr($row, 1, "valign='top' class='$row_class' style='width: 52%;'");
        $loan_list->setCellAttr($row, 2, "valign='top' class='$row_class' style='width: 14%;'");
        $loan_list->setCellAttr($row, 3, "valign='top' class='$row_class' style='width: 14%;'");

        $row++;
    }
    ?>

    <ul class="nav nav-tabs nav-justified circ-action-btn">
		<!--li>
			<a accesskey="L" class="tab notAJAX" id="borrowPageInput" href="#" onclick="return false;" target="listsFrame">本・DVDをかりる</a>
		</li-->
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
	        <input type="submit" value="かりる" onclick="borrowAdd('add',$('#tempLoanID').val(),$('#menuTabberMemberID').val());" class="btn btn-warning button">
	    </form>
	</div>
	<script type="text/javascript">$('#tempLoanID').focus();</script>
	<!--item loan form end-->
	<br>

    <?php
    
    // show reservation alert if any
    if (isset($_GET['reserveAlert']) AND !empty($_GET['reserveAlert'])) {
        $reservedItem = $dbs->escape_string(trim($_GET['reserveAlert']));
        // get reservation data
        $reserve_q = $dbs->query(sprintf('SELECT r.member_id, m.member_name
            FROM reserve AS r
            LEFT JOIN member AS m ON r.member_id=m.member_id
            WHERE item_code=\'%s\' ORDER BY reserve_date ASC LIMIT 1', $reservedItem));
        $reserve_d = $reserve_q->fetch_row();
        $member = $reserve_d[1].' ('.$reserve_d[0].')';
        $reserve_msg = str_replace(array('{itemCode}', '{member}'), array('<b>'.$reservedItem.'</b>', '<b>'.$member.'</b>'), __('Item {itemCode} is being reserved by member {member}'));
        echo '<div class="infoBox">'.$reserve_msg.'</div>';
    }

    // create e-mail lin if there is overdue
    if ($is_overdue) {
        echo '<div style="padding: 5px; background: #ccc;"><div id="emailStatus"></div><a class="sendEmail usingAJAX" href="'.MWB.'membership/overdue_mail.php'.'" postdata="memberID='.$memberID.'" loadcontainer="emailStatus">'.__('Send overdues notice e-mail').'</a> | <span style="color: red; font-weight: bold;">'.__('Total of temporary fines').': '.$_total_temp_fines.'.</span></div>'."\n";
    }
    echo '<div style="max-height:150px;overflow:auto;">';
    echo $loan_list->printTable();
    echo '</div>';
    // hidden form for return and extend process
    echo '<form name="loanHiddenForm" method="post" action="circulation_action.php"><input type="hidden" name="process" value="return" /><input type="hidden" name="loanID" value="" /></form>';

}

?>

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
</script>