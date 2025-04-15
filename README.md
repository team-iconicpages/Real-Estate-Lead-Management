
# Real Estate Lead Management System

This is a web application for managing real estate leads. It allows both **admin** and **employee** users to manage leads, update their statuses, and keep track of follow-ups. Employees can also add new leads and update their assigned leads.

## Features

- **Admin:**
  - View and manage all leads
  - Assign leads to employees
  - Update lead statuses
  - Generate reports
  - Manage users (admins and employees)

- **Employee:**
  - View and manage assigned leads
  - Update lead statuses
  - Add follow-ups for leads
  - View lead details and update status

## Technologies Used

- PHP 7+ (Backend)
- MySQL (Database)
- PDO (Database Abstraction)
- HTML, CSS, JavaScript (Frontend)
- Bootstrap (UI Framework)
- Session Management for Authentication

## Project Structure

```
/real-estate-leads
│
├── /admin
│   ├── /dashboard.php        # Admin dashboard page
│   ├── /reports.php          # Admin reports page
│   ├── /leads/
│   │   ├── /add.php          # Add new lead
│   │   ├── /assign.php       # Assign lead to an employee
│   │   ├── /edit.php         # Edit lead details
│   │   ├── /list.php         # List all leads
│   │   └── /view.php         # View lead details
│   └── /my_leads.php         # Admin's assigned leads page
│
├── /employee
│   ├── /dashboard.php        # Employee dashboard page
│   ├── /followup.php         # Add follow-up for lead
│   ├── /lead_detail.php      # View lead details and update status
│   ├── /my_leads.php         # Employee's assigned leads page
│   └── /update_status.php    # Update lead status
│
├── /auth
│   ├── /login.php            # Login page
│   ├── /logout.php           # Logout functionality
│   └── /register.php         # Admin registration page
│
├── /config
│   └── /db.php               # Database connection
│
├── /includes
│   ├── /header.php           # Common header for all pages
│   └── /footer.php           # Common footer for all pages
│
├── /process
│   ├── /add_followup.php     # Process follow-up submission
│   ├── /add_lead.php         # Process new lead submission
│   ├── /assign_lead.php      # Process lead assignment
│   ├── /edit_lead.php        # Process lead editing
│   ├── /login_process.php    # Process login
│   ├── /update_status.php    # Process status update
│   └── /register_process.php # Process registration
│
└── index.php                 # Home page
```

## Installation

To install and run the project locally, follow these steps:

1. **Clone the repository**:
    ```bash
    git clone https://github.com/yourusername/real-estate-lead-management.git
    ```

2. **Set up the database**:
    - Import the provided SQL schema into your MySQL database.
    - Update the `config/db.php` file with your MySQL database credentials.

3. **Install dependencies** (Optional):
    - If you're using a PHP framework like Composer, install the necessary dependencies (though this project is plain PHP, so no additional packages are required).

4. **Run the project**:
    - Open the project directory in your browser, and access `index.php` to get started.

5. **Login as Admin**:
    - By default, you can log in as an admin with the following credentials:
        - **Email**: admin@admin.com
        - **Password**: admin123

6. **Login as Employee**:
    - Employees can register through the registration page if they have the appropriate credentials from the admin.

## Database Schema

The application uses the following tables:

- `users` - Contains user information for admins and employees.
- `lead_status` - Contains the different lead statuses.
- `leads` - Contains lead information, including assigned employees.
- `lead_followups` - Stores follow-up details for each lead.

For the full SQL schema, see the provided `schema.sql` file.

## Contributing

Feel free to fork this project and create a pull request. Contributions are welcome!

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
