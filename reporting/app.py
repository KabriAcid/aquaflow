
from flask import Flask, jsonify
from flask_cors import CORS
import pandas as pd
import mysql.connector
import os

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
            database="aquaflow"  # Assuming this is your database name
        )
        return conn
    except mysql.connector.Error as err:
        print(f"Error: {err}")
        return None

# --- API Endpoints ---

@app.route('/api/reports/sales', methods=['GET'])
def get_sales_report():
    """
    Generates a sales report and returns it in a standardized JSON format.
    """
    conn = get_db_connection()
    if not conn:
        return jsonify({"success": False, "message": "Database connection failed", "errors": {}}), 500

    try:
        # Example: Fetch sales data using pandas
        query = "SELECT p.name, od.quantity, od.price FROM order_details od JOIN products p ON od.product_id = p.id"
        sales_df = pd.read_sql(query, conn)

        # --- Basic Analysis (Example) ---
        total_revenue = float((sales_df['quantity'] * sales_df['price']).sum())
        top_selling_products = sales_df.groupby('name')['quantity'].sum().nlargest(5).to_dict()

        report_data = {
            "total_revenue": total_revenue,
            "top_selling_products": top_selling_products
        }

        response = {
            "success": True,
            "message": "Sales report generated successfully",
            "data": report_data
        }

        return jsonify(response)

    except Exception as e:
        return jsonify({"success": False, "message": str(e), "errors": {}}), 500
    finally:
        if conn and conn.is_connected():
            conn.close()

if __name__ == '__main__':
    # Running on port 5001 to avoid conflict with other services
    app.run(debug=True, port=5001)
