# Aquaflow Application Blueprint

## Overview

Aquaflow is a web application designed to facilitate the sale and delivery of bottled water. The application provides two main user-facing portals: an Admin Portal for overall system management and a Sales Portal for sales managers to handle their customers and orders.

## Project Structure

The project is organized into a `frontend` for the user interface and a `backend` for the API logic and database interactions.

-   **`backend/`**: Contains all server-side PHP scripts.
    -   `api/`: The core of the backend, with subdirectories for different functionalities (`admin`, `auth`, `products`, `reports`, `sales`, `users`).
    -   `config/`: Includes the `database.php` file for establishing a database connection.
    -   `utils/`: Contains helper scripts for handling authentication (`auth.php`) and formatting JSON responses (`response.php`).
-   **`frontend/`**: Contains all client-side assets.
    -   `css/`: `tailwind.css` for utility classes and a custom `style.css`.
    -   `js/`: JavaScript files for dynamic frontend behavior, prefixed with the portal name (e.g., `admin-dashboard.js`).
    -   `admin/`: The interface for administrators, including pages for the dashboard, inventory, reports, profile, and settings. It uses shared `partials` for the header, footer, and sidebar.
    -   `sales/`: The interface for the sales team.
    -   `login.php`: The main login page for all users.
-   **`index.php`**: The application's main entry point, which directs users to the appropriate portal or login page.

## Style, Design, and Features

### General

*   **Styling**: The UI is built with Tailwind CSS. A global `style.css` file adds base styles, custom fonts (Inter), and utility classes like `multi-shadow` for a consistent, modern aesthetic.
*   **Standardized Portals**: Both the Admin and Sales portals use a consistent structure with a shared header, a dynamic sidebar, and a footer to create a unified user experience.

### Admin Portal (`frontend/admin/`)

*   **Authentication**: Access is restricted to users with the 'admin' role.
*   **Dashboard (`dashboard.php`)**: The central hub for administrators, connected to a live API endpoint (`backend/api/admin/dashboard_summary.php`).
    *   **Design**: A clean, card-based layout with `multi-shadow` effects for depth.
    *   **Stats Cards**: At-a-glance metrics for "Total Sales," "New Customers," "Pending Orders," and "Revenue."
    *   **Data Visualization**: Includes a line chart for "Sales Overview" and a doughnut chart for "Top Products," with data dynamically loaded via `admin-dashboard.js`.
*   **Inventory Management (`inventory.php`)**: Allows admins to monitor and manage product stock levels.
    *   **Functionality**: Displays a table of all products with their current stock. Admins can update stock quantities directly. The page is powered by `admin-inventory.js`, which communicates with `backend/api/products/get_all.php` and `backend/api/products/update.php`.
*   **Sales Reports (`reports.php`)**: For generating and viewing sales data.
    *   **Functionality**: Users can generate a report that displays the top-selling products (bar chart) and sales over time (line chart). It also includes a table of recent orders. The page uses `admin-reports.js` and fetches data from `backend/api/reports/get_sales_report.php`.
*   **Profile Management (`profile.php`)**: Allows the logged-in admin to manage their personal information.
    *   **Functionality**: Admins can update their email address and change their password. The page is handled by `admin-profile.js` and interacts with `backend/api/users/get.php` and `backend/api/users/update.php`.
*   **Settings (`settings.php`)**: A placeholder page for future application-wide settings.

### Sales Portal (`frontend/sales/`)

*   **Authentication**: Access is restricted to users with the 'sales_manager' role.
*   **Dashboard (`dashboard.php`)**: Provides sales managers with a summary of their performance.
    *   **Current State**: The dashboard is visually designed but currently uses hardcoded placeholder data.
    *   **Stats Cards**: Includes key metrics like "My Pending Orders," "My Sales," and "New Customers."
    *   **Data Visualization**: Features a line chart for "My Recent Activity."
*   **Pages**:
    *   `orders.php`: To manage customer orders.
    *   `products.php`: To manage product listings.
    *   `customers.php`: To manage their customer list.

## Plan for Current Request

The user requested assistance. This was interpreted as a general request to continue building out the application based on the existing structure and hierarchy of needs.

**Completed Steps:**

1.  **Implemented Admin Inventory Page:**
    *   Created `frontend/admin/inventory.php` to display product stock.
    *   Modified `backend/api/products/update.php` to allow partial updates (specifically for stock quantity) and added access for the 'admin' role.
    *   Created `frontend/js/admin-inventory.js` to fetch inventory data and handle stock update logic.
2.  **Implemented Admin Profile Page:**
    *   Created `frontend/admin/profile.php` with a form for updating user details.
    *   Created `backend/api/users/get.php` to fetch the current user's data.
    *   Created `backend/api/users/update.php` to handle email and password changes.
    *   Created `frontend/js/admin-profile.js` to manage fetching and updating profile information.
3.  **Implemented Admin Settings Page:**
    *   Created `frontend/admin/settings.php` as a placeholder for future development.
4.  **Finalized Blueprint:**
    *   Updated this `blueprint.md` file to accurately reflect all the newly added features and the current project status.
