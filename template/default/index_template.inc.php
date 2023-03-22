<?php
/**
 * Template for OPAC
 *
 * Copyright (C) 2015 Arie Nugraha (dicarve@gmail.com)
 * Create by Eddy Subratha (eddy.subratha@slims.web.id)
 *
 * Slims 8 (Akasia)
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
 */

// be sure that this file not accessed directly

if (!defined('INDEX_AUTH')) {
  die("can not access this file directly");
} elseif (INDEX_AUTH != 1) {
  die("can not access this file directly");
}

?>
<!--
==========================================================================
   ___  __    ____  __  __  ___      __    _  _    __    ___  ____    __
  / __)(  )  (_  _)(  \/  )/ __)    /__\  ( )/ )  /__\  / __)(_  _)  /__\
  \__ \ )(__  _)(_  )    ( \__ \   /(__)\  )  (  /(__)\ \__ \ _)(_  /(__)\
  (___/(____)(____)(_/\/\_)(___/  (__)(__)(_)\_)(__)(__)(___/(____)(__)(__)

==========================================================================
-->
<!DOCTYPE html>
<html lang="<?php echo substr($sysconf['default_lang'], 0, 2); ?>" xmlns="http://www.w3.org/1999/xhtml" prefix="og: http://ogp.me/ns#">
<head>

<?php
// Meta Template
include "partials/meta.php";
?>

</head>

<body itemscope="itemscope" itemtype="http://schema.org/WebPage">

<!--[if lt IE 9]>
<div class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</div>
<![endif]-->

<?php
// Header
include "partials/header.php";
?>

<?php
// Navigation
include "partials/nav.php";
?>

<?php
// Content
?>
<?php // if(isset($_GET['search']) || isset($_GET['p']) || isset($_GET['borrow']) || isset($_GET['return'])): ?>
<?php if(isset($_GET['search']) || isset($_GET['p'])): ?>
<section  id="content" class="s-main-page" role="main">

  <!-- Search on Front Page
  ============================================= -->
  <div class="s-main-search">
    <?php
    if(isset($_GET['p'])) {
      switch ($_GET['p']) {
      case ''             : $page_title = /*__('Collections')*/ '本のリスト'; break;
      case 'show_detail'  : $page_title = /*__("Record Detail")*/ '本のしょうさい'; break;
      case 'member'       : $page_title = __("Member Area"); break;
      case 'member'       : $page_title = __("Member Area"); break;
      default             : $page_title; break; }
    } else {
      $page_title = /*__('Collections')*/'本のリスト';
    }
    ?>
    <h1 class="s-main-title animated fadeInUp delay1" style="font-size: 14pt;"><?php echo $page_title ?></h1>
    <form action="index.php" method="get" autocomplete="off">
      <input type="text" id="keyword" class="s-search animated fadeInUp delay4" name="keywords" value="" style="font-size: 14pt;" lang="<?php echo $sysconf['default_lang']; ?>" role="search">
      <button type="submit" name="search" value="search" style="font-size: 10pt;" class="s-btn animated fadeInUp delay4"><?php echo __('さがす'); ?></button>
    </form>
    <!-- <a href="#" class="s-search-advances" width="800" height="500" style="font-size: 14pt;"><?php echo __('本をさがす'); ?></a> -->
  </div>

  <!-- Main
  ============================================= -->
  <div class="s-main-content container">
    <div class="row">

      <!-- Show Result
      ============================================= -->
      <div class="col-lg-8 col-sm-9 col-xs-12 animated fadeInUp delay2">

        <?php
          // Generate Output
          // catch empty list
          if(strlen($main_content) == 7) {
            echo '<h2>' . __('ありません') . '</h2><hr/><p>' . __('やりなおしてください') . '</p>';
          } else {
            echo $main_content;
          }

          // Somehow we need to hack the layout
          if(isset($_GET['search']) || (isset($_GET['p']) && $_GET['p'] != 'member')){
            echo '</div>';
          } elseif(isset($_GET['borrow'])){
          	// print_r($dbs);exit;
            include 'borrow_template.php';
            // ini_set('display_errors', 1);
            // ini_set('display_startup_errors', 1);
            // error_reporting(E_ALL);
          	
            if((empty($_GET['id_member'])) OR (trim($_GET['id_member']) == '')){
              echo '<script type="text/javascript">';
              echo 'location.href = "index.php";';
              echo '</script>';
              exit;
            }

            $member_id = $_GET['id_member'];

            $iGetBorrowPageActive = 'inputIsbn';

            if((isset($_GET['borrow_page'])) AND (!empty($_GET['borrow_page']))){
              $iGetBorrowPageActive = $_GET['borrow_page'];
            }

            if(((isset($_GET['doBorrow'])) AND (!empty($_GET['doBorrow'])))){

              if(((isset($_GET['iDoInputIsbn'])) AND (!empty($_GET['iDoInputIsbn'])))){
                // print_r($_GET);exit;

                // ini_set('display_errors', 1);
                // ini_set('display_startup_errors', 1);
                // error_reporting(E_ALL);

                $resultSaveBorrow = doBorrow($dbs,$_GET);
                // print_r($resultSaveBorrow);exit;
                if($resultSaveBorrow == LOAN_NOT_PERMITTED){

                  echo '<script type="text/javascript">';
                  echo 'alert(\''.__('Loan NOT PERMITTED! Membership already EXPIRED!').'\');';
                  echo 'location.href = \'index.php\';';
                  echo '</script>';
                  exit;

                } elseif ($resultSaveBorrow == LOAN_NOT_PERMITTED_PENDING) {
                  
                  echo '<script type="text/javascript">';
                  echo 'alert(\''.__('Loan NOT PERMITTED! Membership under PENDING State!').'\');';
                  echo 'location.href = \'index.php\';';
                  echo '</script>';
                  exit;

                } elseif ($resultSaveBorrow == ITEM_UNAVAILABLE) {
                  
                  echo '<script type="text/javascript">';
                  echo 'alert(\''.__('This Item is currently not available').'\');';
                  echo 'location.href = \'index.php\';';
                  echo '</script>';
                  exit;

                } elseif ($resultSaveBorrow == ITEM_LOAN_FORBID) {
                  
                  echo '<script type="text/javascript">';
                  echo 'alert(\''.__('Loan Forbidden for this Item!').'\');';
                  echo 'location.href = \'index.php\';';
                  echo '</script>';
                  exit;

                } elseif ($resultSaveBorrow == ITEM_NOT_FOUND) {
                  
                  echo '<script type="text/javascript">';
                  echo 'alert(\''.__('This Item is not registered in database').'\');';
                  echo 'location.href = \'index.php\';';
                  echo '</script>';
                  exit;

                } elseif ($resultSaveBorrow == LOAN_LIMIT_REACHED) {
                  
                  echo '<script type="text/javascript">';
                  echo 'alert(\''.__('Loan Limit Reached!').'\');';
                  echo 'location.href = \'index.php\';';
                  echo '</script>';
                  exit;

                } elseif ($resultSaveBorrow == ITEM_SESSION_ADDED) {
                  
                  echo '<script type="text/javascript">';
                  echo 'location.href = "index.php?id_member='.$_GET['id_member'].'&borrow_page=listData&borrow=borrow";';
                  echo '</script>';
                  exit;

                }
                
              }

            } else{

              print_r(show_id_member($dbs,$member_id,$iGetBorrowPageActive));

            }
          	
            echo '</div>';

          } elseif (isset($_GET['return'])) {

            include 'return_template.php';
            
            $item_code = $_GET['item_code'];

            if((empty($_GET['item_code'])) OR (trim($_GET['item_code']) == '')){
              
              echo '<script type="text/javascript">';
              echo 'location.href = "index.php";';
              echo '</script>';
              exit;

            } else {

              $resultReturn = doReturn($dbs,$_GET);
              print_r($resultReturn);

              if($resultReturn == IS_RETURNED){

                echo '<script type="text/javascript">';
                echo 'alert(\''.__('This is item already returned or not exists in loan database').'\');';
                echo 'location.href = \'index.php\';';
                echo '</script>';
                exit;

              } elseif ($resultReturn == RETURNED_SUCCESS) {
                
                echo '<script type="text/javascript">';
                echo "alert('Item ".$_GET['item_code']." is succesfully returned');";
                echo 'location.href = \'index.php\';';
                echo '</script>';
                exit;

              }

            }

            echo '</div>';

          } else {
            
            if(isset($_SESSION['mid'])) {
              
              echo  '</div></div>';

            }

          }

        ?>

      <div class="col-lg-4 col-sm-3 col-xs-12 animated fadeInUp delay4">
        <?php if(isset($_GET['search'])) : ?>
        <h2><?php echo /*__('Search Result')*/'結果'; ?></h2>
        <hr>
        <?php echo $search_result_info; ?>
        <?php endif; ?>

        <br>

        <?php
        if((isset($_GET['borrow_page'])) AND (!empty($_GET['borrow_page']))){
          $iGetBorrowPageActive = $_GET['borrow_page'];
          ?>
          <h2>Menu</h2>
          <a href="index.php?id_member=<?php echo $member_id; ?>&borrow_page=inputIsbn&borrow=borrow" <?php if($iGetBorrowPageActive != 'inputIsbn'){ echo 'style="background: white;border:solid 1px #000000;color:#000000;"'; } ?>>Input Data</a>
          <br>
          <a href="index.php?id_member=<?php echo $member_id; ?>&borrow_page=listData&borrow=borrow" <?php if($iGetBorrowPageActive != 'listData'){ echo 'style="background: white;border:solid 1px #000000;color:#000000;"'; } ?>>List Data</a>
          <br>
          <a href="index.php?id_member=<?php echo $member_id; ?>&borrow_page=historyData&borrow=borrow" <?php if($iGetBorrowPageActive != 'historyData'){ echo 'style="background: white;border:solid 1px #000000;color:#000000;"'; } ?>>History Data</a>
          <?php
        }
        ?>


        <!-- If Member Logged
        ============================================= -->
        <!-- <h2><?php echo /*__('Information')*/'じょうほう'; ?></h2>
        <hr/>
        <p><?php echo (utility::isMemberLogin()) ? $header_info : $info; ?></p>
        <br/> -->

        <!-- Show if clustering search is enabled
        ============================================= -->
        <!-- <?php
          if(isset($_GET['keywords']) && (!empty($_GET['keywords']))) :
            if (($sysconf['enable_search_clustering'])) : ?>
            <h2><?php echo __('Search Cluster'); ?></h2>
 -->
            <hr/>

            <div id="search-cluster">
              <div class="cluster-loading"><?php echo __('Generating search cluster...');  ?></div>
            </div>

            <script type="text/javascript">
              $('document').ready( function() {
                $.ajax({
                  url     : 'index.php?p=clustering&q=<?php echo urlencode($criteria); ?>',
                  type    : 'GET',
                  success : function(data, status, jqXHR) { $('#search-cluster').html(data); }
                });
              });
            </script>

            <?php endif; ?>
          <?php endif; ?>
      </div>
    </div>
  </div>

</section>

<?php else: ?>

<!-- Homepage
============================================= -->
<main id="content" class="s-main" role="main">

    <!-- Search form
    ============================================= -->
    <div class="s-main-search animated fadeInUp delay1">

      <?php /*<div id="simply-search">

        <form action="index.php" method="get" autocomplete="off">
          <h1 class="animated fadeInUp delay2"><?php echo __('SEARCH'); ?></h1>
          <div class="marquee down">
            <p class="s-search-info">
            <?php echo __('start it by typing one or more keywords for title, author or subject'); ?>
            </p>
            <input type="text" class="s-search animated fadeInUp delay4" id="keyword" name="keywords" value="" lang="<?php echo $sysconf['default_lang']; ?>" aria-hidden="true" autocomplete="off">
            <button type="submit" name="search" value="search" class="s-btn animated fadeInUp delay4"><?php echo __('Search'); ?></button>
          </div>
        </form>

        <a href="#" class="s-search-advances" title="<?php echo __('Advanced Search') ?>"><?php echo __('Advanced Search') ?></a>

      </div> */ ?>

	  	<div class="row" style="top: 50%;left: 50%;">
			
			<center>
				<div style="margin-left:30%;">
					<a href="#" class="s-search-borrow">
            <div class="menu-borrow">
              <i class="fa fa-book fa-5x" style="color:black;margin-top: 30%;" aria-hidden="true"></i>
              <br><br><br><br><h3 style="color:white; font-weight: bold; font-size: 40pt;">かりる</h3>
            </div>
          </a>
          <a href="#" class="s-search-return">
            <div class="menu-return" style="margin-right: 30px;">
              <i class="fa fa-refresh fa-5x" style="color:black;margin-top: 30%;" aria-hidden="true"></i>
              <br><br><br><br><h3 style="color:white; font-weight: bold; font-size: 40pt;">かえす</h3>
            </div>
          </a>
          <a href="#" class="s-search-advances">
            <div class="menu-return">
              <i class="fa fa-search fa-5x" style="color:black;margin-top: 30%;" aria-hidden="true"></i>
              <br><br><br><br><h3 style="color:white; font-weight: bold; font-size: 40pt;">さがす</h3>
            </div>
          </a> 
					
				</div>
			</center>
	  	</div>

    </div>

</main>
<?php endif; ?>


<?php

// Advance Search
include "partials/advsearch.php";

// Advance Borrow
include "partials/advborrow.php";

// Advance Return
include "partials/advreturn.php";

// Footer
include "partials/footer.php";

// Chat Engine
include LIB."contents/chat.php";

// Background
include "partials/bg.php";
?>

<script>
  <?php if(isset($_GET['search']) && (isset($_GET['keywords'])) && ($_GET['keywords'] != ''))   : ?>
  $('.biblioRecord .detail-list, .biblioRecord .title, .biblioRecord .abstract, .biblioRecord .controls').highlight(<?php echo $searched_words_js_array; ?>);
  <?php endif; ?>

  //Replace blank cover
  $('.book img').error(function(){
    var title = $(this).parent().attr('title').split(' ');
    $(this).parent().append('<div class="s-feature-title">' + title[0] + '<br/>' + title[1] + '<br/>... </div>');
    $(this).attr({
      src   : './template/default/img/book.png',
      title : title + title[0] + ' ' + title[1]
    });
  });

  //Replace blank photo
  $('.librarian-image img').error(function(){
    $(this).attr('src','./template/default/img/avatar.jpg');
  });

  //Feature list slider
  function mycarousel_initCallback(carousel)
  {
    // Disable autoscrolling if the user clicks the prev or next button.
    carousel.buttonNext.bind('click', function() {
      carousel.startAuto(0);
    });

    carousel.buttonPrev.bind('click', function() {
      carousel.startAuto(0);
    });

    // Pause autoscrolling if the user moves with the cursor over the clip.
    carousel.clip.hover(function() {
      carousel.stopAuto();
    }, function() {
      carousel.startAuto();
    });
  };

  jQuery('#topbook').jcarousel({
      auto: 5,
      wrap: 'last',
      initCallback: mycarousel_initCallback
  });

  $(window).scroll(function() {
    // console.log($(window).scrollTop());
    if ($(window).scrollTop() > 50) {
      $('.s-main-search').removeClass("animated fadeIn").addClass("animated fadeOut");
    } else {
      $('.s-main-search').removeClass("animated fadeOut").addClass("animated fadeIn");
    }
    
  });

  $('.s-search-advances').click(function() {
    $('#advance-search').animate({opacity : 1,}, 500, 'linear');
    $('#simply-search, .s-menu, #content').hide();
    $('.s-header').addClass('hide-header');
    $('.s-background').addClass('hide-background');
	$('#advance-borrow').hide();
	$('#advance-return').hide();
  $('#advTitle').focus();
  });

  $('#hide-advance-search').click(function(){
    $('.s-header').toggleClass('hide-header');
    $('.s-background').toggleClass('hide-background');
    $('#advance-search').animate({opacity : 0,}, 500, 'linear', function(){
		$('#simply-search, .s-menu, #content').show();
		$('#advance-search').show();
		$('#advance-borrow').show();
		$('#advance-return').show();
    });
  });

  $('.s-search-borrow').click(function() {
    $('#advance-borrow').animate({opacity : 1,}, 500, 'linear');
    $('#simply-search, .s-menu, #content').hide();
    $('.s-header').remove();
    $('.s-background').remove();
    $('#advance-search').hide();
	$('#advance-return').hide();
  $('#memberID').focus();
  });

  $('#hide-advance-borrow').click(function(){
    $('.s-header').toggleClass('hide-header');
    $('.s-background').toggleClass('hide-background');
    $('#advance-borrow').animate({opacity : 0,}, 500, 'linear', function(){
		$('#simply-search, .s-menu, #content').show();
		$('#advance-search').show();
		$('#advance-borrow').show();
		$('#advance-return').show();
    });
  });

  $('.s-search-return').click(function() {
    $('#advance-return').animate({opacity : 1,}, 500, 'linear');
    $('#simply-search, .s-menu, #content').hide();
    $('.s-header').remove();
    $('.s-background').remove();
    $('#advance-search').hide();
	$('#advance-borrow').hide();
  $('#quickReturnID').focus();
  });

  $('#hide-advance-return').click(function(){
    $('.s-header').toggleClass('hide-header');
    $('.s-background').toggleClass('hide-background');
    $('#advance-return').animate({opacity : 0,}, 500, 'linear', function(){
		$('#simply-search, .s-menu, #content').show();
		$('#advance-search').show();
		$('#advance-borrow').show();
		$('#advance-return').show();
    });
    window.location.href = 'index.php';
  });

  $('#submitMemberID').click(function(){
    var memberID = $('#memberID').val();
  	$.ajax({
  		url:'member-borrow.php',
  		type: 'POST',
  		data: {'memberID':memberID},
  		async: true,
  		success:function(response){
  			$('#contentBorrow').html(response);
  			console.log(response);
  		}
  	});

  });

  $('#submitQuickReturnID').click(function(){
    var quickReturnID = $('#quickReturnID').val();
    $.ajax({
      url:'member-return.php',
      type: 'POST',
      data: {'quickReturnID':quickReturnID},
      async: true,
      success:function(response){
        $('#contentReturn').html(response);
        $('#quickReturnID').val('');
        $('#quickReturnID').focus();
        console.log(response);
      }
    });

  });

  function borrowProcess(process_type,value){
  	$.ajax({
		url:'borrow-process.php',
		type: 'POST',
		data: {'process_type':process_type,'value':value},
		async: true,
		success:function(response){
			window.location.href = 'index.php';
			/*$('#contentBorrow').html(response);
			console.log(response);*/
		}
	});
  }

  function borrowAdd(process_type,value,memberID){
  	$.ajax({
		url:'borrow-add.php',
		type: 'POST',
		data: {'process_type':process_type,'value':value,'tempLoanID':value,'memberID':memberID},
		async: true,
		success:function(response){
			/*window.location.href = 'index.php';*/
			$('#borrowPageContent').html(response);
			console.log(response);
		}
	});
  }

</script>

</body>
</html>
