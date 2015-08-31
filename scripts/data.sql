--
-- Database: `OfficeBuilder`
--

-- Date Variables
SET @dateThreeDays = DATE(ADDDATE(NOW(), INTERVAL 3 DAY));
SET @dateFourDays = DATE(ADDDATE(NOW(), INTERVAL 4 DAY));
SET @dateFiveDays = DATE(ADDDATE(NOW(), INTERVAL 5 DAY));
SET @year = YEAR(NOW());

--
-- Dumping data for table `Inventory`
--

INSERT INTO `Inventory` (`id`, `product_id`, `units`, `ppu`, `datetime`, `created_on`) VALUES
(1, 1, 12, 3.5, CONCAT(@dateThreeDays, ' 16:00:00'), NOW()),
(2, 1, 4, 4.5, CONCAT(@dateThreeDays, ' 17:00:00'), NOW()),
(3, 1, 4, 5, CONCAT(@dateThreeDays, ' 18:00:00'), NOW()),
(4, 1, 4, 6, CONCAT(@dateThreeDays, ' 19:00:00'), NOW()),
(5, 2, 1, 4.5, CONCAT(@dateThreeDays, ' 17:00:00'), NOW()),
(6, 2, 2, 5, CONCAT(@dateThreeDays, ' 18:00:00'), NOW()),
(7, 2, 2, 6, CONCAT(@dateThreeDays, ' 19:00:00'), NOW()),
(8, 3, 2, 36, CONCAT(@dateThreeDays, ' 00:00:00'), NOW()),
(9, 3, 2, 36, CONCAT(@dateFourDays, ' 00:00:00'), NOW()),
(10, 3, 2, 36, CONCAT(@dateFiveDays, ' 00:00:00'), NOW()),
(11, 4, 2, 25, CONCAT(@dateThreeDays, ' 00:00:00'), NOW()),
(12, 4, 2, 25, CONCAT(@dateFourDays, ' 00:00:00'), NOW()),
(13, 4, 2, 25, CONCAT(@dateFiveDays, ' 00:00:00'), NOW()),
(14, 5, 2, 18, CONCAT(@dateThreeDays, ' 00:00:00'), NOW()),
(15, 5, 2, 18, CONCAT(@dateFourDays, ' 00:00:00'), NOW()),
(16, 5, 2, 18, CONCAT(@dateFiveDays, ' 00:00:00'), NOW()),
(17, 6, 5, 4800, CONCAT(@year, '-12-31 00:00:00'), NOW()),
(18, 7, 3, 7200, CONCAT(@year, '-12-31 00:00:00'), NOW()),
(19, 8, 5, 2400, CONCAT(@year, '-12-31 00:00:00'), NOW());

--
-- Dumping data for table `Products`
--

INSERT INTO `Products` (`id`, `name`, `description`, `type`, `category`, `created_on`) VALUES
(1, 'Ping Pong - Table 1', 'Play ping pong pong at table one. Buy a spot for one hour of play!', 'DateTime', 'pingpong', NOW()),
(2, 'Ping Pong - Table 2', 'Play ping pong pong at table two. Buy a spot for one hour of play!', 'DateTime', 'pingpong', NOW()),
(3, 'The Window Seat', 'Sit right next to the window for a day. Enjoy the sunlight!', 'Day', 'window', NOW()),
(4, 'The Aisle Seat', 'Site in the aisle, be the first to get out.', 'Day', 'window', NOW()),
(5, 'The Middle Seat', 'What a value! A seat that''s priced right.', 'Day', 'window', NOW()),
(6, 'L-Shaped Office', 'An L shaped floor plan that comfortably fits 2 people.', 'Year', 'office', NOW()),
(7, 'T-Shaped Office', 'Our largest office. Fits three people comfortably.', 'Year', 'office', NOW()),
(8, 'Cramped Lil'' Office', 'Need to get away from the noise! Set up shop in our entry-level office.', 'Year', 'office', NOW());
