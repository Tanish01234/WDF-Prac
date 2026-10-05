# SYNORA --- Technical Requirements Document (TRD)

## 1. Technical Overview

Synora is a frontend-only academic portal built with standard web
technologies.

### Technology Stack

  Layer             Technology
  ----------------- --------------------
  Markup            HTML5
  Styling           CSS3
  Layout            CSS Grid + Flexbox
  Logic             JavaScript ES6+
  Data              JSON
  Data loading      Fetch API
  Client storage    LocalStorage
  Version control   Git / GitHub
  Development       VS Code
  Testing           Browser DevTools

No PHP, MySQL or server-side runtime is required for this version.

------------------------------------------------------------------------

## 2. Project Structure

``` text
SYNORA/
│
├── index.html
│
├── pages/
│   ├── about.html
│   ├── login.html
│   ├── register.html
│   ├── dashboard.html
│   ├── courses.html
│   ├── schedule.html
│   ├── events.html
│   ├── notices.html
│   ├── profile.html
│   ├── resources.html
│   ├── faq.html
│   ├── contact.html
│   └── feedback.html
│
├── css/
│   ├── global.css
│   ├── components.css
│   ├── responsive.css
│   ├── home.css
│   ├── about.css
│   ├── login.css
│   ├── register.css
│   ├── dashboard.css
│   ├── courses.css
│   ├── schedule.css
│   ├── events.css
│   ├── notices.css
│   ├── profile.css
│   ├── resources.css
│   ├── faq.css
│   ├── contact.css
│   └── feedback.css
│
├── js/
│   ├── app.js
│   ├── navigation.js
│   ├── theme.js
│   ├── validation.js
│   ├── dashboard.js
│   ├── schedule.js
│   ├── events.js
│   ├── notices.js
│   ├── profile.js
│   └── search.js
│
├── data/
│   ├── events.json
│   ├── students.json
│   ├── faqs.json
│   └── notices.json
│
├── assets/
│   ├── images/
│   └── icons/
│
└── docs/
    ├── PDR.md
    ├── TRD.md
    ├── Architecture.md
    ├── UI_UX_Design.md
    └── Master_Prompt.md
```

------------------------------------------------------------------------

## 3. CSS Strategy

The project uses page-specific styles while avoiding unnecessary
duplication.

### Common CSS

`global.css`

Contains:

-   CSS variables
-   reset/base styles
-   typography
-   body
-   links
-   common utility classes

`components.css`

Contains reusable UI:

-   buttons
-   cards
-   badges
-   inputs
-   modal
-   toast
-   navbar
-   sidebar
-   pagination
-   progress bars

`responsive.css`

Contains cross-page responsive rules.

### Page CSS

Each page has a dedicated stylesheet:

``` text
home.css
about.css
login.css
register.css
dashboard.css
courses.css
schedule.css
events.css
notices.css
profile.css
resources.css
faq.css
contact.css
feedback.css
```

This keeps page-specific styling easy to locate while common components
remain reusable.

------------------------------------------------------------------------

## 4. HTML Requirements

All major pages should follow a structure similar to:

``` html
<header>
    <nav>
        ...
    </nav>
</header>

<main>
    <section>
        ...
    </section>
</main>

<footer>
    ...
</footer>
```

Use:

-   `header`
-   `nav`
-   `main`
-   `section`
-   `article`
-   `aside`
-   `footer`

where appropriate.

Form controls must have labels.

Images must use meaningful `alt` text.

------------------------------------------------------------------------

## 5. JavaScript Requirements

### `app.js`

General initialization and shared utilities.

### `navigation.js`

-   Mobile menu
-   Active navigation state
-   Sidebar open/close
-   Navigation-related events

### `theme.js`

-   Light/dark theme
-   LocalStorage
-   Initial theme restoration

Example:

``` js
const savedTheme = localStorage.getItem("synora-theme");

if (savedTheme) {
    document.documentElement.dataset.theme = savedTheme;
}
```

### `validation.js`

Registration and feedback validation.

### `events.js`

-   Fetch events JSON
-   Render cards
-   Search
-   Filter
-   Sort
-   Pagination

### `schedule.js`

-   Day selection
-   Current-day highlighting
-   Timetable display helpers

### `notices.js`

-   Notice rendering
-   Search/filter
-   Priority state

### `profile.js`

-   Modal
-   Edit form
-   Frontend profile update

### `search.js`

Shared search utilities where appropriate.

------------------------------------------------------------------------

## 6. JSON Data Contracts

### Event

``` json
{
  "id": 1,
  "title": "AI Innovation Hackathon",
  "category": "Hackathon",
  "date": "2026-10-04",
  "time": "10:00 AM",
  "location": "Innovation Lab",
  "description": "Build and present an innovative solution.",
  "status": "Open"
}
```

### Student

``` json
{
  "id": 1,
  "name": "Tanis Bedia",
  "course": "B.Tech IT",
  "semester": 3,
  "email": "student@example.com",
  "attendance": 92,
  "cgpa": 8.6
}
```

### FAQ

``` json
{
  "id": 1,
  "question": "How do I register for an event?",
  "answer": "Open the Events page and select the required event."
}
```

### Notice

``` json
{
  "id": 1,
  "title": "WDF Practical Submission",
  "category": "Academic",
  "priority": "Important",
  "date": "2026-09-28",
  "description": "Submit the required practical work before the announced deadline."
}
```

------------------------------------------------------------------------

## 7. Fetch API Pattern

Use:

``` js
async function loadEvents() {
    try {
        showLoading();

        const response = await fetch("../data/events.json");

        if (!response.ok) {
            throw new Error("Unable to load events");
        }

        const events = await response.json();

        renderEvents(events);
    } catch (error) {
        showError("Events could not be loaded.");
        console.error(error);
    } finally {
        hideLoading();
    }
}
```

The interface must include loading, success, empty and error states
where relevant.

------------------------------------------------------------------------

## 8. Search, Filter and Sort

Recommended pipeline:

``` text
Original JSON
      ↓
Search
      ↓
Category Filter
      ↓
Sort
      ↓
Pagination
      ↓
Render
```

Do not mutate the original dataset unnecessarily.

Example:

``` js
let filteredEvents = [...allEvents];

filteredEvents = filteredEvents.filter(...);
filteredEvents.sort(...);
```

------------------------------------------------------------------------

## 9. Pagination

Recommended configuration:

``` text
recordsPerPage = 6
```

Functions:

``` text
getPaginatedItems()
renderPagination()
goToPage()
```

Pagination should disable previous/next buttons when appropriate.

------------------------------------------------------------------------

## 10. Registration Validation

Validation should be understandable and visible.

### Name

-   Required
-   Reasonable length
-   Alphabetic/space-friendly pattern

### Email

Use HTML email input plus JavaScript validation.

### Mobile

Use a suitable Indian mobile number pattern for the academic demo.

### Password

Minimum:

-   8 characters
-   uppercase
-   lowercase
-   number
-   special character

### Confirm Password

Must match password.

### Terms

Must be checked.

The project must not claim that this frontend-only password handling is
secure account storage. No password is sent to a server in this version.

------------------------------------------------------------------------

## 11. LocalStorage Usage

Suitable keys:

``` text
synora-theme
synora-notifications
synora-ui-preferences
```

Do not store real passwords or sensitive personal information in
LocalStorage.

------------------------------------------------------------------------

## 12. Timetable Data Model

The supplied timetable is from:

-   CHARUSAT
-   Faculty of Technology and Engineering
-   DEPSTAR
-   Department of Information Technology
-   B.Tech IT
-   Academic Year 2026--27
-   Semester 3rd, Odd Term
-   Effective from 06/07/2026

The timetable UI should represent the source document rather than simply
embedding the screenshot.

### Time Slots

``` text
09:10 AM – 10:10 AM
10:10 AM – 11:10 AM
11:10 AM – 12:10 PM
12:10 PM – 01:10 PM  Lunch Break
01:10 PM – 02:10 PM
02:10 PM – 02:20 PM  Short Break
02:20 PM – 03:20 PM
03:20 PM – 04:20 PM
```

### Courses

``` text
ITUC201 — FCN — Fundamentals of Computer Networks
CSUC201 — FDSA — Fundamentals of Data Structures and Algorithms
ITUE203 — WDF — Web Development Frameworks
CEUE203 — OOP — Object Oriented Programming
MSUD203 — DM — Discrete Mathematics
HSUV201 — CPI — Creativity, Problem Solving and Innovation
```

The exact room/faculty information shown in the supplied timetable
should be preserved when implementing the final dataset/UI.

------------------------------------------------------------------------

## 13. UI States

Every dynamic section should consider:

### Loading

``` text
Loading events...
```

### Empty

``` text
No events match your search.
```

### Error

``` text
Unable to load events.
Try again
```

### Success

Normal rendered content.

------------------------------------------------------------------------

## 14. Browser Testing

Test in:

-   Chrome
-   Safari
-   Edge if available

Test viewport widths:

-   Desktop
-   Tablet
-   Mobile

Check:

-   Navigation
-   Forms
-   Console
-   Fetch requests
-   Theme persistence
-   Search
-   Filter
-   Sort
-   Pagination
-   Modals
-   FAQ
-   timetable scrolling

------------------------------------------------------------------------

## 15. Git Commit Strategy

Suggested commits:

``` text
chore: initialize Synora project
docs: add project requirements and sitemap
feat: create semantic HTML pages
feat: add responsive design system
feat: add dashboard and academic pages
feat: add JavaScript navigation and theme
feat: add registration validation
feat: add JSON datasets
feat: add Fetch API event rendering
feat: add search filter sort pagination
feat: add timetable interface
fix: improve mobile layout
fix: resolve console and navigation issues
docs: finalize project documentation
```

The commit history should reflect actual work performed and should not
be fabricated.
