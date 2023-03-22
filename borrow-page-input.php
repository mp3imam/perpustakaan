<ul class="nav nav-tabs nav-justified circ-action-btn">
	<!--li class="active">
		<a accesskey="L" class="tab notAJAX" id="borrowPageInput" href="#" onclick="return false;" target="listsFrame">本・DVDをかりる</a>
	</li-->
	<li class="">
		<a accesskey="C" class="tab notAJAX" id="borrowPageList" href="#" onclick="return false;" target="listsFrame">かえしていない本・DVDのリスト satu</a>
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
        echo '</div>';
    }

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