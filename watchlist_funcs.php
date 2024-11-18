 <?php

  include_once('utilities.php');
  include_once('database.php');

  if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
    return;
  }

  session_start();

  if (empty($_SESSION['logged_in']) || $_SESSION['account_type'] !== 'buyer') {
    return;
  }

  $user_id = (int)$_SESSION['user_id'];
  $item_id = (int)$_POST['arguments'][0];
  $conn = get_connection();

  if ($_POST['functionname'] == "add_to_watchlist") {
    $add_watchlist_sql = "INSERT INTO Watchlists (buyerID, auctionID) VALUES ($user_id, $item_id)";
    $add_result = execute_query($conn, $add_watchlist_sql);
    if ($add_result) {
      $res = "success";
    } else {
      $res = "error";
    }
  } else if ($_POST['functionname'] == "remove_from_watchlist") {
    $remove_watchlist_sql = "DELETE FROM Watchlists WHERE buyerID = $user_id AND auctionID = $item_id";
    $remove_result = execute_query($conn, $remove_watchlist_sql);
    if ($remove_result) {
      $res = "success";
    } else {
      $res = "error";
    }
  }

  close_connection($conn);
  echo $res;

  ?>