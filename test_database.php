<?php
include_once('get_connection.php');

$connection = get_connection();

if ($connection) {
    echo "Database connection successful!";
} else {
    echo "Database connection failed.";
}
?>