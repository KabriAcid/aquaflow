# Aquaflow Application Blueprint

## Overview

Aquaflow is a web application designed to facilitate the sale and delivery of bottled water. The application provides a customer-facing interface for ordering water and an admin portal for managing orders, customers, and products.

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

*   **API:** A central `ajax.php` file handles all asynchronous requests from the admin portal.
*   **Authentication:** Session-based authentication is used to manage user logins and protect admin routes.
*   **API Actions (`frontend/api/ajax.php`):**
    *   **Product Actions:** `get_products`, `get_product`, `add_product`, `update_product`, `delete_product`.
    *   **Order Actions:** `get_orders` (with search and filtering).
    *   **Customer Actions:** `get_customers` (with search), `get_customer_details`, `get_customer_orders`.
    *   **Order Detail Actions:** `get_order_details`.

## Current Task: Initial Admin Portal Development Complete

**Summary:**

The initial scaffolding and front-end development of the admin portal are now complete. The core sections for managing customers, orders, and products have been created with a consistent design and user experience. The front-end is powered by a centralized API (`ajax.php`) that currently serves placeholder data. The next major phase will be to connect the application to a live database.

**Completed Steps:**

1.  **Created Customer Management Pages:**
    *   `manage-customers.php`: Displays a searchable list of all customers.
    *   `customer-details.php`: Shows detailed customer information and their order history.
    *   `admin-customers.js` & `admin-customer-details.js`: Implemented AJAX calls for customer data.
2.  **Created Order Management Pages:**
    *   `manage-orders.php`: Displays a searchable and filterable list of all orders.
    *   `order-details.php`: Shows comprehensive details for a single order.
    *   `admin-orders.js` & `admin-order-details.js`: Implemented AJAX calls for order data.
3.  **Created Product Management Page:**
    *   `manage-products.php`: Displays a list of products with a modal for adding/editing.
    *   `admin-products.js`: Implemented AJAX calls for product CRUD operations.
4.  **Implemented Central API Endpoint:**
    *   Created `frontend/api/ajax.php` to handle all admin-related API requests with placeholder data and logic.