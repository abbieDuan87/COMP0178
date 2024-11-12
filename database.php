<?php
function get_connection()
{
    $config = parse_ini_file('./config.ini');
    $connection = mysqli_connect($config['hostname'],  $config['username'],  $config['password'],  $config['dbname']);
    if (mysqli_connect_errno()) {
        echo 'Database connection failed: ' . mysqli_connect_error();
    }

    return $connection;
}

function execute_query($connection, $sql)
{
    try {
        $result = mysqli_query($connection, $sql);

        if (!$result) {
            die('Query error: ' . mysqli_error($connection));
        }
        return $result;
    } catch (Exception $e) {
        // Log the exception message to the console
        echo "<script>console.log('PHP Exception: " . addslashes($e->getMessage()) . "');</script>";
    }
}

function close_connection($connection)
{
    mysqli_close($connection);
}

function execute_prepared_stmt_query($connection, $sql, $params = [], $types = "") {
    $stmt = mysqli_prepare($connection, $sql);
    if (!$stmt) {
        die("Statement preparation failed: " . mysqli_error($connection));
    }

    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check for errors
    if (!$result) {
        die("Error executing query: " . mysqli_error($connection));
    }

    mysqli_stmt_close($stmt);
    return $result;
}
