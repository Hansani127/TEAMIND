# TEAMIND - AI-Based Smart Decision Support System for Tea Plantation Management

## Project Overview
TEAMIND is a comprehensive web-based AI-powered decision support system designed for tea plantation management. It integrates multiple AI/ML modules to help farmers and plantation managers make data-driven decisions.

## Features
- **Disease Detection**: CNN-based image classification for tea leaf diseases
- **Yield Prediction**: ML-based prediction using environmental factors
- **Demand Forecasting**: Time-series forecasting for market demand
- **Tea Grade Classification**: Computer vision-based quality grading
- **Interactive Dashboard**: Real-time analytics and visualizations
- **Plantation Management**: Full CRUD for plantation records
- **User Authentication**: Secure login with role-based access
- **Admin Panel**: System administration and user management
- **Reports**: Exportable CSV reports for all modules

## Technology Stack
### Frontend
- HTML5, CSS3, JavaScript
- Bootstrap 5
- Chart.js (Data Visualization)

### Backend
- PHP 8
- MySQL
- Apache (XAMPP)

### AI/ML
- Python 3
- TensorFlow / Keras
- Flask API
- scikit-learn
- NumPy, Pandas, OpenCV

## Installation

### 1. Database Setup
1. Install XAMPP and start Apache + MySQL
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Import `database/teamind.sql`

### 2. Project Setup
1. Copy the entire `TEAMIND` folder to `htdocs/`
2. Ensure folder permissions allow file uploads

### 3. Python API Setup
```bash
cd python/
pip install -r requirements.txt
python app.py
```
The Flask API will run on `http://localhost:5000`

### 4. Access the Application
- Landing Page: http://localhost/teamind/
- Login: http://localhost/teamind/login.php
- Default Admin: admin@teamind.com / admin123

## File Structure
```
TEAMIND/
├── index.php              # Landing page
├── login.php              # User login
├── register.php           # User registration
├── logout.php             # Logout handler
├── dashboard.php          # Main dashboard
├── disease.php            # Disease detection module
├── yield.php              # Yield prediction module
├── demand.php             # Demand forecast module
├── grade.php              # Tea grade classification
├── plantations.php        # Plantation management
├── reports.php            # Reports generation
├── profile.php            # User profile
├── settings.php           # System settings
├── admin.php              # Admin panel
├── config/
│   ├── config.php         # Main configuration
│   └── database.php       # Database connection
├── includes/
│   ├── header.php         # HTML head + CSS
│   ├── sidebar.php        # Navigation sidebar
│   ├── navbar.php         # Top navbar
│   └── footer.php         # Footer + scripts
├── assets/
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript files
│   └── images/            # Image assets
├── uploads/
│   ├── disease/           # Disease detection uploads
│   ├── tea/               # Tea grade uploads
│   └── profile/           # Profile images
├── api/                   # PHP API endpoints
├── python/
│   ├── app.py             # Flask API server
│   ├── requirements.txt   # Python dependencies
│   └── models/            # AI model files
├── database/
│   └── teamind.sql        # Database schema
└── reports/               # Generated reports
```

## Default Credentials
- **Admin**: admin@teamind.com / admin123
- **Farmer**: Register via the registration page

## API Endpoints (Flask)
- `POST /predict/disease` - Disease detection from image
- `POST /predict/yield` - Yield prediction from environmental data
- `POST /predict/demand` - Demand forecasting
- `POST /predict/grade` - Tea grade classification from image
- `GET /health` - Health check

## Security Features
- Password hashing with bcrypt
- Prepared statements (SQL injection prevention)
- Input sanitization
- Session management with timeout
- Role-based access control
- File upload validation
- XSS prevention
