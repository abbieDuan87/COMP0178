<?php
session_start();
include_once("database.php");
include_once("utilities.php");
include_once("email_utilities.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $bid_price = $_POST['bid_price'] ?? '';
    $item_id = $_POST['item_id'] ?? '';


    if (isset($_SESSION['logged_in']) & $_SESSION['account_type'] == "buyer") {
        if (empty($bid_price)) {
            $_SESSION['warning_message'] = 'Cannot submit a empty bid price.';
            header('location: listing.php?item_id=' . $item_id);
            exit();
        }

        $bid_price = floatval($bid_price);

        $conn = get_connection();
        $buyer_id = $_SESSION['user_id'];

        // Get the current highest bid for the item and the starting price
        $query = "SELECT Auctions.startingPrice, COALESCE(MAX(bidPrice), 0) AS currentHighestBid, Auctions.title
                  FROM Auctions
                  LEFT JOIN bids ON Auctions.auctionID = bids.auctionID
                  WHERE Auctions.auctionID = $item_id
                  GROUP BY Auctions.auctionID";
        $result = execute_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        $starting_price = $row['startingPrice'];
        $current_highest_bid = $row['currentHighestBid'];

        $auction_title = $row['title'];

        if ($bid_price <= 0) {
            $_SESSION['warning_message'] = 'Please enter a valid bid amount.';
            header('location: listing.php?item_id=' . $item_id);
            exit();
        }

        // Check if the user's bid is higher than or equal to the current highest bid and starting price
        if ($bid_price <= $current_highest_bid) {
            $_SESSION['warning_message'] = 'Your bid must be higher than the current highest bid (' . format_price($current_highest_bid) . ').';
            header('location: listing.php?item_id=' . $item_id);
            exit();
        }

        if ($bid_price < $starting_price) {
            $_SESSION['warning_message'] = 'Your bid must be at least the starting price (' . format_price($starting_price) . ').';
            header('location: listing.php?item_id=' . $item_id);
            exit();
        }

        $query = "INSERT INTO bids (auctionID, buyerID, bidPrice, bidDate, isSuccessful)
        VALUES ($item_id, $buyer_id, $bid_price, now(), FALSE)";

        if (execute_query($conn, $query)) {
            $_SESSION['success_message'] = 'Your bid has been placed successfully!';
            if (isset($_SESSION['email']) && !empty($_SESSION['email'])) {
                send_email_by_type($_SESSION['email'], 'successful_bid', ['auction_title' => $auction_title]);
            }
            send_auction_update_emails($conn, $item_id, $buyer_id, $bid_price, $auction_title, 'watchlist_update_notification');
            send_auction_update_emails($conn, $item_id, $buyer_id, $bid_price, $auction_title, 'outbid_notification');
        } else {
            $_SESSION['warning_message'] = 'There was an error placing your bid. Please try again.';
        }

        close_connection($conn);
        header('location: listing.php?item_id=' . $item_id);
        exit();
    } else {
        $_SESSION['warning_message'] = 'You need to log in to bid.';
        header('location: listing.php?item_id=' . $item_id);
        exit();
    }
}
