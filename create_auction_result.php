<?php 
include_once("header.php");
include_once("database.php");
include_once("email_utilities.php");

if (!isset($_SESSION['user_id']) || $_SESSION['account_type'] != 'seller') {
    header('Location: browse.php');
    exit();
}

$connection = get_connection();
$errors = [];


$title = $_POST["auctionTitle"];
$description = $_POST["auctionDetails"];
$category = $_POST["auctionCategory"];
$starting_price = $_POST["auctionStartPrice"];
$end_date = $_POST["auctionEndDate"];
$item_condition = $_POST["itemCondition"];


if (!is_numeric($starting_price)) {
    $errors[] = "Starting price must be a valid number.";
}

$reserve_price = isset($_POST["auctionReservePrice"]) && is_numeric($_POST["auctionReservePrice"]) ? $_POST["auctionReservePrice"] : "NULL";

$uploadDir = 'uploads/';
$imagePath = 'uploads/bread.png';

if (isset($_FILES['auctionImage']) && $_FILES['auctionImage']['error'] == UPLOAD_ERR_OK) {
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileExtension = pathinfo($_FILES['auctionImage']['name'], PATHINFO_EXTENSION);
    $imagePath = $uploadDir . uniqid() . '.' . $fileExtension;

    if (!move_uploaded_file($_FILES['auctionImage']['tmp_name'], $imagePath)) {
        $errors[] = "Error uploading image.";
    }
}

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header("Location: create_auction.php");
    exit();
}

$sellerID = $_SESSION['user_id'];
$createdDate = date("Y-m-d H:i:s");
$auctionStatus = 1;

$sql = "INSERT INTO Auctions (categoryID, sellerID, title, description, createdDate, endDate, startingPrice, reservePrice, auctionStatus, itemCondition, itemImage) 
        VALUES (
          '$category', 
          $sellerID, 
          '$title', 
          '$description', 
          '$createdDate', 
          '$end_date', 
          $starting_price, 
          $reserve_price, 
          $auctionStatus, 
          '$item_condition',
          '$imagePath'
        )";

    if (execute_query($connection, $sql)) {
        // Get the ID of the newly inserted record
        $inserted_id = mysqli_insert_id($connection);
        
        // Send successful email to seller.
        if (isset($_SESSION['email']) && !empty($_SESSION['email'])) {
            send_email_by_type($_SESSION['email'], "create_auction", ['auction_title' => $title]);
        }

        // Redirect to listing.php with the item_id as a URL parameter
        header("Location: listing.php?item_id=" . $inserted_id);
        exit; // Ensure no further code is executed after the redirect
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($connection);
    }
if (execute_query($connection, $sql)) {
    $inserted_id = mysqli_insert_id($connection);
    header("Location: listing.php?item_id=" . $inserted_id);
    exit;
} else {
    $_SESSION['form_errors'] = ["Error creating auction: " . mysqli_error($connection)];
    $_SESSION['form_data'] = $_POST;
    header("Location: create_auction.php");
    exit();
}

close_connection($connection);
?>