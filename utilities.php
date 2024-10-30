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
  if (strlen($desc) > 250) {
    $desc_shortened = substr($desc, 0, 250) . '...';
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
