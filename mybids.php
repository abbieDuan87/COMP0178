<?php
include_once("header.php");
require("utilities.php");
require("database.php");

$connection = get_connection();

$buyer_id = $_SESSION['user_id'];

$my_bids_query = "
   SELECT 
    Auctions.auctionID, 
    Auctions.title, 
    COALESCE(MAX(Bids.bidPrice), Auctions.startingPrice) AS highestBid,
    COUNT(Bids.bidID) AS bidCount,
    Auctions.endDate,
    COALESCE(UserBids.userBidPrice, Auctions.startingPrice) AS userBidPrice,
    CASE
        WHEN NOW() < Auctions.endDate THEN 'Open'
        WHEN NOW() >= Auctions.endDate 
             AND COALESCE(UserBids.userBidPrice, 0) = COALESCE(MAX(Bids.bidPrice), 0) 
             AND COALESCE(MAX(Bids.bidPrice), 0) >= Auctions.reservePrice THEN 'Won'
        WHEN NOW() >= Auctions.endDate 
             AND COALESCE(MAX(Bids.bidPrice), 0) < Auctions.reservePrice THEN 'Reserve Not Met'
        WHEN NOW() >= Auctions.endDate THEN 'Lost'
    END AS auctionStatus
FROM 
    Auctions
LEFT JOIN 
    Bids ON Auctions.auctionID = Bids.auctionID
LEFT JOIN 
    (SELECT auctionID, MAX(bidPrice) AS userBidPrice 
     FROM Bids 
     WHERE buyerID = $buyer_id 
     GROUP BY auctionID) AS UserBids 
     ON Auctions.auctionID = UserBids.auctionID
WHERE 
    Bids.buyerID = $buyer_id OR UserBids.userBidPrice IS NOT NULL
GROUP BY 
    Auctions.auctionID
";

$result = execute_query($connection, $my_bids_query);

?>

<div class="container">
  <h2 class="my-3">My Bids</h2>

  <ul class="list-group">
    <?php
    if (mysqli_num_rows($result) == 0) {
      echo "<p class='text-center font-weight-light'>No bids found.</p>";
    } else {
      while ($row = mysqli_fetch_assoc($result)) {
        // Using print_my_bids_listing instead
        print_my_bids_listing(
          $row['auctionID'],
          $row['title'],
          $row['highestBid'],
          $row['userBidPrice'],
          $row['bidCount'],
          new DateTime($row['endDate']),
          $row['auctionStatus']
        );
      }
    }
    ?>
  </ul>
</div>


<?php
close_connection($connection);
include_once("footer.php");
?>