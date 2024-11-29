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
INSERT INTO Auctions (categoryID, sellerID, title, description, createdDate, endDate, startingPrice, reservePrice, auctionStatus, itemCondition, itemImage)
VALUES 
-- Motors
(1, 1, 'Motors 1', 'A reliable motorbike in excellent condition.', '2024-03-10 10:00:00', '2024-10-20 10:00:00', 1000, 1200, FALSE, 'good', 'uploads/placeholder.png'),
(1, 2, 'Motors 2', 'Vintage car for collectors.', '2024-02-15 11:00:00', '2024-10-10 11:00:00', 8000, 10000, FALSE, 'used', 'uploads/placeholder.png'),
(1, 1, 'Motors 3', 'Electric scooter with charger.', '2024-04-01 12:00:00', '2025-01-15 12:00:00', 500, 600, TRUE, 'new', 'uploads/placeholder.png'),
(1, 2, 'Motors 4', 'Off-road vehicle for adventurous drivers.', '2024-05-05 13:00:00', '2025-01-20 13:00:00', 7000, 8000, TRUE, 'good', 'uploads/placeholder.png'),
(1, 1, 'Motors 5', 'Spare parts for vintage motorcycles.', '2024-06-22 14:00:00', '2025-02-10 14:00:00', 300, 400, TRUE, 'used', 'uploads/placeholder.png'),
(1, 2, 'Motors 6', 'Classic car in pristine condition.', '2024-07-05 15:00:00', '2025-03-01 15:00:00', 12000, 15000, TRUE, 'new', 'uploads/placeholder.png'),
(1, 1, 'Motors 7', 'Compact city car, ideal for daily commute.', '2024-08-15 16:00:00', '2025-01-25 16:00:00', 4000, 5000, TRUE, 'good', 'uploads/placeholder.png'),
(1, 2, 'Motors 8', 'Sports bike with premium features.', '2024-02-20 17:00:00', '2025-01-30 17:00:00', 9000, 10000, TRUE, 'new', 'uploads/placeholder.png'),
-- Fashion
(2, 1, 'Fashion 1', 'Designer coat, lightly used.', '2024-01-10 10:00:00', '2024-10-15 10:00:00', 300, 400, FALSE, 'good', 'uploads/placeholder.png'),
(2, 2, 'Fashion 2', 'Handmade leather shoes.', '2024-03-12 11:00:00', '2024-10-01 11:00:00', 150, 200, FALSE, 'used', 'uploads/placeholder.png'),
(2, 1, 'Fashion 3', 'Luxury silk scarf.', '2024-02-01 12:00:00', '2025-01-10 12:00:00', 80, 100, TRUE, 'new', 'uploads/placeholder.png'),
(2, 2, 'Fashion 4', 'Vintage designer handbag.', '2024-04-05 13:00:00', '2025-02-15 13:00:00', 500, 600, TRUE, 'good', 'uploads/placeholder.png'),
(2, 1, 'Fashion 5', 'Winter boots, brand new.', '2024-05-15 14:00:00', '2025-01-18 14:00:00', 200, 250, TRUE, 'new', 'uploads/placeholder.png'),
(2, 2, 'Fashion 6', 'Elegant evening gown.', '2024-06-22 15:00:00', '2025-03-01 15:00:00', 800, 900, TRUE, 'good', 'uploads/placeholder.png'),
(2, 1, 'Fashion 7', 'Casual summer outfit set.', '2024-07-10 16:00:00', '2025-01-25 16:00:00', 100, 120, TRUE, 'new', 'uploads/placeholder.png'),
(2, 2, 'Fashion 8', 'Exclusive jewelry set.', '2024-08-20 17:00:00', '2025-01-30 17:00:00', 1200, 1500, TRUE, 'new', 'uploads/placeholder.png'),
-- Home Garden
(3, 1, 'Home Garden 1', 'Set of gardening tools in good condition.', '2024-01-15 10:00:00', '2024-11-01 10:00:00', 50, 70, FALSE, 'used', 'uploads/placeholder.png'),
(3, 2, 'Home Garden 2', 'Luxury outdoor furniture set.', '2024-02-12 11:00:00', '2024-10-10 11:00:00', 300, 400, FALSE, 'good', 'uploads/placeholder.png'),
(3, 1, 'Home Garden 3', 'Brand new smart sprinkler system.', '2024-03-01 12:00:00', '2025-01-15 12:00:00', 100, 150, TRUE, 'new', 'uploads/placeholder.png'),
(3, 2, 'Home Garden 4', 'Vintage garden bench, excellent condition.', '2024-04-05 13:00:00', '2025-02-15 13:00:00', 200, 250, TRUE, 'good', 'uploads/placeholder.png'),
(3, 1, 'Home Garden 5', 'Set of outdoor solar lights.', '2024-05-15 14:00:00', '2025-01-18 14:00:00', 50, 75, TRUE, 'new', 'uploads/placeholder.png'),
(3, 2, 'Home Garden 6', 'Antique flower pots collection.', '2024-06-22 15:00:00', '2025-03-01 15:00:00', 500, 700, TRUE, 'good', 'uploads/placeholder.png'),
(3, 1, 'Home Garden 7', 'New lawnmower with warranty.', '2024-07-10 16:00:00', '2025-01-25 16:00:00', 250, 300, TRUE, 'new', 'uploads/placeholder.png'),
(3, 2, 'Home Garden 8', 'Modern gazebo set.', '2024-08-20 17:00:00', '2025-01-30 17:00:00', 1000, 1200, TRUE, 'new', 'uploads/placeholder.png'),
-- Collectables & Art
(4, 1, 'Collectables & Art 1', 'Vintage oil painting, perfect for collectors.', '2024-01-15 10:00:00', '2024-10-20 10:00:00', 500, 700, FALSE, 'good', 'uploads/placeholder.png'),
(4, 2, 'Collectables & Art 2', 'Rare coin collection, includes coins from the 1800s.', '2024-03-10 11:00:00', '2024-11-05 11:00:00', 1000, 1500, FALSE, 'used', 'uploads/placeholder.png'),
(4, 1, 'Collectables & Art 3', 'Modern sculpture in excellent condition.', '2024-02-20 12:00:00', '2025-01-15 12:00:00', 1200, 1500, TRUE, 'new', 'uploads/placeholder.png'),
(4, 2, 'Collectables & Art 4', 'Handcrafted ceramic vases.', '2024-04-05 13:00:00', '2025-02-15 13:00:00', 300, 400, TRUE, 'good', 'uploads/placeholder.png'),
(4, 1, 'Collectables & Art 5', 'Antique wall clock in working condition.', '2024-05-15 14:00:00', '2025-01-18 14:00:00', 800, 1000, TRUE, 'used', 'uploads/placeholder.png'),
(4, 2, 'Collectables & Art 6', 'Signed photograph of a famous artist.', '2024-06-22 15:00:00', '2025-03-01 15:00:00', 300, 500, TRUE, 'new', 'uploads/placeholder.png'),
(4, 1, 'Collectables & Art 7', 'Vintage vinyl record collection.', '2024-07-10 16:00:00', '2025-01-25 16:00:00', 100, 150, TRUE, 'used', 'uploads/placeholder.png'),
(4, 2, 'Collectables & Art 8', 'Handmade tapestry, great for interior decor.', '2024-08-20 17:00:00', '2025-01-30 17:00:00', 700, 900, TRUE, 'good', 'uploads/placeholder.png'),
-- Sports, Hobbies & Leisure
(5, 1, 'Sports, Hobbies & Leisure 1', 'Mountain bike, well-maintained.', '2024-01-12 10:00:00', '2024-10-15 10:00:00', 400, 500, FALSE, 'used', 'uploads/placeholder.png'),
(5, 2, 'Sports, Hobbies & Leisure 2', 'Set of golf clubs with bag.', '2024-03-08 11:00:00', '2024-11-05 11:00:00', 800, 1000, FALSE, 'good', 'uploads/placeholder.png'),
(5, 1, 'Sports, Hobbies & Leisure 3', 'Brand new badminton racket.', '2024-02-01 12:00:00', '2025-01-15 12:00:00', 50, 80, TRUE, 'new', 'uploads/placeholder.png'),
(5, 2, 'Sports, Hobbies & Leisure 4', 'Camping gear: tent, sleeping bags, and more.', '2024-04-10 13:00:00', '2025-02-15 13:00:00', 300, 400, TRUE, 'good', 'uploads/placeholder.png'),
(5, 1, 'Sports, Hobbies & Leisure 5', 'Fishing kit for beginners.', '2024-05-18 14:00:00', '2025-01-18 14:00:00', 100, 150, TRUE, 'new', 'uploads/placeholder.png'),
(5, 2, 'Sports, Hobbies & Leisure 6', 'Yoga mat and accessories.', '2024-06-25 15:00:00', '2025-03-01 15:00:00', 30, 50, TRUE, 'new', 'uploads/placeholder.png'),
(5, 1, 'Sports, Hobbies & Leisure 7', 'Kite flying set for kids.', '2024-07-15 16:00:00', '2025-01-25 16:00:00', 20, 30, TRUE, 'used', 'uploads/placeholder.png'),
(5, 2, 'Sports, Hobbies & Leisure 8', 'Professional soccer ball.', '2024-08-22 17:00:00', '2025-01-30 17:00:00', 50, 70, TRUE, 'new', 'uploads/placeholder.png'),
-- Electronics
(6, 1, 'Electronics 1', 'Smartphone with minor scratches.', '2024-01-10 10:00:00', '2024-10-10 10:00:00', 200, 250, FALSE, 'used', 'uploads/placeholder.png'),
(6, 2, 'Electronics 2', 'Laptop, lightly used, with charger.', '2024-02-15 11:00:00', '2024-11-05 11:00:00', 800, 1000, FALSE, 'good', 'uploads/placeholder.png'),
(6, 1, 'Electronics 3', 'Brand new noise-cancelling headphones.', '2024-03-01 12:00:00', '2025-01-15 12:00:00', 150, 200, TRUE, 'new', 'uploads/placeholder.png'),
(6, 2, 'Electronics 4', 'Smart TV, perfect for streaming.', '2024-04-05 13:00:00', '2025-02-15 13:00:00', 500, 600, TRUE, 'good', 'uploads/placeholder.png'),
(6, 1, 'Electronics 5', 'Gaming monitor with high refresh rate.', '2024-05-15 14:00:00', '2025-01-18 14:00:00', 300, 400, TRUE, 'new', 'uploads/placeholder.png'),
(6, 2, 'Electronics 6', 'Portable Bluetooth speaker.', '2024-06-22 15:00:00', '2025-03-01 15:00:00', 80, 100, TRUE, 'new', 'uploads/placeholder.png'),
(6, 1, 'Electronics 7', 'Smart home assistant device.', '2024-07-10 16:00:00', '2025-01-25 16:00:00', 120, 150, TRUE, 'new', 'uploads/placeholder.png'),
(6, 2, 'Electronics 8', 'High-end gaming console.', '2024-08-20 17:00:00', '2025-01-30 17:00:00', 400, 500, TRUE, 'new', 'uploads/placeholder.png'),
-- Health & Beauty
(7, 1, 'Health & Beauty 1', 'Skin care set, unopened.', '2024-01-10 10:00:00', '2024-10-20 10:00:00', 50, 70, FALSE, 'new', 'uploads/placeholder.png'),
(7, 2, 'Health & Beauty 2', 'Massage chair, excellent condition.', '2024-02-15 11:00:00', '2024-11-10 11:00:00', 700, 900, FALSE, 'good', 'uploads/placeholder.png'),
(7, 1, 'Health & Beauty 3', 'Hair dryer with advanced settings.', '2024-03-05 12:00:00', '2025-01-15 12:00:00', 100, 150, TRUE, 'new', 'uploads/placeholder.png'),
(7, 2, 'Health & Beauty 4', 'Fitness tracker, brand new.', '2024-04-12 13:00:00', '2025-02-15 13:00:00', 200, 300, TRUE, 'new', 'uploads/placeholder.png'),
(7, 1, 'Health & Beauty 5', 'Luxury perfume set.', '2024-05-18 14:00:00', '2025-01-18 14:00:00', 150, 200, TRUE, 'new', 'uploads/placeholder.png'),
(7, 2, 'Health & Beauty 6', 'Electric toothbrush.', '2024-06-25 15:00:00', '2025-03-01 15:00:00', 50, 70, TRUE, 'new', 'uploads/placeholder.png'),
(7, 1, 'Health & Beauty 7', 'Set of yoga accessories.', '2024-07-20 16:00:00', '2025-01-25 16:00:00', 60, 80, TRUE, 'new', 'uploads/placeholder.png'),
(7, 2, 'Health & Beauty 8', 'Makeup organizer with accessories.', '2024-08-20 17:00:00', '2025-01-30 17:00:00', 80, 100, TRUE, 'new', 'uploads/placeholder.png'),
-- Business, Office & Industrial Supplies
(8, 1, 'Business, Office & Industrial Supplies 1', 'Ergonomic office chair, slightly used.', '2024-01-05 10:00:00', '2024-10-25 10:00:00', 150, 200, FALSE, 'used', 'uploads/placeholder.png'),
(8, 2, 'Business, Office & Industrial Supplies 2', 'Office desk with drawers.', '2024-02-15 11:00:00', '2024-11-15 11:00:00', 300, 350, FALSE, 'good', 'uploads/placeholder.png'),
(8, 1, 'Business, Office & Industrial Supplies 3', 'High-speed printer, excellent condition.', '2024-03-10 12:00:00', '2025-01-15 12:00:00', 400, 450, TRUE, 'good', 'uploads/placeholder.png'),
(8, 2, 'Business, Office & Industrial Supplies 4', 'Industrial-grade filing cabinets.', '2024-04-12 13:00:00', '2025-02-15 13:00:00', 500, 600, TRUE, 'used', 'uploads/placeholder.png'),
(8, 1, 'Business, Office & Industrial Supplies 5', 'Whiteboard set, perfect for meetings.', '2024-05-20 14:00:00', '2025-01-18 14:00:00', 80, 100, TRUE, 'new', 'uploads/placeholder.png'),
(8, 2, 'Business, Office & Industrial Supplies 6', 'Professional shredder for office use.', '2024-06-25 15:00:00', '2025-03-01 15:00:00', 150, 200, TRUE, 'good', 'uploads/placeholder.png'),
(8, 1, 'Business, Office & Industrial Supplies 7', 'Conference room projector.', '2024-07-15 16:00:00', '2025-01-25 16:00:00', 300, 400, TRUE, 'new', 'uploads/placeholder.png'),
(8, 2, 'Business, Office & Industrial Supplies 8', 'Bulk pack of office supplies.', '2024-08-22 17:00:00', '2025-01-30 17:00:00', 50, 70, TRUE, 'new', 'uploads/placeholder.png'),
-- Media
(9, 1, 'Media 1', 'Classic movie DVD set.', '2024-01-10 10:00:00', '2024-10-20 10:00:00', 30, 50, FALSE, 'used', 'uploads/placeholder.png'),
(9, 2, 'Media 2', 'Collection of vintage comics.', '2024-02-18 11:00:00', '2024-11-25 11:00:00', 100, 150, FALSE, 'used', 'uploads/placeholder.png'),
(9, 1, 'Media 3', 'Brand new bestseller novel.', '2024-03-01 12:00:00', '2025-01-15 12:00:00', 20, 30, TRUE, 'new', 'uploads/placeholder.png'),
(9, 2, 'Media 4', 'Digital art magazine subscription.', '2024-04-15 13:00:00', '2025-02-15 13:00:00', 50, 70, TRUE, 'new', 'uploads/placeholder.png'),
(9, 1, 'Media 5', 'Vinyl record of classic rock album.', '2024-05-25 14:00:00', '2025-01-18 14:00:00', 60, 80, TRUE, 'good', 'uploads/placeholder.png'),
(9, 2, 'Media 6', 'Audio course on language learning.', '2024-06-30 15:00:00', '2025-03-01 15:00:00', 40, 50, TRUE, 'new', 'uploads/placeholder.png'),
(9, 1, 'Media 7', 'Box set of popular TV series.', '2024-07-20 16:00:00', '2025-01-25 16:00:00', 100, 150, TRUE, 'used', 'uploads/placeholder.png'),
(9, 2, 'Media 8', 'Collector’s edition comic book.', '2024-08-25 17:00:00', '2025-01-30 17:00:00', 120, 150, TRUE, 'new', 'uploads/placeholder.png');


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

-- Insert Watchlists (linked to buyers and auctions)
INSERT INTO Watchlists (buyerID, auctionID)
VALUES 
    (3, 1), 
    (3, 2),
    (4, 3), 
    (4, 4);
