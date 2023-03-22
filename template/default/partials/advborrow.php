<div id="advance-borrow" class="container">

  <div class="hamburger hamburger--3dy is-active" id="hide-advance-borrow" role="navigation">
    <div class="hamburger-box">
      <div class="hamburger-inner"></div>
    </div>
  </div>


  <h2 style="font-size: 20pt; line-height: 0.3">かりる</h2>
  <div class="row" id="contentBorrow">
    <form action="index.php" method="get" class="form-horizontal form-search" onsubmit="return false;">

      <div class="col-sm-12" style="margin-top: 20px;">
        <div class="control-group">
          <label style="font-size: 20pt;" class="label">メンバーカードをスキャンしてください</label>
          <div class="controls">
            <input type="text" name="memberID" id="memberID" class="form-control" style="color:black;"/>
          </div>
        </div>
      </div>


      <div class="clearfix"></div>
      <div class="col-sm-12">
        <div class="control-group">
          <label></label>
          <div class="controls">
            <button type="submit" style="font-size: 20pt;" name="borrow" value="borrow" clas="btn btn-danger btn-block" id="submitMemberID">かりる</button>
            <button type="submit" style="font-size: 20pt; position: absolute;right: 0;" name="borrow" value="borrow" clas="btn btn-danger btn-block" onclick="location.href='index.php';">ホーム</button>
          </div>
          <div>
          </div>
        </div>
      </div>

    </form>
  </div>
</div>