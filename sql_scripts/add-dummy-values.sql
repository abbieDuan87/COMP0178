-- Insert into Users
INSERT INTO Users (firstName, lastName, email, password, dateOfBirth)
VALUES 
    ('John', 'Doe', 'john.doe@example.com', 'password123', '1980-05-12 00:00:00'),
    ('Jane', 'Smith', 'jane.smith@example.com', 'password456', '1990-07-19 00:00:00'),
    ('Tom', 'Brown', 'tom.brown@example.com', 'password789', '1985-03-22 00:00:00'),
    ('Lucy', 'Johnson', 'lucy.johnson@example.com', 'password101', '1995-11-08 00:00:00');

-- Insert into Sellers (linked to users)
INSERT INTO Sellers (sellerID) 
VALUES 
    (1), 
    (2); 

-- Insert into Buyers (linked to users)
INSERT INTO Buyers (buyerID)
VALUES 
    (3), 
    (4);

-- Insert into Addresses (linked to users)
INSERT INTO Addresses (userID, street, city, postcode)
VALUES 
    (1, '123 Oak Street', 'New York', '10001'),
    (2, '456 Maple Ave', 'Los Angeles', '90001'),
    (3, '789 Pine Road', 'Chicago', '60601'),
    (4, '101 Elm Street', 'San Francisco', '94101');

-- Insert Auctions (linked to categories and sellers)
INSERT INTO Auctions (categoryID, sellerID, title, description, createdDate, endDate, startingPrice, reservePrice, auctionStatus, itemCondition)
VALUES 
    (1, 1, 'Toyota Camry 2015', 'Used Toyota Camry in good condition', '2024-01-01 10:00:00', '2024-01-10 10:00:00', 5000, 6000, TRUE, 'good'),
    (2, 1, 'Designer Handbag', 'Fashionable handbag from 2022 collection', '2024-01-05 12:00:00', '2024-01-15 12:00:00', 100, 150, TRUE, 'new'),
    (3, 2, 'Garden Tools Set', 'Complete set of gardening tools', '2024-01-02 09:00:00', '2024-01-12 09:00:00', 50, 70, TRUE, 'used'),
    (4, 2, 'Vintage Art Piece', 'Original painting from 1950s', '2024-01-03 14:00:00', '2024-01-13 14:00:00', 2000, 2500, TRUE, 'good');

-- Insert Bids (linked to auctions and buyers)
INSERT INTO Bids (auctionID, buyerID, bidPrice, bidDate, isSuccessful)
VALUES 
    (1, 3, 5200, '2024-01-05 08:30:00', TRUE),
    (1, 4, 5300, '2024-01-06 10:00:00', FALSE),
    (2, 3, 110, '2024-01-07 11:00:00', TRUE),
    (3, 4, 55, '2024-01-09 14:45:00', TRUE);

-- Insert Orders (linked to auctions, buyers, sellers)
INSERT INTO Orders (auctionID, buyerID, sellerID, orderDate, orderStatus)
VALUES 
    (1, 3, 1, '2024-01-11 12:00:00', 'placed'),
    (2, 3, 1, '2024-01-16 09:00:00', 'dispatched'),
    (3, 4, 2, '2024-01-13 15:00:00', 'delivered');

-- Insert Watchlists (linked to buyers and auctions)
INSERT INTO Watchlists (buyerID, auctionID)
VALUES 
    (3, 1), 
    (3, 2),
    (4, 3), 
    (4, 4);
