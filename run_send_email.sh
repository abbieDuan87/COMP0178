#!/bin/bash

# Path to the PHP executable in XAMPP (adjust accordingly for your system)
# For Windows
PHP_PATH="C:/xampp/php/php.exe"
# For macOS  -> not sure if it works.. TODO: find the path work for macOS
# PHP_PATH="/Applications/XAMPP/xamppfiles/bin/php"

# Paths to the PHP scripts you want to run
# For Windows
SCRIPT_PATH_1="C:/xampp/htdocs/COMP0178/email_send_from_queue.php"
SCRIPT_PATH_2="C:/xampp/htdocs/COMP0178/email_auction_end.php"
# For macOS  -> not sure if it works..
# SCRIPT_PATH_1="/Applications/XAMPP/htdocs/COMP0178/email_send_from_queue.php"
# SCRIPT_PATH_2="/Applications/XAMPP/htdocs/COMP0178/email_auction_end.php"

# Check if PHP script 1 exists
if [ ! -f "$SCRIPT_PATH_1" ]; then
    echo "Error: PHP script not found at $SCRIPT_PATH_1"
    exit 1
fi

# Check if PHP script 2 exists
if [ ! -f "$SCRIPT_PATH_2" ]; then
    echo "Error: PHP script not found at $SCRIPT_PATH_2"
    exit 1
fi

# Run the first PHP script
echo "Running email_send_from_queue.php..."
$PHP_PATH $SCRIPT_PATH_1

# Check if the first PHP script ran successfully
if [ $? -eq 0 ]; then
    echo "First script executed successfully."
else
    echo "Error running the first PHP script."
    exit 1
fi

# Run the second PHP script
echo "Running email_auction_end.php..."
$PHP_PATH $SCRIPT_PATH_2

# Check if the second PHP script ran successfully
if [ $? -eq 0 ]; then
    echo "Second script executed successfully."
else
    echo "Error running the second PHP script."
    exit 1
fi
