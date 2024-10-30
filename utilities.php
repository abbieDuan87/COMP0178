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
function print_listing_li($item_id, $title, $desc, $startingPrice, $currentPrice, $num_bids, $end_time)
{
  // Truncate long descriptions
  if (strlen($desc) > 100) {
    $desc_shortened = substr($desc, 0, 100) . '...';
  } else {
    $desc_shortened = $desc;
  }

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
          {$desc_shortened}
        </div>
        <div class='col'>
          <div class='row'>
            <div class='col'><span style='font-size: 1.4em' class='font-weight-bolder'>£" . number_format($currentPrice, 2) . "</span></div>
            <div class='col'><div class='mt-1'>{$num_bids} <span>{$bid}</span></div></div>
          </div>
          <div class='row mt-1'>
            <div class='col'><div class='font-weight-light'>starting price: <span class='font-weight-bolder'>£" . number_format($startingPrice, 2) . "</span></div></div>
            <div class='col'>{$time_remaining}</div>
          </div>
        </div>
      </div>
    </li>
    "
  );
}

function pagination($curr_page, $max_page)
{
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
  <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
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
    echo ('<a class="page-link" href="browse.php?' . $querystring . 'page=' . $i . '">' . $i . '</a></li>');
  }

  if ($curr_page != $max_page) {
    echo ('
<li class="page-item">
  <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
    <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
    <span class="sr-only">Next</span>
  </a>
</li>');
  }
}

function sanitise_user_input($data) {
  $data = trim($data);
  $data = htmlspecialchars($data);
  $data = str_replace(['%', '_'], ['\\%', '\\_'], $data);
  return $data;
}
