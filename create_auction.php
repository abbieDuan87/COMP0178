<?php 
include_once("header.php");
include_once("database.php");

if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] != 'seller') {
    header('Location: browse.php');
    exit();
}

$connection = get_connection();
$errors = $_SESSION['form_errors'] ?? [];
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_data']);

$sql = "SELECT * FROM Categories";
$result = execute_query($connection, $sql);
$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}
mysqli_free_result($result);
mysqli_close($connection);
?>

<div class="container">

<div style="max-width: 900px; margin: 10px auto">
  <h2 class="my-3">Create new auction</h2>
  <p class="text-muted">Fields marked with <span class="text-danger">*</span> are required.</p>
  
  <div class="card">
    <div class="card-body">
      <form method="post" action="create_auction_result.php" enctype="multipart/form-data">
        <div class="form-group row">
          <label for="auctionTitle" class="col-sm-2 col-form-label text-right">Title <span class="text-danger">*</span></label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="auctionTitle" id="auctionTitle" placeholder="e.g. Black mountain bike" required>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionImage" class="col-sm-2 col-form-label text-right">Upload Image <span class="text-danger">*</span></label>
          <div class="col-sm-10">
            <input type="file" class="form-control" name="auctionImage" id="auctionImage" accept="image/*" required>
          </div>
        </div>
        <div class="form-group row">
          <label for="itemCondition" class="col-sm-2 col-form-label text-right">Item Condition <span class="text-danger">*</span></label>
          <div class="col-sm-10">
            <select class="form-control" name="itemCondition" id="itemCondition" required>
              <option selected disabled>Choose...</option>
              <option value="new">New</option>
              <option value="good">Good</option>
              <option value="used">Used</option>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionDetails" class="col-sm-2 col-form-label text-right">Details</label>
          <div class="col-sm-10">
            <textarea class="form-control" name="auctionDetails" id="auctionDetails" rows="4"></textarea>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionCategory" class="col-sm-2 col-form-label text-right">Category <span class="text-danger">*</span></label>
          <div class="col-sm-10">
            <select class="form-control" name="auctionCategory" id="auctionCategory" required>
              <option selected disabled value="">Choose...</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['categoryID']); ?>">
                  <?= htmlspecialchars($category['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionStartPrice" class="col-sm-2 col-form-label text-right">Starting price <span class="text-danger">*</span></label>
          <div class="col-sm-10">
	        <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input type="number" class="form-control" name="auctionStartPrice" id="auctionStartPrice" required>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionReservePrice" class="col-sm-2 col-form-label text-right">Reserve price</label>
          <div class="col-sm-10">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input type="number" class="form-control" name="auctionReservePrice" id="auctionReservePrice">
            </div>
            <small id="reservePriceHelp" class="form-text text-muted">Optional. Auctions that end below this price will not go through. This value is not displayed in the auction listing.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionEndDate" class="col-sm-2 col-form-label text-right">End date <span class="text-danger">*</span></label>
          <div class="col-sm-10">
            <input type="datetime-local" class="form-control" name="auctionEndDate" id="auctionEndDate" required>
          </div>
        </div>
        <button type="submit" class="btn btn-primary form-control">Create Auction</button>
      </form>
    </div>
  </div>
</div>

</div>

<?php include_once("footer.php") ?>