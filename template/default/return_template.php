<?php

function doReturn($dbs,$iGetSubmitData){
	
	if((isset($iGetSubmitData['item_code'])) AND (!empty($iGetSubmitData['item_code']))){

		$iGetItemCode = trim($iGetSubmitData['item_code']);
		$iGetReturn = trim($iGetSubmitData['return']);

		// get loan data
	    $iGetLoanData = $dbs->query("SELECT l.*,m.member_id,m.member_name,b.title FROM loan AS l
	        LEFT JOIN item AS i ON i.item_code=l.item_code
	        LEFT JOIN biblio AS b ON i.biblio_id=b.biblio_id
	        LEFT JOIN member AS m ON l.member_id=m.member_id
	        WHERE l.item_code='".$dbs->escape_string($iGetItemCode)."' AND is_lent=1 AND is_return=0");
	    // return $iGetLoanData;
	    if ($iGetLoanData->num_rows < 1){

	    	return IS_RETURNED;

	    } else {
	    	
	    	$return_date = date('Y-m-d');
	        // get data
	        $iGetLoanRow = $iGetLoanData->fetch_assoc();

	  //       // check overdue
	  //       $_on_grace_periode = false;
	  //       // get due date for this loan
	  //       $_loan_q = $dbs->query("SELECT l.due_date, l.loan_rules_id, l.item_code FROM loan AS l WHERE loan_id=".$iGetLoanRow['loan_id']);
	  //       $_loan_d = $_loan_q->fetch_row();

	  // //       $iGetReturnDate = new DateTime($return_date);
			// // $iGetDueDate = new DateTime($_loan_d[0]);
			// $iGetReturnDate = new DateTime('2017-08-17');
			// $iGetDueDate = new DateTime('2017-07-30');

			// $diff = $iGetReturnDate->diff($iGetDueDate);

			// update the loan data
	        $dbs->query("UPDATE loan SET is_return=1, return_date='".$return_date."' WHERE loan_id=".$iGetLoanRow['loan_id']." AND member_id='".$iGetLoanRow['member_id']."' AND is_lent=1 AND is_return=0");

	        return RETURNED_SUCCESS;

	    }

	}

}
