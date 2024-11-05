<!-- This page is for showing a user the auction listings they've made. -->
<?php include_once("header.php") ?>
<?php require("utilities.php") ?>
<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include("database.php");
?>

<div class="container">

  <h2 class="my-3">My listings</h2>

  <?php
  $seller_id = null;
  $curr_page = isset($_GET['page']) & !empty($_GET['page']) ? $_GET['page'] : 1;

  // Check user's credentials (cookie/session).
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    if ($_SESSION['account_type'] == "seller") {
      $seller_id = $_SESSION['user_id'];
    } else {
      echo "You don't have access to this page";
      exit();
    }
  }

  // Perform a query to pull up their auctions.
  $conn = get_connection();
  $query = "SELECT auctions.*, COUNT(bids.bidID) AS bidCount,
    COALESCE(MAX(bids.bidPrice), auctions.startingPrice) AS currentPrice
    FROM auctions
    LEFT JOIN bids ON auctions.auctionID = bids.auctionID
    WHERE auctions.sellerID = $seller_id
    GROUP BY auctions.auctionID";

  $count_sql = "SELECT COUNT(DISTINCT auctions.auctionID) AS total
              FROM auctions
              LEFT JOIN bids on auctions.auctionID = bids.auctionID
              WHERE auctions.sellerID = $seller_id";
  
  // Variables for pagination
  $results_per_page = 6;
  $offset = ($curr_page - 1) * $results_per_page;

  $count_result = execute_query($conn, $count_sql);
  $num_results = mysqli_fetch_assoc($count_result)['total'];
  $max_page = ceil($num_results / $results_per_page);
  ?>

  <ul class="list-group">
    <?php
    // Loop through results and print them out as list items.
    $auction_result = execute_query($conn, $query);
    if (mysqli_num_rows($auction_result) > 0) {
      while ($row = mysqli_fetch_assoc($auction_result)) {
        print_listing_li(
          $row['auctionID'],
          $row['title'],
          $row['currentPrice'],
          $row['bidCount'],
          new DateTime($row['endDate'])
        );
      }
    } else {
      echo "<p class='text-center font-weight-light'>No auction.</p>";
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

  <?php include_once("footer.php") ?>