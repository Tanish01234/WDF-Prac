# SYNORA --- Project Definition & Requirements (PDR)

## 1. Project Identity

**Project Name:** Synora\
**Project Type:** Frontend Student Portal / Academic Campus Portal\
**Academic Context:** Web Development Frameworks (ITUE203)\
**Semester:** 3rd Semester, Odd Term, Academic Year 2026--27\
**Department:** Information Technology, CHARUSAT\
**Technology Scope:** HTML5, CSS3, JavaScript ES6+, JSON, Fetch API,
LocalStorage\
**Current Scope Boundary:** Practical 1 to Practical 6 only. PHP, MySQL
and server-side implementation are intentionally outside the current
implementation scope.

### 1.1 Project Statement

Synora is a modern student portal designed to bring common academic and
campus information into one simple interface. The portal focuses on a
clean student experience while demonstrating the frontend concepts
required in the Web Development Frameworks practical work.

The portal will contain static semantic HTML pages, responsive CSS
layouts, JavaScript-driven interactions, client-side form validation,
JSON-based data, Fetch API rendering, search, filtering, sorting,
pagination and browser-side preference storage.

The project is intentionally designed so that each practical concept can
be demonstrated directly from the running application.

------------------------------------------------------------------------

## 2. Problem Definition

Students commonly receive academic information from different places
such as timetable notices, event announcements, subject information,
classroom details and general campus communication. A student portal can
provide a single organized interface for this information.

The problem addressed by Synora is therefore:

> **Design and implement a responsive frontend student portal that
> organizes academic information, timetable details, events, notices,
> student information and useful resources in a consistent and
> accessible interface, while demonstrating HTML5, CSS3 and JavaScript
> concepts covered in the first six practicals.**

------------------------------------------------------------------------

## 3. Project Objectives

### Primary Objectives

1.  Build a complete frontend student portal using HTML, CSS and
    JavaScript.
2.  Use semantic HTML5 structure across all pages.
3.  Create a consistent responsive design using CSS Grid and Flexbox.
4.  Implement JavaScript DOM manipulation and event handling.
5.  Implement a registration form with client-side validation.
6.  Use JSON files as the frontend data source.
7.  Load JSON using the Fetch API.
8.  Implement search, filtering, sorting and pagination.
9.  Store suitable UI preferences using LocalStorage.
10. Maintain a clean project structure that is easy to explain during
    viva.

### Secondary Objectives

-   Keep the interface professional and uncluttered.
-   Make the portal usable on desktop, tablet and mobile.
-   Provide meaningful loading, empty and error states.
-   Use reusable UI patterns without requiring a frontend framework.
-   Keep the implementation understandable enough for academic
    demonstration.

------------------------------------------------------------------------

## 4. Scope

### 4.1 Included

-   Landing/Home page
-   About page
-   Login page UI
-   Registration page
-   Student dashboard
-   Courses/subjects
-   Timetable
-   Events
-   Notices
-   Student profile
-   Resources
-   FAQ
-   Contact
-   Feedback
-   Responsive navigation
-   Light/dark theme
-   Notifications/toasts
-   Modal dialogs
-   FAQ accordion
-   Search
-   Filters
-   Sorting
-   Pagination
-   JSON datasets
-   Fetch API
-   LocalStorage
-   Client-side validation
-   Password strength indicator
-   Accessibility-ready markup

### 4.2 Not Included in Current Version

-   PHP
-   MySQL
-   Server-side authentication
-   Database CRUD
-   Real account creation
-   Password storage
-   PHP sessions
-   Role-based backend access
-   PHP JSON API
-   Server-side file uploads

These topics begin in later practicals and are deliberately kept out of
the current frontend milestone.

------------------------------------------------------------------------

## 5. User Type

### Primary User --- Student

The student can:

-   View academic overview
-   View subjects
-   Check timetable
-   Browse campus events
-   Search and filter events
-   Read notices
-   View profile information
-   Browse resources
-   Read FAQs
-   Submit frontend feedback
-   Register through a validated frontend form
-   Change UI theme
-   Use the portal on mobile devices

### Administrative Concepts

No actual admin backend is implemented in this version. Any
admin-related visual reference should remain clearly demonstrational and
must not pretend to provide real database-backed administration.

------------------------------------------------------------------------

## 6. Information Architecture

The portal contains the following pages:

1.  `index.html` --- Home
2.  `about.html` --- About Synora
3.  `login.html` --- Login
4.  `register.html` --- Registration
5.  `dashboard.html` --- Student Dashboard
6.  `courses.html` --- Courses
7.  `schedule.html` --- Timetable
8.  `events.html` --- Events
9.  `notices.html` --- Notices
10. `profile.html` --- Student Profile
11. `resources.html` --- Resources
12. `faq.html` --- FAQ
13. `contact.html` --- Contact
14. `feedback.html` --- Feedback

This exceeds the practical requirement of at least 10 StudentHub pages.

------------------------------------------------------------------------

## 7. Core Functional Requirements

### FR-01 --- Navigation

All major pages must be connected through a common navigation system.
Links must work from both desktop and mobile navigation.

### FR-02 --- Responsive Layout

The interface must adapt to desktop, tablet and mobile widths. CSS Grid
and Flexbox should be used for layout rather than relying on fixed-width
positioning.

### FR-03 --- Theme Switching

The user can switch between light and dark themes.

The selected theme is stored in LocalStorage and restored on page load.

### FR-04 --- Dashboard

The dashboard displays:

-   Student greeting
-   Current date
-   Academic statistics
-   Attendance
-   Credits
-   Current/upcoming classes
-   Upcoming events
-   Notices
-   Quick actions

### FR-05 --- Timetable

The timetable page presents the supplied CHARUSAT/DEPSTAR B.Tech IT 3rd
Semester timetable in a readable digital format.

The timetable must preserve the important information from the supplied
timetable image, including:

-   Academic year 2026--27
-   Semester 3rd, Odd Term
-   Effective from 06/07/2026
-   Monday--Saturday schedule
-   Course codes
-   Course names/short names
-   Lecture/practical information
-   Faculty abbreviations where available
-   Break periods
-   Skill Enhancement sessions

### FR-06 --- Registration Validation

The registration form validates:

-   Name
-   Email
-   Mobile number
-   Password
-   Confirm password
-   Course
-   Year
-   Gender
-   Terms acceptance

Validation should use suitable HTML input types, JavaScript checks and
regular expressions.

### FR-07 --- JSON Data

The project will contain JSON datasets for at least:

-   `events.json`
-   `students.json`
-   `faqs.json`

A `notices.json` dataset may also be used for the portal's notice feed.

Each primary practical dataset should contain at least 15 records where
applicable.

### FR-08 --- Fetch API

JSON data must be loaded with JavaScript `fetch()` rather than hardcoded
into every HTML card.

### FR-09 --- Search

Users can search event/notice/student/FAQ content depending on the page.

### FR-10 --- Filter

Events can be filtered by category and other suitable fields.

### FR-11 --- Sort

Events can be sorted by date or title.

### FR-12 --- Pagination

Event data must support page-based display so that a larger JSON dataset
does not appear as one long list.

### FR-13 --- Feedback

The feedback page contains a frontend-only form with clear validation
and success feedback.

### FR-14 --- FAQ Accordion

FAQ questions expand and collapse through JavaScript.

### FR-15 --- Notifications

Notification UI can open/close, show unread count and mark an item as
read in the frontend demonstration.

------------------------------------------------------------------------

## 8. Non-Functional Requirements

### Usability

-   Navigation should be understandable without instructions.
-   Important actions should have clear labels.
-   Error messages should appear close to the relevant field.
-   Buttons should provide visible hover/focus states.

### Accessibility

-   Use semantic HTML elements.
-   Use meaningful headings.
-   Use labels for form controls.
-   Use `alt` text for meaningful images.
-   Maintain readable contrast.
-   Keep keyboard navigation practical.
-   Do not depend only on color to communicate status.

### Responsiveness

Target layouts:

-   Desktop: 1200px and above
-   Tablet: approximately 768px--1199px
-   Mobile: below 768px

The exact breakpoints can be adjusted during implementation based on
layout testing.

### Maintainability

-   Keep page-specific CSS separated.
-   Keep shared CSS in common files.
-   Keep JavaScript modules focused.
-   Keep data in JSON rather than mixing large datasets into HTML.
-   Use meaningful class and variable names.

------------------------------------------------------------------------

## 9. Practical Mapping

  -----------------------------------------------------------------------
  Practical                           Synora Implementation
  ----------------------------------- -----------------------------------
  P1                                  Requirements, sitemap, wireframe,
                                      GitHub structure

  P2                                  14 semantic HTML5 pages

  P3                                  Responsive Grid/Flexbox layouts and
                                      design system

  P4                                  DOM interaction, FAQ, modal, menu,
                                      theme switcher, notifications

  P5                                  Registration validation and
                                      user-friendly errors

  P6                                  JSON + Fetch + search + filter +
                                      sort + pagination

  P7--P14                             Not implemented in this frontend
                                      milestone

  P15                                 Documentation and integration
                                      preparation
  -----------------------------------------------------------------------

------------------------------------------------------------------------

## 10. Success Criteria

The frontend milestone will be considered complete when:

-   All 14 pages open correctly.
-   Navigation works between pages.
-   Layout works on desktop and mobile.
-   Theme switching works and persists after reload.
-   Registration validation catches invalid input.
-   JSON files load through Fetch API.
-   Event data renders dynamically.
-   Search works.
-   Filter works.
-   Sort works.
-   Pagination works.
-   FAQ accordion works.
-   Modal/toast interactions work.
-   Timetable is available digitally.
-   No major browser console errors remain.
-   Documentation explains the project and its practical mapping.

------------------------------------------------------------------------

## 11. Future Scope

The architecture leaves room for later backend integration. Future work
could include:

-   PHP form processing
-   MySQL database
-   Secure registration
-   Login/logout sessions
-   Student CRUD
-   Event CRUD
-   PHP JSON API
-   Admin dashboard
-   Role-based access

These are future extensions and are not required for the current
HTML/CSS/JS submission.
