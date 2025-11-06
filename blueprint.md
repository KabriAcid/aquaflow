# Aquaflow Application Blueprint

## Overview

Aquaflow is a web application designed to facilitate the sale and delivery of bottled water. The application provides three main user-facing portals: an Admin Portal for overall system management, a Sales Portal for sales managers, and a Production Portal for production managers.

## Project Structure

The project is organized into a `frontend` for the user interface and a `backend` for the API logic and database interactions.

-   **`backend/`**: Contains all server-side PHP scripts.
    -   `api/`: The core of the backend, with subdirectories for different functionalities (`admin`, `auth`, `products`, `reports`, `sales`, `users`, `production`).
    -   `config/`: Includes the `database.php` file for establishing a database connection.
    -   `utils/`: Contains helper scripts for handling authentication (`auth.php`) and formatting JSON responses (`response.php`).
-   **`frontend/`**: Contains all client-side assets.
    -   `css/`: `tailwind.css` for utility classes and a custom `style.css`.
    -   `js/`: JavaScript files for dynamic frontend behavior, prefixed with the portal name (e.g., `admin-dashboard.js`, `production-dashboard.js`).
    -   `admin/`: The interface for administrators.
    -   `sales/`: The interface for the sales team.
    -   `production/`: The interface for the production team.
    -   `login.php`: The main login page for all users.
-   **`index.php`**: The application's main entry point, which directs users to the appropriate portal or login page.

## Style, Design, and Features

### General

*   **Styling**: The UI is built with Tailwind CSS. A global `style.css` file adds base styles, custom fonts (Inter), and utility classes like `multi-shadow` for a consistent, modern aesthetic.
*   **Standardized Portals**: All portals (Admin, Sales, Production) use a consistent structure with a shared header, a dynamic sidebar, and a footer to create a unified user experience.

### Admin Portal (`frontend/admin/`)

*   **Authentication**: Access is restricted to users with the 'admin' role.
*   **Dashboard (`dashboard.php`)**: A central hub with live data on sales, customers, and revenue.
*   **Inventory Management (`inventory.php`)**: Allows admins to monitor and manage product stock levels.
*   **Sales Reports (`reports.php`)**: For generating and viewing sales data.
*   **Profile Management (`profile.php`)**: Allows admins to manage their personal information.
*   **Settings (`settings.php`)**: A placeholder page for future application-wide settings.

### Sales Portal (`frontend/sales/`)

*   **Authentication**: Access is restricted to users with the 'sales_manager' role.
*   **Dashboard (`dashboard.php`)**: Provides sales managers with a summary of their performance. Uses hardcoded data.
*   **Pages**: `orders.php`, `products.php`, `customers.php`.

### Production Portal (`frontend/production/`)

*   **Authentication**: Access will be restricted to users with the 'production_manager' role.
*   **Dashboard (`dashboard.php`)**: Will provide a real-time overview of production metrics, including daily output and current stock levels.
*   **Inventory Management (`manage-inventory.php`)**: Will allow production managers to view and update stock quantities to synchronize with the sales and inventory systems.
*   **Production Recording (`manage-production.php`)**: Will feature a form to record daily production quantities of bottled water and other beverages.

## Plan for Current Request

The user has requested the creation of a new **Production Manager Portal**. The portal must be consistent with the existing admin and sales portals and include functionality for recording production, managing inventory, and viewing a dedicated dashboard.

**Planned Steps:**

1.  **Create File Structure**:
    *   Create the `frontend/production/` directory.
    *   Create `partials/` within it for `header.php`, `sidebar.php`, and `footer.php`, adapting them from the admin portal.
2.  **Build the Dashboard (`dashboard.php`)**:
    *   Design a dashboard with summary cards and charts.
    *   Create a new API endpoint (`backend/api/production/dashboard.php`).
    *   Implement `frontend/js/production-dashboard.js` to fetch and render the data.
3.  **Implement Inventory Management (`manage-inventory.php`)**:
    *   Create the page to display and update product stock.
    *   Ensure the 'production_manager' role has permissions for the relevant product API endpoints.
    *   Create `frontend/js/production-inventory.js`.
4.  **Develop Production Recording (`manage-production.php`)**:
    *   Create the page with a form to submit daily production data.
    *   Create a new API endpoint (`backend/api/production/create.php`) to handle the data submission and update inventory.
5.  **Update Authentication**:
    *   Modify `index.php` to handle routing for the 'production_manager' role.
