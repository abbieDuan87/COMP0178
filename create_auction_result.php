<?php include_once("header.php")?>
<?php include_once("database.php");?>

<div class="container my-5">

<?php
    // This function takes the form data and adds the new auction to the database.

/* TODO #1: Connect to MySQL database (perhaps by requiring a file that
            already does this). */

    $connection = get_connection();

    /* TODO #2: Extract form data into variables. Because the form was a 'post'
            form, its data can be accessed via $POST['auctionTitle'], 
            $POST['auctionDetails'], etc. Perform checking on the data to
            make sure it can be inserted into the database. If there is an
            issue, give some semi-helpful feedback to user. */
    $auctionTitle = $_POST["auctionTitle"];
    $auctionDetails = $_POST["auctionDetails"];
    $auctionCategory = $_POST["auctionCategory"];
    $auctionStartPrice = $_POST["auctionStartPrice"];
    $auctionReservePrice = $_POST["auctionReservePrice"];
    $auctionEndDate = $_POST["auctionEndDate"];
    $itemCondition = $_POST["itemCondition"];

    // Check if an image was uploaded
    $imageData = null;
    if (isset($_FILES['auctionImage']) && $_FILES['auctionImage']['error'] == UPLOAD_ERR_OK) {
        // Read the image file as binary data
        $imageData = mysqli_real_escape_string($connection, file_get_contents($_FILES['auctionImage']['tmp_name']));
    }

    $sellerID = $_SESSION['user_id']; 
    $createdDate = date("Y-m-d H:i:s"); // current time
    $auctionStatus = 1;


    /* TODO #3: If everything looks good, make the appropriate call to insert
            data into the database. */
    $sql = "INSERT INTO Auctions (categoryID, sellerID, title, `description`, createdDate, endDate, startingPrice, reservePrice, auctionStatus, itemCondition, itemImage) 
            VALUES (
              '".mysqli_real_escape_string($connection, $auctionCategory)."', 
              $sellerID, 
              '".mysqli_real_escape_string($connection, $auctionTitle)."', 
              '".mysqli_real_escape_string($connection, $auctionDetails)."', 
              '$createdDate', 
              '$auctionEndDate', 
              $auctionStartPrice, 
              $auctionReservePrice, 
              $auctionStatus, 
              '$itemCondition',
              '$imageData'
            )";

    if (execute_query($connection, $sql)) {
        // Get the ID of the newly inserted record
        $inserted_id = mysqli_insert_id($connection);
        
        // Redirect to listing.php with the item_id as a URL parameter
        header("Location: listing.php?item_id=" . $inserted_id);
        exit; // Ensure no further code is executed after the redirect
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($connection);
    }

?>

</div>


<?php include_once("footer.php")?>