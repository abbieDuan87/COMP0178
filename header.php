<?php
  session_start();
  if (!isset($_SESSION['logged_in'])) {
    $_SESSION['logged_in'] = false;
    $_SESSION['account_type'] = 'guest';
}
?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  
  <!-- Bootstrap and FontAwesome CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <!-- Custom CSS file -->
  <link rel="stylesheet" href="css/custom.css">

  <title>ebibi</title>
</head>


<body>

<!-- Navbars -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mx-2">
  <a class="navbar-brand" href="#">ebibi</a>
  <ul class="navbar-nav ml-auto">
    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true): ?>
      <li class="nav-item">
        <a class="nav-link" href="user_settings.php">Settings</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="logout.php">Logout</a>
      </li>
    <?php else: ?>
      <li class="nav-item">
        <button type="button" class="btn nav-link" data-toggle="modal" data-target="#loginModal">Login</button>
      </li>
    <?php endif; ?>
  </ul>
</nav>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <ul class="navbar-nav align-middle">
	<li class="nav-item mx-1">
      <a class="nav-link" href="browse.php">Browse</a>
    </li>
<?php
  if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'buyer') {
  echo('
	<li class="nav-item mx-1">
      <a class="nav-link" href="mybids.php">My Bids</a>
    </li>
	<li class="nav-item mx-1">
      <a class="nav-link" href="recommendations.php">Recommended</a>
    </li>');
  }
  if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'seller') {
  echo('
	<li class="nav-item mx-1">
      <a class="nav-link" href="mylistings.php">My Listings</a>
    </li>
	<li class="nav-item ml-3">
      <a class="nav-link btn border-light" href="create_auction.php">+ Create auction</a>
    </li>');
  }
?>
  </ul>
</nav>

<!-- Login modal -->
<div class="modal fade" id="loginModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Login</h4>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div id="login-error" class="alert alert-danger" style="display: none;"></div> <!-- Error message container -->
        
        <form id="loginForm">
          <div class="form-group">
            <label for="email">Email</label>
            <input type="text" class="form-control" id="email" name="email" placeholder="Email">
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
          </div>
          <button type="button" class="btn btn-primary form-control" onclick="submitLogin()">Sign in</button>
        </form>
        
        <div class="text-center">or <a href="register.php">create an account</a></div>
      </div>

    </div>
  </div>
</div> <!-- End modal -->

<script>
  function submitLogin() {
    const form = document.getElementById("loginForm");
    const formData = new FormData(form);

    fetch("login_result.php", {
      method: "POST",
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.status === "success") {
        window.location.href = "index.php";
      } else {
        const errorDiv = document.getElementById("login-error");
        errorDiv.style.display = "block";
        errorDiv.innerText = data.message;
      }
    })
    .catch(error => {
      console.error("Error:", error);
    });
  }
</script>