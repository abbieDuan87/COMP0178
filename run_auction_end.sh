#!/bin/bash

# Path to the PHP executable in XAMPP (adjust accordingly for your system)
# For Windows
PHP_PATH="C:/xampp/php/php.exe"
# For macOS  -> not sure if it works.. TODO: find the path work for macOS
# PHP_PATH="/Applications/XAMPP/xamppfiles/bin/php"

# Paths to the PHP scripts you want to run
# For Windows
SCRIPT_PATH="C:/xampp/htdocs/COMP0178/email_auction_end.php"
# For macOS  -> not sure if it works..
# SCRIPT_PATH="/Applications/XAMPP/htdocs/COMP0178/email_auction_end.php"

# Check if PHP script exists
if [ ! -f "$SCRIPT_PATH" ]; then
    echo "Error: PHP script not found at $SCRIPT_PATH"
    exit 1
fi

# Run the second PHP script
echo "Running email_auction_end.php..."
$PHP_PATH $SCRIPT_PATH

# Check if the second PHP script ran successfully
if [ $? -eq 0 ]; then
    echo "Script executed successfully."
else
    echo "Error running the second PHP script."
    exit 1
fi
