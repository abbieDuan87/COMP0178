<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Send email based on template type with PHPMailer
function send_email_by_type($recipients, $type, $data = [])
{
    $template = get_email_content($type, $data);
    send_email($recipients, $template['subject'], $template['body']);
}

// Main function to send an email using PHPMailer
function send_email($recipients, $subject, $body)
{
    $config = parse_ini_file('./config.ini');
    $sender_name = $config['sender_name'];
    $sender_email = $config['sender_email'];
    $smtp_host = $config['smtp_host'];
    $smtp_username = $config['smtp_username'];
    $smtp_password = $config['smtp_password'];
    $smtp_port = $config['smtp_port'];
    $smtp_secure = $config['smtp_secure'];

    $mail = new PHPMailer(true);

    try {
        // server config
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = $smtp_secure;
        $mail->Port = $smtp_port;

        // sender info
        $mail->setFrom($sender_email, $sender_name);

        // Add multiple recipients
        if (is_array($recipients)) {
            foreach ($recipients as $email) {
                $mail->addAddress($email);
            }
        } else {
            $mail->addAddress($recipients); // Add single recipient
        }

        // email content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $body;

        // send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}

function send_auction_update_emails($conn, $auction_id, $current_bidder_id, $bid_amount, $auction_title, $notification_type)
{
    if ($notification_type === 'outbid_notification') {
        $query = "
            SELECT DISTINCT Users.email
            FROM Bids
            INNER JOIN Users ON Bids.buyerID = Users.userID
            WHERE Bids.auctionID = $auction_id
              AND Bids.buyerID != $current_bidder_id
              AND Bids.bidPrice < $bid_amount";
    } else if ($notification_type === 'watchlist_update_notification') {
        $query = "
            SELECT DISTINCT Users.email
            FROM Watchlists
            INNER JOIN Users ON Watchlists.buyerID = Users.userID
            WHERE Watchlists.auctionID = $auction_id
              AND Users.userID != $current_bidder_id";
    } else {
        return;
    }

    $result = execute_query($conn, $query);

    $emails = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $emails[] = $row['email'];
    }

    if (!empty($emails)) {
        send_email_by_type(
            $emails,
            $notification_type,
            ['auction_title' => $auction_title, 'bid_amount' => $bid_amount]
        );
    }
}

// Function to get and format email template by type with data placeholders
function get_email_content($type, $data = [])
{
    $templates = [
        'registration' => [
            'subject' => "Welcome to Auction Site!",
            'body' => "Hi,\r\n\r\nWelcome to our auction site!\r\nYour account was created successfully!\r\n\r\nBest regards,\r\nEbibi Team"
        ],
        'create_auction' => [
            'subject' => "Your Auction Has Been Successfully Created!",
            'body' => "Hello,\r\n\r\nCongratulations! Your auction '{auction_title}' has been successfully created and is now live for bidders to view. We wish you the best of luck in attracting great bids!\r\n\r\nThank you for choosing Ebibi,\r\nEbibi Team"
        ],
        'create_bid' => [
            'subject' => "Your Bid Was Successfully Created!",
            'body' => "Hello,\r\n\r\nYour bid for '{auction_title}' has been successfully placed. Keep an eye on your bid to stay in the lead!\r\n\r\nThank you for participating,\r\nEbibi Team"
        ],
        'successful_bid' => [
            'subject' => "Your Bid Was Successful!",
            'body' => "Hello,\r\n\r\nYour recent bid for '{auction_title}' was successful. Keep bidding for more items!\r\n\r\nBest regards,\r\nEbibi Team"
        ],
        'outbid_notification' => [
            'subject' => "You've Been Outbid!",
            'body' => "Hello,\r\n\r\nSomeone has outbid you on auction '{auction_title}'. Return to the auction site to increase your bid!\r\n\r\nBest regards,\r\nEbibi Team"
        ],
        'auction_closed_winner' => [
            'subject' => "Congratulations, You Won the Auction!",
            'body' => "Hello,\r\n\r\nCongratulations on winning the auction '{auction_title}'! Log in to view the details.\r\n\r\nBest regards,\r\nEbibi Team"
        ],
        'auction_closed_bidders' => [
            'subject' => "Auction Closed: {auction_title}",
            'body' => "Hello,\r\n\r\nThe auction for '{auction_title}' has closed. Another user won the auction.\r\n\r\nThank you for participating,\r\nEbibi Team"
        ],
        'no_bids_notification' => [
            'subject' => "Your Auction '{auction_title}' Didn't Receive Any Bids",
            'body' => "Hello,\r\n\r\nUnfortunately, no one placed any bids on your auction '{auction_title}'. Better luck next time!\r\n\r\nBest regards,\r\nEbibi Team"
        ],
        'auction_closed_seller' => [
            'subject' => "Your Auction '{auction_title}' Has Ended",
            'body' => "Hello,\r\n\r\nWe wanted to inform you that your auction '{auction_title}' has officially ended.\r\n\r\nPlease log in to your account to check the results.\r\n\r\nIf you have any questions or need assistance, feel free to reach out to us.\r\n\r\nBest regards,\r\nEbibi Team"
        ],
        'watchlist_update_notification' => [
            'subject' => "Update on an Auction in Your Watchlist: '{auction_title}'",
            'body' => "Hello,\r\n\r\nWe wanted to let you know that there's been a recent update to the auction '{auction_title}' that you're watching. \r\n\r\nPlease log in to view the latest changes and ensure you stay informed about any new bids, adjustments, or important details.\r\n\r\nThank you for staying engaged,\r\nEbibi Team"
        ]
    ];

    $template = $templates[$type] ?? null;
    if ($template) {
        foreach ($data as $key => $value) {
            $template['subject'] = str_replace("{" . $key . "}", $value, $template['subject']);
            $template['body'] = str_replace("{" . $key . "}", $value, $template['body']);
        }
    }
    return $template;
}
