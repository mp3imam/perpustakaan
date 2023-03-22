<div class="s-detail animated delay9 fadeInUp" itemscope itemtype="http://schema.org/Book" vocab="http://schema.org/" typeof="Book">
  
  <!-- Book Cover
  ============================================= -->
  <div class="cover">
    <?php echo $image ?>
  </div>
  
  <!-- Title 
  ============================================= -->
  <!-- <h3 class="s-detail-type"><?php echo $gmd_name ?></h3> -->
  <h1 class="s-detail-title" itemprop="name" property="name"><?php echo $title ?></h1>
  <?php if($sysconf['social_shares']) { echo $social_shares; } ?>
  
  <div class="s-detail-author" itemprop="author" property="author" itemscope itemtype="http://schema.org/Person">
  <h1><?php echo  $authors ?></h1>
  <br>
  </div>

  <!-- Abstract 
  ============================================= -->
  <hr>
  <?php if($notes) : ?>
    <div class="s-detail-abstract" itemprop="description" property="description">
      <p><?php echo $notes ?></p>
      <hr/>
    </div>
    <?php else : ?>
    <!-- <div>
      <em><?php echo /*__('Description Not Available')*/'ないようのせつめいはありません'; ?></em>
      <br><br><br>
    </div> -->
  <?php endif; ?>

  <!-- Availability
  ============================================= -->  
  <i class="fa fa-check-circle-o"></i> <?php echo /*__('Availability')*/'かしだしじょうきょう'; ?>
  <?php echo ($availability) ? $availability : '<p class="s-alert">'.__('No copy data').'</p>'; ?>
  <!-- Item Details
  ============================================= -->  
  <h3><i class="fa fa-circle-o"></i> <?php echo /*__('Detail Information')*/'その他'; ?></h3>
  <div class="row">
  <div class="col-lg-12">  
  <table class="s-table">
    <tbody>      
      <!-- ============================================= -->  
      <!-- <tr>
        <th><?php echo /*__('Series Title')*/'シリーズタイトル'; ?></th>
        <td>
          <div itemprop="alternativeHeadline" property="alternativeHeadline"><?php echo ($series_title) ? $series_title : '-'; ?></div>
        </td>
      </tr> -->
      <!-- ============================================= -->  
    <!--   <tr>
        <th><?php echo /*__('Call Number')*/'本だなのばしょ'; ?></th>
        <td>
          <div><?php echo ($call_number) ? $call_number : '-'; ?></div>
        </td>
      </tr> -->
      <!-- ============================================= -->  
      <tr>
        <th><?php echo /*__('Publisher')*/'しゅっぱんしゃ'; ?></th>
        <td>
          <span itemprop="publisher" property="publisher" itemtype="http://schema.org/Organization" itemscope><?php echo $publisher_name ?></span>
          <!-- <span itemprop="publisher" property="publisher"><?php echo $publish_place ?></span>.,
          <span itemprop="datePublished" property="datePublished"><?php echo $publish_year ?></span> -->
        </td>
      </tr>
      <!-- ============================================= -->  
      <!-- <tr>
        <th><?php echo /*__('Collation')*/'判型、ページ数、等'; ?></th>
        <td>
          <div itemprop="numberOfPages" property="numberOfPages"><?php echo ($collation) ? $collation : '-'; ?></div>
        </td>
      </tr> -->
      <!-- ============================================= -->  
      <!-- <tr>
        <th><?php echo /*__('Language')*/'ことば'; ?></th>
        <td>
          <div><meta itemprop="inLanguage" property="inLanguage" content="<?php echo $language_name ?>" /><?php echo $language_name ?></div>
        </td>
      </tr> -->
      <!-- ============================================= -->  
      <!-- <tr>
        <th><?php echo /*__('ISBN/ISSN')*/'ISBN　コード'; ?></th>
        <td>
          <div itemprop="isbn" property="isbn"><?php echo ($isbn_issn) ? $isbn_issn : '-'; ?></div>
        </td>
      </tr> -->
      <!-- ============================================= -->  
      <!-- <tr>  
        <th><?php echo /*__('Classification')*/'分類'; ?></th>
        <td>
          <div><?php echo ($classification) ? $classification : '-'; ?></div>
        </td>
      </tr> -->
      <!-- ============================================= -->  
      <!-- <tr>
        <th><?php echo /*__('Content Type')*/'内容タイプ'; ?></th>
        <td>
          <div itemprop="bookFormat" property="bookFormat"><?php echo ($content_type) ? $content_type : '-'; ?></div>
        </td>
      </tr> -->
      <!-- ============================================= -->  
    </tbody>
  </table>
  </div>
  <div class="col-lg-6">
  <table class="s-table">
    <tbody>    
      <!-- ============================================= -->  
      <!-- <tr>
        <th><?php echo /*__('Media Type')*/'メディアタイプ'; ?></th>
        <td>
          <div itemprop="bookFormat" property="bookFormat"><?php echo ($media_type) ? $media_type : '-'; ?></div>
        </td>
      </tr> -->
      <!-- ============================================= -->  
      <!-- <tr>
        <th><?php echo /*__('Carrier Type')*/'？タイプ'; ?></th>
        <td>
        <div itemprop="bookFormat" property="bookFormat"><?php echo ($carrier_type) ? $carrier_type : '-'; ?></div>
        </td>
      </tr> -->
      <!-- ============================================= -->  
      <!-- <tr>
        <th><?php echo /*__('Edition')*/'版'; ?></th>
        <td>
          <div itemprop="bookEdition" property="bookEdition"><?php echo ($edition) ? $edition : '-'; ?></div>
        </td>
      </tr> -->
      <!-- ============================================= -->  
      <!-- <tr>
        <th><?php echo /*__('Subject(s)')*/'主題'; ?></th>
        <td>
          <div class="s-subject" itemprop="keywords" property="keywords"><?php echo ($subjects) ? $subjects : '-'; ?></div>
        </td>
      </tr> -->
      <!-- ============================================= -->  
      <!-- <tr>
        <th><?php echo /*__('Specific Detail Info')*/'特定詳細'; ?></th>
        <td>
          <div><?php echo ($spec_detail_info) ? $spec_detail_info : '-'; ?></div>
        </td>
      </tr> -->
      <!-- ============================================= -->  
      <!-- <tr>
        <th><?php echo /*__('Statement of Responsibility')*/'責任証明'; ?></th>
        <td>
          <div itemprop="author" property="author"><?php echo ($sor) ? $sor : '-';  ?></div>
        </td>
      </tr>     -->
      <!-- ============================================= -->  
    </tbody>
  </table>
  </div>
  </div>

  <!-- Related biblio data
  ============================================= -->  
  <!-- <h3><i class="fa fa-circle-o"></i> <?php echo /*__('Other version/related')*/'その他'; ?></h3>
  <?php echo ($related) ? $related : '<p class="s-alert">ほかのバージョンはありません</p>'; ?>
  <br>

  <?php if ($file_att) : ?>
  <!-- Attachment -->
  ============================================= -->  
  <!-- <h3><i class="fa fa-arrow-circle-o-down"></i> <?php echo __('File Attachment'); ?></h3>
  <div itemprop="associatedMedia">
    <div class="s-download">
      <?php echo $file_att; ?>
    </div> 
  </div>
  <?php endif; ?> -->

  <!-- Comment
  ============================================= -->  
  <!-- <?php if(isset($_SESSION['mid']) && $sysconf['comment']['enable']) : ?>
  <h3><i class="fa fa-comments-o"></i> <?php echo __('Comments'); ?></h3>
  <?php echo showComment($biblio_id); ?>
  <?php endif; ?> -->

</div>