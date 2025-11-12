## Project Overview (Client-Friendly)
Aquaflow is a modern web-based management system for a water & beverage factory. It centralizes product catalog, inventory levels, production logging, customer ordering, sales oversight, and performance reporting. Four user roles interact with the platform:
- Admin: Full control (users, products, orders, inventory, reports, settings)
- Sales Manager: Manages customer orders and sales performance
- Production Manager: Logs production output, monitors materials & stock
- Customer: Browses products, places and tracks orders

# Assets
- I have provided all the sample exported reports (CSV) for the admin reportings page.
- The SQL schema is provided in the schema folder as aquaflow.sql

## Login details ()

Password for all the roles is Pa$$w0rd!

Admin:
- Email: admin@aquaflow.com (Role: admin)
- Password: Pa$$w0rd!

Sales Manager:
- Email: sales@aquaflow.com (Role: sales_manager)
- Password: Pa$$w0rd!

Production Manager:
- Email: production@aquaflow.com (Role: production_manager)
- Password: Pa$$w0rd!

Customer:
- Email: customer@aquaflow.com (Role: customer)
- Password: Pa$$w0rd!

## What It Does for Your Business
- Real-time visibility into orders, stock status, and production output
- Simplifies team workflows (sales ↔ inventory ↔ production)
- Generates actionable sales, inventory, and financial reports
- Reduces manual errors and improves fulfillment speed
- Scales as product lines, staff, and order volume grow

## Technology (Explained Simply)
- Backend Logic: PHP scripts (fast, lightweight server processing) in backend/
- Data Storage: MySQL database (structured factory data)
- Reporting Engine: Python microservice (advanced reports) in app.py
- Interface: HTML + Tailwind CSS (clean, responsive design) and vanilla JavaScript for interactivity (charts, forms, dashboards)
- Visuals: Chart.js (graphs) + Lucide icons (consistent iconography)
- Security: Session and token-based access; role-based page restriction
- Key Config Files: .env (credentials), database.php, constants.php

## Core Folders (Plain Meaning)
- frontend/: User-facing pages (public, admin, sales, production, customer portals)
- backend/api/: Data endpoints (products, users, orders, inventory, reports)
- reporting/: Python reporting service (CSV exports, analytics)
- uploads/: Product images
- logs/: System logs
- docs/: Reference and planning (e.g. prompt.md, docs/API.md)

## Installation (Step by Step)
1. Prerequisites: Install XAMPP (Apache + MySQL) and Python 3.8+
2. Clone or copy the project into your web root (e.g. htdocs/aquaflow)
3. Create database aquaflow in phpMyAdmin; import schema (see docs/DATABASE.md)
4. Configure environment: edit .env with DB credentials
5. Install PHP dependencies (if any): run `composer install` using composer.json
6. Start Apache & MySQL in XAMPP
7. (Optional Reports) Setup Python service:
   - `cd reporting`
   - `python -m venv venv`
   - Activate venv (Windows: `venv\Scripts\activate`, mac/Linux: `source venv/bin/activate`)
   - `pip install -r requirements.txt` (see README.md and root requirements.txt)
   - Run: `python app.py` (listens on http://127.0.0.1:5001)
8. Access the web app at: http://localhost/aquaflow/index.php
9. Create initial admin account via registration, then assign roles (user management pages in frontend/admin/)

## Using the System
- Admin logs in and configures products, stock, and team
- Sales managers process and update customer orders
- Production managers log daily output and watch material levels
- Customers place and monitor orders online
- Reports (sales, inventory, financial) generated via admin portal (frontend/admin/reports.php) using backend + Python microservice

## Maintenance
- Update product data via admin product pages
- Monitor low stock alerts in inventory screens
- Run Python service when advanced reporting is needed
- Adjust system settings in settings.php
- Backups: dump MySQL database regularly; copy uploads

## Key Benefits Summary
Faster operations, fewer mistakes, better visibility, easier scaling, and unified team workflow.