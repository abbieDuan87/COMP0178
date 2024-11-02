<?php include_once("header.php")?>
<?php include_once("database.php");?>

<div class="container my-5">

<?php
session_start();
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

    $sellerID = 123; // dummy seller ID for now 
    $createdDate = date("Y-m-d H:i:s"); // current time
    $auctionStatus = 1;
    $itemCondition = 'new';


/* TODO #3: If everything looks good, make the appropriate call to insert
            data into the database. */
    $sql = "INSERT INTO Auctions (categoryID, sellerID, title, `description`, createdDate, endDate, startingPrice, reservePrice, auctionStatus, itemCondition) 
            VALUES (
              '".mysqli_real_escape_string($connection, $auctionCategory)."', 
              '".mysqli_real_escape_string($connection, $sellerID)."', 
              '".mysqli_real_escape_string($connection, $auctionTitle)."', 
              '".mysqli_real_escape_string($connection, $auctionDetails)."', 
              '$createdDate', 
              '$auctionEndDate', 
              $auctionStartPrice, 
              $auctionReservePrice, 
              $auctionStatus, 
              '$itemCondition'
            )";
    execute_query($connection, $sql);

// If all is successful, let user know.
echo('<div class="text-center">Auction successfully created! <a href="FIXME">View your new listing.</a></div>');


?>

</div>


<?php include_once("footer.php")?>