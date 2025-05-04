# Question Bank Webapp

A PHP-MySQL web application designed for educators and students to manage and search a question bank with auto-calculated exam probability. The project features a responsive and modern user interface built using Bootstrap and includes both a public search interface and an admin panel for managing questions.

> **Important Note:**  
> For demonstration purposes, admin passwords are stored using MD5 hashing. For a production system, please use stronger password hashing methods (e.g., `password_hash()` and `password_verify()` with bcrypt).

---

## Features

- **Public Interface:**
  - **Responsive Search Form:**  
    - Subject field is mandatory (with auto-complete suggestions using jQuery UI).  
    - Optional filters for Year and Minimum Importance (Probability).
  - **Search Results:**  
    - Displays matching questions filtered and sorted by the auto-calculated probability.
    - Lists details including Subject, Year, Question, Answer, and Probability.
  - **File Download/Preview:**  
    - Questions may include an attached PDF or image file that users can view or download.

- **Admin Panel:**
  - **Secure Login:**  
    - Admins log in with their username and MD5-hashed password.
  - **Dashboard:**  
    - View, edit, and delete questions.
    - See a list of questions with details and the computed probability for each.
  - **Add New Question:**  
    - Form to input Subject, Year, Question, Answer, and an optional file upload.
    - Auto-calculates the probability based on keyword matching, MySQL full‑text search, and recency weighting.
    - Includes auto-complete functionality for the Subject field.
  - **Edit Question:**  
    - Pre-populated form for editing question details.
    - Option to replace the existing file.
    - Automatically recalculates the probability based on the updated data.
  - **Secure Logout:**  
    - Ends the admin session.

- **Advanced Functionality:**
  - **Subject Auto-Complete:**  
    - Utilizes an AJAX endpoint (`subject_autocomplete.php`) and jQuery UI to auto-fill the subject field.
  - **Probability Calculation:**  
    - Employs full‑text search and recency weighting logic (implemented in PHP in `functions.php`) to compute a percentage value representing the likelihood of a question appearing in an exam.
  - **Responsive Design:**  
    - Built with Bootstrap and includes a mobile-friendly layout.

---

## File Structure

question-bank/ ├── admin/ │ ├── add_question.php # Form to add new questions (with file upload and auto-complete for subject). │ ├── dashboard.php # Admin dashboard listing all questions (with options to edit or delete). │ ├── edit_question.php # Form to edit an existing question (pre-populated fields and auto-complete). │ ├── login.php # Admin login page with MD5 password verification. │ └── logout.php # Script to destroy the session and log out the admin. ├── config.php # Database connection settings. ├── download.php # Script to force file downloads for attached files. ├── functions.php # Common functions (keyword extraction, probability calculation). ├── index.php # Public search page (with subject auto-complete and optional filtering). ├── install.sql # SQL script to create the required database schema. ├── subject_autocomplete.php # AJAX endpoint returning JSON array of distinct subjects. └── .htaccess # Apache rewrite and security configuration.

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

# Edit Question (admin/edit_question.php):

Modify existing question data (fields are pre-populated).

Optionally update the attachment.

The probability is recalculated on update.

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


