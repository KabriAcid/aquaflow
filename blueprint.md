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
    *   **Manage Products (`manage-products.php`):** A list of all water products, with options to add, edit, or delete products.
    *   **Shared Partials:** The admin portal uses shared partials for the sidebar, top bar, and footer to ensure a consistent look and feel.

### Backend

*   **API:** A central `ajax.php` file handles all asynchronous requests from the admin portal.
*   **Authentication:** Session-based authentication is used to manage user logins and protect admin routes.
*   **API Actions (`frontend/api/ajax.php`):**
    *   **`get_customers`:** Returns a list of all customers. Supports searching by name or email.
    *   **`get_customer_details`:** Returns details for a single customer.
    *   **`get_customer_orders`:** Returns the order history for a single customer.
    *   **`get_order_details`:** Returns complete details for a single order.
    *   *Other actions for products and dashboard widgets to be implemented.*

## Current Task: Build Admin Portal

**Plan:**

1.  **Create Customer Management Pages:**
    *   `manage-customers.php`: Display a searchable list of all customers.
    *   `customer-details.php`: Show detailed customer information and their order history.
    *   `admin-customers.js`: Implement AJAX calls to fetch and display customer data.
    *   `admin-customer-details.js`: Implement AJAX calls for the customer detail view.
2.  **Create Order Management Pages:**
    *   `manage-orders.php`: Display a searchable and filterable list of all orders. *(Yet to be created)*
    *   `order-details.php`: Show comprehensive details for a single order.
    *   `admin-orders.js`: Implement AJAX calls to fetch and display order data. *(Yet to be created)*
    *   `admin-order-details.js`: Implement AJAX calls for the order detail view.
3.  **Implement API Endpoint:**
    *   Create `frontend/api/ajax.php` to handle all admin-related API requests.
    *   Add actions for `get_customers`, `get_customer_details`, `get_customer_orders`, and `get_order_details` with placeholder data.