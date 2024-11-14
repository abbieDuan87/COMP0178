<?php
include_once("database.php");
include_once("email_utilities.php");

$conn = get_connection();

$batch_limit = 10;
$select_queued_email_sql = "SELECT * FROM EmailQueue WHERE status = 'pending' LIMIT $batch_limit";

$results = execute_query($conn, $select_queued_email_sql);

while ($row = mysqli_fetch_assoc($results)) {
    $id = $row['id'];
    $recipient  = $row['recipient'];
    $subject = $row['subject'];
    $body = $row['body'];

    if (send_email($recipient, $subject, $body)) {
        $update_email_sql = "UPDATE EmailQueue SET status = 'sent' WHERE id = $id";
        execute_query($conn, $update_email_sql);
    } else {
        $update_query = "UPDATE EmailQueue SET status = 'failed' WHERE id = $id";
        execute_query($conn, $update_email_sql);
    }
}

close_connection($conn);
