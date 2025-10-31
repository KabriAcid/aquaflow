# Aquaflow Application Blueprint

## Overview

Aquaflow is a web application designed to facilitate the sale and delivery of bottled water. The application provides a customer-facing interface for ordering water and a sales manager portal for managing orders, customers, and products.

## Project Structure

```
/
|-- backend/
|   |-- api/
|   |   |-- auth/
|   |   |   |-- login.php
|   |   |   |-- logout.php
|   |   |   `-- register.php
|   |   |-- customers/
|   |   |   |-- get_all.php
|   |   |   `-- get_single.php
|   |   |-- orders/
|   |   |   |-- create.php
|   |   |   |-- get_all.php
|   |   |   `-- get_single.php
|   |   |-- products/
|   |   |   |-- create.php
|   |   |   |-- delete.php
|   |   |   |-- get_all.php
|   |   |   |-- get_single.php
|   |   |   `-- update.php
|   |   `-- sales/
|   |       `-- summary.php
|   |-- utils/
|   |   |-- auth.php
|   |   `-- response.php
|   `-- config/
|       `-- database.php
|-- frontend/
|   |-- css/
|   |   `-- tailwind.css
|   |-- images/
|   |-- js/
|   |   `-- main.js
|   |-- sales/
|   |   |-- partials/
|   |   |   |-- footer.php
|   |   |   |-- sidebar.php
|   |   |   `-- topbar.php
|   |   |-- add-product.php
|   |   |-- customer-details.php
|   |   |-- customers.php
|   |   |-- dashboard.php
|   |   |-- edit-product.php
|   |   |-- order-details.php
|   |   |-- orders.php
|   |   `-- products.php
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

*   **Styling:** The frontend is styled using Tailwind CSS, providing a modern and responsive design.
*   **Customer Pages:**
    *   **Home (`index.php`):** The main landing page.
    *   **Shop (`shop.php`):** Displays available water products for purchase.
    *   **About (`about.php`):** Provides information about Aquaflow.
    *   **Contact (`contact.php`):** A contact form for inquiries.
    *   **Login (`login.php`):** A login page for both customers and sales managers.
    *   **Register (`register.php`):** A registration page for new customers.
*   **Sales Manager Portal (`frontend/sales/`):**
    *   **Dashboard (`dashboard.php`):** Provides a summary of key sales metrics, including total sales, total orders, new customers, and pending deliveries.
    *   **Orders (`orders.php`):** A list of all customer orders, with links to view order details. Includes search by customer name/order ID and filtering by order status.
    *   **Order Details (`order-details.php`):** Detailed information about a specific order, including the customer, order items, and total amount.
    *   **Customers (`customers.php`):** A list of all registered customers. Includes search by customer name/email.
    *   **Customer Details (`customer-details.php`):** Detailed information about a specific customer, including their order history.
    *   **Products (`products.php`):** A list of all water products, with options to add, edit, or delete products. Includes search by product name.
    *   **Add Product (`add-product.php`):** A form to add a new product.
    *   **Edit Product (`edit-product.php`):** A form to edit an existing product.
    *   **Shared Partials:** The sales portal uses shared partials for the sidebar, top bar, and footer to ensure a consistent look and feel.

### Backend

*   **API:** The backend provides a RESTful API for all frontend functionality.
*   **Authentication:**
    *   Session-based authentication is used to manage user logins.
    *   The `backend/utils/auth.php` file provides a centralized function for role-based access control.
    *   The `login.php` endpoint handles user authentication and session creation.
    *   The `logout.php` endpoint destroys the user session.
    *   The `register.php` endpoint handles new customer registration.
*   **Database:**
    *   The application uses a MySQL database to store user, product, and order data.
    *   The `config/database.php` file contains the database connection settings.
*   **API Endpoints:**
    *   **Sales Summary (`/api/sales/summary.php`):** Returns a summary of sales data for the sales manager dashboard. (Requires `sales_manager` role)
    *   **Orders (`/api/orders/`):**
        *   `get_all.php`: Returns a list of all orders. (Accessible by `sales_manager` and `customer` roles)
        *   `get_single.php`: Returns a single order by ID. (Accessible by `sales_manager` and the `customer` who owns the order)
        *   `create.php`: Creates a new order. (Accessible by `customer` role)
    *   **Customers (`/api/customers/`):**
        *   `get_all.php`: Returns a list of all customers. (Requires `sales_manager` role)
        *   `get_single.php`: Returns a single customer by ID. (Accessible by `sales_manager` and the `customer` themselves)
    *   **Products (`/api/products/`):**
        *   `get_all.php`: Returns a list of all products. (Accessible by `sales_manager` and `customer` roles)
        *   `get_single.php`: Returns a single product by ID. (Accessible by `sales_manager` and `customer` roles)
        *   `create.php`: Creates a new product. (Requires `sales_manager` role)
        *   `update.php`: Updates an existing product. (Requires `sales_manager` role)
        *   `delete.php`: Deletes a product. (Requires `sales_manager` role)

## Current Task: Enhance User Experience with Search and Filtering

**Plan:**

1.  **Add Search to Products Page:**
    *   Modify `frontend/sales/products.php` to include a search bar that filters products by name.
2.  **Add Search to Customers Page:**
    *   Modify `frontend/sales/customers.php` to include a search bar that filters customers by name or email.
3.  **Add Search and Filtering to Orders Page:**
    *   Modify `frontend/sales/orders.php` to include a search bar that filters orders by customer name or order ID.
    *   Add a dropdown to filter orders by status (pending, shipped, delivered, cancelled).
