# Software Requirements Specification — Ilusalong

**Standard:** ISO/IEC/IEEE 29148:2018 (Requirements engineering)
**Document ID:** ILU-SRS-001
**Version:** 1.0
**Status:** Baseline

---

## 1. Introduction

### 1.1 Purpose
This document specifies the requirements for **Ilusalong**, a web application for a beauty salon. It is intended for the development team, the customer (salon owner) and the assessor, and serves as the baseline for verification of the delivered system.

### 1.2 Scope
The system provides:

- a public catalogue of salon services grouped by category;
- service detail pages with a rating and client reviews;
- assignment of a master (stylist) to each service and a derived master rating;
- client registration, login and online appointment booking;
- an administrator panel for service management, booking management and review statistics.

Out of scope: online payments, e-mail/SMS notifications, staff scheduling with time-slot conflict resolution, and a separate master account role.

### 1.3 Intended audience and reading suggestions
- **Customer / product owner** — sections 1, 2, 3.1 (functional requirements) and 5.
- **Developer** — all sections, especially 3.3 (data) and 3.4 (interfaces).
- **Assessor / QA** — sections 3.1, 3.2 and 4 (verification).

### 1.4 Definitions and acronyms

| Term | Meaning |
|------|---------|
| Guest | Visitor who is not authenticated |
| Client | Authenticated user with role `user` |
| Administrator | Authenticated user with role `admin` |
| Service | A salon procedure offered for booking (table `services`) |
| Master | Person performing a service; stored as `services.master_name` |
| Booking | A client reservation of a service (table `enrollments`) |
| Rating | A 1–5 score with optional review text (table `ratings`) |
| SRS | Software Requirements Specification |

### 1.5 References
- ISO/IEC/IEEE 29148:2018 — Requirements engineering.
- ISO/IEC 25010:2011 — System and software quality models.
- Project source code and `docs/USER_GUIDE.md` (user and administrator guide).

---

## 2. Overall description

### 2.1 Product perspective
Ilusalong is a self-contained server-rendered web application implemented in plain PHP following an MVC-like separation (route → controller → model → view). It runs on an Apache/PHP/MySQL stack (XAMPP) and has no external service dependencies and no client-side framework.

### 2.2 Product functions (summary)
1. Category-filtered service catalogue.
2. Service detail view with rating summary, reviews and master information.
3. Client accounts: registration, login, logout, session persistence.
4. Appointment booking and personal booking list.
5. Review submission and update by authenticated clients.
6. Administrator service management (create, read, update, delete) including master assignment and image upload.
7. Administrator booking and client data management.
8. Administrator review statistics, including per-master averages and review moderation.
9. Interface localisation (Estonian, Russian, English) and light/dark theme.

### 2.3 User classes and characteristics

| Actor | Description | Frequency of use | Key needs |
|-------|-------------|------------------|-----------|
| Guest | Unregistered visitor | High | Browse services, prices and reviews without registration |
| Client | Registered salon customer (`user`) | Medium | Book a service, see own bookings, rate a service |
| Administrator | Salon staff (`admin`) | Daily | Maintain services and masters, follow bookings and ratings |

### 2.4 Operating environment
- Server: Apache 2.4 + PHP 8.x + MariaDB 10.4 (XAMPP).
- Client: desktop browser (Chrome, Firefox, Edge); layout adapts down to tablet width.
- Local deployment: `http://localhost:8080/salon`.

### 2.5 Design and implementation constraints
- **CON-01** No PHP framework and no build step; plain PHP templates.
- **CON-02** Database access only through PDO prepared statements.
- **CON-03** The database schema is fixed by the existing `salon_db`; migrations must be idempotent so they can be re-run safely.
- **CON-04** Masters are not a separate entity: a master is a name stored on the service (`services.master_name`). A master rating is therefore derived, not stored.
- **CON-05** All user-visible strings must exist in all three language files (`lang/et.php`, `lang/ru.php`, `lang/en.php`).
- **CON-06** Deployment target is a local XAMPP installation; no hosting or CI is assumed.

### 2.6 Assumptions and dependencies
- **ASM-01** Categories are pre-seeded and change rarely; the administrator does not manage categories.
- **ASM-02** The same master name string identifies the same person across services.
- **ASM-03** Service images are uploaded to the server filesystem.
- **DEP-01** Data storage is MariaDB/MySQL `salon_db`.
- **DEP-02** Sessions are handled by PHP's native session mechanism.

---

## 3. Specific requirements

Requirement priority follows MoSCoW: **M** = must, **S** = should, **C** = could.
Verification method: **T** = test, **I** = inspection, **D** = demonstration.

### 3.1 Functional requirements

#### 3.1.1 Catalogue and service information

| ID | Requirement | Priority | Verification |
|----|-------------|----------|--------------|
| FR-01 | The system shall display all active services of the salon on the home page. | M | T |
| FR-02 | The system shall display the list of service categories as selectable filter controls. | M | T |
| FR-03 | When a category is selected, the system shall display only services belonging to that category. | M | T |
| FR-04 | Each service card shall show title, price, duration, master name (if assigned) and the average service rating with the number of ratings. | M | T |
| FR-05 | Selecting a service card shall open the service detail page for that service. | M | D |
| FR-06 | The service detail page shall show title, full description, price, duration, category and image. | M | T |
| FR-07 | The service detail page shall show the master name, the master's average rating and the number of services assigned to that master, when a master is assigned. | M | T |
| FR-08 | The service detail page shall show the average rating, the number of reviews and the distribution of scores 1–5. | S | T |
| FR-09 | The service detail page shall list all reviews for the service with score, text, author and date, newest first. | M | T |
| FR-10 | If a service has no reviews, the system shall display an explicit empty state instead of a rating value. | S | T |

#### 3.1.2 Accounts and session

| ID | Requirement | Priority | Verification |
|----|-------------|----------|--------------|
| FR-11 | A guest shall be able to register with username, e-mail, phone, login and password. | M | T |
| FR-12 | The system shall reject registration when the e-mail or login already exists and shall inform the user without disclosing other accounts' data. | M | T |
| FR-13 | Passwords shall be stored as a bcrypt hash; the plain password shall not be written to the database. | M | I, T |
| FR-14 | A registered user shall be able to log in with login and password and shall receive an authenticated session. | M | T |
| FR-15 | The system shall show "Log in" / "Register" links to guests and "My bookings" / "Log out" links to authenticated users. | S | D |
| FR-16 | An authenticated user shall be able to log out; the session shall then be terminated. | M | T |
| FR-17 | New registrations shall receive the role `user`; the `admin` role shall only be assigned out of band (database). | M | I |

#### 3.1.3 Booking

| ID | Requirement | Priority | Verification |
|----|-------------|----------|--------------|
| FR-18 | An authenticated client shall be able to create a booking for a selected service by choosing the date and time of the appointment in a single date-time field. | M | T |
| FR-19 | The system shall store the booking with the client identifier (taken from the session, not from the form), the service identifier, the chosen date/time and the initial status `confirmed`. | M | T |
| FR-20 | The system shall reject a booking request without a date/time value. | M | T |
| FR-21 | An authenticated client shall be able to view a list of their own bookings only. | M | T |
| FR-22 | A guest attempting to book shall be redirected to the login page. | S | T |

#### 3.1.4 Ratings and reviews

| ID | Requirement | Priority | Verification |
|----|-------------|----------|--------------|
| FR-23 | An authenticated client shall be able to submit a score from 1 to 5 and an optional review text for a service. | M | T |
| FR-24 | The system shall allow at most one review per client per service; a repeated submission shall update the existing score and text instead of creating a duplicate. | M | T |
| FR-25 | The system shall reject a score outside the range 1–5. | M | T |
| FR-26 | Guests shall be able to read reviews but shall not be able to submit one. | M | T |
| FR-27 | The average service rating shall be computed from the stored reviews and shall be displayed with one decimal place. | M | T |
| FR-28 | The average master rating shall be computed as the mean of the ratings of all services assigned to that master. | M | T |

#### 3.1.5 Localisation and theme

| ID | Requirement | Priority | Verification |
|----|-------------|----------|--------------|
| FR-29 | The user shall be able to switch the interface language between Estonian, Russian and English. | M | D |
| FR-30 | The selected language shall persist for the session and shall not reset the current page or filter. | S | T |
| FR-31 | The user shall be able to switch between light and dark theme; the choice shall persist in the browser. | C | D |

#### 3.1.6 Administrator panel

| ID | Requirement | Priority | Verification |
|----|-------------|----------|--------------|
| FR-32 | The administrator panel shall be accessible only to users with role `admin`; any other request shall be redirected to the public site. | M | T |
| FR-33 | The administrator shall see a table of all services with image, ID, category, title, master, price and duration. | M | T |
| FR-34 | The administrator shall be able to add a service with category, title, description, master name, price, duration and image. | M | T |
| FR-35 | The administrator shall be able to edit every service field; clearing the master field shall remove the master assignment. | M | T |
| FR-36 | The administrator shall be able to delete a service; its reviews shall be deleted together with it. | M | T |
| FR-37 | The administrator shall be able to view all client bookings with client, service, date, time and phone. | M | T |
| FR-38 | The administrator shall be able to delete a booking. | M | T |
| FR-39 | The administrator shall see overall review statistics: total reviews, average score, number of rated services. | S | T |
| FR-40 | The administrator shall see per-service statistics: title, average score, number of reviews. | S | T |
| FR-41 | The administrator shall see per-master statistics: master, average score, number of reviews, number of assigned services. | S | T |
| FR-42 | The administrator shall see the score distribution 1–5 and the most recent reviews. | S | T |
| FR-43 | The administrator shall be able to delete an individual review. | M | T |
| FR-44 | The administrator panel shall provide navigation between services, client data, review statistics and the public site. | S | D |

### 3.2 Non-functional requirements

| ID | Requirement | Priority | Verification |
|----|-------------|----------|--------------|
| NFR-01 | The home page, service page and administrator list shall render in under 1 s on the reference local machine with 10 services and 50 reviews. | S | T |
| NFR-02 | The layout shall remain usable from 360 px to 1920 px viewport width without horizontal scrolling. | S | D |
| NFR-03 | All SQL access shall use parameter binding; no user input shall be concatenated into SQL. | M | I |
| NFR-04 | All output of user-supplied data shall be HTML-escaped. | M | I |
| NFR-05 | The visual language shall be consistent across public and administrator pages (shared stylesheet, shared colour tokens). | S | I |
| NFR-06 | Light and dark themes shall satisfy a contrast ratio of at least 4.5:1 for body text. | S | I |
| NFR-07 | Data integrity shall be enforced by foreign keys between ratings, bookings, services and users. | M | I |
| NFR-08 | Database migrations shall be idempotent: running them repeatedly on an already migrated database shall neither fail nor duplicate data. | M | T |
| NFR-09 | The code shall be organised as route / controller / model / view with one responsibility per file. | S | I |
| NFR-10 | Every user-visible string shall be resolved from the language files, with a fallback text when a key is missing. | S | I |
| NFR-11 | The application shall run on PHP 8.x with MariaDB 10.4 without additional extensions beyond PDO MySQL. | M | T |
| NFR-12 | Session identifiers shall be regenerated on login and cleared on logout. | S | I |

### 3.3 Data requirements

Database: `salon_db`, charset `utf8`.

**categories** — service categories.

| Column | Type | Constraints |
|--------|------|-------------|
| id | int | PK |
| name | varchar(100) | not null |

**services** — offered procedures.

| Column | Type | Constraints |
|--------|------|-------------|
| id | int | PK |
| category_id | int | FK → categories.id |
| title | varchar(255) | not null |
| master_name | varchar(150) | nullable — assigned master |
| description | text | nullable |
| price | decimal(10,2) | not null |
| duration | int | nullable, minutes |
| schedule_days | varchar(100) | nullable |
| image | varchar(255) | nullable, file name |
| is_active | tinyint(1) | not null |

**users** — clients and administrators.

| Column | Type | Constraints |
|--------|------|-------------|
| id | int | PK |
| username | varchar(100) | not null, display name |
| email | varchar(150) | not null, unique |
| phone | varchar(50) | nullable |
| login | varchar(50) | not null, unique |
| password | varchar(255) | not null, bcrypt hash |
| status | enum('user','admin') | not null |
| registration_date | date | not null |

**enrollments** — bookings.

| Column | Type | Constraints |
|--------|------|-------------|
| id | int | PK |
| user_id | int | FK → users.id |
| course_id | int | FK → services.id (legacy name for service) |
| status | varchar(50) | not null |
| enrollment_date | timestamp | not null |

**ratings** — reviews.

| Column | Type | Constraints |
|--------|------|-------------|
| id | int | PK |
| user_id | int | FK → users.id |
| service_id | int | FK → services.id |
| rating | tinyint | not null, 1–5 |
| review | text | nullable |
| created_at | timestamp | not null |

Business rule: at most one row per (`user_id`, `service_id`); enforced by the application and by a unique index in `install_ratings.sql`.

### 3.4 External interface requirements

| ID | Interface | Description |
|----|-----------|-------------|
| IF-01 | Web UI | Server-rendered HTML5 pages; single shared stylesheet `public/css/style.css`; small inline scripts for theme switching and delete confirmation. |
| IF-02 | Database | MySQL protocol via PDO, DSN `mysql:host=localhost;dbname=salon_db;charset=utf8`. |
| IF-03 | Filesystem | Service images uploaded through the administrator form and stored on the server. |
| IF-04 | HTTP routes | Public: `index.php?action=index|show|book|my_bookings|submit_rating|login|register|logout`. Admin: `admin/index.php?action=services|add|edit|delete|clients|delete_enrollment|ratings|delete_rating`. |

### 3.5 Known deviations and technical debt

| ID | Item | Impact |
|----|------|--------|
| TD-01 | `users.pass` is a legacy plaintext column kept for compatibility. | Security risk; must be removed before production use. |
| TD-02 | `enrollments.course_id` refers to a service (legacy naming from the original template). | Readability only. |
| TD-03 | Master is a denormalised string on `services`; renaming a master requires updating every row. | Data maintenance. |
| TD-04 | No pagination on service, booking or review lists. | Performance limit at large data volumes. |
| TD-05 | No CSRF token on state-changing forms. | Security risk for public deployment. |

---

## 4. Verification

| Requirement group | Method | Acceptance criterion |
|-------------------|--------|----------------------|
| FR-01…FR-10 | Demonstration + test | All catalogue and service data displayed match the database content; filtering returns only the selected category. |
| FR-11…FR-17 | Test | Registration creates a row with `status='user'` and a hash in `password`; login establishes a session; logout clears it. |
| FR-18…FR-22 | Test | A booking created by a client is stored with the correct `user_id` and `service_id` and appears only in that client's list. |
| FR-23…FR-28 | Test | Submitting twice with the same user and service leaves exactly one row with the latest values; average values match a manual calculation. |
| FR-29…FR-31 | Demonstration | Language and theme switches work on every page and survive navigation. |
| FR-32…FR-44 | Test | A `user`-role request to `admin/index.php` is redirected; CRUD operations and statistics reflect the database. |
| NFR-01…NFR-12 | Inspection + targeted test | Measured render time, escaped output, bound parameters, re-runnable migrations. |

---

## 5. Traceability summary

| Use case | Requirements |
|----------|--------------|
| Browse catalogue and filter by category | FR-01, FR-02, FR-03, FR-04, FR-05 |
| Inspect service, master and reviews | FR-06…FR-10, FR-26 |
| Register and log in | FR-11…FR-17 |
| Book a service and review own bookings | FR-18…FR-22 |
| Rate a service | FR-23…FR-28 |
| Maintain services and masters | FR-32…FR-36 |
| Follow bookings | FR-37, FR-38 |
| Analyse and moderate reviews | FR-39…FR-43 |
| Localisation and theme | FR-29…FR-31 |

---

## 6. Change log

| Version | Date | Change |
|---------|------|--------|
| 1.0 | 2026-11-16 | Initial baseline: catalogue, ratings, masters, booking, administrator panel. |
