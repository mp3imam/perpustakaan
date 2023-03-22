<?php


function show_id_member($dbs,$member_id,$borrowPageActive){
	
	$iGetMember = $dbs->query("SELECT * FROM member WHERE member_id = '".$member_id."'");
	$iGetRow = $iGetMember->fetch_row();

	$iGetLoan = $dbs->query("SELECT loan.item_code, loan.loan_date, loan.due_date, loan.is_return, biblio.title FROM loan LEFT JOIN item ON loan.item_code = item.item_code LEFT JOIN biblio ON item.biblio_id = biblio.biblio_id WHERE member_id = '".$member_id."' AND is_return = 1");
	$iGetLoanRow = $iGetLoan->fetch_all();

	$iGetLoanNotReturn = $dbs->query("SELECT loan.item_code, loan.loan_date, loan.due_date, loan.is_return, biblio.title FROM loan LEFT JOIN item ON loan.item_code = item.item_code LEFT JOIN biblio ON item.biblio_id = biblio.biblio_id WHERE member_id = '".$member_id."' AND is_return = 0");
	$iGetLoanNotReturnRow = $iGetLoanNotReturn->fetch_all();

	if($iGetRow[2] == 1){ $iGetGender = 'Male'; } elseif ($iGetRow[2] == 0) { $iGetGender = 'Female'; }

	if($iGetRow[4] == 1){ $iGetMemberType = 'Standard'; }

	if($borrowPageActive == 'inputIsbn'){

		$strContentBorrowPage = '<!--div style="border:solid 1px #f2f2f2;padding:10px 20px;-->
			<br><br><br>
			<form action="index.php" method="get">
				Please input Item Code :
				<input type="text" name="iDoInputIsbn">
				<input type="hidden" name="borrow" value="borrow" />
				<input type="hidden" name="borrow_page" value="inputIsbn" />
				<input type="hidden" name="id_member" value="'.$member_id.'" />
				<button type="submit" name="doBorrow" value="borrow" clas="btn btn-danger btn-block">Borrow</button>
			</form>
		<!--/div-->';

	} elseif ($borrowPageActive == 'listData') {
		
		if(sizeof($iGetLoanNotReturnRow) > 0){
			
			$subStrContentBoorowPage = '';

			for ($i=0; $i < sizeof($iGetLoanNotReturnRow); $i++) { 
				
				if($iGetLoanNotReturnRow[$i][3] == 1){
					
					$iGetLoanNotReturnStatus = 'Returned';

				} else {
					
					$iGetLoanNotReturnStatus = 'Not Returned Yet';

				}

				$subStrContentBoorowPage .= '<tr>
					<td style="padding:5px;color:black;">
						'.$iGetLoanNotReturnRow[$i][0].'
					</td>
					<td style="padding:5px;color:black;">
						'.$iGetLoanNotReturnRow[$i][4].'
					</td>
					<td style="padding:5px;color:black;">
						'.$iGetLoanNotReturnRow[$i][1].'
					</td>
					<td style="padding:5px;color:black;">
						'.$iGetLoanNotReturnRow[$i][2].'
					</td>
					<td style="padding:5px;color:black;">
						'.$iGetLoanNotReturnStatus.'
					</td>
				</tr>';

			}

		} else {

			$subStrContentBoorowPage = '<tr><td colspan="5"><center>Not Found History Data</center></td></tr>';

		}

		$strContentBorrowPage = '<!--div style="border:solid 1px #f2f2f2;padding:10px 20px;-->
			<br><br><br><table border="1" width="100%">
				<thead>
					<tr class="detailListHeader">
						<td style="padding:5px;text-align:center;background:black;color:white;">
							<b>Item Code</b>
						</td>
						<td style="padding:5px;text-align:center;background:black;color:white;">
							<b>Title</b>
						</td>
						<td style="padding:5px;text-align:center;background:black;color:white;">
							<b>Date</b>
						</td>
						<td style="padding:5px;text-align:center;background:black;color:white;">
							<b>Due Date</b>
						</td>
						<td style="padding:5px;text-align:center;background:black;color:white;">
							<b>Status</b>
						</td>
					</tr>
				</thead>
				<tbody>
				'.$subStrContentBoorowPage.'
				</tbody>
			</table>
		<!--/div-->';

	} elseif ($borrowPageActive == 'historyData') {
		
		if(sizeof($iGetLoanRow) > 0){
			
			$subStrContentBoorowPage = '';

			for ($i=0; $i < sizeof($iGetLoanRow); $i++) { 
				
				if($iGetLoanRow[$i][3] == 1){
					
					$iGetLoanStatus = 'Returned';

				} else {
					
					$iGetLoanStatus = 'Not Returned Yet';

				}

				$subStrContentBoorowPage .= '<tr>
					<td style="padding:5px;color:black;">
						'.$iGetLoanRow[$i][0].'
					</td>
					<td style="padding:5px;color:black;">
						'.$iGetLoanRow[$i][4].'
					</td>
					<td style="padding:5px;color:black;">
						'.$iGetLoanRow[$i][1].'
					</td>
					<td style="padding:5px;color:black;">
						'.$iGetLoanRow[$i][2].'
					</td>
					<td style="padding:5px;color:black;">
						'.$iGetLoanStatus.'
					</td>
				</tr>';

			}

		} else {

			$subStrContentBoorowPage = '<tr><td colspan="5"><center>Not Found History Data</center></td></tr>';

		}

		$strContentBorrowPage = '<!--div style="border:solid 1px #f2f2f2;padding:10px 20px;-->
			<br><br><br><table border="1" width="100%">
				<thead>
					<tr class="detailListHeader">
						<td style="padding:5px;text-align:center;background:black;color:white;">
							<b>Item Code</b>
						</td>
						<td style="padding:5px;text-align:center;background:black;color:white;">
							<b>Title</b>
						</td>
						<td style="padding:5px;text-align:center;background:black;color:white;">
							<b>Date</b>
						</td>
						<td style="padding:5px;text-align:center;background:black;color:white;">
							<b>Due Date</b>
						</td>
						<td style="padding:5px;text-align:center;background:black;color:white;">
							<b>Status</b>
						</td>
					</tr>
				</thead>
				<tbody>
				'.$subStrContentBoorowPage.'
				</tbody>
			</table>
		<!--/div-->';

	}

	$strShow = '<div style="border:solid 1px #f2f2f2;padding:10px 20px;">
				<table>
					<tr>
						<td>
							Member ID
						</td>
						<td>
							&nbsp;:&nbsp;
						</td>
						<td>
							'.$iGetRow[0].'
						</td>
					</tr>
					<tr>
						<td>
							Member Name
						</td>
						<td>
							&nbsp;:&nbsp;
						</td>
						<td>
							'.$iGetRow[1].'
						</td>
					</tr>
					<tr>
						<td>
							Gender
						</td>
						<td>
							&nbsp;:&nbsp;
						</td>
						<td>
							'.$iGetGender.'
						</td>
					</tr>
					<tr>
						<td>
							Phone
						</td>
						<td>
							&nbsp;:&nbsp;
						</td>
						<td>
							'.$iGetRow[13].'
						</td>
					</tr>
					<tr>
						<td>
							Fax
						</td>
						<td>
							&nbsp;:&nbsp;
						</td>
						<td>
							'.$iGetRow[14].'
						</td>
					</tr>
					<tr>
						<td>
							Email
						</td>
						<td>
							&nbsp;:&nbsp;
						</td>
						<td>
							'.$iGetRow[7].'
						</td>
					</tr>
					<tr>
						<td>
							Member Type
						</td>
						<td>
							&nbsp;:&nbsp;
						</td>
						<td>
							'.$iGetMemberType.'
						</td>
					</tr>
					<tr>
						<td>
							Registration Date
						</td>
						<td>
							&nbsp;:&nbsp;
						</td>
						<td>
							'.$iGetRow[16].'
						</td>
					</tr>
					<tr>
						<td>
							Member Expired
						</td>
						<td>
							&nbsp;:&nbsp;
						</td>
						<td>
							'.$iGetRow[17].'
						</td>
					</tr>
				</table>
	</div>';
	return $strShow.$strContentBorrowPage;
	// return $iGetRow;
}

function doBorrow($dbs,$iGetSubmitData){

	if(((isset($iGetSubmitData['iDoInputIsbn'])) AND (!empty($iGetSubmitData['iDoInputIsbn']))) AND ((isset($iGetSubmitData['id_member'])) AND (!empty($iGetSubmitData['id_member'])))) {

		$iGetInputIsbn = trim($iGetSubmitData['iDoInputIsbn']);
		$iGetBorrowPage = trim($iGetSubmitData['borrow_page']);
		$iGetBorrow = trim($iGetSubmitData['borrow']);
		$iGetMemberId = trim($iGetSubmitData['id_member']);

		$iGetMember = $dbs->query("SELECT * FROM member WHERE member_id = '".$iGetMemberId."'");
		$iGetRow = $iGetMember->fetch_row();
		
		// you cant borrow any collection if your membership is expired or in pending state
        if ($iGetRow[17] < date('Y-m-d')) {
            return LOAN_NOT_PERMITTED;
        }
        if ($iGetRow[19] != 0) {
            return LOAN_NOT_PERMITTED_PENDING;
        }

		$iDoCheckItem = $dbs->query("SELECT b.title, i.coll_type_id,
            b.gmd_id, ist.no_loan FROM biblio AS b
            LEFT JOIN item AS i ON b.biblio_id=i.biblio_id
            LEFT JOIN mst_item_status AS ist ON i.item_status_id=ist.item_status_id
            WHERE i.item_code='$iGetInputIsbn'");

		$iDoCheckItemRow = $iDoCheckItem->fetch_row();

		if ($iDoCheckItem->num_rows > 0) {

			// first, check for availability for this item
            $_avail_q = $dbs->query("SELECT item_code FROM loan AS L
                WHERE L.item_code='$iGetInputIsbn' AND L.is_lent=1 AND L.is_return=0");
            // if we find any record then it means the item is unavailable
            if ($_avail_q->num_rows > 0) {
                return ITEM_UNAVAILABLE;
            }
            // check loan status for item
            if ((integer)$iDoCheckItemRow[3] > 0) {
                return ITEM_LOAN_FORBID;
            }

            // check limit loan
            $iGetLoanRule = $dbs->query("SELECT * FROM mst_loan_rules WHERE member_type_id=".intval($iGetRow[4]));

            $iGetLoanRuleRow = $iGetLoanRule->fetch_assoc();
            // return $iGetLoanRuleRow;

            // check current member borrow
            $iGetMemberBorrow = $dbs->query("SELECT loan.item_code, loan.loan_date, loan.due_date, loan.is_return, biblio.title FROM loan LEFT JOIN item ON loan.item_code = item.item_code LEFT JOIN biblio ON item.biblio_id = biblio.biblio_id WHERE member_id = '".$iGetMemberId."' AND is_return = 0");

            $iGetMemberBorrowRow = $iGetMemberBorrow->fetch_all();

            if(sizeof($iGetMemberBorrowRow) > $iGetLoanRuleRow['loan_limit']){

            	return LOAN_LIMIT_REACHED;

            } else {

            	// do save
            	$data['item_code'] = $iGetInputIsbn;
                $data['member_id'] = $iGetMemberId;
                $data['loan_date'] = date('Y-m-d');
                $data['due_date'] = date('Y-m-d', strtotime('+'.$iGetLoanRuleRow['loan_periode'].' days'));
                $data['renewed'] = 'literal{0}';
                $data['is_lent'] = 1;
                $data['is_return'] = 'literal{0}';
                $data['loan_rules_id'] = 'literal{0}';

                // return $data;

                include SIMBIO.'simbio_DB/simbio_dbop.inc.php';

                $sql_op = new simbio_dbop($dbs);

                $sql_op->insert('loan', $data);

            	return ITEM_SESSION_ADDED;

            }

		} else {

			return ITEM_NOT_FOUND;

		}

	} else {
		return "Please Input Item Code";
	}

}