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
  if ($num_bids <= 1) {
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
