# Drop the database if exists
DROP DATABASE IF EXISTS auction;
CREATE DATABASE auction;
USE auction;

# Create the tables
CREATE TABLE Users(  
    userID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    firstName VARCHAR(255) NOT NULL,
    lastName VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    dateOfBirth DATETIME
) COMMENT '';


CREATE TABLE Sellers(
    sellerID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    FOREIGN KEY (sellerID) REFERENCES Users(userID)
);

CREATE TABLE Buyers(
    buyerID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    FOREIGN KEY (buyerID) REFERENCES Users(userID)
);

CREATE TABLE Addresses(
    addressID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    userID INT NOT NULL,
    street VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    postcode VARCHAR(255) NOT NULL,
    FOREIGN KEY (userID) REFERENCES Users(userID)
);


CREATE TABLE Categories (
    categoryID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(40) NOT NULL
);

INSERT INTO Categories (name)
VALUES 
    ('Motors'),
    ('Fashion'),
    ('Home Garden'),
    ('Collectables & Art'),
    ('Sports, Hobbies & Leisure'),
    ('Electronics'),
    ('Health & Beauty'),
    ('Business, Office & Industrial Supplies'),
    ('Media'),
    ('Others');

CREATE TABLE Auctions (
    auctionID INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
    categoryID INT NOT NULL,
    sellerID INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description VARCHAR(255), 
    createdDate DATETIME NOT NULL, 
    endDate DATETIME NOT NULL,
    startingPrice FLOAT NOT NULL,
    reservePrice FLOAT,
    itemImage BINARY,
    auctionStatus BOOLEAN NOT NULL,
    itemCondition ENUM('new', 'good', 'used') NOT NULL,
    FOREIGN KEY (categoryID) REFERENCES Categories(categoryID)
);

CREATE TABLE Bids(
    bidID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    auctionID INT NOT NULL,
    buyerID INT NOT NULL,
    bidPrice FLOAT NOT NULL,
    bidDate DATETIME NOT NULL,
    isSuccessful BOOLEAN NOT NULL,
    FOREIGN KEY (auctionID) REFERENCES Auctions(auctionID),
    FOREIGN KEY (buyerID) REFERENCES Buyers(buyerID)
);

CREATE TABLE Orders(
    orderID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    auctionID INT NOT NULL,
    buyerID INT NOT NULL,
    sellerID INT NOT NULL,
    orderDate DATETIME NOT NULL,
    orderStatus ENUM('placed', 'dispatched', 'delivered'),
    FOREIGN KEY (auctionID) REFERENCES Auctions(auctionID),
    FOREIGN KEY (buyerID) REFERENCES Buyers(buyerID),
    FOREIGN KEY (sellerID) REFERENCES sellers(sellerID)
);

CREATE TABLE Watchlists(
    buyerID INT NOT NULL,
    auctionID INT NULL NULL,
    FOREIGN KEY (auctionID) REFERENCES Auctions(auctionID),
    FOREIGN KEY (buyerID) REFERENCES Buyers(buyerID)
);
