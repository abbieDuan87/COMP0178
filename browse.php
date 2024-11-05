<?php
include_once("header.php");
require("utilities.php");
require("database.php");

$connection = get_connection();

// Display success message if it exists
if (isset($_SESSION['success_message'])) {
  echo '<div class="alert alert-success text-center">';
  echo $_SESSION['success_message'];
  echo '</div>';
  unset($_SESSION['success_message']);
}
?>

<!-- the search bar -->
<div class="container">
  <h2 class="my-3">Browse listings</h2>
  <?php include("search_form.php") ?> 
</div>

<?php
// Retrieve these from the URL
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : "";
$category = isset($_GET['cat']) ? $_GET['cat'] : "0";
$ordering = isset($_GET['order_by']) ? $_GET['order_by'] : "pricelow";
$curr_page = isset($_GET['page']) & !empty($_GET['page']) ? $_GET['page'] : 1;
$active_only = isset($_GET['active_only']) ? $_GET['active_only'] : "";

// Variables for pagination
$results_per_page = 6;
$offset = ($curr_page - 1) * $results_per_page;

// Build and query the total number of results without pagination
list($count_sql, $count_params, $count_types) = build_auction_query($keyword, $category, $ordering, $active_only, true);
$count_result = execute_prepared_stmt_query($connection, $count_sql, $count_params, $count_types);
$num_results = mysqli_fetch_assoc($count_result)['total'];
$max_page = ceil($num_results / $results_per_page);

// Build query the filtered auction list with limit and offset
list($auction_list_sql, $params, $types) = build_auction_query($keyword, $category, $ordering, $active_only);
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