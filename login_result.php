<?php
session_start();
include_once("database.php");

header('Content-Type: application/json'); // Set header for JSON response

$response = [
    "status" => "error",
    "message" => ""
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Extract and validate input
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $response['message'] = "Please fill in both email and password.";
        echo json_encode($response);
        exit();
    }

    // Connect to the database and check credentials
    $conn = get_connection();
    $query = "SELECT * FROM Users WHERE email = '$email'";
    $result = execute_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['userID'];
            $_SESSION['email'] = $user['email'];

            // Check if the user is a buyer or seller
            $accountQuery = "SELECT * FROM Buyers WHERE buyerID = {$user['userID']}";
            $accountResult = execute_query($conn, $accountQuery);
            $_SESSION['account_type'] = mysqli_num_rows($accountResult) > 0 ? "buyer" : "seller";

            // Set success message and send a success response
            $_SESSION['success_message'] = "Welcome, " . $user['firstName'] . "! You have successfully logged in.";
            $response['status'] = "success";
            echo json_encode($response);
            exit();
        } else {
            // Incorrect password
            $response['message'] = "Incorrect password. Please try again.";
            echo json_encode($response);
            exit();
        }
    } else {
        // Email not found
        $response['message'] = "Email not found. Please register or try again.";
        echo json_encode($response);
        exit();
    }
    
    close_connection($conn);

} else {
    $response['message'] = "Invalid request method.";
    echo json_encode($response);
}
?>