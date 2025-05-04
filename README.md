# Question Bank Webapp

A PHP-MySQL web application for managing and searching a question bank with auto-calculated exam probability. This application lets administrators add questions (with PDF/image attachments), view a dashboard, and automatically compute a probability indicator based on historical exam records. The public interface offers responsive search functionality using Bootstrap, where "Subject" is mandatory and optional filters include "Year" and "Minimum Importance" (probability).

## Features

- **Public Interface:**
  - **Search & Filtering:**  
    - Subject (mandatory)
    - Year (optional)
    - Minimum Importance (optional, based on probability percentage)
  - **Display:** A responsive table displaying question details including subject, year, question text, answer, and probability.
  - **File Management:** Ability to view or download attached PDF or image files.
  
- **Admin Panel:**
  - **Secure Login:** Admins can log in to access the dashboard.
  - **Dashboard:** View all questions with options to add, edit, or delete.
  - **Add Questions:** Form to add new questions, including file uploads (PDF, JPG, JPEG, or PNG) and auto-calculated probability based on keyword matching, full-text search, and recency weighting.
  - **Logout:** Secure termination of the admin session.
  
- **Advanced Probability Calculation:**
  - Uses full-text search and recency weighting (via PHP functions) to compute the likelihood of a question (or similar ones) appearing in future exams.
  
- **Responsive Design:**
  - Built using Bootstrap to ensure the UI is mobile-friendly and responsive.

## File Structure

question-bank/ ├── admin/ │ ├── add_question.php # Add new question with file upload and probability calculation. │ ├── dashboard.php # Dashboard displaying all questions. │ ├── login.php # Admin login page. │ └── logout.php # Admin logout. ├── config.php # Database connection settings. ├── download.php # Script to force file downloads. ├── functions.php # Common functions (keyword extraction, probability calculation). ├── index.php # Public search page. ├── install.sql # SQL script for database schema and sample data. └── .htaccess # Rewrite and security settings.

## Database Setup:
Create a MySQL database (e.g., question_bank) and update your credentials in config.php if needed.

Run the install.sql script using your preferred MySQL client or phpMyAdmin:

## Uploads Folder:
Create an uploads folder in the project root and make sure it has write permissions (chmod 755 or 777 as needed).

## Web Server Setup:

Deploy the files on a PHP-supported web server (e.g., Apache with PHP 7+).

Ensure that the .htaccess file is enabled if using Apache for URL rewriting and security.

## Usage
# Public Search
# Navigate to index.php in your browser.

Enter the Subject (required) and optionally specify a Year and/or a minimum Importance (Probability).

The results will display all matching questions for the selected subject, listing their details along with the computed probability.

# Admin Panel
# Login: yourdomain.com/admin/login.php

Access the admin panel via admin/login.php.

# Log in using the default credentials: Username: admin Password: admin123

# Dashboard:

After logging in, you are redirected to admin/dashboard.php where you can view, add, or manage questions.

# Adding Questions:

Use the Add New Question button in the dashboard.

Fill in the fields (subject, year, question, answer) and choose a file if needed.

On submission, the application auto-calculates the probability based on historical exam records using keyword matching, full-text search, and recency weighting.

# Logout:

Use the Logout button to securely end your admin session.

## Database Schema Overview
# The application uses three primary tables:

# questions
Field	Type	Description
id	INT AUTO_INCREMENT	Primary Key
subject	VARCHAR(255)	Subject of the question
year	VARCHAR(4)	Year associated with the question
question	TEXT	The question text
answer	TEXT	The answer text
file_path	VARCHAR(255)	Relative path to the attached file (if any)
probability	FLOAT	Auto-calculated probability (%)

# admins
Field	Type	Description
id	INT AUTO_INCREMENT	Primary Key
username	VARCHAR(100)	Admin username
password	VARCHAR(255)	Admin password (MD5)

# exam_history
Field	Type	Description
id	INT AUTO_INCREMENT	Primary Key
exam_date	DATE	Date the exam was conducted
question_id	INT	Foreign key referencing questions.id

## .htaccess (Optional)
The included .htaccess file in the project root contains directives to:

Disable directory indices.

Protect sensitive files such as config.php and install.sql.

Redirect non-existing URLs to index.php for friendly URLs.

Optionally, enforce HTTPS.

--

### Notes on MD5 Usage

- **Security Notice:**  
  MD5 is used here solely for demonstration purposes. In production, consider using more secure alternatives like `password_hash()` and `password_verify()` with the bcrypt algorithm.

You can now include this `README.md` file in your GitHub repository to provide a comprehensive guide for installation, usage, and an overview of the database schema.

## Contributing
Feel free to fork this repository, make improvements, or add features. Pull requests are welcome!

## License
This project is open source and available under the MIT License.


