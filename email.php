<?php

// Main function to send an email
function send_email($recipient, $subject, $mail_body)
{
    $config = parse_ini_file('./config.ini');
    $sender_name = $config['sender_name'];
    $sender_email = $config['sender_email'];

    $headers = "From: " . $sender_name . " <" . $sender_email . ">\r\n";
    $headers .= "Reply-To: " . $sender_email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    mail($recipient, $subject, $mail_body, $headers);
}


// Registration email
function send_registration_email($recipient)
{
    $subject = "Welcome to Auction Site!";
    $body = "Hi,\r\n\r\nWelcome to our auction site!\r\nYour account was created successfully!\r\n\r\nBest regards,\r\nAuctionCat Team";
    send_email($recipient, $subject, $body);
}

// Successful bid email
function send_successful_bid_email($recipient)
{
    $subject = "Your Bid Was Successful!";
    $body = "Hello,\r\n\r\nYour recent bid was successful. Keep bidding for more items!\r\n\r\nBest regards,\r\nAuctionCat Team";
    send_email($recipient, $subject, $body);
}

// Outbid notification email
function send_outbid_notification_email($recipient)
{
    $subject = "You’ve Been Outbid!";
    $body = "Hello,\r\n\r\nSomeone has outbid you on an item. Return to the auction site to increase your bid!\r\n\r\nBest regards,\r\nAuctionCat Team";
    send_email($recipient, $subject, $body);
}

// Auction winner notification
function send_winner_notification_email($recipient)
{
    $subject = "Congratulations, You Won the Auction!";
    $body = "Hello,\r\n\r\nCongratulations on winning the auction! Log in to view the details.\r\n\r\nBest regards,\r\nAuctionCat Team";
    send_email($recipient, $subject, $body);
}
