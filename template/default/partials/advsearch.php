<div id="advance-search" class="container">

<div class="hamburger hamburger--3dy is-active" id="hide-advance-search" role="navigation">
  <div class="hamburger-box">
    <div class="hamburger-inner"></div>
  </div>
</div>


<h2 style="font-size: 20pt;">本をさがす</h2>
<div class="row">
  <form action="index.php" method="get" class="form-horizontal form-search">

    <div class="col-sm-6">
      <div class="control-group">
        <label class="label" style="font-size: 20pt;">本・DVDのなまえ</label>
        <div class="controls">
          <input type="text" name="title" id="advTitle" class="form-control" style="color:black;"/>
        </div>
      </div>
    </div>

    <div class="col-sm-6">
      <div class="control-group">
        <label class="label" style="font-size: 20pt;">作者</label>
        <div class="controls">
          <input type="text" name="author" class="form-control" style="color:black;"/>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-sm-6">
      <div class="control-group">
        <label></label>
        <div class="controls">
          <input type="hidden" name="searchtype" value="advance" />
          <button type="submit" name="search" value="search" style="font-size: 20pt;" clas="btn btn-danger btn-block">さがす</button>
        </div>
      </div>
    </div>

  </form>
</div>
</div>
