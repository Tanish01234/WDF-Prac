# SYNORA --- Master Build Prompt

## 0. Role

You are implementing **Synora**, a frontend-only student portal for an
academic Web Development Frameworks practical project.

Build the project as a clean, understandable student-level web
application using:

-   HTML5
-   CSS3
-   JavaScript ES6+
-   JSON
-   Fetch API
-   LocalStorage

Do not introduce React, Next.js, Vue, Angular, Node.js, PHP, MySQL,
Tailwind, Bootstrap or another framework unless explicitly requested
later.

The current milestone is intentionally limited to the frontend concepts
covered up to Practical 6.

------------------------------------------------------------------------

# 1. Project Goal

Create a polished student portal named:

> **SYNORA**

Suggested tagline:

> **Your Student Life, Connected.**

The application should combine:

-   student dashboard
-   academic subjects
-   official timetable
-   campus events
-   notices
-   resources
-   student profile
-   registration
-   FAQ
-   contact
-   feedback

The portal should feel like a real modern academic product while
remaining straightforward enough to explain in a viva.

Do not over-engineer the project.

------------------------------------------------------------------------

# 2. Academic Alignment

The implementation must visibly demonstrate the concepts from the first
six practicals.

## Practical 1

Demonstrate:

-   project planning
-   requirement analysis
-   sitemap
-   wireframe
-   GitHub-ready structure
-   documentation

## Practical 2

Demonstrate:

-   semantic HTML5
-   accessibility-ready structure
-   linked pages
-   meaningful headings
-   labels
-   alt text

## Practical 3

Demonstrate:

-   CSS3
-   CSS Grid
-   Flexbox
-   media queries
-   responsive layout
-   consistent typography
-   spacing
-   contrast

## Practical 4

Demonstrate:

-   DOM selection
-   event handling
-   class manipulation
-   LocalStorage
-   FAQ accordion
-   modal
-   hamburger menu
-   notification banner/toast
-   light/dark theme
-   basic transitions

## Practical 5

Demonstrate:

-   registration form
-   HTML input types
-   JavaScript validation
-   regular expressions
-   password strength
-   confirm password
-   terms acceptance
-   inline error messages
-   real-time validation where useful

## Practical 6

Demonstrate:

-   JSON
-   Fetch API
-   dynamic rendering
-   search
-   filter
-   sorting
-   pagination
-   loading state
-   error state
-   empty state
-   modular JavaScript

------------------------------------------------------------------------

# 3. Hard Scope Boundary

Do NOT implement:

-   PHP
-   MySQL
-   database
-   server authentication
-   real password storage
-   PHP sessions
-   backend CRUD
-   PHP JSON API
-   real admin backend
-   server-side uploads

These belong to later practicals.

If a UI mentions login or profile editing, it must be clearly
frontend/demo behaviour.

Never pretend that frontend validation is server security.

------------------------------------------------------------------------

# 4. Required Pages

Create these 14 pages:

``` text
index.html
pages/about.html
pages/login.html
pages/register.html
pages/dashboard.html
pages/courses.html
pages/schedule.html
pages/events.html
pages/notices.html
pages/profile.html
pages/resources.html
pages/faq.html
pages/contact.html
pages/feedback.html
```

Every page must be reachable through navigation.

------------------------------------------------------------------------

# 5. Required CSS Architecture

Use page-specific CSS plus shared CSS.

``` text
css/
├── global.css
├── components.css
├── responsive.css
├── home.css
├── about.css
├── login.css
├── register.css
├── dashboard.css
├── courses.css
├── schedule.css
├── events.css
├── notices.css
├── profile.css
├── resources.css
├── faq.css
├── contact.css
└── feedback.css
```

## Important rule

Do not copy the entire design system into every page CSS file.

### global.css

Contains:

-   variables
-   reset
-   body
-   typography
-   base elements

### components.css

Contains:

-   buttons
-   cards
-   badges
-   modal
-   toast
-   navbar
-   sidebar
-   inputs
-   pagination
-   progress

### responsive.css

Contains shared responsive behaviour.

### Page CSS

Contains only page-specific layout and styling.

------------------------------------------------------------------------

# 6. Required JavaScript Architecture

``` text
js/
├── app.js
├── navigation.js
├── theme.js
├── validation.js
├── dashboard.js
├── schedule.js
├── events.js
├── notices.js
├── profile.js
└── search.js
```

Avoid one huge `script.js`.

Each file should have a clear responsibility.

------------------------------------------------------------------------

# 7. JSON DATA

Create:

``` text
data/
├── events.json
├── students.json
├── faqs.json
└── notices.json
```

The practical explicitly calls for `events.json`, `students.json` and
`faqs.json` with at least 15 records each.

Create realistic academic/campus data.

Do not use meaningless placeholder records such as:

``` text
Event 1
Event 2
Student 2
FAQ 3
```

Use believable but clearly sample data.

------------------------------------------------------------------------

# 8. EVENT JSON REQUIREMENT

Each event should contain enough information for the UI.

Example:

``` json
{
  "id": 1,
  "title": "AI Innovation Hackathon",
  "category": "Hackathon",
  "date": "2026-10-04",
  "time": "10:00 AM",
  "location": "Innovation Lab",
  "description": "Build and present an innovative technology solution.",
  "status": "Open"
}
```

Create at least 15 events.

Suggested categories:

-   Hackathon
-   Workshop
-   Seminar
-   Cultural
-   Sports
-   Academic
-   Placement

------------------------------------------------------------------------

# 9. STUDENT JSON REQUIREMENT

Create at least 15 sample student records.

Fields may include:

``` text
id
name
studentId
course
semester
email
attendance
cgpa
status
```

Use fictional sample data.

Do not expose real student personal information.

------------------------------------------------------------------------

# 10. FAQ JSON REQUIREMENT

Create at least 15 FAQ records.

Example:

``` json
{
  "id": 1,
  "question": "How do I register for an event?",
  "answer": "Open the Events page and select the event you want to view."
}
```

Render FAQ dynamically where practical.

------------------------------------------------------------------------

# 11. NOTICE JSON

Create realistic campus notices.

Fields:

``` text
id
title
category
priority
date
description
```

Suggested categories:

-   Academic
-   Important
-   Event
-   General

------------------------------------------------------------------------

# 12. DESIGN SYSTEM

Use a premium but restrained design.

## Dark

``` text
Background: #090B10
Surface: #10141C
Elevated: #151A24
Text: #F5F7FA
Muted: #8B93A5
Border: #232936
Accent: #7C5CFF
```

## Light

``` text
Background: #F6F7FB
Surface: #FFFFFF
Text: #111318
Muted: #687083
Border: #E5E7EB
Accent: #6D4AFF
```

Use CSS custom properties so theme switching is easy.

------------------------------------------------------------------------

# 13. GLOBAL UX

Desktop navigation:

``` text
SYNORA

MAIN
Overview
Courses
Schedule
Events
Notices

ACADEMIC
Progress
Resources

PERSONAL
Profile
Settings
```

Top bar:

``` text
Search
Notifications
Theme
Profile
```

Mobile:

``` text
Logo
Notification
Theme
Hamburger
```

------------------------------------------------------------------------

# 14. HOME PAGE

Create a clean landing page.

Hero:

``` text
Your Student Life,
Connected.

One place for your academics,
schedule, events and campus updates.

[ Explore Portal ]
[ View Events ]
```

Show:

-   feature cards
-   academic snapshot
-   events preview
-   portal benefits
-   footer

Do not make it look like a marketing agency website.

------------------------------------------------------------------------

# 15. DASHBOARD

Dashboard should be the main product page.

Include:

### Greeting

``` text
Good Evening, Tanis
Here's what's happening today.
```

The greeting can be generated based on current time.

### Stats

``` text
CGPA
Attendance
Credits
Tasks
```

### Today's Schedule

Show:

-   time
-   subject
-   room
-   status

### Upcoming Events

Render a small event preview.

### Notices

Show latest notices.

### Quick Actions

Buttons:

``` text
View Schedule
Browse Events
View Courses
Open Resources
```

------------------------------------------------------------------------

# 16. COURSES

Display the semester subjects.

Use the official course names:

``` text
ITUC201 — FCN
Fundamentals of Computer Networks

CSUC201 — FDSA
Fundamentals of Data Structures and Algorithms

ITUE203 — WDF
Web Development Frameworks

CEUE203 — OOP
Object Oriented Programming

MSUD203 — DM
Discrete Mathematics

HSUV201 — CPI
Creativity, Problem Solving and Innovation
```

The supplied timetable image is the source for the semester/course
context.

Course cards may display:

-   course code
-   short name
-   full name
-   credits
-   attendance
-   progress
-   faculty abbreviations where appropriate

------------------------------------------------------------------------

# 17. OFFICIAL TIMETABLE

Use the supplied CHARUSAT/DEPSTAR timetable as the source.

Header information:

``` text
CHARUSAT
Faculty of Technology and Engineering
DEPSTAR
Department of Information Technology
B.Tech IT

Academic Year: 2026–27
Semester: 3rd (Odd Term)
With Effect From: 06/07/2026
```

Time slots:

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

Courses:

``` text
ITUC201 — FCN
Fundamentals of Computer Networks

CSUC201 — FDSA
Fundamentals of Data Structures and Algorithms

ITUE203 — WDF
Web Development Frameworks

CEUE203 — OOP
Object Oriented Programming

MSUD203 — DM
Discrete Mathematics

HSUV201 — CPI
Creativity, Problem Solving and Innovation
```

The final implementation must carefully transcribe the
day/time/course/room/faculty details visible in the supplied timetable.

Do not invent missing values.

For entries that contain group-specific practical allocations, preserve
the A/B/C structure.

Example pattern:

``` text
A: ITUC201 FCN (P) — Room 218 — MH
B: ITUC201 FCN (P) — Room 218 — JP
C: ITUE203 WDF (P) — Room 303 — VKM
```

If the exact text in a cell is uncertain during implementation, use the
supplied source image rather than guessing.

------------------------------------------------------------------------

# 18. SCHEDULE UI

Provide two views where practical:

### Full timetable

Structured table.

### Today

Timeline view:

``` text
09:10
WDF
Room 222

10:10
FCN
Room 222

11:10
Practical
...
```

Highlight the current/upcoming class when possible.

Breaks must be clearly labelled.

------------------------------------------------------------------------

# 19. EVENTS PAGE

This is the primary Practical 6 demonstration.

Top:

``` text
Campus Events
Discover what's happening around Synora.

[ Search events... ]

[ All ]
[ Hackathon ]
[ Workshop ]
[ Academic ]
[ Cultural ]

Sort: Latest
```

Cards must be rendered from `events.json`.

Do not hardcode event cards into the HTML.

------------------------------------------------------------------------

# 20. EVENTS LOGIC

Required sequence:

``` text
fetch JSON
↓
store data
↓
search
↓
filter
↓
sort
↓
paginate
↓
render
```

Functions should be understandable.

Suggested:

``` text
loadEvents()
renderEvents()
applySearch()
applyFilter()
applySort()
renderPagination()
goToPage()
```

Show:

-   loading
-   success
-   empty
-   error

------------------------------------------------------------------------

# 21. REGISTRATION

Use the practical's required fields:

``` text
Name
Email
Mobile
Password
Confirm Password
Course
Year
Gender
Terms
```

Validation requirements:

-   required checks
-   email validation
-   mobile validation
-   password strength
-   confirm password
-   terms
-   inline messages

Use real-time validation for a polished demonstration.

Do not store passwords.

------------------------------------------------------------------------

# 22. LOGIN

The login page is frontend-only.

It may use a demo navigation flow, but must not pretend to perform
secure authentication.

If a demo login is added, clearly keep it as a frontend demonstration.

------------------------------------------------------------------------

# 23. FAQ

Render questions from JSON if practical.

Clicking a question should:

-   expand answer
-   collapse answer
-   update `aria-expanded`

Only one open at a time is preferred for a clean interface.

------------------------------------------------------------------------

# 24. PROFILE

Profile should contain:

-   avatar
-   student name
-   course
-   semester
-   student ID
-   email
-   academic summary

Edit button opens a modal.

The modal demonstrates DOM manipulation.

No server update is required.

------------------------------------------------------------------------

# 25. THEME SWITCHER

Use CSS variables.

Theme:

``` text
dark
light
```

Store:

``` text
synora-theme
```

in LocalStorage.

On page load:

1.  read saved value
2.  apply theme
3.  default to dark if no preference exists

Do not store sensitive data.

------------------------------------------------------------------------

# 26. MOBILE MENU

Desktop:

``` text
sidebar
```

Mobile:

``` text
hamburger
```

Clicking hamburger:

-   opens drawer
-   adds overlay if needed
-   locks/controls navigation appropriately
-   close button works
-   clicking navigation closes drawer

------------------------------------------------------------------------

# 27. MODALS

Use for:

-   profile editing
-   event details
-   optional confirmation

Requirements:

-   open
-   close button
-   outside click if suitable
-   Escape key if practical
-   focus-friendly behaviour

------------------------------------------------------------------------

# 28. TOASTS

Use for frontend actions:

``` text
✓ Theme updated
✓ Profile updated
✓ Feedback submitted
```

Error:

``` text
Unable to complete the action.
```

Keep toast messages short.

------------------------------------------------------------------------

# 29. ACCESSIBILITY

Use semantic HTML.

Avoid:

``` html
<div onclick="...">
```

when a button is appropriate.

Prefer:

``` html
<button type="button">
```

Use:

-   labels
-   headings
-   landmarks
-   focus states
-   alt text
-   ARIA only where needed

------------------------------------------------------------------------

# 30. CODE STYLE

Keep code readable.

Prefer:

``` js
const events = [];
```

over unnecessarily complex abstractions.

Use descriptive names:

``` text
eventList
searchInput
selectedCategory
currentPage
```

Avoid cryptic names.

Add comments only where they clarify logic.

Do not fill the project with artificial comments explaining every
obvious line.

------------------------------------------------------------------------

# 31. DO NOT OVER-ENGINEER

Avoid:

-   unnecessary libraries
-   unnecessary frameworks
-   huge utility systems
-   artificial design patterns
-   complex state management
-   build tools
-   backend simulation
-   fake API servers

The goal is a strong HTML/CSS/JS academic project.

------------------------------------------------------------------------

# 32. VISUAL QUALITY

The final interface should have:

-   consistent spacing
-   clean typography
-   subtle borders
-   restrained shadows
-   clear hierarchy
-   responsive grids
-   professional buttons
-   polished forms
-   consistent cards

Avoid:

-   excessive gradients
-   excessive glassmorphism
-   glowing everything
-   giant animations
-   random colors
-   crowded screens

------------------------------------------------------------------------

# 33. TIMETABLE IMPLEMENTATION RULE

The supplied timetable image is authoritative for the timetable content.

Do not replace it with a generic timetable.

The interface may improve the presentation, but the underlying academic
information should remain faithful to the supplied source.

Recommended approach:

1.  Store timetable data in JavaScript or JSON.
2.  Render it into the UI.
3.  Provide desktop table.
4.  Provide mobile horizontal scroll or daily view.
5.  Highlight breaks.
6.  Highlight today's/upcoming class.

------------------------------------------------------------------------

# 34. TESTING

Before declaring the project complete, test:

### Navigation

-   all links
-   mobile menu
-   active state

### Forms

-   empty
-   invalid
-   valid
-   password mismatch
-   weak password
-   terms unchecked

### Events

-   JSON loads
-   search
-   filter
-   sort
-   pagination
-   empty result
-   fetch error

### Theme

-   dark
-   light
-   reload persistence

### UI

-   modal
-   FAQ
-   toast
-   notification
-   responsive layout

### Browser

Check browser console for errors.

------------------------------------------------------------------------

# 35. FINAL QUALITY GATE

The project is not finished if:

-   a page has broken navigation
-   JSON fails silently
-   search does not work
-   pagination shows incorrect records
-   mobile layout overflows
-   text becomes unreadable
-   forms accept obviously invalid values
-   theme resets unexpectedly
-   console contains avoidable errors
-   timetable contains guessed academic information

------------------------------------------------------------------------

# 36. Documentation Expectations

Keep project documentation clear and practical.

Use these files:

``` text
PDR.md
TRD.md
Architecture.md
UI_UX_Design.md
Master_Prompt.md
```

Documentation should explain:

-   what was built
-   why it was built
-   how it is structured
-   how the practical requirements are demonstrated
-   how to run it
-   how to test it
-   what is intentionally outside the scope

Avoid inflated claims.

------------------------------------------------------------------------

# 37. Implementation Order

Follow this sequence:

### Phase 1

Create:

-   folder structure
-   common CSS
-   page skeletons
-   navigation
-   footer

### Phase 2

Create:

-   design system
-   responsive layouts
-   dashboard
-   courses
-   timetable
-   profile

### Phase 3

Create:

-   JavaScript navigation
-   theme
-   modal
-   FAQ
-   notifications
-   UI interactions

### Phase 4

Create:

-   registration
-   validation
-   password strength

### Phase 5

Create:

-   JSON datasets
-   Fetch API
-   events rendering
-   search
-   filter
-   sort
-   pagination
-   notices

### Phase 6

Final:

-   responsive testing
-   accessibility check
-   console cleanup
-   documentation
-   GitHub cleanup

------------------------------------------------------------------------

# 38. Final Instruction

Build Synora as a coherent academic student portal, not as a collection
of unrelated demo pages.

Every feature should have a reason to exist.

Every practical requirement should have a visible demonstration.

The application should be polished enough for a faculty demonstration
but simple enough that a student can explain the HTML, CSS and
JavaScript logic during viva.

Do not add technologies outside the defined scope.

Do not invent academic timetable details.

Do not claim backend security where there is no backend.

Keep the implementation clean, readable and maintainable.
