@echo off

:: Create directories
mkdir config
mkdir backend\utils
mkdir backend\api\auth
mkdir backend\api\products
mkdir backend\api\orders
mkdir backend\api\inventory
mkdir backend\api\production
mkdir backend\api\payments
mkdir backend\api\customers
mkdir backend\api\users
mkdir backend\api\reports
mkdir uploads\products
mkdir logs
mkdir docs

:: Create empty files
echo. > .env
echo. > .htaccess
echo. > index.php
echo. > config\database.php
echo. > config\jwt.php
echo. > config\constants.php
echo. > backend\utils\response.php
echo. > backend\utils\validator.php
echo. > backend\utils\auth.php
echo. > backend\utils\helpers.php
echo. > backend\api\auth\login.php
echo. > backend\api\auth\register.php
echo. > backend\api\auth\profile.php
echo. > backend\api\products\get_all.php
echo. > backend\api\products\get_single.php
echo. > backend\api\products\create.php
echo. > backend\api\products\update.php
echo. > backend\api\products\delete.php
echo. > backend\api\orders\get_all.php
echo. > backend\api\orders\get_single.php
echo. > backend\api\orders\create.php
echo. > backend\api\orders\update_status.php
echo. > backend\api\orders\cancel.php
echo. > backend\api\inventory\get_all.php
echo. > backend\api\inventory\update_stock.php
echo. > backend\api\inventory\get_alerts.php
echo. > backend\api\production\get_all.php
echo. > backend\api\production\create.php
echo. > backend\api\production\schedule.php
echo. > backend\api\production\materials.php
echo. > backend\api\payments\initiate.php
echo. > backend\api\payments\verify.php
echo. > backend\api\payments\get_by_order.php
echo. > backend\api\customers\get_all.php
echo. > backend\api\customers\get_single.php
echo. > backend\api\users\get_all.php
echo. > backend\api\users\create.php
echo. > backend\api\users\update.php
echo. > backend\api\users\delete.php
echo. > backend\api\reports\sales.php
echo. > backend\api\reports\production.php
echo. > backend\api\reports\inventory.php
echo. > backend\api\reports\financial.php
echo. > logs\api.log
echo. > docs\API.md
echo. > docs\SETUP.md
echo. > docs\DATABASE.md

@echo Directory structure and files created successfully!