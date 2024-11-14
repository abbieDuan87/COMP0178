<?php

// Queue email to the emailqueue database.
function queue_email_by_type($conn, $recipients, $type, $data = [])
{
    // Ensure recipients is an array for consistency
    if (!is_array($recipients)) {
        $recipients = [$recipients];
    }

    // Retrieve the email template and replace placeholders
    $template = get_email_content($type, $data);
    if ($template) {
        $subject = $template['subject'];
        $body = $template['body'];

        foreach ($recipients as $recipient) {
            $recipient = mysqli_real_escape_string($conn, $recipient);
            $subject = mysqli_real_escape_string($conn, $subject);
            $body = mysqli_real_escape_string($conn, $body);
            $query = "INSERT INTO EmailQueue (recipient, subject, body, status) VALUES ('$recipient', '$subject', '$body', 'pending')";
            mysqli_query($conn, $query);
        }
    } else {
        echo "Error: Email type '$type' not found.";
    }
}

// Generalised function to send an email by type with custom data
// Example usage:
// send_email_by_type('auctioncat@mail.com', 'registration');
// send_email_by_type('auctioncat@mail.com', 'create_auction', ['auction_title' => 'Vintage Watch']);
// send_email_by_type('bidder@mail.com', 'outbid_notification', ['auction_title' => 'Vintage Watch']);
// send_email_by_type('auctioncat@mail.com', 'auction_closed', [
//     'auction_title' => 'Vintage Watch',
//     'result_message' => "Congratulations to the winner!"
// ]);
function send_email_by_type($recipients, $type, $data = [])
{
    // Check if $recipients is an array, and join them into a string if necessary
    if (is_array($recipients)) {
        $recipients = implode(',', $recipients); // Join multiple recipients with commas
    }

    // Get email content (subject and body)
    $template = get_email_content($type, $data);

    // If the template exists, send the email
    if ($template) {
        return send_email($recipients, $template['subject'], $template['body']);
    } else {
        echo "Error: Email type '$type' not found.";
        return false;
    }
}

// Main function to send an email
function send_email($recipients, $subject, $body)
{
    // Configuration for sender details
    $config = parse_ini_file('./config.ini');
    $sender_name = $config['sender_name'];
    $sender_email = $config['sender_email'];

    // Email headers
    $headers = "From: " . $sender_name . " <" . $sender_email . ">\r\n";
    $headers .= "Reply-To: " . $sender_email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Send the email (to multiple recipients)
    $result = mail($recipients, $subject, $body, $headers);
    return $result;
}

// Function to get and format email template by type with data placeholders
function get_email_content($type, $data = [])
{
    $templates = [
        'registration' => [
            'subject' => "Welcome to Auction Site!",
            'body' => "Hi,\r\n\r\nWelcome to our auction site!\r\nYour account was created successfully!\r\n\r\nBest regards,\r\nAuctionCat Team"
        ],
        'create_auction' => [
            'subject' => "Your Auction Has Been Successfully Created!",
            'body' => "Hello,\r\n\r\nCongratulations! Your auction '{auction_title}' has been successfully created and is now live for bidders to view. We wish you the best of luck in attracting great bids!\r\n\r\nThank you for choosing AuctionCat,\r\nAuctionCat Team"
        ],
        'create_bid' => [
            'subject' => "Your Bid Was Successfully Created!",
            'body' => "Hello,\r\n\r\nYour bid for '{auction_title}' has been successfully placed. Keep an eye on your bid to stay in the lead!\r\n\r\nThank you for participating,\r\nAuctionCat Team"
        ],
        'successful_bid' => [
            'subject' => "Your Bid Was Successful!",
            'body' => "Hello,\r\n\r\nYour recent bid for '{auction_title}' was successful. Keep bidding for more items!\r\n\r\nBest regards,\r\nAuctionCat Team"
        ],
        'outbid_notification' => [
            'subject' => "You've Been Outbid!",
            'body' => "Hello,\r\n\r\nSomeone has outbid you on auction '{auction_title}'. Return to the auction site to increase your bid!\r\n\r\nBest regards,\r\nAuctionCat Team"
        ],
        'auction_closed_winner' => [
            'subject' => "Congratulations, You Won the Auction!",
            'body' => "Hello,\r\n\r\nCongratulations on winning the auction '{auction_title}'! Log in to view the details.\r\n\r\nBest regards,\r\nAuctionCat Team"
        ],
        'auction_closed_bidders' => [
            'subject' => "Auction Closed: {auction_title}",
            'body' => "Hello,\r\n\r\nThe auction for '{auction_title}' has closed. Another user won the auction.\r\n\r\nThank you for participating,\r\nAuctionCat Team"
        ],
        'no_bids_notification' => [
            'subject' => "Your Auction '{auction_title}' Didn't Receive Any Bids",
            'body' => "Hello,\r\n\r\nUnfortunately, no one placed any bids on your auction '{auction_title}'. Better luck next time!\r\n\r\nBest regards,\r\nAuctionCat Team"
        ],
        'auction_closed_seller' => [
            'subject' => "Your Auction '{auction_title}' Has Ended",
            'body' => "Hello,\r\n\r\nWe wanted to inform you that your auction '{auction_title}' has officially ended.\r\n\r\nPlease log in to your account to check the results.\r\n\r\nIf you have any questions or need assistance, feel free to reach out to us.\r\n\r\nBest regards,\r\nAuctionCat Team"
        ]
    ];

    // Retrieve the template and replace placeholders with actual data
    $template = $templates[$type] ?? null;
    if ($template) {
        foreach ($data as $key => $value) {
            $template['subject'] = str_replace("{" . $key . "}", $value, $template['subject']);
            $template['body'] = str_replace("{" . $key . "}", $value, $template['body']);
        }
    }
    return $template;
}

/**
 * Queue outbid notification emails for users who were outbid in an auction.
 *
 * @param mysqli $conn Database connection
 * @param int $auction_id ID of the auction
 * @param int $current_bidder_id ID of the current bidder who placed a higher bid
 * @param float $bid_amount The amount of the current bid
 * @param string $auction_title Title of the auction item
 */
function queue_outbid_notifications_email($conn, $auction_id, $current_bidder_id, $bid_amount, $auction_title)
{
    // Query to find other bidders who were outbid
    $outbid_query = "
        SELECT DISTINCT Users.email
        FROM Bids
        INNER JOIN Users ON Bids.buyerID = Users.userID
        WHERE Bids.auctionID = $auction_id
          AND Bids.buyerID != $current_bidder_id
          AND Bids.bidPrice < $bid_amount";

    $outbid_result = execute_query($conn, $outbid_query);

    while ($row = mysqli_fetch_assoc($outbid_result)) {
        $outbid_email = $row['email'];
        queue_email_by_type($conn, $outbid_email, 'outbid_notification', ['auction_title' => $auction_title]);
    }
}
