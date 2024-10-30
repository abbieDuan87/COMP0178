<?php
include_once("database.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $accountType = $_POST["accountType"];
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $passwordConfirmation = $_POST["passwordConfirmation"];

    $errorMessage = '';
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($passwordConfirmation)) {
        $errorMessage = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $errorMessage = "Password must be at least 6 characters long.";
    } elseif ($password !== $passwordConfirmation) {
        $errorMessage = "Passwords do not match.";
    }

    if (!empty($errorMessage)) {
        $_SESSION['error_message'] = $errorMessage;
        header("Location: register.php");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $conn = get_connection();
    $userSql = "INSERT INTO users (firstName, lastName, email, password) VALUES ('$firstName', '$lastName', '$email', '$hashedPassword')";

    if (execute_query($conn, $userSql)) {

        $userId = mysqli_insert_id($conn);

        if ($accountType === "seller") {
            $sellerSql = "INSERT INTO sellers (sellerID) VALUES ('$userId')";
            execute_query($conn, $sellerSql);
        } else {
            $buyerSql = "INSERT INTO buyers (buyerID) VALUES ('$userId')";
            execute_query($conn, $buyerSql);
        }

        header("Location: successful_registration.php");
        exit();
    } else {
        echo "Error: Unable to register user.";
    }

    close_connection($conn);
} else {
    echo "Invalid request.";
}

?>