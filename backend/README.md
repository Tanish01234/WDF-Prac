# SYNORA Backend & Database Integration
## Practical 7 & Practical 8 Documentation

**Institution:** CHARUSAT • Faculty of Technology and Engineering • DEPSTAR  
**Department:** Information Technology • B.Tech IT Semester 3 (AY 2026–27)  
**Course Code:** ITUE203 — Web Development Frameworks (WDF)  
**Student:** Tanis Bedia (25DIT006)

---

## 1. Directory Structure

```
WDF/
├── backend/
│   ├── config/
│   │   └── db.php               # PDO database connection layer with safe error handling
│   ├── forms/
│   │   ├── register.php         # P7: Registration processor (POST, validation, sanitization, CSV)
│   │   ├── contact.php          # P7: Contact inquiry processor (POST, validation, CSV)
│   │   └── feedback.php         # P7: Student portal feedback processor (POST, validation, JSON)
│   ├── storage/
│   │   ├── registrations.csv    # P7: Storage file for student registration records
│   │   ├── contacts.csv         # P7: Storage file for academic contact desk inquiries
│   │   └── feedback.json        # P7: Storage file for portal feedback entries
│   ├── events-db.php            # P8: Relational query demo (students, events, registrations joins)
│   ├── test-db.php              # P8: Database connectivity & prepared statement test script
│   └── README.md                # Technical guide and viva checklist
│
├── database/
│   ├── schema.sql               # P8: MySQL DDL table definitions (PK, FK, indexes)
│   ├── seed.sql                 # P8: Realistic demo dataset for students, events, registrations
│   └── er-diagram.md            # P8: Entity-Relationship model, dictionary, and normalization
│
├── pages/                       # Existing frontend pages (register, contact, feedback, events, etc.)
├── js/validation.js             # Dual client validation + Fetch API integration to PHP endpoints
└── css/                         # Preserved Synora design system and components
```

---

## 2. Practical 7 — PHP Form Processing & File Storage

### Concept & Objectives
Practical 7 demonstrates server-side form handling without database dependencies. It accepts HTTP `POST` submissions, validates and sanitizes input data on the server, and persists records safely into CSV or JSON files.

### Workflow
```
Client Form Submission (register.html / contact.html / feedback.html)
                       ↓ (HTTP POST)
PHP Form Processor (backend/forms/*.php)
                       ↓
1. Method Verification: Reject non-POST requests with HTTP 405 Method Not Allowed
2. Server Validation: Re-validate name, email, mobile, required fields independently
3. Input Sanitization: Trim whitespace, strip HTML tags, prevent delimiter corruption
4. File Storage: Safely write records using flock() file locking (CSV / JSON)
                       ↓
Response Generation (HTTP 200 OK or HTTP 422 Unprocessable Content):
  - Returns JSON if request was made via Fetch API (X-Requested-With / Accept header)
  - Returns styled HTML response page if submitted via standard browser form POST
```

### Storage Files
1. **`backend/storage/registrations.csv`**
   - **Columns:** `Name`, `Email`, `Mobile`, `Course`, `Year`, `Gender`, `Submitted At`
   - **Security Note:** In accordance with academic guidelines for P7/P8, **no passwords** are stored in the CSV file.
2. **`backend/storage/contacts.csv`**
   - **Columns:** `Name`, `Email`, `Subject`, `Message`, `Submitted At`
3. **`backend/storage/feedback.json`**
   - **Structure:** JSON array of objects with `id`, `name`, `email`, `category`, `rating`, `comments`, `submitted_at`.

---

## 3. Practical 8 — MySQL Schema & PHP Database Connectivity

### Concept & Objectives
Practical 8 establishes a normalized relational database model using MySQL and connects PHP via PDO (PHP Data Objects) with parameterized prepared statements.

### Domain Entities & ER Model
- **`students`**: Academic profile entities (`id` PK, `roll_no` UNIQUE, `name`, `email`, `course`, `semester`, `division`, `batch`).
- **`events`**: Campus events and workshops (`id` PK, `title`, `description`, `category`, `event_date`, `location`, `coordinator`, `max_capacity`).
- **`registrations`**: Associative relational entity linking students to events (`id` PK, `student_id` FK → `students.id`, `event_id` FK → `events.id`, `status`, `registered_at`).
  - Enforces composite uniqueness: A student cannot register for the same event multiple times.
  - Foreign keys configure `ON DELETE CASCADE` and `ON UPDATE CASCADE`.

Detailed ER diagrams, attribute descriptions, and 3NF normalization proofs are documented in [`database/er-diagram.md`](file:///Users/tanisbedia/Desktop/2026/WDF/database/er-diagram.md).

### PDO Architecture (`backend/config/db.php`)
- **Connection Engine:** PDO MySQL driver.
- **Attributes:**
  - `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` (Throws exceptions on SQL/connection errors).
  - `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC` (Associative arrays).
  - `PDO::ATTR_EMULATE_PREPARES => false` (Enforces native database server prepared statements).
- **Security:** Technical error details are logged to PHP error logs via `error_log()`. Client-facing exceptions show clean messages without exposing database passwords, hostnames, or internal stack traces.

---

## 4. Local Setup & Execution Guide

### Prerequisites
- PHP 8.1+ (CLI and web module)
- MySQL 8.0+ or MariaDB 10.4+
- (Optional) XAMPP, MAMP, or Homebrew stack

### Step 1: Initialize Database
Run using MySQL CLI or import through phpMyAdmin:
```bash
# 1. Create database and tables
mysql -u root -p < database/schema.sql

# 2. Insert sample demo dataset
mysql -u root -p synora < database/seed.sql
```

### Step 2: Configure Environment (If not using standard defaults)
Default credentials configured in `backend/config/db.php`:
- **Host:** `127.0.0.1` (Port: `3306`)
- **Database:** `synora`
- **User:** `root`
- **Password:** `""` (empty)

To customize, export environment variables:
```bash
export DB_HOST="127.0.0.1"
export DB_PORT="3306"
export DB_NAME="synora"
export DB_USER="root"
export DB_PASS="your_password"
```

### Step 3: Run Local Server
From the project root:
```bash
php -S 127.0.0.1:8000
```
Open in browser:
- Portal Dashboard: `http://127.0.0.1:8000/pages/dashboard.html`
- Registration Form (P7): `http://127.0.0.1:8000/pages/register.html`
- Database Test Diagnostic (P8): `http://127.0.0.1:8000/backend/test-db.php`
- Live MySQL Relational Query (P8): `http://127.0.0.1:8000/backend/events-db.php`

---

## 5. Test Matrix & Viva Verification Checklist

| Test ID | Test Scenario | Execution | Expected Output | Status |
| :--- | :--- | :--- | :--- | :--- |
| **Test 1** | Valid Registration (P7) | POST to `backend/forms/register.php` with valid fields | `HTTP 200`, record saved to `registrations.csv` | **PASSED** |
| **Test 2** | Missing Required Fields (P7) | POST with empty name/email/course | `HTTP 422`, array of validation error messages | **PASSED** |
| **Test 3** | Invalid Email Format (P7) | POST with `email=invalid-address` | `HTTP 422`, "Please enter a valid email address" | **PASSED** |
| **Test 4** | Invalid Mobile Number (P7) | POST with `mobile=123` | `HTTP 422`, "Mobile number must be a valid 10-digit Indian number" | **PASSED** |
| **Test 5** | Unsupported HTTP Method (P7) | GET request to `backend/forms/register.php` | `HTTP 405 Method Not Allowed`, error notice | **PASSED** |
| **Test 6** | File Storage Concurrency (P7) | Concurrent write using `flock()` | Records appended without dataset corruption | **PASSED** |
| **Test 7** | MySQL Connection (P8) | Access `backend/test-db.php` | `HTTP 200`, "Database connection successful" | **PASSED** |
| **Test 8** | Invalid DB Configuration (P8) | Connect with invalid db name | Safe user-facing error; credentials not leaked | **PASSED** |
| **Test 9** | Parameterized Prepared Query (P8) | Query student by `roll_no = :roll_no` | Record returned; no direct string concatenation | **PASSED** |
| **Test 10** | Relational Foreign Key Join (P8) | Access `backend/events-db.php` | Joined data from `students`, `events`, `registrations` | **PASSED** |
| **Test 11** | Foreign Key Integrity (P8) | Insert registration with invalid `student_id` | MySQL rejects with constraint violation (`SQLState 23000`) | **PASSED** |

---

## 6. Milestone Boundary Notice (Strict Scope Control)

As mandated by the laboratory syllabus for Practical 7 & Practical 8:
- **NO Password Hashing** (e.g. `password_hash()`)
- **NO Authentication Backend** (`login.php` remains client-side demo)
- **NO PHP Sessions** (`session_start()`, session cookies)
- **NO Role-Based Access Control (RBAC)**
- **NO Student / Event CRUD Operations**
- **NO Admin Dashboard Backend**

These security and management features belong strictly to **Practical 9 and Practical 10** and will be implemented in subsequent milestones.
