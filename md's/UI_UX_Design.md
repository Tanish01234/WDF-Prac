# SYNORA --- UI/UX Design Specification

## 1. Design Direction

Synora should feel like a modern academic product rather than a
traditional college ERP.

### Design keywords

-   Premium
-   Clean
-   Academic
-   Modern
-   Calm
-   Structured
-   Responsive
-   Accessible

The design should communicate information quickly without filling every
area with decoration.

------------------------------------------------------------------------

## 2. Brand

**Name:** Synora

**Suggested tagline:**

> Your Student Life, Connected.

The logo can be a simple `S` mark or geometric symbol paired with the
word `SYNORA`.

Avoid using an over-complicated logo because the portal itself is the
main visual identity.

------------------------------------------------------------------------

## 3. Color System

### Dark Theme

``` text
Background:       #090B10
Surface:          #10141C
Elevated Surface: #151A24
Primary Text:     #F5F7FA
Secondary Text:   #8B93A5
Border:           #232936
Accent:           #7C5CFF
Accent Soft:      #9A86FF
Success:          #35C98A
Warning:          #F2B84B
Danger:           #F26B6B
Info:             #4EA1FF
```

### Light Theme

``` text
Background:       #F6F7FB
Surface:          #FFFFFF
Elevated Surface: #FFFFFF
Primary Text:     #111318
Secondary Text:   #687083
Border:           #E5E7EB
Accent:           #6D4AFF
Success:          #168A5B
Warning:          #A66A00
Danger:           #C23B3B
Info:             #1D67B1
```

Colors are examples for the implementation and may be tuned after
browser testing.

------------------------------------------------------------------------

## 4. Typography

Use a clean system/sans-serif stack.

Recommended:

``` css
font-family:
    Inter,
    ui-sans-serif,
    system-ui,
    -apple-system,
    BlinkMacSystemFont,
    "Segoe UI",
    sans-serif;
```

### Type scale

``` text
Display: 48–64px
H1:      36–44px
H2:      28–32px
H3:      20–24px
Body:    15–16px
Small:   13–14px
Caption: 12px
```

Avoid excessive font sizes on mobile.

------------------------------------------------------------------------

## 5. Spacing System

Use a consistent spacing scale:

``` text
4px
8px
12px
16px
20px
24px
32px
40px
48px
64px
```

The main content should feel spacious.

------------------------------------------------------------------------

## 6. Border Radius

Recommended:

``` text
Small control: 10px
Card:          16px
Large panel:   20px
Modal:         20px
Pill:          999px
```

Do not make every element extremely rounded.

------------------------------------------------------------------------

## 7. Shadows

Keep shadows subtle.

Dark mode should rely more on:

-   borders
-   surface contrast
-   soft elevation

rather than huge shadows.

------------------------------------------------------------------------

## 8. Dashboard UI

### Layout

``` text
┌──────────────┬────────────────────────────────────┐
│              │ Top Bar                            │
│              ├────────────────────────────────────┤
│   Sidebar    │ Greeting                           │
│              │                                    │
│              │ Statistics                         │
│              │                                    │
│              │ Schedule + Events                  │
│              │                                    │
│              │ Notices                            │
└──────────────┴────────────────────────────────────┘
```

### Dashboard sections

1.  Greeting
2.  Quick statistics
3.  Today's schedule
4.  Upcoming events
5.  Recent notices
6.  Academic progress
7.  Quick actions

------------------------------------------------------------------------

## 9. Sidebar UX

Navigation groups:

``` text
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

Active page:

-   Slight accent background
-   Accent indicator
-   Higher text contrast

Do not use color alone to indicate active state.

------------------------------------------------------------------------

## 10. Top Bar

Elements:

``` text
Search
Notifications
Theme
Profile
```

Search may later support a keyboard shortcut visual such as:

``` text
⌘ K
```

This is a visual interaction idea, not a requirement for a global
command palette.

------------------------------------------------------------------------

## 11. Event Cards

Event cards should show:

-   Category
-   Title
-   Short description
-   Date
-   Time
-   Location
-   Status
-   Action

Example:

``` text
HACKATHON

AI Innovation Challenge

04 OCT 2026
Innovation Lab

OPEN

View Details →
```

------------------------------------------------------------------------

## 12. Registration UX

The registration page should prioritize clarity.

### Field layout

Desktop:

``` text
First Name     Last Name
Email          Mobile
Course         Year
Gender
Password
Confirm Password
Terms
Submit
```

On mobile all fields become one column.

### Validation

Valid:

``` text
✓ Email looks good
```

Invalid:

``` text
Please enter a valid email address.
```

Password:

``` text
Weak
Fair
Good
Strong
```

The password meter is a UI aid and does not imply secure password
storage.

------------------------------------------------------------------------

## 13. Timetable UX

The supplied official timetable is dense, so the digital version should
not reproduce the same visual density.

### Desktop

Use a structured table:

``` text
Day | Time | Course | Type | Room | Faculty
```

Add filters:

``` text
[ All Days ]
[ All Subjects ]
[ Today ]
```

### Daily view

A student-friendly timeline can also be provided:

``` text
09:10
│
├── WDF
│   ITUE203
│   Room 222
│
10:10
│
├── FCN
│   ITUC201
│   Room 222
│
11:10
│
└── Practical
```

### Breaks

Break periods should be visually distinct:

``` text
LUNCH BREAK
12:10 PM – 01:10 PM

SHORT BREAK
02:10 PM – 02:20 PM
```

On mobile, horizontal scrolling is preferable to shrinking the table too
far.

------------------------------------------------------------------------

## 14. FAQ UX

Use accordion behaviour.

Closed:

``` text
How do I register for an event?       +
```

Open:

``` text
How do I register for an event?       −

Open the Events page and choose the
event you want to register for.
```

Use `aria-expanded`.

------------------------------------------------------------------------

## 15. Notifications UX

Unread count:

``` text
🔔 3
```

Panel:

``` text
Notifications

● WDF practical reminder
  10 minutes ago

● New campus event
  2 hours ago

● Profile updated
  Yesterday
```

Use toast notifications for immediate frontend actions:

``` text
✓ Profile updated successfully
```

------------------------------------------------------------------------

## 16. Responsive UX

### Desktop

-   Persistent sidebar
-   Multi-column cards
-   Full timetable

### Tablet

-   Reduced sidebar width
-   Two-column content
-   Compact spacing

### Mobile

-   Hamburger navigation
-   One-column cards
-   Horizontal timetable scrolling
-   Full-width forms
-   Compact top bar

------------------------------------------------------------------------

## 17. Motion

Use motion only where it supports understanding.

Recommended:

``` text
150–250ms
ease
```

Use:

-   button hover
-   card hover
-   menu transition
-   modal transition
-   accordion transition
-   progress animation

Avoid:

-   continuous decorative animation
-   excessive bouncing
-   large page transitions
-   animation that delays content

Respect reduced-motion preferences where practical.

------------------------------------------------------------------------

## 18. Empty and Error States

### Empty

``` text
No events found

Try changing your search or filter.
```

### Loading

``` text
Loading events...
```

### Error

``` text
We couldn't load the events.

[ Try Again ]
```

These states make the application feel complete without requiring a
backend.

------------------------------------------------------------------------

## 19. Accessibility

Minimum design checklist:

-   semantic headings
-   labels
-   keyboard focus
-   visible focus ring
-   sufficient contrast
-   meaningful alt text
-   button labels
-   accessible accordion
-   accessible modal close
-   no color-only status indicators

------------------------------------------------------------------------

## 20. Visual Quality Checklist

Before submission:

-   [ ] No inconsistent border radius
-   [ ] No random colors
-   [ ] Same button language everywhere
-   [ ] Same spacing rhythm
-   [ ] Same typography
-   [ ] Same card treatment
-   [ ] Mobile checked
-   [ ] Tablet checked
-   [ ] Dark theme checked
-   [ ] Light theme checked
-   [ ] Empty states checked
-   [ ] Error states checked
-   [ ] Focus states checked
