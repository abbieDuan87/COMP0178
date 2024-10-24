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
