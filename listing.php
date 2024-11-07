<?php include_once("header.php") ?>
<?php require("utilities.php") ?>
<?php require("database.php") ?>

<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Display success message if it exists
if (isset($_SESSION['success_message']) || isset($_SESSION['warning_message'])) {
  $alertType = isset($_SESSION['success_message']) ? 'alert-success' : 'alert-warning';
  $message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : $_SESSION['warning_message'];

  echo "<div class='alert $alertType text-center'>$message</div>";

  unset($_SESSION['success_message'], $_SESSION['warning_message']);
}

$connection = get_connection();

// Get info from the URL:
$item_id = $_GET['item_id'];

// Query the auction info of the corresponding id
$auction_info_query = " SELECT 
              Auctions.title, 
              Auctions.description, 
              Auctions.startingPrice, 
              COALESCE(MAX(Bids.bidPrice), Auctions.startingPrice) AS currentPrice,
              Auctions.createdDate,
              Auctions.endDate, 
              Auctions.itemCondition, 
              COUNT(Bids.bidID) AS num_bids
            FROM 
              Auctions
            LEFT JOIN 
              Bids ON Auctions.auctionID = Bids.auctionID
            WHERE 
              Auctions.auctionID = $item_id
            GROUP BY 
              Auctions.auctionID ";

$auction_info_result = execute_query($connection, $auction_info_query);

// Get all the auction information we need to display
if ($row = mysqli_fetch_assoc($auction_info_result)) {
  $title = $row['title'];
  $description = $row['description'];
  $current_price = $row['currentPrice'];
  $starting_price = $row['startingPrice'];
  $num_bids = $row['num_bids'];
  $created_date = $row['createdDate'];
  $end_time = new DateTime($row['endDate']);
  $item_condition = $row['itemCondition'];
} else {
  echo "No auction found with this ID.";
  exit();
}

// Calculate end date
$now = new DateTime();
if ($now < $end_time) {
  $time_to_end = date_diff($now, $end_time);
  $time_remaining = ' (in ' . display_time_remaining($time_to_end) . ')';
}

$has_session = session_status() == PHP_SESSION_ACTIVE ? true : false;
$watching = false; // later determined this by using query.

?>

<div class="container">

  <div class="row"> <!-- Row #1 with auction title + watch button -->
    <div class="col-sm-8"> <!-- Left col -->
      <h2 class="my-3"><?php echo ($title); ?></h2>
    </div>
    <div class="col-sm-4 align-self-center"> <!-- Right col -->
      <?php
      /* The following watchlist functionality uses JavaScript, but could
     just as easily use PHP as in other places in the code */
      if ($now < $end_time):
      ?>
        <div id="watch_nowatch" <?php if ($has_session && $watching) echo ('style="display: none"'); ?>>
          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">+ Add to watchlist</button>
        </div>
        <div id="watch_watching" <?php if (!$has_session || !$watching) echo ('style="display: none"'); ?>>
          <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
          <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">Remove watch</button>
        </div>
      <?php endif /* Print nothing otherwise */ ?>
    </div>
  </div>

  <div class="row"> <!-- Row #2 with auction description + bidding info -->
    <div class="col-sm-8"> <!-- Left col with item info -->
      <div>
        <img src="https://placehold.co/600x500" class="img-fluid rounded" alt="example image" style="max-width: 100%; max-height: 400px; width: auto; height: auto;">
      </div>

    </div>

    <div class="col-sm-4"> <!-- Right col with bidding info -->

      <h3 class="font-weight-bold"><?php echo format_price($current_price) ?></h3>

      <a class="mb-1 text-body" href="#bid-history-table"><?php echo $num_bids . ($num_bids <= 1 ? ' bid' : ' bids'); ?></a>

      <p class="text-secondary">
        <?php if ($now > $end_time): ?>
          This auction ended <?php echo (date_format($end_time, 'j M H:i')) ?>
          <!-- TODO: Print the result of the auction here? -->
        <?php else: ?>
          Ends in <span><?php echo display_time_remaining($time_to_end); ?></span> (<?php echo date_format($end_time, 'j M H:i'); ?>)<br>
      </p>

      <p>Condition: <span class="font-weight-bold"><?php echo ucfirst($item_condition); ?></span></p>

      <!-- Bidding form -->
      <?php display_bid_form($item_id); ?>
    <?php endif ?>


    </div> <!-- End of right col with bidding info -->
  </div> <!-- End of row #2 -->

  <!-- description -->
  <div class="card mt-4">
    <div class="card-header font-weight-bold bg-light">
      About this item
    </div>
    <div class="card-body">
      <p class="card-text"><?php echo ($description); ?></p>
    </div>
  </div>

  <!-- show bid history -->
  <?php
  $sql_bid_history = "SELECT bids.*, users.username 
                      FROM bids
                      JOIN users ON users.userID = bids.buyerID
                      WHERE bids.auctionID = $item_id
                      ORDER BY bids.bidDate DESC, bids.bidPrice DESC";

  $bid_history_result = execute_query($connection, $sql_bid_history);
  ?>
  <div class="mt-3 mb-5" id="bid-history-table">
    <?php render_bid_history_table($bid_history_result, $starting_price, $created_date) ?>
  </div>


  <?php close_connection($connection); ?>
  <?php include_once("footer.php") ?>


  <script>
    // JavaScript functions: addToWatchlist and removeFromWatchlist.

    function addToWatchlist(button) {
      console.log("These print statements are helpful for debugging btw");

      // This performs an asynchronous call to a PHP function using POST method.
      // Sends item ID as an argument to that function.
      $.ajax('watchlist_funcs.php', {
        type: "POST",
        data: {
          functionname: 'add_to_watchlist',
          arguments: [<?php echo ($item_id); ?>]
        },

        success: function(obj, textstatus) {
          // Callback function for when call is successful and returns obj
          console.log("Success");
          var objT = obj.trim();

          if (objT == "success") {
            $("#watch_nowatch").hide();
            $("#watch_watching").show();
          } else {
            var mydiv = document.getElementById("watch_nowatch");
            mydiv.appendChild(document.createElement("br"));
            mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
          }
        },

        error: function(obj, textstatus) {
          console.log("Error");
        }
      }); // End of AJAX call

    } // End of addToWatchlist func

    function removeFromWatchlist(button) {
      // This performs an asynchronous call to a PHP function using POST method.
      // Sends item ID as an argument to that function.
      $.ajax('watchlist_funcs.php', {
        type: "POST",
        data: {
          functionname: 'remove_from_watchlist',
          arguments: [<?php echo ($item_id); ?>]
        },

        success: function(obj, textstatus) {
          // Callback function for when call is successful and returns obj
          console.log("Success");
          var objT = obj.trim();

          if (objT == "success") {
            $("#watch_watching").hide();
            $("#watch_nowatch").show();
          } else {
            var mydiv = document.getElementById("watch_watching");
            mydiv.appendChild(document.createElement("br"));
            mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
          }
        },

        error: function(obj, textstatus) {
          console.log("Error");
        }
      }); // End of AJAX call

    } // End of addToWatchlist func
  </script>