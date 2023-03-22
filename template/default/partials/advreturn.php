<div id="advance-return" class="container">

  <div class="hamburger hamburger--3dy is-active" id="hide-advance-return" role="navigation">
    <div class="hamburger-box">
      <div class="hamburger-inner"></div>
    </div>
  </div>


  <h2 style="font-size: 20pt;">かえす</h2>
  <div class="row">
    <form action="index.php" method="get" class="form-horizontal form-search" onsubmit="return false;">

      <div class="col-sm-12">
        <div class="control-group">
          <label class="label" style="font-size: 20pt;">ISBN/バーコードをスキャンして下さい</label>
          <div class="controls">
            <input type="text" name="quickReturnID" id="quickReturnID" class="form-control" style="color:black;" />
          </div>
        </div>
      </div>

      <div class="clearfix"></div>
      <div class="col-sm-12">
        <div class="control-group">
          <label></label>
          <div class="controls">
            <button type="submit" name="return" value="return" style="font-size: 20pt;" clas="btn btn-danger btn-block" id="submitQuickReturnID">かえす</button>
            <button type="submit" style="font-size: 20pt; position: absolute;right: 0;" name="borrow" value="borrow" clas="btn btn-danger btn-block" onclick="location.href='index.php';">ホーム</button>
          </div>
        </div>
      </div>

    </form>
  </div>
  <br>
  <div class="row" id="contentReturn"></div>
</div>
