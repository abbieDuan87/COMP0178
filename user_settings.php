<?php
include_once("header.php");
include_once("database.php");

if (empty($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = get_connection();

$query = "
    SELECT 
        u.username, u.firstName, u.lastName, u.email, 
        a.street, a.city, a.postcode 
    FROM Users u 
    LEFT JOIN Addresses a ON u.userID = a.userID 
    WHERE u.userID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
close_connection($conn);

$user = [
    'username' => $user_data['username'],
    'firstName' => $user_data['firstName'],
    'lastName' => $user_data['lastName'],
    'email' => $user_data['email']
];
$address = [
    'street' => $user_data['street'] ?? '',
    'city' => $user_data['city'] ?? '',
    'postcode' => $user_data['postcode'] ?? ''
];

$errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_errors']);
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);
?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($success_message): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($success_message) ?>
    </div>
<?php endif; ?>

<div class="container my-5">
    <h2 class="text-center">User Settings</h2>
    <form method="post" action="update_user_settings.php">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>
        <div class="form-group">
            <label for="firstName">First Name</label>
            <input type="text" class="form-control" id="firstName" name="firstName" value="<?= htmlspecialchars($user['firstName']) ?>" required>
        </div>
        <div class="form-group">
            <label for="lastName">Last Name</label>
            <input type="text" class="form-control" id="lastName" name="lastName" value="<?= htmlspecialchars($user['lastName']) ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="form-group">
            <label for="street">Street</label>
            <input type="text" class="form-control" id="street" name="street" value="<?= htmlspecialchars($address['street']) ?>">
        </div>
        <div class="form-group">
            <label for="city">City</label>
            <input type="text" class="form-control" id="city" name="city" value="<?= htmlspecialchars($address['city']) ?>">
        </div>
        <div class="form-group">
            <label for="postcode">Postcode</label>
            <input type="text" class="form-control" id="postcode" name="postcode" value="<?= htmlspecialchars($address['postcode']) ?>">
        </div>
        <div class="form-group">
            <label for="password">New Password (leave blank to keep current password)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>

<?php include_once("footer.php"); ?>