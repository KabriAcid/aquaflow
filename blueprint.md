# Aquaflow Application Blueprint

## Overview

Aquaflow is a web application designed to facilitate the sale and delivery of bottled water. The application provides a customer-facing interface for ordering water and an admin portal for managing orders, customers, and products. The application is now fully connected to a MySQL database.

## Project Structure

```
/
|-- frontend/
|   |-- admin/
|   |   |-- partials/
|   |   |   |-- footer.php
|   |   |   |-- sidebar.php
|   |   |   `-- topbar.php
|   |   |-- customer-details.php
|   |   |-- dashboard.php
|   |   |-- manage-customers.php
|   |   |-- manage-orders.php
|   |   |-- manage-products.php
|   |   `-- order-details.php
|   |-- api/
|   |   `-- ajax.php
|   |-- css/
|   |   |-- tailwind.css
|   |   `-- style.css
|   |-- images/
|   |-- js/
|   |   |-- admin-customers.js
|   |   |-- admin-customer-details.js
|   |   |-- admin-dashboard.js
|   |   |-- admin-orders.js
|   |   |-- admin-order-details.js
|   |   `-- admin-products.js
|   |-- about.php
|   |-- contact.php
|   |-- config.php
|   |-- index.php
|   |-- login.php
|   |-- register.php
|   `-- shop.php
`-- index.php
```

## Style, Design, and Features

### Frontend

*   **Styling:** The frontend is styled using Tailwind CSS and a custom stylesheet (`style.css`), providing a modern and responsive design.
*   **Customer Pages:**
    *   **Home (`index.php`):** The main landing page.
    *   **Shop (`shop.php`):** Displays available water products for purchase.
    *   **About (`about.php`):** Provides information about Aquaflow.
    *   **Contact (`contact.php`):** A contact form for inquiries.
    *   **Login (`login.php`):** A login page for both customers and admins.
    *   **Register (`register.php`):** A registration page for new customers.
*   **Admin Portal (`frontend/admin/`):**
    *   **Dashboard (`dashboard.php`):** Provides a summary of key sales metrics.
    *   **Manage Customers (`manage-customers.php`):** A list of all registered customers with search functionality.
    *   **Customer Details (`customer-details.php`):** Detailed information about a specific customer, including their order history.
    *   **Manage Orders (`manage-orders.php`):** A list of all customer orders, with search and filtering capabilities.
    *   **Order Details (`order-details.php`):** Detailed information about a specific order.
    *   **Manage Products (`manage-products.php`):** A list of all water products, with options to add, edit, or delete products via a modal interface.
    *   **Shared Partials:** The admin portal uses shared partials for the sidebar, top bar, and footer to ensure a consistent look and feel.

### Backend

*   **Database:** The application uses a MySQL database (`aquaflow`) to store all data.
*   **Configuration:** A `config.php` file stores database credentials and other configuration settings.
*   **API:** A central `ajax.php` file handles all asynchronous requests from the admin portal. It uses PDO with prepared statements to ensure secure database interactions.
*   **Authentication:** Session-based authentication is used to manage user logins and protect admin routes.
*   **API Actions (`frontend/api/ajax.php`):**
    *   **Product Actions:** Full CRUD functionality for products.
    *   **Customer Actions:** Full CRUD functionality for customers.
    *   **Order Actions:** Full CRUD functionality for orders.

## Current Task: Admin Portal & Database Integration Complete

**Summary:**

The development of the admin portal is now complete, with full integration into a MySQL database. All placeholder data and logic have been replaced with live database queries using secure prepared statements. The application now provides a robust and fully functional back-end for managing products, customers, and orders.

**Completed Steps:**

1.  **Admin Portal Scaffolding:**
    *   Created all necessary PHP files for the dashboard, customer management, order management, and product management sections.
    *   Implemented the front-end layout with HTML, Tailwind CSS, and custom styles.
    *   Created corresponding JavaScript files to handle dynamic content and user interactions.
2.  **API Implementation:**
    *   Developed a central `ajax.php` endpoint to handle all back-end requests.
    *   Initially implemented API actions with placeholder data.
3.  **Database Integration:**
    *   Created a `config.php` file for database credentials.
    *   Connected the application to a MySQL database using PDO.
    *   Replaced all placeholder logic in `ajax.php` with live database queries.
    *   Implemented secure CRUD operations for products, customers, and orders using prepared statements.
4.  **Finalization:**
    *   The admin portal is now fully functional and data-driven.
    *   The project is ready for further feature development or deployment.