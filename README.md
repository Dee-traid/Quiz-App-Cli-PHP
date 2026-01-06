# PHP CLI Quiz Management System

A command-line quiz application built with PHP (OOP) and PostgreSQL.  
This project demonstrates backend fundamentals such as authentication, database design, secure password handling, and timed user interactions — all within a terminal environment.

---

##  Problem Statement

Many quiz platforms focus on web interfaces, but few demonstrate how backend systems can function independently of a UI.  
This project solves that by implementing a **fully interactive quiz system via the command line**, focusing on logic, security, and data integrity.

---

##  Features

###  User Module
- User registration and login
- Secure password hashing (`password_hash`, `password_verify`)
- Subject-based quiz selection
- Timed quiz sessions
- Automatic score calculation
- Quiz result persistence with timestamps
- View past quiz attempts

###  Admin Module
- Admin authentication
- Create, update, view, and delete quiz questions
- Manage subjects and courses

###  Core System
- CLI-based user interaction
- PDO database abstraction
- PostgreSQL integration
- Object-Oriented architecture
- Question randomization (shuffling)
- Quiz timer enforcement
- Defensive input validation

---

##  Tech Stack

- **Language:** PHP (CLI)
- **Database:** PostgreSQL
- **Architecture:** Object-Oriented Programming (OOP)
- **Security:** Password hashing, prepared statements
- **Version Control:** Git & GitHub

---


Quick Start (Local Setup)

### 1. Prerequisites
* PHP 8.1 or higher
* PostgreSQL

### 2. Setup the Database
1. Create a database named `quiz_app`.
2. Import the schema:
   ```bash
   psql -U postgres -d quiz_app -f DatabaseSchema.sql

## 📁 Project Structure

php-cli-quiz-app/
│
├── index.php
├── DatabaseHelper.php
├── AppManager.php
├── Admin.php
├── User.php
├── Question.php
├── Quiz.php
├── README.md
├── .gitignore
├── .env.example
└── LICENSE



##  Author

ADEJOH JOSHUA 
Backend PHP Developer  

- GitHub: https://github.com/Dee-Traid 
- LinkedIn: https://linkedin.com/in/adejoh-joshua

