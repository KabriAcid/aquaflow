from flask import Flask, jsonify, request, Response
from flask_cors import CORS
import mysql.connector
import os
from datetime import datetime, timedelta
import csv
import io

app = Flask(__name__)
CORS(app)  # Allow requests from the main PHP application

# --- Database Connection ---
def get_db_connection():
    """Establishes a connection to the MySQL database."""
    try:
        conn = mysql.connector.connect(
            host=os.environ.get("DB_HOST", "127.0.0.1"),
            user=os.environ.get("DB_USER", "root"),
            password=os.environ.get("DB_PASSWORD", ""),
            database="aquaflow"
        )
        return conn
    except mysql.connector.Error as err:
        print(f"Error connecting to database: {err}")
        return None

# --- Utility Functions ---
def format_currency(value):
    """Format number as currency"""
    return float(value) if value else 0.0

def fetch_all(cursor):
    """Fetch all results and convert to list of dicts"""
    columns = [desc[0] for desc in cursor.description]
    return [dict(zip(columns, row)) for row in cursor.fetchall()]

def safe_json_response(data):
    """Convert database types to JSON-safe types"""
    if isinstance(data, dict):
        return {k: safe_json_response(v) for k, v in data.items()}
    elif isinstance(data, list):
        return [safe_json_response(item) for item in data]
    elif isinstance(data, (datetime,)):
        return data.isoformat()
    elif data is None:
        return None
    else:
        return data

# --- API Endpoints ---

@app.route('/api/reports/sales', methods=['GET'])
def get_sales_report():
    """
    Generates a comprehensive sales report with top products, sales over time, and recent orders.
    Query Parameters:
        - start_date: YYYY-MM-DD (optional, defaults to 30 days ago)
        - end_date: YYYY-MM-DD (optional, defaults to today)
        - limit: int (optional, defaults to 10 for recent orders)
    """
    conn = get_db_connection()
    if not conn:
        return jsonify({"success": False, "message": "Database connection failed", "data": None}), 500

    try:
        # Get query parameters
        start_date = request.args.get('start_date', (datetime.now() - timedelta(days=30)).strftime('%Y-%m-%d'))
        end_date = request.args.get('end_date', datetime.now().strftime('%Y-%m-%d'))
        limit = int(request.args.get('limit', 10))

        cursor = conn.cursor(dictionary=True)
        
        # --- 1. Top Selling Products ---
        top_products_query = """
            SELECT 
                p.name,
                SUM(oi.quantity) as total_quantity,
                SUM(oi.subtotal) as total_revenue
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.order_date BETWEEN %s AND %s
                AND o.status != 'cancelled'
            GROUP BY p.id, p.name
            ORDER BY total_quantity DESC
            LIMIT 10
        """
        cursor.execute(top_products_query, (start_date, end_date))
        top_products = cursor.fetchall()
        
        # --- 2. Sales Over Time (Daily) ---
        sales_over_time_query = """
            SELECT 
                DATE(o.order_date) as date,
                COUNT(DISTINCT o.id) as order_count,
                SUM(o.total_amount) as total_sales
            FROM orders o
            WHERE o.order_date BETWEEN %s AND %s
                AND o.status != 'cancelled'
            GROUP BY DATE(o.order_date)
            ORDER BY date ASC
        """
        cursor.execute(sales_over_time_query, (start_date, end_date))
        sales_over_time = cursor.fetchall()
        
        # --- 3. Recent Orders ---
        recent_orders_query = """
            SELECT 
                o.id as order_id,
                o.order_number,
                u.full_name as customer_name,
                o.order_date,
                o.total_amount,
                o.status,
                o.payment_status
            FROM orders o
            JOIN users u ON o.customer_id = u.id
            WHERE o.order_date BETWEEN %s AND %s
            ORDER BY o.order_date DESC
            LIMIT %s
        """
        cursor.execute(recent_orders_query, (start_date, end_date, limit))
        recent_orders = cursor.fetchall()
        
        # --- 4. Summary Statistics ---
        summary_query = """
            SELECT 
                COUNT(DISTINCT o.id) as total_orders,
                SUM(o.total_amount) as total_revenue,
                AVG(o.total_amount) as average_order_value,
                COUNT(DISTINCT o.customer_id) as unique_customers
            FROM orders o
            WHERE o.order_date BETWEEN %s AND %s
                AND o.status != 'cancelled'
        """
        cursor.execute(summary_query, (start_date, end_date))
        summary = cursor.fetchone() or {}
        
        # --- 5. Status Breakdown ---
        status_query = """
            SELECT 
                o.status,
                COUNT(*) as count,
                SUM(o.total_amount) as total_amount
            FROM orders o
            WHERE o.order_date BETWEEN %s AND %s
            GROUP BY o.status
        """
        cursor.execute(status_query, (start_date, end_date))
        status_breakdown = cursor.fetchall()
        
        cursor.close()

        # Build response data
        report_data = {
            "top_products": safe_json_response(top_products),
            "sales_over_time": safe_json_response(sales_over_time),
            "recent_orders": safe_json_response(recent_orders),
            "summary": safe_json_response(summary),
            "status_breakdown": safe_json_response(status_breakdown),
            "date_range": {
                "start_date": start_date,
                "end_date": end_date
            }
        }

        response = {
            "success": True,
            "message": "Sales report generated successfully",
            "data": report_data
        }

        return jsonify(response)

    except Exception as e:
        print(f"Error generating sales report: {e}")
        return jsonify({"success": False, "message": str(e), "data": None}), 500
    finally:
        if conn and conn.is_connected():
            conn.close()


@app.route('/api/reports/sales/export', methods=['GET'])
def export_sales_report_csv():
    """
    Exports sales report to CSV format.
    Query Parameters:
        - start_date: YYYY-MM-DD (optional, defaults to 30 days ago)
        - end_date: YYYY-MM-DD (optional, defaults to today)
        - report_type: 'orders' | 'products' | 'summary' (optional, defaults to 'orders')
    """
    conn = get_db_connection()
    if not conn:
        return jsonify({"success": False, "message": "Database connection failed"}), 500

    try:
        start_date = request.args.get('start_date', (datetime.now() - timedelta(days=30)).strftime('%Y-%m-%d'))
        end_date = request.args.get('end_date', datetime.now().strftime('%Y-%m-%d'))
        report_type = request.args.get('report_type', 'orders')

        cursor = conn.cursor(dictionary=True)
        
        if report_type == 'orders':
            # Export all orders
            query = """
                SELECT 
                    o.order_number as 'Order Number',
                    u.full_name as 'Customer Name',
                    o.order_date as 'Order Date',
                    o.delivery_city as 'Delivery City',
                    o.delivery_state as 'Delivery State',
                    o.subtotal as 'Subtotal',
                    o.delivery_fee as 'Delivery Fee',
                    o.total_amount as 'Total Amount',
                    o.status as 'Status',
                    o.payment_status as 'Payment Status'
                FROM orders o
                JOIN users u ON o.customer_id = u.id
                WHERE o.order_date BETWEEN %s AND %s
                ORDER BY o.order_date DESC
            """
            filename = f"sales_orders_{start_date}_to_{end_date}.csv"

        elif report_type == 'products':
            # Export product sales
            query = """
                SELECT 
                    p.name as 'Product Name',
                    p.category as 'Category',
                    SUM(oi.quantity) as 'Total Quantity Sold',
                    AVG(oi.unit_price) as 'Average Unit Price',
                    SUM(oi.subtotal) as 'Total Revenue'
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                JOIN orders o ON oi.order_id = o.id
                WHERE o.order_date BETWEEN %s AND %s
                    AND o.status != 'cancelled'
                GROUP BY p.id, p.name, p.category
                ORDER BY SUM(oi.subtotal) DESC
            """
            filename = f"sales_by_product_{start_date}_to_{end_date}.csv"

        else:  # summary
            # Export summary by day
            query = """
                SELECT 
                    DATE(o.order_date) as 'Date',
                    COUNT(DISTINCT o.id) as 'Total Orders',
                    COUNT(DISTINCT o.customer_id) as 'Unique Customers',
                    SUM(o.subtotal) as 'Subtotal',
                    SUM(o.delivery_fee) as 'Delivery Fees',
                    SUM(o.total_amount) as 'Total Revenue',
                    AVG(o.total_amount) as 'Average Order Value'
                FROM orders o
                WHERE o.order_date BETWEEN %s AND %s
                    AND o.status != 'cancelled'
                GROUP BY DATE(o.order_date)
                ORDER BY DATE(o.order_date) ASC
            """
            filename = f"sales_summary_{start_date}_to_{end_date}.csv"

        cursor.execute(query, (start_date, end_date))
        rows = cursor.fetchall()
        cursor.close()
        
        # Create CSV in memory
        output = io.StringIO()
        if rows:
            writer = csv.DictWriter(output, fieldnames=rows[0].keys())
            writer.writeheader()
            writer.writerows(rows)
        
        # Create response
        csv_data = output.getvalue()
        
        return Response(
            csv_data,
            mimetype='text/csv',
            headers={'Content-Disposition': f'attachment; filename="{filename}"'}
        )

    except Exception as e:
        print(f"Error exporting CSV: {e}")
        return jsonify({"success": False, "message": str(e)}), 500
    finally:
        if conn and conn.is_connected():
            conn.close()


@app.route('/api/reports/inventory', methods=['GET'])
def get_inventory_report():
    """
    Generates inventory report with stock levels, low stock alerts, and reorder recommendations.
    """
    conn = get_db_connection()
    if not conn:
        return jsonify({"success": False, "message": "Database connection failed", "data": None}), 500

    try:
        # Current inventory status
        inventory_query = """
            SELECT 
                p.id,
                p.name,
                p.category,
                p.volume,
                i.current_stock,
                i.minimum_stock_level,
                i.reorder_point,
                i.last_restocked,
                CASE 
                    WHEN i.current_stock <= i.minimum_stock_level THEN 'Critical'
                    WHEN i.current_stock <= i.reorder_point THEN 'Low'
                    ELSE 'Normal'
                END as stock_status
            FROM products p
            JOIN inventory i ON p.id = i.product_id
            ORDER BY 
                CASE 
                    WHEN i.current_stock <= i.minimum_stock_level THEN 1
                    WHEN i.current_stock <= i.reorder_point THEN 2
                    ELSE 3
                END,
                p.name ASC
        """
        inventory_df = pd.read_sql(inventory_query, conn)
        
        # Low stock items
        low_stock_df = inventory_df[inventory_df['stock_status'].isin(['Critical', 'Low'])]
        
        report_data = {
            "inventory": safe_json_response(inventory_df.to_dict('records')),
            "low_stock_items": safe_json_response(low_stock_df.to_dict('records')),
            "summary": {
                "total_products": int(len(inventory_df)),
                "critical_stock": int(len(inventory_df[inventory_df['stock_status'] == 'Critical'])),
                "low_stock": int(len(inventory_df[inventory_df['stock_status'] == 'Low'])),
                "normal_stock": int(len(inventory_df[inventory_df['stock_status'] == 'Normal']))
            }
        }

        return jsonify({
            "success": True,
            "message": "Inventory report generated successfully",
            "data": report_data
        })

    except Exception as e:
        print(f"Error generating inventory report: {e}")
        return jsonify({"success": False, "message": str(e), "data": None}), 500
    finally:
        if conn and conn.is_connected():
            conn.close()


@app.route('/api/reports/financial', methods=['GET'])
def get_financial_report():
    """
    Generates financial report with revenue, costs, and profit analysis.
    Query Parameters:
        - start_date: YYYY-MM-DD (optional)
        - end_date: YYYY-MM-DD (optional)
    """
    conn = get_db_connection()
    if not conn:
        return jsonify({"success": False, "message": "Database connection failed", "data": None}), 500

    try:
        start_date = request.args.get('start_date', (datetime.now() - timedelta(days=30)).strftime('%Y-%m-%d'))
        end_date = request.args.get('end_date', datetime.now().strftime('%Y-%m-%d'))

        # Revenue breakdown
        revenue_query = """
            SELECT 
                SUM(o.subtotal) as product_revenue,
                SUM(o.delivery_fee) as delivery_revenue,
                SUM(o.total_amount) as total_revenue,
                COUNT(o.id) as total_orders,
                COUNT(CASE WHEN o.payment_status = 'paid' THEN 1 END) as paid_orders,
                COUNT(CASE WHEN o.payment_status = 'unpaid' THEN 1 END) as unpaid_orders
            FROM orders o
            WHERE o.order_date BETWEEN %s AND %s
                AND o.status != 'cancelled'
        """
        revenue_df = pd.read_sql(revenue_query, conn, params=(start_date, end_date))
        
        # Revenue by status
        status_revenue_query = """
            SELECT 
                o.status,
                COUNT(*) as order_count,
                SUM(o.total_amount) as revenue
            FROM orders o
            WHERE o.order_date BETWEEN %s AND %s
            GROUP BY o.status
        """
        status_revenue_df = pd.read_sql(status_revenue_query, conn, params=(start_date, end_date))
        
        report_data = {
            "revenue_summary": safe_json_response(revenue_df.to_dict('records')[0] if not revenue_df.empty else {}),
            "revenue_by_status": safe_json_response(status_revenue_df.to_dict('records')),
            "date_range": {
                "start_date": start_date,
                "end_date": end_date
            }
        }

        return jsonify({
            "success": True,
            "message": "Financial report generated successfully",
            "data": report_data
        })

    except Exception as e:
        print(f"Error generating financial report: {e}")
        return jsonify({"success": False, "message": str(e), "data": None}), 500
    finally:
        if conn and conn.is_connected():
            conn.close()


@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    conn = get_db_connection()
    db_status = "connected" if conn else "disconnected"
    if conn and conn.is_connected():
        conn.close()
    
    return jsonify({
        "status": "healthy",
        "database": db_status,
        "timestamp": datetime.now().isoformat()
    })


if __name__ == '__main__':
    # Running on port 5001 to avoid conflict with other services
    print("Starting AquaFlow Reporting Microservice on http://127.0.0.1:5001")
    print("Endpoints available:")
    print("  - GET /api/reports/sales")
    print("  - GET /api/reports/sales/export")
    print("  - GET /api/reports/inventory")
    print("  - GET /api/reports/financial")
    print("  - GET /health")
    app.run(debug=True, host='127.0.0.1', port=5001)