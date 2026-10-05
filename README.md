# SYNORA — Academic Student Portal

> **"Your Student Life, Connected."**

[![Academic Year](https://img.shields.io/badge/Academic%20Year-2026--27-blue.svg)](#)
[![Semester](https://img.shields.io/badge/Semester-3rd%20(Odd%20Term)-purple.svg)](#)
[![Course](https://img.shields.io/badge/Course-ITUE203%20Web%20Development%20Frameworks-orange.svg)](#)
[![Department](https://img.shields.io/badge/Department-Information%20Technology-green.svg)](#)
[![Institute](https://img.shields.io/badge/Institute-DEPSTAR%20%7C%20CHARUSAT-red.svg)](#)

---

## 📌 Project Overview

**Synora** is a comprehensive, responsive academic portal engineered specifically for undergraduate students of the Department of Information Technology at **DEPSTAR, Charotar University of Science and Technology (CHARUSAT)**.

The application serves as a central hub for student academics, bringing together the official university timetable, semester courses, dynamically rendered campus events, department notices, student profile management, and academic resources into a single, cohesive, and accessible frontend experience.

---

## 👨‍🎓 Student Developer Information

- **Name:** Tanis Bedia
- **Student ID / Roll No:** `25DIT006`
- **Program:** B.Tech Information Technology
- **Division / Batch:** `3IT-A` (Lab Batch A)
- **Course Coordinator:** Prof. Jignesh Vania (JV)

---

## 🎯 Practical Alignment Matrix (Practicals 1 to 6)

Synora is architected so that each curriculum practical concept is demonstrably functional:

| Practical | Topic & Curriculum Competency | Synora Implementation Proof | File Reference |
| :--- | :--- | :--- | :--- |
| **Practical 1** | Project Planning, Requirements Analysis, Sitemap, Wireframe & GitHub Setup | Detailed documentation specifications, Information Architecture, and clean GitHub repository layout. | [`docs/PDR.md`](file:///Users/tanisbedia/Desktop/2026/WDF/docs/PDR.md)<br>[`docs/TRD.md`](file:///Users/tanisbedia/Desktop/2026/WDF/docs/TRD.md)<br>[`docs/Architecture.md`](file:///Users/tanisbedia/Desktop/2026/WDF/docs/Architecture.md) |
| **Practical 2** | Semantic HTML5 & Accessibility Landmark Architecture | 14 interconnected semantic HTML5 pages utilizing `<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<aside>`, and `<footer>` with complete ARIA support. | [`index.html`](file:///Users/tanisbedia/Desktop/2026/WDF/index.html)<br>[`pages/`](file:///Users/tanisbedia/Desktop/2026/WDF/pages/) |
| **Practical 3** | Responsive CSS3, CSS Grid, Flexbox & Media Queries | Unified CSS Design Tokens with light & dark modes, modular component stylesheets, and full responsiveness across desktop (≥1200px), tablet (768px–1199px), and mobile (<768px). | [`css/global.css`](file:///Users/tanisbedia/Desktop/2026/WDF/css/global.css)<br>[`css/components.css`](file:///Users/tanisbedia/Desktop/2026/WDF/css/components.css)<br>[`css/responsive.css`](file:///Users/tanisbedia/Desktop/2026/WDF/css/responsive.css) |
| **Practical 4** | JavaScript DOM Manipulation, UI Interactivity & LocalStorage | Light/Dark theme switcher with `localStorage` persistence, interactive off-canvas mobile drawer, modal dialogues, toast notifications, dynamic greetings, and notification bell counter. | [`js/theme.js`](file:///Users/tanisbedia/Desktop/2026/WDF/js/theme.js)<br>[`js/navigation.js`](file:///Users/tanisbedia/Desktop/2026/WDF/js/navigation.js)<br>[`js/app.js`](file:///Users/tanisbedia/Desktop/2026/WDF/js/app.js)<br>[`pages/faq.html`](file:///Users/tanisbedia/Desktop/2026/WDF/pages/faq.html) |
| **Practical 5** | Registration Form with Frontend Validation & User-Friendly Error Handling | Comprehensive registration validation using JavaScript Regular Expressions for Name, Email, Indian Mobile, Password complexity rating meter, password matching, course/year selection, gender, and terms acceptance. | [`js/validation.js`](file:///Users/tanisbedia/Desktop/2026/WDF/js/validation.js)<br>[`pages/register.html`](file:///Users/tanisbedia/Desktop/2026/WDF/pages/register.html) |
| **Practical 6** | External JSON Data Rendering via Fetch API, Search, Filter & Pagination | Dynamic asynchronous rendering of `data/events.json` with multi-field instant search, category chip filters, sorting (date/title), pagination (6 per page), and loading/empty/error states. | [`js/events.js`](file:///Users/tanisbedia/Desktop/2026/WDF/js/events.js)<br>[`data/events.json`](file:///Users/tanisbedia/Desktop/2026/WDF/data/events.json)<br>[`pages/events.html`](file:///Users/tanisbedia/Desktop/2026/WDF/pages/events.html) |

---

## 🛠️ Technology Stack & Scope Boundaries

### Allowed Technologies (Frontend Only):
- **Markup:** HTML5 (Semantic & Accessible)
- **Styling:** CSS3 (Custom Properties / Variables, CSS Grid, Flexbox, Media Queries)
- **Scripting:** JavaScript ES6+ (Vanilla / Modular)
- **Data:** JSON (`data/*.json`)
- **Data Loading:** Asynchronous Fetch API (`fetch()` + `async`/`await`)
- **Client Storage:** Browser `localStorage` (Theme preferences, non-sensitive demo states)

### Strict Scope Boundary:
In strict compliance with the academic curriculum up to Practical 6, Synora **does not** introduce:
- Node.js backend runtimes
- React, Next.js, Vue, or Angular
- Tailwind CSS or Bootstrap CSS frameworks
- PHP backend endpoints or MySQL databases
- Server-side sessions or server-side password storage

*(Server-side authentication, PHP form handling, and MySQL database CRUD begin in Practical 7 onward).*

---

## 📂 Project Architecture

```text
SYNORA/
├── index.html                   # Root portal landing page
│
├── pages/                       # 13 Sub-pages
│   ├── about.html               # Project background & curriculum mapping
│   ├── login.html               # Frontend student sign-in gateway
│   ├── register.html            # Registration form with regex validation (P5)
│   ├── dashboard.html           # Main student dashboard & live widgets
│   ├── courses.html             # 3rd semester subjects, credits, faculty
│   ├── schedule.html            # Official digital timetable & live class tracker
│   ├── events.html              # Fetch API dynamic events, search, filter, paginate (P6)
│   ├── notices.html             # Department circulars with priority badges
│   ├── profile.html             # Student credentials & profile edit modal
│   ├── resources.html           # Academic lab manuals, repositories, digital library
│   ├── faq.html                 # Accordion knowledge base (P4)
│   ├── contact.html             # Department contact desk & inquiry form
│   └── feedback.html            # Student experience feedback form
│
├── css/                         # Page-specific & shared stylesheets
│   ├── global.css               # Design tokens, CSS variables, typography, reset
│   ├── components.css           # Reusable buttons, cards, modals, toasts, sidebars
│   ├── responsive.css           # Breakpoints & mobile drawer navigation rules
│   ├── home.css                 # Landing hero & feature cards
│   ├── about.css                # About hero & practical matrix
│   ├── login.css                # Login form card
│   ├── register.css             # Registration card & validation layout
│   ├── dashboard.css            # Dashboard welcome card & widget grids
│   ├── courses.css              # Course cards & attendance progress
│   ├── schedule.css             # Official timetable table, break rows, badges
│   ├── events.css               # Event cards, filter chips, search input
│   ├── notices.css              # Notice cards & priority badges
│   ├── profile.css              # Profile card & academic records
│   ├── resources.css            # Academic resource cards
│   ├── faq.css                  # FAQ container & search styles
│   ├── contact.css              # Contact channels & office hours
│   └── feedback.css             # Star rating & feedback card
│
├── js/                          # Modular JavaScript files
│   ├── app.js                   # Toast manager, dynamic greetings, accordions, modals
│   ├── navigation.js            # Mobile drawer, overlay, active link sync
│   ├── theme.js                 # Dark/Light mode switcher with LocalStorage
│   ├── validation.js            # Regex validation, password strength meter (P5)
│   ├── dashboard.js             # Live schedule widget & preview feeds
│   ├── schedule.js              # Authoritative timetable data model & live class detector
│   ├── events.js                # Fetch API, search, filter, sort, paginate (P6)
│   ├── notices.js               # Notice feed loader & modal reader
│   ├── profile.js               # Profile state update & LocalStorage demo sync
│   └── search.js                # Debounce and text normalization utilities
│
├── data/                        # JSON Data Contracts
│   ├── events.json              # 16 Realistic campus events (Hackathons, Workshops, etc.)
│   ├── students.json            # 16 Sample student records
│   ├── faqs.json                # 16 Academic & campus portal FAQs
│   └── notices.json             # 10 Department announcements
│
├── assets/                      # Media Assets
│   ├── images/
│   │   └── timetable_source.png # Authoritative CHARUSAT DEPSTAR timetable screenshot
│   └── icons/
│
└── docs/                        # Project Specification Documents
    ├── PDR.md                   # Project Definition & Requirements
    ├── TRD.md                   # Technical Requirements Document
    ├── Architecture.md          # System & UI Architecture
    ├── UI_UX_Design.md          # Visual Design Specification
    └── Master_Prompt.md         # Master Build Directives
```

---

## 📅 Official Academic Timetable (Faithful Transcription)

The digital timetable on `pages/schedule.html` is an accurate digital representation of the official **CHARUSAT / DEPSTAR B.Tech IT 3rd Semester (Odd Term 2026–27)** timetable (Effective from `06/07/2026`):

### Academic Time Slots:
- `09:10 AM – 10:10 AM`: Academic Slot 1
- `10:10 AM – 11:10 AM`: Academic Slot 2
- `11:10 AM – 12:10 PM`: **🍴 Lunch Break**
- `12:10 PM – 01:10 PM`: Academic Slot 3 (Practicals / Theory)
- `01:10 PM – 02:10 PM`: Academic Slot 4 (Practicals / Counselling / CPI)
- `02:10 PM – 02:20 PM`: **☕ Short Break**
- `02:20 PM – 03:20 PM`: Academic Slot 5 (Practicals / Theory)
- `03:20 PM – 04:20 PM`: Academic Slot 6 (Practicals / Theory)

### Enrolled Subjects & Faculty Directory:
1. **ITUC201 — FCN:** Fundamentals of Computer Networks (Theory: Prof. Ritesh Patel `RHP` | Lab: Prof. M. H. Vaghela `MH`, Prof. Jignesh Patel `JP`)
2. **CSUC201 — FDSA:** Fundamentals of Data Structures & Algorithms (Theory: Prof. Chintan Raval `CUR`, Prof. Parth Patel `PHP` | Lab: Prof. Amit Patel `ARP`)
3. **ITUE203 — WDF:** Web Development Frameworks (Theory: Prof. Jignesh Vania `JV`, Prof. V. K. Modi `VKM` | Lab: Prof. Jignesh Patel `JP`, Prof. Shital Patel `SHP`)
4. **CEUE203 — OOP:** Object Oriented Programming (Theory: Prof. Dharmendra Gohel `DAG` | Lab: Prof. M. P. Dave `MPD`)
5. **MSUD203 — DM:** Discrete Mathematics (Theory: Prof. R. V. Savaliya `RVS`)
6. **HSUV201 — CPI:** Creativity, Problem Solving and Innovation (Theory: Prof. V. H. Panchal `VHP`)

---

## 🚀 How to Run Locally

Because the project utilizes the **Fetch API** to load external JSON datasets (`data/*.json`), files should be served through a local HTTP server rather than the `file://` protocol to ensure browser security policies allow JSON retrieval.

### Option 1: Python 3 Built-in Server (Recommended)
Open a terminal inside the project root directory and run:
```bash
python3 -m http.server 3000
```
Then visit:
```text
http://localhost:3000
```

### Option 2: VS Code Live Server Extension
1. Open the project folder in VS Code.
2. Right-click `index.html` and choose **"Open with Live Server"**.

### Option 3: Node.js `npx serve`
```bash
npx serve .
```

---

## 🧪 Testing Checklist & Verification

- [x] **Navigation:** All 14 pages load with 100% interconnected links.
- [x] **Theme Switcher:** Dark & Light modes toggle seamlessly; choice persists in `localStorage` across page reloads.
- [x] **Registration Validation:** Client-side regex catches empty inputs, invalid emails, invalid mobile numbers, and weak passwords; password meter dynamically rates strength.
- [x] **Events Fetch API:** `data/events.json` loads dynamically; text search, category filters, sorting, and pagination (6 items/page) operate with zero hardcoded cards.
- [x] **Official Timetable:** Live class detection identifies ongoing lectures; day filter isolates daily schedules; cell click modal reveals complete faculty and room metadata.
- [x] **Console:** Zero uncaught JavaScript exceptions or broken resource requests.

---

## 📜 Academic Declaration

This project is developed by **Tanis Bedia (25DIT006)** for the course **Web Development Frameworks (ITUE203)** at the Department of Information Technology, DEPSTAR, CHARUSAT University. All frontend code, styles, and architectures are developed to meet the prescribed requirements for Practicals 1 through 6.
