# TASK.md

## Workflow Columns
- **Backlog**: Candidate tasks
- **To Do**: Ready for development
- **In Progress**: Actively worked on
- **Review/QA**: PRs & tests
- **Done**: Completed tasks

## Current Sprint (2025‑05‑05 to 2025‑05‑15)
### To Do
- [x] Create repository & initial commit (2025‑05‑06)
- [ ] Setup GitHub Actions CI/CD pipeline (2025‑05‑07)
  - [ ] PHP unit tests & linting
  - [ ] JavaScript/TypeScript tests & linting
  - [ ] Build & deploy workflow
- [x] Setup Docker & Docker Compose
- [x] Configure PostgreSQL and .env.example
- [x] Install Sanctum, Spatie Translatable
- [x] Define database schema (ERD)

### In Progress
- [x] Scaffold Laravel 12 monolith + SPA frontend
- [x] Implement user authentication endpoints (register, login, logout)
- [x] Seed initial roles (platform_admin, org_admin, donor)
- [x] Build i18n middleware & language switcher
- [ ] Implement project CRUD operations
- [ ] Set up email templates system
- [ ] Configure payment gateway integration

### Review/QA
- [ ] Code review: Auth controllers & tests
- [ ] Review project CRUD implementation
- [ ] Test email template system
- [ ] Verify payment gateway integration

### Done
- [x] Add README.md, PLANNING.md, TASK.md stubs
- [x] Fixed all dashboard route mappings and sidebar links to match actual Vue file structure (2025-05-08)
- [x] Implemented basic project structure with Vue 3 + Vite
- [x] Set up authentication system with Sanctum
- [x] Created role-based access control system
- [x] Implemented language switching functionality
- [x] Created basic frontend components (ProjectCard, SidebarLink, CookieBanner)

## Backlog
- Create Project CRUD APIs & tests
- Integrate Stripe & Twint payment adapters
- Implement `/api/payments/intent` & webhook handler
- Build SPA routing & layout
- Custom donation page builder
- Admin dashboards (Vue/React)
- Email templates & automation workflows
- Reports & analytics pages
- Write E2E tests (Cypress)
- Setup GitHub Actions CI pipeline
- Configure Sentry & Laravel Telescope
- Develop sponsorship run lap-count stub

## Discovered During Work
- [ ] Add validation for project image uploads
- [ ] Implement bulk email functionality for donations
- [ ] Add export functionality for participant data
- [ ] Create donation receipt generation system
- [ ] Implement participant management system
- [ ] Add member group management features
- [ ] Create email template management system
- [ ] Implement donation tracking and reporting
- [ ] Add project duplication functionality
- [ ] Create participant import/export system