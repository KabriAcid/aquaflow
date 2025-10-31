-- Sample products to insert into aquaflow.products
-- Run this after importing schema/aquaflow.sql

INSERT INTO products (name, category, size, volume, unit_price, minimum_order_quantity, description, status) VALUES
('Sparkling Water - Lime', 'bottled_water', 'Regular', '500ml', 75.00, 24, 'Sparkling water with a hint of lime', 'active'),
('Sparkling Water - Berry', 'bottled_water', 'Regular', '500ml', 80.00, 24, 'Sparkling water with mixed berry flavor', 'active'),
('Mineral Water - Still', 'bottled_water', 'Large', '2L', 180.00, 12, 'Mineral still water - 2L bottle', 'active'),
('Natural Spring Water', 'bottled_water', 'Small', '330ml', 40.00, 60, 'Small 330ml natural spring water', 'active'),
('Tropical Punch', 'beverage', 'Regular', '500ml', 160.00, 24, 'Tropical fruit punch drink', 'active'),
('Iced Tea - Lemon', 'beverage', 'Regular', '500ml', 140.00, 24, 'Refreshing iced lemon tea', 'active'),
('ElectroBoost - Mango', 'beverage', 'Standard', '330ml', 210.00, 24, 'Electrolyte beverage - mango flavor', 'active'),
('Family Mega Pack Water', 'package', 'Pack of 48', '500ml x 48', 1900.00, 2, 'Bulk pack of 48 x 500ml bottles', 'active'),
('Office Starter Pack', 'package', 'Pack of 36', '500ml x 36', 1400.00, 2, 'Office starter pack - 36 bottles', 'active'),
('Party Pack - Mixed', 'package', 'Pack of 60', 'various', 3500.00, 1, 'Large party pack with mixed beverages', 'active');

-- Optional: initialize inventory rows for the newly inserted products using product name lookup
INSERT INTO inventory (product_id, current_stock, minimum_stock_level)
SELECT id, 300, 50 FROM products WHERE name = 'Sparkling Water - Lime' LIMIT 1;
INSERT INTO inventory (product_id, current_stock, minimum_stock_level)
SELECT id, 250, 50 FROM products WHERE name = 'Sparkling Water - Berry' LIMIT 1;
INSERT INTO inventory (product_id, current_stock, minimum_stock_level)
SELECT id, 120, 30 FROM products WHERE name = 'Mineral Water - Still' LIMIT 1;
INSERT INTO inventory (product_id, current_stock, minimum_stock_level)
SELECT id, 600, 100 FROM products WHERE name = 'Natural Spring Water' LIMIT 1;
INSERT INTO inventory (product_id, current_stock, minimum_stock_level)
SELECT id, 200, 40 FROM products WHERE name = 'Tropical Punch' LIMIT 1;
INSERT INTO inventory (product_id, current_stock, minimum_stock_level)
SELECT id, 220, 40 FROM products WHERE name = 'Iced Tea - Lemon' LIMIT 1;
INSERT INTO inventory (product_id, current_stock, minimum_stock_level)
SELECT id, 180, 30 FROM products WHERE name = 'ElectroBoost - Mango' LIMIT 1;
INSERT INTO inventory (product_id, current_stock, minimum_stock_level)
SELECT id, 60, 10 FROM products WHERE name = 'Family Mega Pack Water' LIMIT 1;
INSERT INTO inventory (product_id, current_stock, minimum_stock_level)
SELECT id, 80, 10 FROM products WHERE name = 'Office Starter Pack' LIMIT 1;
INSERT INTO inventory (product_id, current_stock, minimum_stock_level)
SELECT id, 30, 5 FROM products WHERE name = 'Party Pack - Mixed' LIMIT 1;

-- End of sample products
