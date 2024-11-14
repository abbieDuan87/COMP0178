<?php
include_once("database.php");
include_once("email_utilities.php");

$conn = get_connection();

// Step 1: Find auctions that have ended but are still active
$query = "SELECT * FROM Auctions WHERE endDate < NOW() AND auctionStatus = 1";
$auctions = execute_query($conn, $query);

// Step 2: Process each auction
while ($auction = mysqli_fetch_assoc($auctions)) {
    $auction_id = $auction['auctionID'];
    $auction_title = $auction['title'];
    $seller_id = $auction['sellerID'];

    // Step 3: Get all bids for this auction and order by bidPrice descending (highest first)
    $bids_query = "
        SELECT 
            b.bidID,
            b.buyerID, 
            b.bidPrice, 
            b.isSuccessful, 
            u.email AS bidder_email, 
            u.firstName, 
            u.lastName
        FROM Bids b
        JOIN Users u ON u.userID = b.buyerID
        WHERE b.auctionID = $auction_id
        ORDER BY b.bidPrice DESC
    ";

    $bids_result = execute_query($conn, $bids_query);

    // Step 4: If no bids, notify the seller and skip the auction
    if (mysqli_num_rows($bids_result) == 0) {
        // Notify seller that no one bid on their auction
        $seller_query = "SELECT email FROM Users WHERE userID = $seller_id";
        $seller_result = execute_query($conn, $seller_query);
        $seller_email = mysqli_fetch_assoc($seller_result)['email'];

        // Send notification to the seller
        send_email_by_type($seller_email, 'no_bids_notification', ['auction_title' => $auction_title]);

        // Skip to the next auction
        continue;
    }

    // Step 5: Process the bids (only if there are bids)
    $winner_email = "";
    $seller_email = "";
    $outbid_bidders = [];
    $highest_bid = null;

    // First, fetch the highest bid (winner) and the other bidders
    $is_first_bid = true;
    while ($bid = mysqli_fetch_assoc($bids_result)) {
        $bid_status = 'outbid';  // Default to outbid

        if ($is_first_bid) {
            // The first bid (highest bid) is the winner
            $bid_status = 'winner';
            $winner_email = $bid['bidder_email']; // Store winner email
            $highest_bid = $bid['bidID']; // Store the highest bid ID
            $is_first_bid = false;
        }

        // Add to outbid list for all non-winners
        if ($bid_status == 'outbid') {
            $outbid_bidders[] = $bid['bidder_email'];
        }

        // Update the bid status in the database (all others as outbid)
        $update_bid_sql = "UPDATE Bids SET isSuccessful = 0 WHERE bidID = {$bid['bidID']}";
        execute_query($conn, $update_bid_sql);
    }

    // Mark the highest bid as successful
    if ($highest_bid) {
        $update_bid_sql = "UPDATE Bids SET isSuccessful = 1 WHERE bidID = $highest_bid";
        execute_query($conn, $update_bid_sql);
    }

    // Step 6: Get the seller's email
    $seller_query = "SELECT email FROM Users WHERE userID = $seller_id";
    $seller_result = execute_query($conn, $seller_query);
    $seller_email = mysqli_fetch_assoc($seller_result)['email'];

    // Step 7: Send notifications

    // Notify the winner
    if ($winner_email) {
        send_email_by_type($winner_email, 'auction_closed_winner', ['auction_title' => $auction_title]);
    }

    // Notify the seller
    if ($seller_email) {
        send_email_by_type($seller_email, 'auction_closed_seller', ['auction_title' => $auction_title]);
    }

    // Notify the outbid bidders
    send_email_by_type($outbid_bidders, 'auction_closed_bidders', ['auction_title' => $auction_title]);

    // Mark auction as completed (inactive)
    $update_auction_sql = "UPDATE Auctions SET auctionStatus = 0 WHERE auctionID = $auction_id";
    execute_query($conn, $update_auction_sql);
}

// Close the database connection
close_connection($conn);
