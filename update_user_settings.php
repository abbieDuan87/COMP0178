<?php
session_start();
include_once("database.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $conn = get_connection();
    $errors = [];

    $username = trim($_POST['username']);
    $first_name = trim($_POST['firstName']);
    $last_name = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $street = trim($_POST['street']);
    $city = trim($_POST['city']);
    $postcode = trim($_POST['postcode']);
    $password = trim($_POST['password']);

    $query = "SELECT userID FROM Users WHERE (username = ? OR email = ?) AND userID != ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $username, $email, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = "Username or email is already taken.";
    }

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: user_settings.php");
        exit();
    }

    $query = "UPDATE Users SET username = ?, firstName = ?, lastName = ?, email = ?";
    $params = [$username, $first_name, $last_name, $email];
    $types = "ssss";

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query .= ", password = ?";
        $params[] = $hashed_password;
        $types .= "s";
    }

    $query .= " WHERE userID = ?";
    $params[] = $user_id;
    $types .= "i";

    $stmt = $conn->prepare($query);
    if (!$stmt->bind_param($types, ...$params)) {        
        echo "Error binding parameters: " . $stmt->error;
        exit();
    }

    if (!$stmt->execute()) {
        echo "Error updating user: " . $stmt->error;
        exit();
    }

    if (!empty($street) || !empty($city) || !empty($postcode)) {
        $query = "INSERT INTO Addresses (userID, street, city, postcode)
                  VALUES (?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE 
                      street = VALUES(street), 
                      city = VALUES(city), 
                      postcode = VALUES(postcode)";
        $stmt = $conn->prepare($query);
        if (!$stmt->bind_param("isss", $user_id, $street, $city, $postcode)) {
            echo "Error binding parameters: " . $stmt->error;
            exit();
        }

        if (!$stmt->execute()) {
            echo "Error updating address: " . $stmt->error;
            exit();
        }
    }

    $_SESSION['success_message'] = "Your details have been updated successfully.";
    header("Location: user_settings.php");
    exit();
}
?>