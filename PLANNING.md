# PLANNING.md

## Project Overview
**Name:** Fundoo Fullstack Platform
**Description:** A bilingual (German/FR) fullstack fundraising platform. Laravel 12 monolith serves both API endpoints for a Vue/React SPA frontend and renders server-side views where needed.

## Scope & Goals
- **Frontend:** SPA using Vue 3 + Vite with Tailwind CSS—dynamic UI for Projects, Donation pages, Sponsorship Runs.
- **Backend:** Laravel 12 as API & Web, PostgreSQL DB, Sanctum for auth, Spatie Translatable for i18n content.
- **Bilingual:** German (/de) and French (/fr) routing & translations.
- **Roles & Dashboards:** Platform Admin, Org Admin, Donor/Participant.
- **Key Features:**
  - User/Role management with token-based auth via Sanctum (SPA sessions & API tokens)
  - Project/Campaign CRUD with JSON i18n fields
  - Sponsorship Runs with electronic lap-count stub
  - Donation workflows: QR‑Bill, Twint, Stripe integrations
  - Custom donation page builder (templates, packages)
  - Crowdfunding (goal-setting, progress)
  - Email automation (Mailables, Notifications)
  - Reports & analytics dashboard
  - Payments & Webhooks submodule

## Architecture & Tech Stack
- **Backend:** Laravel 12 (API & Web controllers)
- **Frontend:** Vue 3 + Vite + Tailwind CSS
- **Auth:** Laravel Sanctum (SPA session + API tokens)
- **DB:** PostgreSQL
- **I18n:** `spatie/laravel-translatable` + vue-i18n/react-i18next
- **Testing:** PHPUnit (unit & feature), Pest, Cypress (E2E)
- **CI/CD:** GitHub Actions
- **Containers:** Docker + Docker Compose
- **Linting:** PHP CS Fixer, ESLint, Stylelint
- **Monitoring:** Sentry, Laravel Telescope
- **Dev Tools:** Tinker, Horizon (queues)

## Modules & Boundaries
1. **Auth & User Management**
2. **Project Management**
3. **Donation Processing**
4. **Sponsorship Run**
5. **Payments & Webhooks**
6. **Custom Donation Pages**
7. **Frontend SPA**
8. **Email & Notifications**
9. **Admin Dashboards**
10. **Analytics & Reporting**

## Non-Functional Requirements
- PSR‑12 code style, PSR‑5 docblocks
- ≥ 90% test coverage
- API responses ⩽ 500ms
- GDPR & OWASP Top10 compliance
- Scalable to 1k concurrent users

## Roadmap & Milestones
| Phase | Description                             | ETA       |
|-------|-----------------------------------------|-----------|
| 1     | Scaffold & Auth                         | 2025‑05‑15|
| 2     | Core CRUD (Users, Projects, Donations)  | 2025‑05‑30|
| 3     | Frontend SPA basics & i18n              | 2025‑06‑10|
| 4     | Sponsorship & Lap stub                  | 2025‑06‑20|
| 5     | Payment Integration & Webhooks          | 2025‑07‑05|
| 6     | Custom Donation Pages & Email           | 2025‑07‑20|
| 7     | Dashboards & Analytics                  | 2025‑08‑01|
| 8     | QA, Security audit, Performance tuning  | 2025‑08‑10|
| 9     | Launch & Deployment                     | 2025‑08‑20|