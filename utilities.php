<?php

// display_time_remaining:
// Helper function to help figure out what time to display
function display_time_remaining($interval)
{

  if ($interval->days == 0 && $interval->h == 0) {
    // Less than one hour remaining: print mins + seconds:
    $time_remaining = $interval->format('%im %Ss');
  } else if ($interval->days == 0) {
    // Less than one day remaining: print hrs + mins:
    $time_remaining = $interval->format('%hh %im');
  } else {
    // At least one day remaining: print days + hrs:
    $time_remaining = $interval->format('%ad %hh');
  }

  return $time_remaining;
}

// print_listing_li:
// This function prints an HTML <li> element containing an auction listing
function print_listing_li($item_id, $title, $currentPrice, $num_bids, $end_time)
{

  // Fix language of bid vs. bids
  if ($num_bids == 1) {
    $bid = ' bid';
  } else {
    $bid = ' bids';
  }

  // Calculate time to auction end
  $now = new DateTime();
  if ($now > $end_time) {
    $time_remaining = 'This auction has ended';
  } else {
    // Get interval:
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = display_time_remaining($time_to_end) . ' remaining';
  }

  // Print HTML
  echo (
    "
    <li class='list-group-item'>
      <div class='row ml-1 mt-2 mb-1'>
        <div class='col-6'>
          <h5><a href='listing.php?item_id={$item_id}'>{$title}</a></h5>
        </div>
        <div class='col'>
          <div class='row'>
            <div class='col'><span style='font-size: 1.4em' class='font-weight-bolder'>£" . number_format($currentPrice, 2) . "</span></div>
            <div class='col'>
              <div class='mt-1'>{$num_bids} <span>{$bid}</span></div>
              <div class='text-muted'>{$time_remaining}</div>
            </div>
          </div>
        </div>
      </div>
    </li>
    "
  );
}

// print_mylisting_li:
// This function prints an HTML <li> element containing an auction listing
function print_my_listings_li($item_id, $title, $currentPrice, $num_bids, $end_time, $auction_status)
{

  // Fix language of bid vs. bids
  if ($num_bids == 1) {
    $bid = ' bid';
  } else {
    $bid = ' bids';
  }

  // Calculate time to auction end
  $now = new DateTime();
  if ($now > $end_time) {
    $time_remaining = 'This auction has ended';
  } else {
    // Get interval:
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = display_time_remaining($time_to_end) . ' remaining';
  }

  // Conditional colouring for auction status
  $status_colour = match($auction_status) {
      'Sold' => 'green',
      'Reserve not met' => 'orange',
      'Open' => 'grey',
      'Closed - No bids' => 'black'
  };

  // Print HTML
     echo "
    <li class='list-group-item'>
        <div class='row ml-1 mt-2 mb-1'>
            <div class='col-4'>
                <h5><a href='listing.php?item_id={$item_id}'>{$title}</a></h5>
            </div>
            <div class='col-3'>
                <span style='font-size: 1.4em' class='font-weight-bolder'>£" . number_format($currentPrice, 2) . "</span>
                <div class='mt-1'>{$num_bids}{$bid}</div>
            </div>
            <div class='col-3'>
                <div class = 'font-weight-bold mt-2'>Status:<span style='color:{$status_colour}'> {$auction_status}</span></div>
                <div class='text-muted mt-1'>{$time_remaining}</div>
            </div>
        </div>
    </li>
    ";
}

// print_my_bids_listing:
// specifically for the "My Bids" page, showing both the highest bid and the user's bid.
function print_my_bids_listing($item_id, $title, $highestBid, $userBid, $num_bids, $end_time, $auctionStatus)
{
  // Fix language of bid vs. bids
  if ($num_bids == 1) {
    $bid = ' bid';
  } else {
    $bid = ' bids';
  }

  // Calculate time to auction end
  $now = new DateTime();
  if ($now > $end_time) {
    $time_remaining = 'This auction has ended';
  } else {
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = display_time_remaining($time_to_end) . ' remaining';
  }

   $status_colour = match($auctionStatus) {
    'Won' => 'green',
    'Lost' => 'red',
    'Open' => 'grey',
    'Reserve Not Met' => 'orange',
  };

  // Print HTML
  echo (
    "
    <li class='list-group-item'>
      <div class='row ml-1 mt-2 mb-1'>
        <div class='col-6 d-flex align-items-center'>
          <h5><a href='listing.php?item_id={$item_id}'>{$title}</a></h5>
        </div>
        <div class='col'>
          <div class='row'>
            <div class='col'>
              <span style='font-size: 1.4em' class='font-weight-bolder'>£" . number_format($highestBid, 2) . "</span>
              <div class='text-muted'>Your bid: £" . number_format($userBid, 2) . "</div>
              <div class='mt-1'>{$num_bids} <span>{$bid}</span></div>
            </div>
            <div class='col'>
            <div class= 'font-weight-bold mt-2'> Status:<span style='color:{$status_colour}'> {$auctionStatus}</span></div>
              <div class='text-muted'>{$time_remaining}</div>
            </div>
          </div>
        </div>
      </div>
    </li>
    "
  );
}

function pagination($curr_page, $max_page, $target_link = "browse.php")
{

  if ($max_page <= 0) {
    return; // if no max_page (which means no result), show nothing
  }

  // Copy any currently-set GET variables to the URL.
  $querystring = "";
  foreach ($_GET as $key => $value) {
    if ($key != "page") {
      $querystring .= "$key=$value&amp;";
    }
  }

  $high_page_boost = max(3 - $curr_page, 0);
  $low_page_boost = max(2 - ($max_page - $curr_page), 0);
  $low_page = max(1, $curr_page - 2 - $low_page_boost);
  $high_page = min($max_page, $curr_page + 2 + $high_page_boost);

  if ($curr_page != 1) {
    echo ('
<li class="page-item">
  <a class="page-link" href="' . $target_link . '?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
    <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
    <span class="sr-only">Previous</span>
  </a>
</li>');
  }

  for ($i = $low_page; $i <= $high_page; $i++) {
    if ($i == $curr_page) {
      // Highlight the link
      echo ('<li class="page-item active">');
    } else {
      // Non-highlighted link
      echo ('<li class="page-item">');
    }

    // Do this in any case
    echo ('<a class="page-link" href="' . $target_link . '?' . $querystring . 'page=' . $i . '">' . $i . '</a></li>');
  }

  if ($curr_page != $max_page) {
    echo ('
<li class="page-item">
  <a class="page-link" href="' . $target_link . '?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
    <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
    <span class="sr-only">Next</span>
  </a>
</li>');
  }
}

function sanitise_user_input($data)
{
  $data = trim($data);
  $data = htmlspecialchars($data);
  $data = str_replace(['%', '_'], ['\\%', '\\_'], $data);
  return $data;
}

/**
 * Constructs a SQL query to retrieve auction listings or count them based on 
 * specified filters and ordering options.
 *
 * @param string $keyword       The search term to filter auctions by title or description.
 * @param int|string $category  The category ID to filter auctions; "0" for no category filter.
 * @param string $ordering      Specifies the sorting order: 'pricehigh', 'pricelow', or 'date'.
 * @param bool|string $active_only If true or "1", restricts results to active auctions only.
 * @param bool $count_mode      If true, generates a query to count the filtered auctions 
 *                              instead of retrieving auction details.
 * 
 * @return array                An array containing:
 *                              - The generated SQL query string.
 *                              - An array of parameters for the query.
 *                              - A string of parameter types for prepared statements.
 * 
 * Usage:
 * - When $count_mode is set to true, the function generates a simplified query to count 
 *   the total number of auctions matching the filters, without applying GROUP BY or ORDER BY.
 * - When $count_mode is false, the function generates a detailed query to retrieve auction 
 *   listings with bid count and current price, applying grouping and ordering as specified.
 */
function build_auction_query($keyword, $category, $ordering, $active_only, $count_mode = false)
{
  // Start with base query depending on count mode
  $query = $count_mode
    ? "SELECT COUNT(DISTINCT auctions.auctionID) AS total FROM auctions"
    : "SELECT auctions.*, COUNT(bids.bidID) AS bidCount, 
          COALESCE(MAX(bids.bidPrice), auctions.startingPrice) AS currentPrice 
        FROM auctions LEFT JOIN bids ON auctions.auctionID = bids.auctionID";

  // Initialize query parts for filtering and parameter collection
  $filters = [];
  $params = [];
  $types = "";

  // Category filter
  if ($category != "0") {
    $filters[] = "auctions.categoryID = ?";
    $params[] = $category;
    $types .= "i";
  }

  // Keyword filter
  if (!empty($keyword)) {
    $filters[] = "(auctions.title LIKE ? OR auctions.description LIKE ?)";
    $keyword_param = "%" . sanitise_user_input($keyword) . "%";
    $params[] = $keyword_param;
    $params[] = $keyword_param;
    $types .= "ss";
  }

  // Active-only filter
  if (!empty($active_only) && $active_only == "1") {
    $filters[] = "(endDate >= NOW() AND auctionStatus = TRUE)";
  }

  // Apply filters with WHERE clause
  if (!empty($filters)) {
    $query .= " WHERE " . implode(" AND ", $filters);
  }

  // Apply grouping and ordering only if not in count mode
  if (!$count_mode) {
    $query .= " GROUP BY auctions.auctionID";

    if ($ordering === 'pricehigh') {
      $query .= " ORDER BY currentPrice DESC";
    } else if ($ordering === 'pricelow') {
      $query .= " ORDER BY currentPrice";
    } else if ($ordering === 'date') {
      $query .= " ORDER BY endDate";
    }
  }

  return [$query, $params, $types];
}

function mask_username($username)
{
  return substr($username, 0, 1) . "***" . substr($username, -1, 1);
}

function format_price($price)
{
  return '£' . (number_format($price, 2));
}

function render_bid_history_table($bid_history_result, $starting_price, $created_date)
{
?>
  <table class="table table-borderless">
    <thead class="border bg-light">
      <tr>
        <th scope="col">Bidder</th>
        <th scope="col">Bid Amount</th>
        <th scope="col">Bid Time</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      if (mysqli_num_rows($bid_history_result) > 0): 
        // Fetch the first (latest) bid
        $is_first_row = true; 
        while ($row = mysqli_fetch_assoc($bid_history_result)): 
      ?>
          <tr class="border <?php echo $is_first_row ? 'font-weight-bold' : ''; ?>">
            <td><?php echo htmlspecialchars(mask_username($row['username'])); ?></td>
            <td><?php echo htmlspecialchars(format_price($row['bidPrice'])); ?></td>
            <td><?php echo htmlspecialchars($row['bidDate']); ?></td>
          </tr>
          <?php 
          // Once we hit the first row (latest bid), set $is_first_row to false
          $is_first_row = false;
          endwhile; 
        ?>
        <tr class="border">
          <td>Starting Price</td>
          <td><?php echo htmlspecialchars(format_price($starting_price)); ?></td>
          <td><?php echo htmlspecialchars($created_date); ?></td>
        </tr>
      <?php else: ?>
        <tr class="border">
          <td colspan="3" class="text-center">No bids found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
<?php
}

function display_bid_form($item_id)
{
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    $account_type = $_SESSION['account_type'];

    if ($account_type == "buyer") {
      echo '
            <form method="POST" action="place_bid.php">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">£</span>
                    </div>
                    <input type="number" class="form-control" id="bid" name="bid_price">
                    <input type="hidden" name="item_id" value="' . htmlspecialchars($item_id) . '">
                </div>
                <button type="submit" class="btn btn-primary form-control">Place bid</button>
            </form>';
    }
  } else {
    echo '<button class="btn btn-primary form-control mt-2" disabled>Place bid</button>';
    echo "<div class='text-center text-muted mt-1'>Interested in this item? <br> Log in as a buyer to make your bid!</div>";
  }
}

/**
 * Constructs a SQL query to retrieve auction listings for recommendation.
 * The recommendations are based on auctions-based collaborative filtering. assuming users would like to explore
 * other auctions of the same categories as the ones they bid on, but have not bid on yet.
 * @param int $user_id The userID to recommend auctions for
 * @param boolean $count_mode Whether the result sql is to count the number of auctions or retrieving acution detail
 * @return array An array containing:
 *                 - The generated SQL query string.
 *                 - An array of parameters for the query.
 *                 - A string of parameter types for prepared statements.
 */
function get_recommended_auctions_sql($user_id, $count_mode)
{
  $query_auctions_select_clause = "
        SELECT 
            a.auctionID,
            a.title,
            COALESCE(MAX(b.bidPrice), a.startingPrice) AS currentPrice,
            COUNT(b.bidID) AS bidCount,
            a.endDate
   ";
  $count_select_clause = "SELECT COUNT(DISTINCT a.auctionID) AS total ";
  // Choose the SELECT clause based on count mode
  $result_sql = $count_mode ? $count_select_clause : $query_auctions_select_clause;
  // Add auctions-based collaborative filtering, choosing live auctions of the same categories the user has bid on
  $result_sql .= "
        FROM 
            Auctions a
        JOIN 
            Categories c ON a.categoryID = c.categoryID
        LEFT JOIN 
            Bids b ON a.auctionID = b.auctionID
        WHERE 
            a.categoryID IN (
                SELECT DISTINCT
                    a.categoryID
                FROM 
                    Bids b
                JOIN 
                    Auctions a ON b.auctionID = a.auctionID
                WHERE 
                    b.buyerID = ?  -- User's previous bids
            )
            AND a.auctionStatus = TRUE  -- Only ongoing auctions
            AND a.endDate > NOW()       -- Auction must not have ended yet
            AND a.auctionID NOT IN (    -- Exclude auctions the user has already bid on
                SELECT auctionID
                FROM Bids
                WHERE buyerID = ?
            )
  ";
  // if not for counting, the query result needs to be aggregated, and ordered with auctions ending soon placed in the front
  if (!$count_mode) {
    $result_sql .= "
        GROUP BY 
            a.auctionID, a.title, a.endDate
        ORDER BY 
            a.endDate ASC
      ";
  }
  return [$result_sql, [$user_id, $user_id], 'ii'];
}

/**
 * Constructs a SQL query to retrieve popular live auctions based on bid count.
 * The recommendations exclude auctions the user has already bid on.
 *
 * @param int $user_id The userID to recommend auctions for
 * @param boolean $count_mode Whether the result SQL is to count the number of auctions or retrieving auction details
 * @return array An array containing:
 *                 - The generated SQL query string.
 *                 - An array of parameters for the query.
 *                 - A string of parameter types for prepared statements.
 */
function get_popular_auctions_sql($user_id, $count_mode)
{
    $query_auctions_select_clause = "
        SELECT 
            a.auctionID,
            a.title,
            COALESCE(MAX(b.bidPrice), a.startingPrice) AS currentPrice,
            COUNT(b.bidID) AS bidCount,
            a.endDate
    ";
    $count_select_clause = "SELECT COUNT(DISTINCT a.auctionID) AS total ";

    // Choose the SELECT clause based on count mode
    $result_sql = $count_mode ? $count_select_clause : $query_auctions_select_clause;

    // Add the logic for retrieving popular live auctions
    $result_sql .= "
        FROM 
            Auctions a
        LEFT JOIN 
            Bids b ON a.auctionID = b.auctionID
        WHERE 
            a.auctionStatus = TRUE  -- Only ongoing auctions
            AND a.endDate > NOW()   -- Auction must not have ended yet
            AND a.auctionID NOT IN (    -- Exclude auctions the user has already bid on
                SELECT auctionID
                FROM Bids
                WHERE buyerID = ?
            )
    ";

    // If not in count mode, aggregate results and order by bid count
    if (!$count_mode) {
        $result_sql .= "
        GROUP BY 
            a.auctionID, a.title, a.endDate
        ORDER BY 
            bidCount DESC, a.endDate ASC  -- Most popular auctions first, tiebreaker is soonest ending
        ";
    }

    // Return the final query, parameters, and parameter types
    return [$result_sql, [$user_id], 'i'];
}

