# UI Design Documentation — Mockup and Prototype

**Document ID:** ILU-DES-001
**Version:** 1.0
**Related documents:** ILU-SRS-001 (`docs/SRS.md`), `docs/USER_GUIDE.md`

---

## 1. Purpose and scope

This document describes the visual design of **Ilusalong** and its clickable prototype: the design system, the screen inventory, the state model, the layout specification and the prototype's interaction map. It documents what exists in the Figma file and how that file relates to the implemented interface.

The design covers two areas:

1. **Public site** — catalogue, service page, booking, account screens.
2. **Administrator panel** — services, client bookings, review statistics.

---

## 2. Design approach

### 2.1 Source of truth
The implemented PHP interface is the source of truth. The Figma mockup was produced from the **rendered HTML** of the running application rather than drawn from scratch, so that the design matches the delivered product one-to-one. This is a deliberate choice for an existing product: the design document must describe the interface as built, not an alternative concept.

### 2.2 Principles
| Principle | Implementation |
|-----------|----------------|
| Calm, salon-like palette | Soft rose background, white cards, dusty-pink accent |
| One primary action per screen | A single filled accent button; all other actions are text links |
| Readability first | Text-dominant layout, generous line spacing, 1200 px content column |
| Consistency | One stylesheet shared by the public site and the administrator panel, one set of tokens |
| Reversibility | Light/dark theme switch without page reload |

### 2.3 Target users and their screens

| Actor | Primary screens |
|-------|-----------------|
| Guest | Catalogue, service page, login, registration |
| Client | Catalogue, service page (with review form), booking, my bookings |
| Administrator | Services list, add/edit service, client bookings, review statistics |

---

## 3. Design system

### 3.1 Colour tokens

Tokens are CSS custom properties defined in `public/css/style.css`; the dark theme overrides the same names under `[data-theme="dark"]`.

| Token | Light | Dark | Usage |
|-------|-------|------|-------|
| `--bg-main` | `#fff0f5` | `#121212` | Page background |
| `--bg-card` | `#ffffff` | `#1e1e1e` | Cards, header, footer, table background |
| `--primary` | `#d87093` | `#ff69b4` | Accent, buttons, links, active pill |
| `--primary-hover` | `#c71585` | `#ff1493` | Hover state of accent elements |
| `--text-main` | `#4a4a4a` | `#e0e0e0` | Body text |
| `--text-secondary` | `#888888` | `#aaaaaa` | Meta text, footer, empty states |
| `--border-color` | `#ffb6c1` | `#333333` | Borders, table rules, input outlines |

Destructive action colour: `#ff4d6d` (delete links, fixed in both themes).

### 3.2 Typography

| Role | Specification |
|------|---------------|
| Font family | `'Segoe UI', Tahoma, Geneva, Verdana, sans-serif` (system stack, no web font request) |
| Logo | 24 px, bold, accent colour |
| Section heading (h2) | Accent colour, ~22–24 px |
| Body | 14–16 px, `--text-main` |
| Meta (price, duration, count) | 13–14 px, `--text-secondary` |
| Form labels | 600 weight, `--text-main` |

### 3.3 Spacing and layout

| Item | Value |
|------|-------|
| Content column | `max-width: 1200px`, horizontally centred, `padding: 20px` |
| Header | White bar, `padding: 15px 0`, bottom border 2 px, subtle shadow `0 2px 5px rgba(219,112,147,.1)` |
| Header inner | flex, `justify-content: space-between`, `gap: 15px`, wraps on narrow screens |
| Nav right group | flex, `gap: 20px` |
| Card grid | CSS grid, `repeat(auto-fill, minmax(300px, 1fr))`, `gap: 20px` |
| Form container | `max-width: 800px`, `padding: 30px`, centred, radius 8 px |
| Footer | White bar, top border 1 px, centred text, pushed to bottom (`margin-top: auto`) |

### 3.4 Shape and elevation

| Element | Radius | Shadow |
|---------|--------|--------|
| Category pill | 20 px (fully rounded) | none |
| Button | 6 px | none |
| Card / form container | 8 px | `0 4px 6px rgba(0,0,0,.02)` |
| Table container | 8 px | none, 1 px border |
| Input / select / textarea | 6 px | none, 1 px border, focus → accent border |

### 3.5 Components

| Component | Description | States |
|-----------|-------------|--------|
| Header | Logo left; right group: theme toggle, language switcher, user menu | default |
| Logo | Text link to home | hover: none (accent colour stays) |
| Theme toggle | Circular 38 px icon button (moon / sun) | default, hover (accent fill, white icon) |
| Language switcher | `ET | RU | EN` text links | default, hover (accent text, soft background) |
| Category pill | Rounded filter button | default, hover, **active** (accent fill, white text) |
| Service card | White card: image, title, price, duration, master, rating, "Details"/"Book" button | default, hover |
| Rating block | Average score, star row, number of reviews | with ratings, empty state |
| Review item | Author, date, star row, review text | — |
| Review form | Star selector (1–5) + textarea + submit | guest (login prompt), client |
| Button (`.btn`) | Filled accent, 10×20 px padding, 600 weight | default, hover |
| Text link action | Inline accent link ("Edit", "Delete", "Back") | hover: underline |
| Table (admin) | Header row on `--border-color`, 12 px cell padding, row separators | empty state |
| Back link | `←` + accent text link | hover: underline |
| Footer | © 2026 Ilusalong | — |

---

## 4. Screen inventory

### 4.1 Public site

| # | Screen | Route | Key elements |
|---|--------|-------|--------------|
| P1 | Catalogue (home) | `index.php` | Header, category pills, service cards with master and rating, footer |
| P2 | Catalogue — filtered category | `index.php?action=index&category=N` | Same as P1 with one pill active and a reduced card set |
| P3 | Service detail | `index.php?action=show&id=N` | Image, title, price, duration, description, master card (name, rating, service count), rating summary, review list, review form or login prompt |
| P4 | Booking form | `index.php?action=book&id=N` | Service summary (title, price, duration), single date-time field, confirm button, back link |
| P5 | My bookings | `index.php?action=my_bookings` | Table/list of the client's bookings |
| P6 | Login | `index.php?action=login` | Login, password, submit, link to registration |
| P7 | Registration | `index.php?action=register` | Username, e-mail, phone, login, password, submit |

### 4.2 Administrator panel

| # | Screen | Route | Key elements |
|---|--------|-------|--------------|
| A1 | Services list | `admin/index.php?action=services` | Heading, "Add new service" button, 8-column table (image, ID, category, title, master, price, duration, actions) |
| A2 | Add service | `admin/index.php?action=add` | Category select, title, master (with suggestions), description, price, duration, image upload, submit, back link |
| A3 | Edit service | `admin/index.php?action=edit&id=N` | Same fields pre-filled, submit ("Save changes"), back link |
| A4 | Client bookings | `admin/index.php?action=clients` | Table of bookings with client, service, date/time, phone, delete |
| A5 | Review statistics | `admin/index.php?action=ratings` | KPI cards, per-service table, **master rating table**, score distribution, recent reviews with delete |

---

## 5. State model

The interface has no separate modal layer; states are expressed on the page itself.

| State | Trigger | Visual expression |
|-------|---------|-------------------|
| Category active | Category selected | Pill filled with `--primary`, white text |
| Hover (pill, button) | Pointer over control | Fill changes to `--primary-hover` |
| Hover (link) | Pointer over text action | Accent colour / underline |
| Empty catalogue | No services in category | Empty-state row/message in secondary text |
| No reviews yet | Service without ratings | "No ratings" text instead of stars and average |
| Guest on service page | Not authenticated | Review form replaced by a login prompt link |
| Dark theme | Toggle pressed | Token overrides applied on `<html data-theme="dark">`, persisted in `localStorage` |
| Form focus | Field focused | Border switches to accent colour, no default outline |
| Destructive confirmation | "Delete" pressed | Native `confirm()` dialog |

---

## 6. Mockup (Figma)

### 6.1 How it was produced
1. The application was opened in Chrome (`http://localhost:8080/salon`) in its normal state.
2. Rendered pages were captured with the **html.to.design** Figma plugin (paste-HTML / page-capture mode), which converts DOM + CSS into editable Figma layers.
3. Each capture becomes one top-level frame (`Html → Body`) containing nested frames, text layers and image fills.
4. Administrator screens were captured after logging in with the administrator account, so the real data and table layouts are present.

### 6.2 Frame organisation

| Frame | Content |
|-------|---------|
| `01 Главная` | P1 catalogue |
| `02 Админ-услуги` | A1 services list |
| `03 Редактирование` | A3 edit form |
| `04 Добавление` | A2 add form |
| `05 Клиенты` | A4 bookings |
| `06 Отзывы` | A5 statistics |

Naming convention: `NN <Screen name>` — a numeric prefix guarantees a stable order in the layers panel and in the prototype destination list.

### 6.3 Known limitations of the imported mockup
- The layout is largely block-based (`display: flex` is used in 5 rules only), so imported frames contain a mix of auto-layout and absolutely positioned layers; grouping is required before the frames behave as components.
- The plugin does not create Figma **components**, **variants**, **text styles** or **colour variables**. The design system in section 3 is documented here as the authoritative token list; re-creating it as Figma styles is a manual step.
- Fonts resolve to the system stack; on a machine without `Segoe UI` the text metrics shift.
- Server-side states (empty catalogue, no reviews, guest prompt) are not present in the mockup because they were not rendered when capturing.

### 6.4 Recommended refinement
To turn the captured frames into a maintainable design file:
1. Create Figma variables for the 7 colour tokens and two modes (light/dark).
2. Create text styles for logo, h2, body, meta, label.
3. Convert the category pill, the service card and the primary button into components with default/hover states.
4. Reuse those components across frames instead of the captured copies.

---

## 7. Prototype

### 7.1 Prototype scope
The prototype is a clickable walkthrough of the administrator scenario, extended by the public entry point:

`Catalogue → Admin panel → Services list → Edit service → Save → Services list`

### 7.2 Interaction map

| From | Element | Trigger | To |
|------|---------|---------|-----|
| P1 Catalogue | `Admin paneel` link | On click | A1 Services list |
| A1 Services list | Header `Teenused` | On click | A1 |
| A1 Services list | Header `Kliendiandmed` | On click | A4 Client bookings |
| A1 Services list | Header `Hinnangud` | On click | A5 Review statistics |
| A1 Services list | Header `Veebilehele` | On click | P1 Catalogue |
| A1 Services list | `Lisa uus teenus` | On click | A2 Add service |
| A1 Services list | `Muuda` | On click | A3 Edit service |
| A1 Services list | `Kustuta` | On click | A1 (simulated) |
| A2 Add service | `Tagasi nimekirja` | On click | A1 |
| A3 Edit service | `Tagasi nimekirja` | On click | A1 |
| A3 Edit service | `Salvesta muudatused` | On click | A1 (simulated save) |

### 7.3 Prototype configuration
- **Start frame:** P1 catalogue (`01 Главная`).
- **Transition:** Navigate to, animation `Dissolve`, 250 ms.
- **Scrolling:** every page frame set to vertical scrolling; header is part of the frame content.
- **Scale:** frames are 1440 px wide (desktop reference); tablet behaviour is documented, not prototyped.

### 7.4 Category switching
A prototype cannot filter a list the way the code does. Category switching is therefore represented by **separate frames per state**: P1 plus one copy per category, in which the corresponding pill is styled active and only that category's cards remain. Without these copies the filter cannot be demonstrated in the prototype.

### 7.5 Automation
The interaction map in 7.2 can be applied programmatically: the plugin in `figma-prototype/` (`manifest.json`, `code.js`) detects screens by their on-screen text and sets `On click → Navigate to` reactions, vertical scrolling and the flow starting point. Its configuration block maps button captions to target screens and can be edited without touching the plugin logic.

### 7.6 Not covered by the prototype
- Real data filtering, sorting and pagination.
- Form validation and error messages.
- Authentication flow (the prototype assumes an already authenticated administrator).
- Dark theme rendering of every screen.
- Responsive breakpoints.

---

## 8. Traceability to requirements

| Screen | Requirements (ILU-SRS-001) |
|--------|----------------------------|
| P1, P2 | FR-01…FR-05 |
| P3 | FR-06…FR-10, FR-23…FR-28 |
| P4, P5 | FR-18…FR-22 |
| P6, P7 | FR-11…FR-17 |
| Header, language switcher, theme toggle | FR-14, FR-15, FR-29…FR-31 |
| A1, A2, A3 | FR-32…FR-36 |
| A4 | FR-37, FR-38 |
| A5 | FR-39…FR-43 |
| All administrator frames | FR-44 |

---

## 9. Change log

| Version | Date | Change |
|---------|------|--------|
| 1.0 | 2026-11-16 | Design system, screen inventory, mockup description, prototype interaction map. |
