# Water and Beverage Factory Management System

## Complete Development Guide - Procedural PHP Backend

---

## Backend Directory Structure (Procedural PHP)

```
aquaflow/
│
├── .env                          # MySQL credentials, JWT secret
├── .htaccess                     # URL rewriting
├── index.php                     # Router - routes all API requests
│
├── config/                       # Configuration files
│   ├── database.php              # PDO connection function
│   ├── jwt.php                   # JWT encode/decode functions
│   └── constants.php             # App constants
│
├── backend/                      # Backend logic and API
│   ├── utils/                    # Utility functions
│   │   ├── response.php          # json_response(), error_response() functions
│   │   ├── validator.php         # validate_email(), validate_required() functions
│   │   ├── auth.php              # authenticate_user(), check_role() functions
│   │   └── helpers.php           # sanitize_input(), generate_order_number() functions
│   │
│   ├── api/                      # API endpoints
│   │   ├── auth/
│   │   │   ├── login.php         # Login logic
│   │   │   ├── register.php      # Registration logic
│   │   │   └── profile.php       # Get/update profile
│   │   │
│   │   ├── products/
│   │   │   ├── get_all.php       # List all products
│   │   │   ├── get_single.php    # Get product by ID
│   │   │   ├── create.php        # Create product (admin)
│   │   │   ├── update.php        # Update product (admin)
│   │   │   └── delete.php        # Delete product (admin)
│   │   │
│   │   ├── orders/
│   │   │   ├── get_all.php       # List orders (filtered by role)
│   │   │   ├── get_single.php    # Order details
│   │   │   ├── create.php        # Create order
│   │   │   ├── update_status.php # Update order status
│   │   │   └── cancel.php        # Cancel order
│   │   │
│   │   ├── inventory/
│   │   │   ├── get_all.php       # List inventory
│   │   │   ├── update_stock.php  # Update stock levels
│   │   │   └── get_alerts.php    # Low stock alerts
│   │   │
│   │   ├── production/
│   │   │   ├── get_all.php       # Production records
│   │   │   ├── create.php        # Log production
│   │   │   ├── schedule.php      # Production schedule
│   │   │   └── materials.php     # Material tracking
│   │   │
│   │   ├── payments/
│   │   │   ├── initiate.php      # Initiate payment via Flutterwave
│   │   │   ├── verify.php        # Verify payment using Flutterwave callback URL
│   │   │   └── get_by_order.php  # Get payment by order ID
│   │   │
│   │   ├── customers/
│   │   │   ├── get_all.php       # List customers (sales/admin)
│   │   │   └── get_single.php    # Customer details
│   │   │
│   │   ├── users/
│   │   │   ├── get_all.php       # List users (admin)
│   │   │   ├── create.php        # Create user (admin)
│   │   │   ├── update.php        # Update user (admin)
│   │   │   └── delete.php        # Delete user (admin)
│   │   │
│   │   └── reports/
│   │       ├── sales.php         # Sales reports
│   │       ├── production.php    # Production reports
│   │       ├── inventory.php     # Inventory reports
│   │       └── financial.php     # Financial reports
│
├── uploads/
│   └── products/                 # Product images
│
├── logs/
│   └── api.log                   # Error logs
│
└── docs/
    ├── API.md                    # API documentation
    ├── SETUP.md                  # Setup instructions
    └── DATABASE.md               # Database schema
```

---

## Database Schema

### Complete MySQL Tables

**Users Table**

- id, full_name, email, phone, password_hash, role (customer/sales_manager/production_manager/admin), status, address, city, state, postal_code, created_at, updated_at, last_login

**Products Table**

- id, name, category (bottled_water/beverage/package), size, volume, unit_price, minimum_order_quantity, description, image_url, status, created_at, updated_at, created_by

**Inventory Table**

- id, product_id, current_stock, minimum_stock_level, last_restocked, last_updated

**Orders Table**

- id, order_number, customer_id, order_date, delivery_address, delivery_date, special_instructions, subtotal, delivery_fee, total_amount, status (pending/processing/out_for_delivery/delivered/cancelled), payment_status, assigned_to, created_at, updated_at

**Order Items Table**

- id, order_id, product_id, quantity, unit_price, subtotal

**Payments Table**

- id, order_id, payment_method, amount, transaction_reference, payment_status, payment_date, receipt_url, notes

**Production Table**

- id, production_date, product_id, shift, quantity_produced, equipment_used, operator_id, notes, status, created_at, created_by

**Materials Table**

- id, material_name, unit, current_stock, reorder_level, unit_cost, supplier, last_updated

**Material Usage Table**

- id, production_id, material_id, quantity_used

**Production Schedule Table**

- id, scheduled_date, product_id, planned_quantity, shift, assigned_to, status, notes, created_at

**Settings Table**

- id, setting_key, setting_value, setting_type, updated_at, updated_by

**Activity Logs Table**

- id, user_id, action, entity_type, entity_id, description, ip_address, created_at

**Notifications Table**

- id, user_id, title, message, type, is_read, created_at

---

## PHP Backend Implementation Guide

### Core Files Structure

**`.env`**

```
DB_HOST=localhost
DB_NAME=aquaflow
DB_USER=root
DB_PASS=
JWT_SECRET=your_secret_key_here
JWT_EXPIRY=3600
```

**`config/database.php`**

- Function: get_db_connection()
- Returns: PDO connection object with error mode exception
- Uses: .env variables for credentials

**`config/jwt.php`**

- Function: generate_jwt($user_id, $email, $role)
- Function: validate_jwt($token)
- Function: get_token_from_header()
- Uses: Firebase JWT library

**`utils/response.php`**

- Function: success_response($message, $data, $code)
- Function: error_response($message, $errors, $code)
- Outputs: JSON with success, message, data/errors

**`utils/auth.php`**

- Function: authenticate_request() - validates JWT
- Function: require_role($allowed_roles) - checks user role
- Returns: user data array or exits with 401/403

**`utils/validator.php`**

- Function: validate_required($fields, $data)
- Function: validate_email($email)
- Function: validate_min_length($value, $min)
- Returns: array of errors or empty array

**`utils/helpers.php`**

- Function: sanitize_input($data)
- Function: generate_order_number()
- Function: upload_product_image($file)
- Function: hash_password($password)
- Function: verify_password($password, $hash)
- Function: `initiate_flutterwave_payment($order_id, $amount, $currency, $callback_url)`

  - Initiates a payment request to Flutterwave API.
  - Parameters: `$order_id` (unique order ID), `$amount` (payment amount), `$currency` (e.g., USD, NGN), `$callback_url` (URL to handle payment status).
  - Returns: Payment link or error response.

- Function: `verify_flutterwave_payment($transaction_id)`
  - Verifies payment status using Flutterwave API.
  - Parameters: `$transaction_id` (Flutterwave transaction ID).
  - Returns: Payment status (success, failed) and details.

**`index.php` - Router**

- Parse REQUEST_URI and REQUEST_METHOD
- Extract endpoint (e.g., /api/products/123)
- Route to appropriate file in /api/ folder
- Handle CORS headers
- Load .env variables

---

## API Endpoints Reference

### Authentication (`/api/auth/`)

- **POST /api/auth/register** - Register new user
- **POST /api/auth/login** - User login
- **GET /api/auth/profile** - Get user profile (requires auth)
- **PUT /api/auth/profile** - Update profile (requires auth)

### Products (`/api/products/`)

- **GET /api/products** - List products (query: ?category=&search=)
- **GET /api/products/{id}** - Get product details
- **POST /api/products** - Create product (admin only)
- **PUT /api/products/{id}** - Update product (admin only)
- **DELETE /api/products/{id}** - Delete product (admin only)

### Orders (`/api/orders/`)

- **GET /api/orders** - List orders (filtered by role)
- **GET /api/orders/{id}** - Order details
- **POST /api/orders** - Create order (customer)
- **PUT /api/orders/{id}** - Update order status
- **DELETE /api/orders/{id}** - Cancel order

### Inventory (`/api/inventory/`)

- **GET /api/inventory** - List inventory
- **PUT /api/inventory/{id}** - Update stock
- **GET /api/inventory/alerts** - Low stock alerts

### Production (`/api/production/`)

- **GET /api/production** - Production records
- **POST /api/production** - Log production
- **GET /api/production/schedule** - Production schedule
- **POST /api/production/schedule** - Create schedule

### Payments (`/api/payments/`)

- **POST /api/payments/initiate** - Initiate payment via Flutterwave
- **PUT /api/payments/{id}/verify** - Verify payment using Flutterwave callback URL
- **GET /api/payments/order/{orderId}** - Get payment by order

### Customers (`/api/customers/`) - Sales/Admin only

- **GET /api/customers** - List customers
- **GET /api/customers/{id}** - Customer details

### Users (`/api/users/`) - Admin only

- **GET /api/users** - List all users
- **POST /api/users** - Create user
- **PUT /api/users/{id}** - Update user
- **DELETE /api/users/{id}** - Delete user

### Reports (`/api/reports/`)

- **GET /api/reports/sales** - Sales reports (query: ?start_date=&end_date=)
- **GET /api/reports/production** - Production reports
- **GET /api/reports/inventory** - Inventory reports
- **GET /api/reports/financial** - Financial reports (admin only)

---

## Response Format Standard

**Success Response:**

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {}
}
```

**Error Response:**

```json
{
  "success": false,
  "message": "Error description",
  "errors": {}
}
```

---

## Frontend Development Prompt for AI/Copilot

### PROJECT SETUP COMMAND

Create a complete web-based Water and Beverage Factory Management System frontend using pure HTML5, Tailwind, and JavaScript and AJAX. The system manages water and beverage factory operations with 4 user roles: Customer, Sales Manager, Production Manager, and Admin.

### TECHNICAL REQUIREMENTS

- Use semantic HTML5 with proper document structure
- Tailwind CSS with responsive design (mobile-first, breakpoints: 640px, 1024px)
- JavaScript (ES6+) with AJAX for Fetch API for backend communication
- Store authentication data in localStorage: authToken, userRole, userId, userName
- API base URL: http://localhost/aquaflow/api
- Include Authorization header in all authenticated requests: Bearer {token}
- Display loading spinners during API calls
- Show success/error toast notifications for user actions
- Implement form validation before API submission

### COLOR SCHEME

- Primary: #2563eb (blue) for main actions
- Success: #10b981 (green) for success states
- Warning: #f59e0b (orange) for alerts
- Danger: #ef4444 (red) for errors
- Neutral: Gray shades for backgrounds and text

### PAGE STRUCTURE REQUIREMENTS

**PUBLIC PAGES (No Authentication Required):**

1. **Landing Page (index.php)**

   - Hero section with company name and tagline
   - Featured products grid (3 products)
   - Login and Register buttons prominently displayed
   - About section briefly describing factory services
   - Footer with contact information

2. **Login Page (login.php)**

   - Email and password input fields
   - "Remember me" checkbox
   - "Forgot password?" link
   - Login button that calls POST /api/auth/login
   - On success: store token and user data in localStorage, redirect based on role
   - Display error messages below form

3. **Register Page (register.php)**

   - Fields: full name, email, phone, password, confirm password, address, city, state, postal code
   - All fields required except address fields (optional)
   - Register button calls POST /api/auth/register
   - Password validation: minimum 8 characters
   - On success: redirect to login page with success message

4. **Products Catalog (products.php)**
   - Display all products from GET /api/products in grid layout
   - Show: product image, name, size, price, minimum order quantity
   - Filter dropdown: All, Bottled Water, Beverages, Packages
   - Search bar to filter by name
   - Each product card has "View Details" button
   - If logged in as customer, show "Add to Cart" button
   - If not logged in, "Add to Cart" shows "Login to Order"

**CUSTOMER DASHBOARD (customer/ folder):**

5. **Customer Dashboard (customer/dashboard.php)**

   - Top navigation: Logo, Products, My Orders, Cart, Profile, Logout
   - Welcome message: "Welcome back, {userName}"
   - Stats cards: Total Orders, Pending Orders, Completed Orders, Total Spent (from GET /api/orders)
   - Recent orders table showing last 5 orders with status
   - Quick action buttons: Browse Products, View Cart, Track Order

6. **Browse Products (customer/products.php)**

   - Same as public products page but with functional "Add to Cart"
   - Add to Cart validates minimum order quantity
   - Cart icon in header shows item count badge
   - Toast notification: "{Product} added to cart"

7. **Shopping Cart (customer/cart.php)**

   - Table: Product Image, Name, Price, Quantity (with +/- buttons), Subtotal, Remove button
   - Update quantity must respect minimum order quantity
   - Cart summary: Subtotal, Delivery Fee, Total
   - "Continue Shopping" and "Proceed to Checkout" buttons
   - Clear cart option with confirmation

8. **Checkout (customer/checkout.php)**

   - Order summary (read-only list of cart items)
   - Delivery address form (pre-filled from profile, editable)
   - Delivery date picker (minimum: tomorrow)
   - Special instructions textarea
   - Payment method radio buttons: Credit Card, Bank Transfer, Cash on Delivery
   - "Place Order" button calls POST /api/orders with cart items
   - On success: clear cart, redirect to payment page or order confirmation

9. **Payment (customer/payment.php)**

   - Display order details and total amount
   - Redirect to Flutterwave payment link on "Confirm Payment".
   - Handle callback to update payment status and show success or failure message.

10. **My Orders (customer/orders.php)**

    - Tabs: All, Pending, Processing, Delivered, Cancelled
    - Orders table: Order Number, Date, Total, Status, Actions
    - Status displayed as colored badge
    - Actions: View Details, Track Order, Cancel (only for pending)
    - Cancel button calls DELETE /api/orders/{id} with confirmation

11. **Order Details (customer/order-details.php)**

    - Order number, date, status
    - Progress bar: Pending → Processing → Out for Delivery → Delivered
    - Items table with quantities and prices
    - Delivery address and date
    - Payment information
    - Download invoice button
    - Cancel button if status is pending

12. **Profile (customer/profile.php)**
    - Form with user details (pre-filled from GET /api/auth/profile)
    - Editable fields: full name, phone, address, city, state, postal code
    - "Update Profile" button calls PUT /api/auth/profile
    - Change password section (current password, new password, confirm new password)
    - Success/error messages displayed

**SALES MANAGER DASHBOARD (sales/ folder):**

13. **Sales Dashboard (sales/dashboard.php)**

    - Navigation sidebar: Dashboard, Orders, Customers, Inventory, Reports, Logout
    - Stats cards: Today's Sales, New Orders, Pending Orders, Revenue This Month
    - Recent orders table (last 10) with "Update Status" button
    - Low stock alerts section
    - Top selling products chart

14. **Orders Management (sales/orders.php)**

    - All orders table from GET /api/orders
    - Columns: Order ID, Customer, Date, Items Count, Total, Status, Actions
    - Filter by status dropdown
    - Date range filter
    - Search by order number or customer name
    - Actions: View Details, Update Status
    - Update Status modal with dropdown: Pending, Processing, Out for Delivery, Delivered
    - Calls PUT /api/orders/{id}

15. **Customers Management (sales/customers.php)**

    - Customers table from GET /api/customers
    - Columns: Name, Email, Phone, Total Orders, Total Spent, Join Date, Actions
    - Search by name or email
    - Actions: View Details, View Orders
    - Customer details modal shows order history

16. **Inventory View (sales/inventory.php)**

    - Inventory table from GET /api/inventory
    - Columns: Product, Category, Current Stock, Minimum Level, Status
    - Status color: Green (sufficient), Orange (low), Red (critical)
    - "Request Restock" button for low stock items

17. **Sales Reports (sales/reports.php)**
    - Date range selector (from, to)
    - Sales summary cards: Total Revenue, Total Orders, Average Order Value
    - Sales chart (line chart showing daily sales)
    - Top products table (product, quantity sold, revenue)
    - Export to PDF and Excel buttons

**PRODUCTION MANAGER DASHBOARD (production/ folder):**

18. **Production Dashboard (production/dashboard.php)**

    - Navigation sidebar: Dashboard, Log Production, Records, Materials, Schedule, Reports
    - Stats cards: Today's Output, Materials Used, Efficiency Rate, Upcoming Schedules
    - Today's production summary
    - Quick "Log Production" button

19. **Log Production (production/log.php)**

    - Form fields: Production Date, Product (dropdown), Shift (Morning/Afternoon/Night), Quantity Produced, Equipment Used, Operator, Materials Used (dynamic add), Notes
    - "Submit Production Log" calls POST /api/production
    - Clear form after submission
    - Success message displayed

20. **Production Records (production/records.php)**

    - Production history table from GET /api/production
    - Columns: Date, Product, Shift, Quantity, Operator, Status
    - Filter by date range and product
    - View details button shows materials used

21. **Materials Tracking (production/materials.php)**

    - Materials table from GET /api/production/materials
    - Columns: Material Name, Current Stock, Unit, Reorder Level, Last Updated
    - Highlight materials below reorder level
    - "Update Stock" button for each material

22. **Production Schedule (production/schedule.php)**

    - Calendar view or table of schedules from GET /api/production/schedule
    - "Add Schedule" button opens form modal
    - Form: Date, Product, Planned Quantity, Shift, Assigned To
    - "Create Schedule" calls POST /api/production/schedule

23. **Production Reports (production/reports.php)**
    - Date range selector
    - Production output chart (bar chart by product)
    - Efficiency metrics
    - Material usage breakdown
    - Export functionality

**ADMIN DASHBOARD (admin/ folder):**

24. **Admin Dashboard (admin/dashboard.php)**

    - Full navigation: Dashboard, Users, Products, Orders, Inventory, Reports, Settings
    - Comprehensive stats: Total Revenue, Total Orders, Total Customers, Total Products, Active Users, Low Stock Count
    - Recent activities feed from activity logs
    - Quick actions panel

25. **User Management (admin/users.php)**

    - Users table from GET /api/users
    - Columns: Name, Email, Role, Status, Join Date, Last Login, Actions
    - "Add User" button opens form modal
    - Form: Name, Email, Phone, Role (dropdown), Password
    - "Create User" calls POST /api/users
    - Actions: Edit, Delete (with confirmation), Activate/Deactivate
    - Edit calls PUT /api/users/{id}
    - Delete calls DELETE /api/users/{id}

26. **Product Management (admin/products.php)**

    - Products table with thumbnails from GET /api/products
    - "Add Product" button opens form modal
    - Form: Name, Category (dropdown), Size, Volume, Price, Minimum Order Qty, Description, Image Upload, Status
    - "Create Product" calls POST /api/products with FormData
    - Actions: Edit, Delete
    - Edit updates product via PUT /api/products/{id}

27. **All Orders (admin/orders.php)**

    - Same as sales orders page but with all orders access
    - Additional filter by customer
    - Can update any order status

28. **Inventory Management (admin/inventory.php)**

    - Inventory table from GET /api/inventory
    - "Adjust Stock" button for each product
    - Stock adjustment modal: Product, Current Stock, Adjustment (+/-), New Stock
    - Update calls PUT /api/inventory/{id}
    - Set minimum stock levels

29. **Financial Reports (admin/financial.php)**

    - Date range selector
    - Revenue breakdown by payment method
    - Total revenue, expenses (if tracked), profit
    - Outstanding payments table
    - Payment status breakdown pie chart
    - Export to Excel

30. **System Reports (admin/reports.php)**

    - Comprehensive analytics dashboard
    - Sales analytics (line chart)
    - Production analytics (bar chart)
    - Customer growth chart
    - Inventory turnover
    - Period comparison (this month vs last month)

31. **System Settings (admin/settings.php)**
    - Form with settings from GET /api/settings
    - General: Company Name, Contact Email, Phone, Address
    - Payment: Delivery Fee, Payment Methods Enabled
    - Delivery: Default Delivery Days, Delivery Zones
    - "Save Settings" calls PUT /api/settings
    - Success message on save

### REUSABLE COMPONENTS

**Navigation Bar (all dashboards)**

- Logo and company name on left
- User name and role displayed on right
- Logout button calls logout and clears localStorage
- For customer: show cart icon with badge count

**Sidebar (manager and admin dashboards)**

- Vertical navigation menu
- Icons and labels for each section
- Active page highlighted
- Collapsible on mobile (hamburger menu)

**Footer (all pages)**

- Copyright text: © 2025 aquaflow. All rights reserved.
- Quick links: Home, About, Contact, Privacy Policy

**Modal/Dialog Component**

- Reusable modal with title, body content, close button
- Overlay background
- Used for forms, confirmations, details view

**Toast Notification**

- Position: top-right corner
- Auto-dismiss after 3 seconds
- Types: success (green), error (red), info (blue), warning (orange)

### FUNCTIONALITY REQUIREMENTS

**Authentication Flow:**

- All protected pages check for authToken in localStorage on load
- If no token: redirect to login page
- If token exists: validate by calling GET /api/auth/profile
- If invalid: clear localStorage and redirect to login
- Store user data: userId, userName, userRole, authToken

**Shopping Cart (Customer):**

- Store cart in localStorage: cartItems array [{productId, name, price, quantity, image, minQty}]
- Add to cart: check if product exists, if yes increase quantity, if no add new item
- Respect minimum order quantity validation
- Calculate totals dynamically
- Clear cart after successful order

**Role-Based Access:**

- Customer: can only access customer/\* pages
- Sales Manager: can only access sales/\* pages
- Production Manager: can only access production/\* pages
- Admin: can access admin/\* pages
- If user tries to access unauthorized page: redirect to their dashboard

**Form Validation:**

- Validate all required fields before submission
- Email format validation
- Password strength validation (min 8 chars)
- Minimum order quantity validation
- Show inline error messages below fields
- Disable submit button during API call

**API Error Handling:**

- Catch all API errors
- Display error message from response or generic error
- Handle 401: redirect to login
- Handle 403: show "Access denied" message
- Handle 404: show "Not found" message
- Handle 500: show "Server error, try again later"

**Loading States:**

- Show spinner or "Loading..." text during API calls
- Disable buttons during submission
- Hide spinner on success or error

**Data Refresh:**

- Refresh data on page load
- Reload data after create/update/delete operations
- Show updated data without page reload where possible

### FILE STRUCTURE

```
aquaflow/
├── index.php
├── login.php
├── register.php
├── products.php
├── css/
│   ├── style.css (global styles)
├── js/
│   ├── app.js (main app logic, routing)
│   ├── auth.js (login, logout, auth check)
│   ├── api.js (API call functions)
│   ├── cart.js (cart management)
│   ├── validation.js (form validation)
│   └── utils.js (helpers, formatters)
├── customer/
│   ├── dashboard.php
│   ├── products.php
│   ├── cart.php
│   ├── checkout.php
│   ├── payment.php
│   ├── orders.php
│   ├── order-details.php
│   └── profile.php
├── sales/
│   ├── dashboard.php
│   ├── orders.php
│   ├── customers.php
│   ├── inventory.php
│   └── reports.php
├── production/
│   ├── dashboard.php
│   ├── log.php
│   ├── records.php
│   ├── materials.php
│   ├── schedule.php
│   └── reports.php
├── admin/
│   ├── dashboard.php
│   ├── users.php
│   ├── products.php
│   ├── orders.php
│   ├── inventory.php
│   ├── financial.php
│   ├── reports.php
│   └── settings.php
└── assets/
    ├── images/
    └── icons/
```

### JAVASCRIPT MODULES

**api.js - API Communication**

- Define API_BASE_URL constant
- Create fetchAPI(endpoint, method, body) function
- Automatically include Authorization header from localStorage
- Return parsed JSON response
- Throw errors on non-200 responses

**auth.js - Authentication**

- login(email, password): calls /api/auth/login, stores token
- register(userData): calls /api/auth/register
- logout(): clears localStorage, redirects to login
- checkAuth(): verifies token, returns user data or redirects to login
- getUserRole(): returns role from localStorage

**cart.js - Cart Management**

- getCart(): returns cart from localStorage
- addToCart(product): adds/updates cart
- removeFromCart(productId): removes item
- updateQuantity(productId, quantity): updates quantity
- clearCart(): empties cart
- calculateTotal(): returns total amount

**validation.js - Form Validation**

- validateEmail(email): returns boolean
- validateRequired(fields): checks if fields are filled
- validatePasswordStrength(password): min 8 chars
- validateMinQuantity(quantity, minQty): checks minimum order
- displayErrors(errors): shows error messages on form

**utils.js - Utilities**

- formatCurrency(amount): formats as currency
- formatDate(dateString): formats date
- showToast(message, type): displays toast notification
- showLoading(element): shows spinner
- hideLoading(element): hides spinner
- confirmAction(message): returns confirm dialog result

### IMPLEMENTATION PRIORITY

**Phase 1: Core Pages**

1. Create index.php, login.php, register.php
2. Implement auth.js with login/register functions
3. Create products.php with product display

**Phase 2: Customer Features**

1. Create customer dashboard and navigation
2. Implement cart.js and shopping cart functionality
3. Create checkout and order placement flow
4. Implement order tracking pages

**Phase 3: Manager Dashboards**

1. Create sales manager dashboard and order management
2. Create production manager dashboard and production logging
3. Implement report pages

**Phase 4: Admin Panel**

1. Create admin dashboard
2. Implement user management
3. Implement product management
4. Create system reports and settings

**Phase 5: Polish & Testing**

1. Add responsive design
2. Implement loading states and error handling
3. Add toast notifications
4. Test all user flows

### STYLING GUIDELINES

- Use Tailwind CSS Grid for layouts, Flexbox for components
- Box-shadow for cards and elevated elements
- Border-radius: 8px for buttons, 12px for cards
- Consistent spacing: 8px, 16px, 24px, 32px
- Font sizes: 14px (body), 18px (headings), 24px (titles)
- Transitions: 0.3s ease for hover effects

### ACCESSIBILITY

- Use semantic HTML (header, nav, main, section, article, footer)
- Add alt text to all images
- Use labels for all form inputs
- Keyboard navigation support (tab order)
- ARIA labels for icons and buttons
- Color contrast ratio minimum 4.5:1
