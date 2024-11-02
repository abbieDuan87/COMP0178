-- Insert into Users
INSERT INTO Users (firstName, lastName, email, username, password)
VALUES 
('John', 'Doe', 'john.doe@example.com', 'johndoe', 'password123'),
('Jane', 'Smith', 'jane.smith@example.com', 'janesmith', 'password456'),
('Tom', 'Brown', 'tom.brown@example.com', 'tombrown', 'password789'),
('Lucy', 'Johnson', 'lucy.johnson@example.com', 'lucyjohnson', 'password101');

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
    (2, 2, 'Designer Handbag', 'Fashionable handbag from 2022 collection', '2024-01-05 12:00:00', '2024-01-15 12:00:00', 100, 150, TRUE, 'new'),
    (3, 1, 'Garden Tools Set', 'Complete set of gardening tools', '2024-01-02 09:00:00', '2026-01-12 09:00:00', 50, 70, TRUE, 'used'),
    (4, 1, 'Vintage Abstract Art', 'Rare abstract painting from 1950s, rich colors and expressive brushwork', '2024-01-03 14:00:00', '2024-01-13 14:00:00', 1800, 2400, TRUE, 'good'),
    (10, 2, 'Monster Hunter Rise - Limited Edition', 'PS4 edition of Monster Hunter Rise in perfect condition', '2024-10-03 14:00:00', '2025-01-13 14:00:00', 60, 80, TRUE, 'new'),
    (6, 1, 'Kratos Sackboy Keychain', 'Used on display and in good condition, keyring missing screw', '2024-09-03 14:00:00', '2024-12-13 14:00:00', 20, 25, TRUE, 'good'),
    (6, 2, 'Nintendo Switch Console', 'Standard Nintendo Switch with two controllers, perfect condition', '2024-12-07 09:00:00', '2025-01-17 09:00:00', 250, 300, TRUE, 'new'),
    (6, 1, 'Xbox Series X Bundle', 'Includes Xbox Series X with two controllers and three games', '2024-10-08 11:00:00', '2025-01-18 11:00:00', 450, 500, TRUE, 'new'),
    (6, 2, 'PlayStation 5', 'PS5 in great condition, with original box and controller', '2024-01-09 15:00:00', '2024-10-19 15:00:00', 500, 600, TRUE, 'good'),
    (6, 1, 'Retro Game Console Collection', 'Classic consoles: NES, SNES, and Sega Genesis with controllers', '2024-08-10 13:00:00', '2025-01-20 13:00:00', 300, 350, TRUE, 'used'),
    (6, 2, 'Rare Gaming Posters Collection', 'Vintage gaming posters from the 1990s in excellent condition', '2024-10-11 14:30:00', '2025-01-21 14:30:00', 75, 100, TRUE, 'good');

-- Insert Bids (linked to auctions and buyers)
INSERT INTO Bids (auctionID, buyerID, bidPrice, bidDate, isSuccessful)
VALUES 
    (1, 3, 5200, '2024-01-05 08:30:00', TRUE),
    (1, 4, 5300, '2024-01-06 10:00:00', FALSE),
    (2, 3, 110, '2024-01-07 11:00:00', TRUE),
    (2, 4, 120, '2024-01-08 13:15:00', FALSE),
    (3, 4, 55, '2024-01-09 14:45:00', FALSE),
    (3, 4, 75, '2024-01-09 14:45:00', TRUE),  
    (4, 3, 1850, '2024-01-10 10:30:00', TRUE),
    (4, 4, 2000, '2024-01-11 16:00:00', FALSE);

    

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
