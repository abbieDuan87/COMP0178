<?php include_once("header.php") ?>
<?php require("utilities.php") ?>
<?php require("database.php") ?>

<?php
// This page is for showing a buyer recommended items based on their bid
// history. It will be pretty similar to browse.php, except there is no
// search bar. This can be started after browse.php is working with a database.
// Feel free to extract out useful functions from browse.php and put them in
// the shared "utilities.php" where they can be shared by multiple files.


// Check user's credentials (cookie/session).
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// If not buyer, then redirect to 'browse.php'
if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] != 'buyer') {
    header('Location: browse.php');
    exit();
}
$user_id = $_SESSION['user_id'];

// Perform a query to pull up ongoing auctions of the same categories as the user has bid on
$connection = get_connection();

$curr_page = isset($_GET['page']) & !empty($_GET['page']) ? $_GET['page'] : 1;
$results_per_page = 6;
$offset = ($curr_page - 1) * $results_per_page;

// Build and query the total number of results without pagination
list($count_sql, $count_params, $count_types) = get_recommended_auctions_sql($user_id, true);
$count_result = execute_prepared_stmt_query($connection, $count_sql, $count_params, $count_types);
$num_results = mysqli_fetch_assoc($count_result)['total'];
if ($num_results === 0) {
    // if no personalised recommendations can be found, fallback to popular auctions
    list($count_sql, $count_params, $count_types) = get_popular_auctions_sql($user_id, true);
    $count_result = execute_prepared_stmt_query($connection, $count_sql, $count_params, $count_types);
    $num_results = mysqli_fetch_assoc($count_result)['total'];
    list($recommendation_list_sql, $params, $types) = get_popular_auctions_sql($user_id, false);
} else {
    // Build query for personalised recommendations
    list($recommendation_list_sql, $params, $types) = get_recommended_auctions_sql($user_id, false);
}
// Set max page based on number of recommendations
$max_page = ceil($num_results / $results_per_page);
// Continue building sql with pagination and execute
$recommendation_list_sql .= " LIMIT ? OFFSET ?"; // return only [LIMIT] results, starting from [OFFSET + 1]
$params[] = $results_per_page;
$params[] = $offset;
$types .= 'ii'; // integer
$recommendation_list_result = execute_prepared_stmt_query($connection, $recommendation_list_sql, $params, $types);

?>

<div class="container mt-5">

    <h2 class="my-3">Recommendations for you</h2>

    <?php
    if (mysqli_num_rows($recommendation_list_result) === 0) {
        echo "<p class='text-center font-weight-light'>Sorry, there is no active auction right now, please check back later.</p>";
    }
    ?>

    <ul class="list-group">

        <?php
        while ($row = mysqli_fetch_assoc($recommendation_list_result)) {
            print_listing_li(
                $row['auctionID'],
                $row['title'],
                $row['currentPrice'],
                $row['bidCount'],
                new DateTime($row['endDate'])
            );
        }
        ?>

    </ul>

    <!-- Pagination for results listings -->
    <nav aria-label="Search results pages" class="mt-5">
        <ul class="pagination justify-content-center">
            <?php
            pagination($curr_page, $max_page, "recommendations.php");
            ?>
        </ul>
    </nav>


</div>

<?php close_connection($connection); ?>
<?php include_once("footer.php") ?>

