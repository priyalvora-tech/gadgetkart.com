# GadgetKart – Mini Electronics E-Commerce Website

A college-friendly mini e-commerce website built with **HTML, CSS, JavaScript, PHP and MySQL**.

## Categories
- Mobile Accessories: Chargers, Cables, Power Banks, Phone Cases
- Computer Accessories: Mouse, Keyboard, Webcams, USB Hubs
- Gaming: Headsets, Controllers, Gaming Mouse, Gaming Keyboard

## Features
- Responsive home page
- Product catalogue with local product images
- Search, category filter and price/name sorting
- Individual product details
- PHP session-based shopping cart
- Registration and login with password hashing
- Checkout and order storage in MySQL
- Contact form stored in MySQL
- Admin dashboard
- Admin product add/edit/archive functionality with direct PC image upload & persistent asset storage
- JavaScript validation and UI interactions

## Software required
- XAMPP (Apache + MySQL + PHP 8.x recommended)
- Web browser (Chrome, Edge, Firefox, etc.)

## Installation in XAMPP
1. Copy the `GadgetKart` folder into `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** from XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin/`.
4. Import `database/gadgetkart.sql`.
5. Check the DB settings in `includes/config.php`:
   - Host: `localhost`
   - Database: `gadgetkart`
   - User: `root`
   - Password: empty by default in XAMPP
6. Open `http://localhost/GadgetKart/`.

## Demo Admin Login
- Email: `admin@gadgetkart.local`
- Password: `Admin@123`

## Important
The project uses PHP sessions for the shopping cart. No payment gateway is connected; checkout stores a demo order in MySQL.

## Suggested viva explanation
**HTML:** page structure and forms  
**CSS:** responsive design and styling  
**JavaScript:** validation, navigation interaction and client-side behaviour  
**PHP:** server-side processing, sessions, authentication, cart and database operations  
**MySQL:** users, products, orders, order items and contact messages
