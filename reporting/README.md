# AquaFlow Reporting Microservice

A Python Flask microservice for generating sales, inventory, and financial reports with CSV export capabilities.

## Features

- **Sales Reports**: Comprehensive sales analytics with top products, sales over time, and recent orders
- **Inventory Reports**: Stock level monitoring with low stock alerts
- **Financial Reports**: Revenue breakdown and payment status tracking
- **CSV Export**: Export reports in CSV format for further analysis
- **RESTful API**: JSON responses compatible with the main PHP application

## Requirements

- Python 3.8 or higher
- MySQL database (shared with main application)

## Installation

### 1. Install Python Dependencies

```bash
cd reporting
pip install -r requirements.txt
```

Or with virtual environment (recommended):

```bash
cd reporting
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate
pip install -r requirements.txt
```

### 2. Configure Database

The microservice reads database credentials from environment variables:

- `DB_HOST` (default: 127.0.0.1)
- `DB_USER` (default: root)
- `DB_PASSWORD` (default: empty)
- Database: aquaflow (hardcoded)

Set environment variables (optional):

**Windows PowerShell:**

```powershell
$env:DB_HOST="127.0.0.1"
$env:DB_USER="root"
$env:DB_PASSWORD=""
```

**Linux/Mac:**

```bash
export DB_HOST="127.0.0.1"
export DB_USER="root"
export DB_PASSWORD=""
```

## Running the Microservice

### Development Mode

```bash
cd reporting
python app.py
```

The service will start on `http://127.0.0.1:5001`

### Production Mode (with Gunicorn)

Install Gunicorn:

```bash
pip install gunicorn
```

Run:

```bash
gunicorn -w 4 -b 127.0.0.1:5001 app:app
```

### Windows Service (Optional)

For Windows, you can use `NSSM` (Non-Sucking Service Manager) to run as a service.

## API Endpoints

### Sales Reports

**Generate Sales Report**

```
GET /api/reports/sales
```

Query Parameters:

- `start_date` (optional): YYYY-MM-DD format, defaults to 30 days ago
- `end_date` (optional): YYYY-MM-DD format, defaults to today
- `limit` (optional): Number of recent orders to return, defaults to 10

Response:

```json
{
  "success": true,
  "message": "Sales report generated successfully",
  "data": {
    "top_products": [...],
    "sales_over_time": [...],
    "recent_orders": [...],
    "summary": {...},
    "status_breakdown": [...],
    "date_range": {...}
  }
}
```

**Export Sales Report to CSV**

```
GET /api/reports/sales/export
```

Query Parameters:

- `start_date` (optional): YYYY-MM-DD format
- `end_date` (optional): YYYY-MM-DD format
- `report_type` (optional): 'orders', 'products', or 'summary', defaults to 'orders'

Returns: CSV file download

### Inventory Reports

**Generate Inventory Report**

```
GET /api/reports/inventory
```

Response:

```json
{
  "success": true,
  "message": "Inventory report generated successfully",
  "data": {
    "inventory": [...],
    "low_stock_items": [...],
    "summary": {...}
  }
}
```

### Financial Reports

**Generate Financial Report**

```
GET /api/reports/financial
```

Query Parameters:

- `start_date` (optional): YYYY-MM-DD format
- `end_date` (optional): YYYY-MM-DD format

Response:

```json
{
  "success": true,
  "message": "Financial report generated successfully",
  "data": {
    "revenue_summary": {...},
    "revenue_by_status": [...],
    "date_range": {...}
  }
}
```

### Health Check

**Check Service Health**

```
GET /health
```

Response:

```json
{
  "status": "healthy",
  "database": "connected",
  "timestamp": "2025-11-12T10:30:00"
}
```

## Integration with PHP Application

The PHP application proxies requests through:

- `/backend/api/reports/get_sales_report.php` → Python `/api/reports/sales`
- `/backend/api/reports/export_sales_report.php` → Python `/api/reports/sales/export`

This ensures proper authentication and session management from the PHP side.

## Testing

Test the microservice directly:

```bash
# Health check
curl http://127.0.0.1:5001/health

# Sales report
curl "http://127.0.0.1:5001/api/reports/sales?start_date=2025-11-01&end_date=2025-11-12"

# Export CSV
curl "http://127.0.0.1:5001/api/reports/sales/export?report_type=orders" -o sales_report.csv

# Inventory report
curl http://127.0.0.1:5001/api/reports/inventory

# Financial report
curl "http://127.0.0.1:5001/api/reports/financial?start_date=2025-11-01&end_date=2025-11-12"
```

## Troubleshooting

### Database Connection Issues

1. Verify MySQL is running
2. Check database credentials
3. Ensure `aquaflow` database exists
4. Check firewall settings

### Port Already in Use

If port 5001 is already in use, modify the port in `app.py`:

```python
app.run(debug=True, host='127.0.0.1', port=5002)  # Change to 5002 or any available port
```

Then update PHP proxy endpoints with the new port.

### CORS Issues

If experiencing CORS issues, verify `flask-cors` is installed and configured correctly in `app.py`.

## Maintenance

### Logs

The microservice logs errors to console. For production, configure proper logging:

```python
import logging
logging.basicConfig(
    filename='reporting.log',
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
```

### Monitoring

Monitor the `/health` endpoint regularly to ensure service availability.

## Future Enhancements

- [ ] Add customer analytics reports
- [ ] Implement caching for frequently accessed reports
- [ ] Add Excel (.xlsx) export format
- [ ] Implement report scheduling
- [ ] Add PDF export with charts
- [ ] Performance optimization for large datasets

## License

Part of the AquaFlow Water & Beverage Management System
