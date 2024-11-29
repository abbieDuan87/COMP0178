Here’s a comprehensive README.md file for your auction database project:

# Auction Database Project

## Table of Contents

- [Project Overview](#project-overview)
- [Features](#features)
- [Setup Instructions](#setup-instructions)
- [Database Schema](#database-schema)
- [Project Functionalities](#project-functionalities)
- [Technologies Used](#technologies-used)

---

## Project Overview

This project is a web-based auction system designed to enable users to list items for auction, place bids, and manage their interactions. Buyers and sellers can register, log in, and access features tailored to their roles. Additionally, the system implements a watchlist, bid notifications, and email alerts to enhance the user experience.

---

## Features

### General Features

- User authentication: Registration and login.
- Role-based functionalities for buyers and sellers.
- Password hashing for secure authentication.
- Dynamic navigation menus based on user roles.

### Buyer Features

- Browse and search auction listings by categories, keywords, and price.
- Place bids on active auctions.
- Manage a personal watchlist:
  - Add and remove items.
  - View all watched items on a dedicated watchlist page.
- Receive notifications for auction updates.

### Seller Features

- Create auction listings:
  - Upload images for items.
  - Specify starting prices, reserve prices, and auction end times.
  - Ensure data validation for all required fields.
- View and manage personal listings.
- Track auction statuses.

### Notifications and Alerts

- Email notifications:
  - Upon registration.
  - Bid updates and auction status changes.
- Alerts for bid results or auction outcomes.
- Watchlist management updates.

### User Settings

- Update personal information, including:
  - Username
  - First name and last name
  - Email
  - Address
  - Password (with optional update logic)
- Validation to ensure unique usernames and emails.

---

## Setup Instructions

### Prerequisites

- [XAMPP](https://www.apachefriends.org/index.html) installed for local PHP and MySQL server setup.
- `PHP 7.4` or higher.
- Browser for testing the application.

### Installation Steps

1. Clone the repository:

   ```bash
   git clone https://github.com/your-repo/auction-database.git

   2.	Move to the project directory:
   ```

cd auction-database

    3.	Import the database schema:
    •	Open phpMyAdmin or any MySQL client.
    •	Import the provided auction.sql file to set up the database structure.
    4.	Configure the database connection:
    •	Update the config.ini file in the root directory:

hostname=127.0.0.1
username=root
password=
dbname=auction
sender_name=AuctionCat
sender_email=auctioncat@mail.com
smtp_host=smtp.zoho.eu
smtp_username=auctioncat@zohomail.eu
smtp_password="your_password"
smtp_port=465
smtp_secure=ssl

    5.	Start XAMPP and ensure Apache and MySQL are running.
    6.	Access the application in your browser:

http://localhost/auction-database/

Database Schema

Tables

    1.	Users
    •	userID (PK)
    •	username
    •	password
    •	firstName
    •	lastName
    •	email
    •	role (e.g., buyer, seller)
    2.	Auctions
    •	auctionID (PK)
    •	categoryID (FK)
    •	sellerID (FK)
    •	title
    •	description
    •	startingPrice
    •	reservePrice
    •	createdDate
    •	endDate
    •	itemCondition
    •	auctionStatus
    3.	Bids
    •	bidID (PK)
    •	auctionID (FK)
    •	buyerID (FK)
    •	bidPrice
    •	bidDate
    4.	Addresses
    •	addressID (PK)
    •	userID (FK)
    •	street
    •	city
    •	postcode
    5.	Watchlists
    •	watchlistID (PK)
    •	buyerID (FK)
    •	auctionID (FK)

Project Functionalities

Auction Management

    •	Sellers can list items with images, prices, and conditions.
    •	Buyers can browse, bid, and manage watched items.

Watchlist

    •	Buyers can add auctions to their watchlist and remove them later.
    •	Dedicated page for viewing and managing the watchlist.

Notifications

    •	Email alerts for:
    •	Registration confirmation.
    •	Auction and bidding updates.

User Settings

    •	Update profile details and address with live form validation.

Validation and Security

    •	Input sanitization to prevent SQL injection.
    •	Password hashing using PHP’s password_hash() function.

Technologies Used

    •	Backend: PHP 7.4+
    •	Frontend: HTML, CSS, JavaScript, Bootstrap 4
    •	Database: MySQL
    •	Email Service: PHPMailer
