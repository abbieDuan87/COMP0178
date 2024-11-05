<div id="searchSpecs">
    <form method="get" action="browse.php">
      <div class="row">
        <div class="col-md-4 pr-0">
          <div class="form-group">
            <label for="keyword" class="sr-only">Search keyword:</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-transparent pr-0 text-muted">
                  <i class="fa fa-search"></i>
                </span>
              </div>
              <input type="text" class="form-control border-left-0" id="keyword" placeholder="Search for anything" name="keyword">
            </div>
          </div>
        </div>
        <div class="col-md-3 pr-0">
          <div class="form-group">
            <label for="cat" class="sr-only">Search within:</label>
            <select class="form-control" id="cat" name="cat">
              <option selected value="0">All categories</option>
              <?php
              $category_sql = "SELECT * FROM categories";
              $category_result = execute_query($connection, $category_sql);
              if (mysqli_num_rows($category_result) > 0) {
                while ($row = mysqli_fetch_assoc($category_result)) {
                  $category_id = $row['categoryID'];
                  $category_name = $row['name'];
                  echo "<option value='{$category_id}'>{$category_name}</option>";
                }
              }
              ?>
            </select>
          </div>
        </div>
        <div class="col-md-3 pr-0">
          <div class="form-inline">
            <label class="mx-2" for="order_by">Sort by:</label>
            <select class="form-control" id="order_by" name="order_by">
              <option selected value="pricelow">Price (low to high)</option>
              <option value="pricehigh">Price (high to low)</option>
              <option value="date">Soonest expiry</option>
            </select>
          </div>
        </div>
        <div class="col-md-1">Active only <input type="checkbox" id="active_only" name="active_only" value="1"></div>
        <div class="col-md-1 px-0">
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </div>
    </form>
  </div>