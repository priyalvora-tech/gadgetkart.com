CREATE DATABASE IF NOT EXISTS gadgetkart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gadgetkart;

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  is_admin TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  category VARCHAR(80) NOT NULL,
  subcategory VARCHAR(80) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  image VARCHAR(255) NOT NULL,
  short_description VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  address TEXT NOT NULL,
  city VARCHAR(80) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  status VARCHAR(40) NOT NULL DEFAULT 'Placed',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Demo admin password: Admin@123
INSERT INTO users(name,email,password,is_admin) VALUES
('GadgetKart Admin','admin@gadgetkart.local','$2y$12$YYdIJZexsOlH9II6vWr6e.s4lwr3dXV79lehN8t2zvDexQkTjRuie',1);

INSERT INTO products(name,category,subcategory,price,stock,image,short_description,description,is_featured) VALUES
('65W Fast USB-C Charger','Mobile Accessories','Chargers',1299,25,'charger.png','Compact GaN-style fast charger for everyday devices.','A compact 65W USB-C charger designed for phones, tablets and compatible laptops. Clean design, travel-friendly size and fast charging support.',1),
('20W PD Wall Charger','Mobile Accessories','Chargers',699,40,'charger20.png','Pocket-sized power delivery charger.','Reliable 20W power delivery charger with a modern compact body for everyday smartphone charging.',1),
('Braided USB-C Cable 2m','Mobile Accessories','Cables',449,60,'cable.png','Durable 2-metre charging and data cable.','A flexible braided USB-C cable built for repeated everyday charging and data transfer.',1),
('USB-C to Lightning Cable','Mobile Accessories','Cables',599,35,'cable-lightning.png','Convenient cable for compatible Apple devices.','A practical USB-C to Lightning style cable for charging and data transfer on compatible devices.',0),
('10,000mAh Power Bank','Mobile Accessories','Power Banks',1199,30,'powerbank.png','Slim backup power with dual outputs.','A portable 10,000mAh power bank with dual output support for daily travel and emergency charging.',1),
('20,000mAh Power Bank','Mobile Accessories','Power Banks',1699,22,'powerbank20.png','High-capacity travel power companion.','A 20,000mAh capacity power bank for longer trips, commutes and multi-device charging.',0),
('ShockGuard Phone Case','Mobile Accessories','Phone Cases',399,50,'phonecase.png','Slim protective case with raised edges.','A stylish everyday phone case featuring raised edge protection and a grippy finish.',1),
('Clear MagSafe Style Case','Mobile Accessories','Phone Cases',549,45,'phonecase-clear.png','Minimal clear case with magnetic ring design.','A transparent protective case with a clean magnetic-ring style pattern for compatible devices.',0),
('SilentClick Wireless Mouse','Computer Accessories','Mouse',899,28,'mouse.png','Quiet wireless mouse with ergonomic shape.','A comfortable wireless mouse for study, office and everyday computer use.',1),
('ProTrack RGB Mouse','Computer Accessories','Mouse',1499,20,'gaming-mouse.png','Responsive RGB mouse for work and play.','A responsive mouse with programmable buttons and RGB lighting for productivity and gaming setups.',1),
('MechaLite Mechanical Keyboard','Computer Accessories','Keyboard',1999,18,'keyboard.png','Compact mechanical-style typing experience.','A compact keyboard with tactile switches, clean legends and a desk-friendly layout.',1),
('OfficeType Wireless Keyboard','Computer Accessories','Keyboard',1199,25,'keyboard-wireless.png','Clean wireless keyboard for daily work.','A low-profile wireless keyboard designed for quiet everyday typing and flexible desk setups.',0),
('Full HD USB Webcam','Computer Accessories','Webcams',1799,26,'webcam.png','1080p camera for classes and meetings.','A plug-and-play Full HD webcam with built-in microphone for meetings, online classes and calls.',1),
('WideView Streaming Webcam','Computer Accessories','Webcams',2499,14,'webcam-pro.png','Wide-angle webcam with privacy shutter.','A wide-angle USB webcam with a privacy shutter, suitable for streaming, meetings and presentations.',0),
('4-Port USB 3.0 Hub','Computer Accessories','USB Hubs',799,35,'hub.png','Expand one USB port into four.','A compact 4-port USB hub for connecting peripherals, flash drives and other everyday accessories.',1),
('USB-C 6-in-1 Hub','Computer Accessories','USB Hubs',1599,24,'hub-usbc.png','Versatile expansion for modern laptops.','A multi-port USB-C hub combining common ports for displays, storage and peripherals.',0),
('Pulse Gaming Headset','Gaming','Headsets',2299,19,'headset.png','Immersive stereo audio with boom mic.','A comfortable gaming headset with cushioned earcups, adjustable headband and a boom microphone.',1),
('Arena 7.1 Gaming Headset','Gaming','Headsets',3299,12,'headset-pro.png','Surround-style gaming audio and RGB accents.','A gaming headset designed for positional audio, long sessions and an engaging desktop setup.',1),
('GamePad X Wireless Controller','Gaming','Controllers',2499,16,'controller.png','Wireless controller for compatible PCs.','A comfortable wireless controller with dual sticks, shoulder buttons and a familiar gamepad layout.',1),
('DualShock Style USB Controller','Gaming','Controllers',1299,30,'controller-usb.png','Plug-and-play wired controller.','A wired USB controller built for straightforward PC gaming and casual sessions.',0),
('Raptor RGB Gaming Mouse','Gaming','Gaming Mouse',1699,18,'gaming-mouse.png','Lightweight mouse with adjustable DPI.','A lightweight gaming mouse with adjustable DPI, RGB accents and programmable controls.',1),
('Clutch 60% Gaming Keyboard','Gaming','Gaming Keyboard',2499,15,'gaming-keyboard.png','Compact keyboard built for gaming desks.','A compact 60% keyboard concept with gaming-focused key response and RGB lighting.',1),
('Titan Full-Size Gaming Keyboard','Gaming','Gaming Keyboard',2899,13,'gaming-keyboard-full.png','Full layout with gaming-focused features.','A full-size gaming keyboard with dedicated navigation keys and programmable lighting.',0),
('RGB Desk Mat XL','Gaming','Gaming Mouse',999,21,'deskmat.png','Large surface for mouse and keyboard.','An oversized desk mat designed for smoother mouse movement and a coordinated gaming setup.',0);

