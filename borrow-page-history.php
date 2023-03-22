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
// print_r($_SESSION);
require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO.'simbio_DB/datagrid/simbio_dbgrid.inc.php';

// check if there is member ID
if (isset($_SESSION['memberID']) AND !empty($_SESSION['memberID'])) {
    /* LOAN HISTORY LIST */
    $memberID = trim($_SESSION['memberID']);
    // table spec
    $table_spec = 'loan AS l
        LEFT JOIN item AS i ON l.item_code=i.item_code
        LEFT JOIN biblio AS b ON i.biblio_id=b.biblio_id';

    // create datagrid
    $datagrid = new simbio_datagrid();
    // $datagrid->setSQLColumn(
    //     'l.item_code AS \''.__('Item Code').'\'',
    //     'b.title AS \''.__('Title').'\'',
    //     'l.loan_date AS \''.__('Loan Date').'\'',
    //     'IF(is_return = 0, \'<i>'.__('Not Returned Yet').'</i>\', return_date) AS \''.__('Returned Date').'\'');
    $datagrid->setSQLColumn(
        'l.item_code AS `ISBN/バーコード`',
        'b.title AS `本・DVDのなまえ`',
        'l.loan_date AS `かりた日`',
        'IF(is_return = 0, \'<i>`かえしていない本`</i>\', return_date) AS `かえす日`');
    // $datagrid->setSQLorder("l.loan_date DESC");
	$datagrid->setSQLorder("return_date DESC");

    $criteria = 'l.member_id=\''.$dbs->escape_string($memberID).'\' ';
    // is there any search
    if (isset($_GET['keywords']) AND $_GET['keywords']) {
        $keyword = $dbs->escape_string($_GET['keywords']);
        $criteria .= " AND (l.item_code LIKE '%$keyword%' OR b.title LIKE '%$keyword%')";
    }
    $datagrid->setSQLCriteria($criteria);

    // set table and table header attributes
    $datagrid->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
    $datagrid->table_header_attr = 'class="dataListHeader" style="font-weight: bold;font-size:15px;"';
    $datagrid->icon_edit = SWB.'admin/'.$sysconf['admin_template']['dir'].'/'.$sysconf['admin_template']['theme'].'/edit.gif';
    // special properties
    $datagrid->using_AJAX = false;
    $datagrid->column_width = array(0 => '20%', 1 => '52%', 2 => '14%', 3 => '14%');
    $datagrid->disableSort('Return Date');

    // put the result into variables
    $datagrid_result = $datagrid->createDataGrid($dbs, $table_spec, 1000, false);
    if (isset($_GET['keywords']) AND $_GET['keywords']) {
        $msg = str_replace('{result->num_rows}', $datagrid->num_rows, __('Found <strong>{result->num_rows}</strong> from your keywords')); //mfc
        echo '<div class="infoBox">'.$msg.' : "'.$_GET['keywords'].'"</div>';
    }

    // print_r($datagrid);
    ?>

    <ul class="nav nav-tabs nav-justified circ-action-btn">
		<!--li>
			<a accesskey="L" class="tab notAJAX" id="borrowPageInput" href="#" onclick="return false;" target="listsFrame">本・DVDをかりる</a>
		</li-->
		<li>
			<a accesskey="C" class="tab notAJAX" id="borrowPageList" href="#" onclick="return false;" target="listsFrame">かえしていない本・DVDのリスト</a>
		</li>
		<li class="active">
			<a accesskey="H" class="tab notAJAX" id="borrowPageHistory" href="#" onclick="return false;" target="listsFrame">今までにかりた本・DVD</a>
		</li>
	</ul>
	<br>
    <?php
    echo '<div style="max-height:280px;overflow:auto;">';
    // $datagrid_result = str_replace('<tr class="dataListHeader" style="font-weight: bold;">','<tr class="dataListHeader" style="font-weight: bold;position:fixed;">',$datagrid_result);
    echo $datagrid_result;
    echo '</div>';

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