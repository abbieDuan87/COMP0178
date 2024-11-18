<?php
include_once("database.php");
include_once("email_utilities.php");

$conn = get_connection();

$query = "SELECT * FROM Auctions WHERE endDate < NOW() AND auctionStatus = 1";
$auctions = execute_query($conn, $query);

while ($auction = mysqli_fetch_assoc($auctions)) {
    $auction_id = $auction['auctionID'];
    $auction_title = $auction['title'];
    $seller_id = $auction['sellerID'];

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

    if (mysqli_num_rows($bids_result) == 0) {
        $seller_query = "SELECT email FROM Users WHERE userID = $seller_id";
        $seller_result = execute_query($conn, $seller_query);
        $seller_email = mysqli_fetch_assoc($seller_result)['email'];

        send_email_by_type($seller_email, 'no_bids_notification', ['auction_title' => $auction_title]);

        continue;
    }

    $winner_email = "";
    $seller_email = "";
    $outbid_bidders = [];
    $highest_bid = null;

    $is_first_bid = true;
    while ($bid = mysqli_fetch_assoc($bids_result)) {
        $bid_status = 'outbid';

        if ($is_first_bid) {
            $bid_status = 'winner';
            $winner_email = $bid['bidder_email'];
            $highest_bid = $bid['bidID'];
            $is_first_bid = false;
        }

        if ($bid_status == 'outbid') {
            $outbid_bidders[] = $bid['bidder_email'];
        }

        $update_bid_sql = "UPDATE Bids SET isSuccessful = 0 WHERE bidID = {$bid['bidID']}";
        execute_query($conn, $update_bid_sql);
    }

    if ($highest_bid) {
        $update_bid_sql = "UPDATE Bids SET isSuccessful = 1 WHERE bidID = $highest_bid";
        execute_query($conn, $update_bid_sql);
    }

    $seller_query = "SELECT email FROM Users WHERE userID = $seller_id";
    $seller_result = execute_query($conn, $seller_query);
    $seller_email = mysqli_fetch_assoc($seller_result)['email'];

    if ($winner_email) {
        send_email_by_type($winner_email, 'auction_closed_winner', ['auction_title' => $auction_title]);
    }

    if ($seller_email) {
        send_email_by_type($seller_email, 'auction_closed_seller', ['auction_title' => $auction_title]);
    }

    send_email_by_type($outbid_bidders, 'auction_closed_bidders', ['auction_title' => $auction_title]);

    $watcher_query = "
    SELECT DISTINCT Users.email
    FROM Watchlists
    INNER JOIN Users ON Watchlists.buyerID = Users.userID
    WHERE Watchlists.auctionID = $auction_id AND Users.email != '$winner_email'";

    $watcher_result = execute_query($conn, $watcher_query);
    while ($watcher = mysqli_fetch_assoc($watcher_result)) {
        $watcher_emails[] = $watcher['email'];
    }

    if (!empty($watcher_emails)) {
        send_email_by_type($watcher_emails, 'watchlist_update_notification', ['auction_title' => $auction_title]);
    }

    $watcher_emails = [];

    $update_auction_sql = "UPDATE Auctions SET auctionStatus = 0 WHERE auctionID = $auction_id";
    execute_query($conn, $update_auction_sql);
}

close_connection($conn);
