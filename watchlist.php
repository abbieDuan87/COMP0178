<?php
include_once("header.php");
include_once("utilities.php");
include_once("database.php");

if (!isset($_SESSION['logged_in']) || $_SESSION['account_type'] !== 'buyer') {
    $_SESSION['error_message'] = "You must be logged in as a buyer to view your watchlist.";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = get_connection();

$watchlist_query = "
    SELECT Auctions.*, 
           COALESCE(MAX(Bids.bidPrice), Auctions.startingPrice) AS currentPrice,
           COUNT(Bids.bidID) AS num_bids
    FROM Watchlists
    JOIN Auctions ON Watchlists.auctionID = Auctions.auctionID
    LEFT JOIN Bids ON Auctions.auctionID = Bids.auctionID
    WHERE Watchlists.buyerID = ?
    GROUP BY Auctions.auctionID";

$stmt = $conn->prepare($watchlist_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$now = new DateTime();
?>

<div class="container mt-5">
    <h2 class="text-center">My Watchlist</h2>
    <ul class="list-group">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                if (isset($row['endDate']) && !empty($row['endDate'])) {
                    $end_time = new DateTime($row['endDate']);
                    if ($now > $end_time) {
                        $time_remaining = 'This auction has ended';
                    } else {
                        $time_to_end = date_diff($now, $end_time);
                        $time_remaining = display_time_remaining($time_to_end) . ' remaining';
                    }
                } else {
                    $time_remaining = 'Invalid end date';
                }
                ?>
                <li class="list-group-item">
                    <h5><a href="listing.php?item_id=<?= htmlspecialchars($row['auctionID']) ?>">
                        <?= htmlspecialchars($row['title']) ?>
                    </a></h5>
                    <p>Current Price: £<?= number_format($row['currentPrice'], 2) ?></p>
                    <p>Bids: <?= $row['num_bids'] ?> | <?= $time_remaining ?></p>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">Your watchlist is empty.</p>
        <?php endif; ?>
    </ul>
</div>

<?php
$stmt->close();
close_connection($conn);
include_once('footer.php');
?>

<script>
    function removeFromWatchlist(itemId) {
        fetch("watchlist_funcs.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                functionname: "remove_from_watchlist",
                arguments: [itemId],
            }),
        })
        .then((response) => response.text())
        .then((res) => {
            if (res.trim() === "success") {
                location.reload();
            } else {
                alert("Error: Could not remove item from watchlist.");
            }
        })
        .catch((err) => {
            console.error("Fetch error:", err);
            alert("An unexpected error occurred.");
        });
    }
</script>