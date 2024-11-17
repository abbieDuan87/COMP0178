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
      echo "<p class='text-center font-weight-light'>Oops! It seems that you do not have permission to access this page. Please contact support if you believe this is a mistake.</p>";
      exit();
    }
  } else {
    echo "<p class='text-center font-weight-light'>Hi there! It looks like you need to log in to view this page.<br>You will be redirected to the browse page shortly. </p>";
    header("refresh:5;url=browse.php");
    exit();
  }

  // Perform a query to pull up their auctions.
  $conn = get_connection();
  $query = "SELECT auctions.*, COUNT(bids.bidID) AS bidCount,
    COALESCE(MAX(bids.bidPrice), auctions.startingPrice) AS currentPrice,
    CASE 
           WHEN NOW() < auctions.endDate THEN 'Open'
           WHEN NOW() >= Auctions.endDate AND COUNT(Bids.bidID) = 0 THEN 'Closed - No bids'
           WHEN NOW() >= auctions.endDate AND COALESCE(MAX(bids.bidPrice), 0) >= auctions.reservePrice THEN 'Sold'
           WHEN NOW() >= auctions.endDate AND COALESCE(MAX(bids.bidPrice), 0) < auctions.reservePrice THEN 'Reserve not met'
       END AS auctionStatus
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
        print_my_listings_li(
          $row['auctionID'],
          $row['title'],
          $row['currentPrice'],
          $row['bidCount'],
          new DateTime($row['endDate']),
          $row['auctionStatus']
        );
      }
    } else {
      echo "<p class='text-center font-weight-light'>
      No listings found at the moment.</p>";
      echo "<div class='text-center'>
      <a href='create_auction.php' class='btn btn-outline-primary btn-sm'>Create an Auction</a>
      </div>";
    }
    ?>
  </ul>

  <!-- Pagination for results listings -->
  <nav aria-label="Search results pages" class="mt-5">
    <ul class="pagination justify-content-center">

      <?php
      pagination($curr_page, $max_page, "mylistings.php");
      ?>

    </ul>
  </nav>

  <?php include_once("footer.php") ?>