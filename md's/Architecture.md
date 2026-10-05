# SYNORA --- System Architecture

## 1. Architecture Summary

Synora uses a lightweight frontend architecture based on separation of
concerns.

``` text
┌──────────────────────────────────────────────┐
│                  SYNORA UI                   │
│              HTML5 + CSS3                   │
└──────────────────────┬───────────────────────┘
                       │
                       ▼
┌──────────────────────────────────────────────┐
│             JavaScript Layer                 │
│ DOM | Events | Validation | State | UI       │
└───────────────┬──────────────────────┬───────┘
                │                      │
                ▼                      ▼
        ┌───────────────┐      ┌───────────────┐
        │ LocalStorage  │      │   Fetch API   │
        │ UI preference │      │ JSON datasets │
        └───────────────┘      └───────┬───────┘
                                       │
                                       ▼
                              ┌─────────────────┐
                              │ data/*.json     │
                              │ events          │
                              │ students        │
                              │ faqs            │
                              │ notices         │
                              └─────────────────┘
```

There is no backend service in the current milestone.

------------------------------------------------------------------------

## 2. Architectural Principles

### Separation of concerns

-   HTML defines structure.
-   CSS defines presentation.
-   JavaScript defines behaviour.
-   JSON defines sample data.
-   LocalStorage stores non-sensitive UI preferences.

### Progressive enhancement

The HTML should remain meaningful even before JavaScript enhancements
are applied.

### Reusability

Shared components should be reused through common CSS and JavaScript
utilities.

### Simplicity

The architecture intentionally avoids unnecessary frameworks because the
practical work is focused on core HTML, CSS and JavaScript concepts.

------------------------------------------------------------------------

## 3. Page Architecture

### Home

Purpose:

-   Introduce Synora
-   Provide portal entry points
-   Show feature overview

### About

Purpose:

-   Project context
-   Portal purpose
-   Feature explanation

### Login

Purpose:

-   Frontend login interface
-   Demonstration navigation

No real authentication is implemented.

### Register

Purpose:

-   Demonstrate form controls
-   Client-side validation
-   Password strength

### Dashboard

Purpose:

-   Central student overview
-   Academic summary
-   Schedule
-   Events
-   Notices

### Courses

Purpose:

-   Display academic subjects
-   Show progress and attendance

### Schedule

Purpose:

-   Display official timetable information in a student-friendly
    interface

### Events

Purpose:

-   Demonstrate Fetch API
-   Search
-   Filter
-   Sort
-   Pagination

### Notices

Purpose:

-   Display campus communication

### Profile

Purpose:

-   Student information
-   Edit profile modal

### Resources

Purpose:

-   Organize useful academic links/material placeholders

### FAQ

Purpose:

-   Dynamic accordion
-   Optional JSON rendering

### Contact

Purpose:

-   Contact information
-   Frontend form

### Feedback

Purpose:

-   Form validation
-   User feedback UI

------------------------------------------------------------------------

## 4. Component Architecture

### Navigation

Desktop:

``` text
Logo | Sidebar | Main Content
```

Mobile:

``` text
Logo | Actions | Hamburger
```

The mobile menu is opened and closed with JavaScript.

### Cards

Card variants:

-   Statistic
-   Course
-   Event
-   Notice
-   Resource

### Buttons

Variants:

-   Primary
-   Secondary
-   Ghost
-   Danger
-   Icon

### Feedback

Components:

-   Inline error
-   Toast
-   Alert
-   Empty state
-   Loading state

### Overlay

-   Modal
-   Mobile drawer

------------------------------------------------------------------------

## 5. Data Flow --- Events

``` text
events.json
    │
    ▼
fetch()
    │
    ▼
response.json()
    │
    ▼
allEvents
    │
    ├── Search
    ├── Category Filter
    ├── Sort
    └── Pagination
    │
    ▼
renderEvents()
    │
    ▼
DOM
```

The source array should remain available so that changing a search or
filter can recalculate the visible results.

------------------------------------------------------------------------

## 6. Data Flow --- Registration

``` text
User Input
    │
    ▼
Form Submit
    │
    ▼
preventDefault()
    │
    ▼
Validation
    │
    ├── Invalid → Inline Error
    │
    └── Valid
          │
          ▼
    Success Message
```

No server request is made.

------------------------------------------------------------------------

## 7. Data Flow --- Theme

``` text
User clicks theme toggle
        │
        ▼
Change document theme
        │
        ▼
localStorage.setItem()
        │
        ▼
Reload page
        │
        ▼
Read saved preference
        │
        ▼
Restore theme
```

------------------------------------------------------------------------

## 8. Timetable Architecture

The timetable should be represented as structured data instead of
relying on the screenshot.

Recommended structure:

``` js
const timetable = {
    monday: [
        {
            start: "09:10",
            end: "10:10",
            code: "ITUE203",
            shortName: "WDF",
            type: "T",
            room: "222",
            faculty: "JV"
        }
    ]
};
```

For practical/group periods, use a `groups` field:

``` js
{
    start: "11:10",
    end: "12:10",
    type: "Practical",
    groups: [
        { group: "A", code: "ITUC201", subject: "FCN", room: "218", faculty: "MH" },
        { group: "B", code: "ITUC201", subject: "FCN", room: "218", faculty: "JP" },
        { group: "C", code: "ITUE203", subject: "WDF", room: "303", faculty: "VKM" }
    ]
}
```

This makes the data reusable for a daily schedule view.

------------------------------------------------------------------------

## 9. Responsive Architecture

### Desktop

``` text
Sidebar | Main
```

### Tablet

``` text
Collapsed sidebar
Main content
```

### Mobile

``` text
Top bar
Content
Bottom spacing
```

Cards switch from multi-column grid to one-column or two-column layouts
according to available width.

The timetable may use horizontal scrolling on small screens rather than
compressing text until it becomes unreadable.

------------------------------------------------------------------------

## 10. Accessibility Architecture

Use:

-   semantic landmarks
-   heading hierarchy
-   visible focus states
-   labels
-   `aria-expanded` for accordions
-   `aria-label` for icon-only buttons
-   keyboard-friendly modals
-   meaningful button text

Example:

``` html
<button
    class="faq-trigger"
    aria-expanded="false"
    aria-controls="faq-answer-1">
    How do I register for an event?
</button>
```

------------------------------------------------------------------------

## 11. Security Boundary

Because this is frontend-only:

-   Do not claim that frontend validation provides complete security.
-   Do not store real passwords.
-   Do not expose API secrets.
-   Do not create fake backend authentication.
-   Do not describe LocalStorage as secure credential storage.

Frontend validation is for user experience and practical demonstration.
Real security requires server-side validation and secure backend
handling, which belongs to later practicals.

------------------------------------------------------------------------

## 12. Future Backend Boundary

The frontend architecture can later be connected to:

``` text
Synora Frontend
      │
      ▼
PHP API
      │
      ▼
MySQL
```

Potential future endpoints:

``` text
POST /register
POST /login
GET  /students
GET  /events
POST /events
PUT  /events/:id
DELETE /events/:id
```

These are architectural future references only and are not implemented
in the current frontend milestone.

------------------------------------------------------------------------

## 13. Architecture Quality Checklist

-   [ ] Pages separated
-   [ ] CSS separated
-   [ ] JS responsibilities separated
-   [ ] JSON separated from UI
-   [ ] No unnecessary inline CSS
-   [ ] No unnecessary inline JavaScript
-   [ ] Shared components centralized
-   [ ] Fetch errors handled
-   [ ] Loading state handled
-   [ ] Empty state handled
-   [ ] Responsive layouts tested
-   [ ] Timetable represented structurally
-   [ ] LocalStorage limited to non-sensitive preferences
