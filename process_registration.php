<?php
include_once("database.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $account_type = $_POST["accountType"];
    $first_name = $_POST["firstName"];
    $last_name = $_POST["lastName"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $password_confirmation = $_POST["passwordConfirmation"];

    $error_message = '';
    if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password) || empty($password_confirmation) || empty($account_type)) {
        $error_message = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } elseif ($password !== $password_confirmation) {
        $error_message = "Passwords do not match.";
    }

    if (!empty($error_message)) {
        $_SESSION['error_message'] = $error_message;
        header("Location: register.php");
        exit();
    }

    $conn = get_connection();

    $sql_check_registration = "SELECT * FROM Users WHERE username = '$username' OR email = '$email'";
    $check_registration_result = execute_query($conn, $sql_check_registration);

    if (mysqli_num_rows($check_registration_result) == 0) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $user_sql = "INSERT INTO users (firstName, lastName, email, username, password) VALUES ('$first_name', '$last_name', '$email', '$username', '$hashed_password')";

        if (execute_query($conn, $user_sql)) {

            $user_id = mysqli_insert_id($conn);

            if ($account_type == "seller") {
                $seller_sql = "INSERT INTO sellers (sellerID) VALUES ('$user_id')";
                execute_query($conn, $seller_sql);
            } else {
                $buyer_sql = "INSERT INTO buyers (buyerID) VALUES ('$user_id')";
                execute_query($conn, $buyer_sql);
            }

            header("Location: successful_registration.php");
            exit();
        } else {
            echo "Error: Unable to register user due to a database error.";
        }
    } else {
        $_SESSION['error_message'] = 'A user with this username or email address already exists. Please choose a different one.';
        header('location: register.php');
        exit();
    }

    close_connection($conn);
} else {
    echo "Invalid request.";
}
