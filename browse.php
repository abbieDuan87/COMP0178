<?php include_once("header.php") ?>
<?php require("utilities.php") ?>
<?php require("database.php") ?>

<?php
$connection = get_connection();
?>

<div class="container">

  <h2 class="my-3">Browse listings</h2>

  <div id="searchSpecs">
    <!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
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
  </div> <!-- end search specs bar -->


</div>

<?php
// Retrieve these from the URL
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : "";
$category = isset($_GET['cat']) ? $_GET['cat'] : "0";
$ordering = isset($_GET['order_by']) ? $_GET['order_by'] : "pricelow";
$curr_page = isset($_GET['page']) & !empty($_GET['page'])? $_GET['page'] : 1;
$active_only = isset($_GET['active_only']) ? $_GET['active_only'] : ""; 

// Base query
$auction_list_sql = "SELECT auctions.*, COUNT(bids.bidID) AS bidCount,
        COALESCE(MAX(bids.bidPrice), auctions.startingPrice) AS currentPrice
        FROM auctions
        LEFT JOIN bids ON auctions.auctionID = bids.auctionID ";

// Initialise query parts and parameters array
$filters = [];
$params = [];
$types = "";

// Category filter
if ($category != "0") {
  // $auction_list_sql .= "WHERE auctions.categoryID = {$category} ";
  $filters[] = "auctions.categoryID = ?";
  $params[] = $category;
  $types .= "i"; // integer
}

// keyword filter - Search
if (!empty($keyword)) {
  $filters[] = "(auctions.title LIKE ? OR auctions.description LIKE ?)";
  $keyword_param = "%" . sanitise_user_input($keyword) . "%";
  $params[] = $keyword_param;
  $params[] = $keyword_param;
  $types .= "ss"; // string 
}

if (!empty($active_only) && $active_only == "1") {
  $filters[] = "(endDate >= NOW() AND auctionStatus = TRUE)";
}

// Append WHERE clause if filters exist
if (!empty($filters)) {
  $auction_list_sql .= " WHERE " . implode(" AND ", $filters);
}

// Add GROUP BY and ORDER BY
$auction_list_sql .= " GROUP BY auctions.auctionID";

if ($ordering === 'pricehigh') {
  $auction_list_sql .= " ORDER BY currentPrice DESC";
} else if ($ordering === 'pricelow') {
  $auction_list_sql .= " ORDER BY currentPrice";
} else if ($ordering === 'date') {
  $auction_list_sql .= " ORDER BY endDate";
}

// Variables for pagination
$results_per_page = 6;
$offset = ($curr_page - 1) * $results_per_page;

// Query the total number of filtered results
$count_sql = "SELECT COUNT(DISTINCT auctions.auctionID) AS total
              FROM auctions
              LEFT JOIN bids on auctions.auctionID = bids.auctionID";
if (!empty($filters)) {
  $count_sql .= " WHERE " . implode(" AND ", $filters);
}

$count_result = execute_prepared_stmt_query($connection, $count_sql, $params, $types);
$num_results = mysqli_fetch_assoc($count_result)['total'];
$max_page = ceil($num_results / $results_per_page);

// Query the filtered auction list with limit and offset
$auction_list_sql .= " LIMIT ? OFFSET ?"; // return only [LIMIT] results, starting from [OFFSET + 1]
$params[] = $results_per_page;
$params[] = $offset;
$types .= 'ii'; // integer

$auction_list_result = execute_prepared_stmt_query($connection, $auction_list_sql, $params, $types);

?>

<div class="container mt-5">

  <?php
  if (mysqli_num_rows($auction_list_result) == 0) {
    echo "<p class='text-center font-weight-light'>No result.</p>";
  }
  ?>

  <ul class="list-group">

    <?php
    while ($row = mysqli_fetch_assoc($auction_list_result)) {
      print_listing_li(
        $row['auctionID'],
        $row['title'],
        $row['description'],
        $row['startingPrice'],
        $row['currentPrice'],
        $row['bidCount'],
        new DateTime($row['endDate'])
      );
    }
    ?>

  </ul>

  <!-- Pagination for results listings -->
  <nav aria-label="Search results pages" class="mt-5">
    <ul class="pagination justify-content-center">

      <?php
      pagination($curr_page, $max_page);
      ?>

    </ul>
  </nav>


</div>

<?php close_connection($connection); ?>
<?php include_once("footer.php") ?>