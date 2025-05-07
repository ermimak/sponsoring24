# TASK.md

## Workflow Columns
- **Backlog**: Candidate tasks
- **To Do**: Ready for development
- **In Progress**: Actively worked on
- **Review/QA**: PRs & tests
- **Done**: Completed tasks

## Current Sprint (2025‑05‑05 to 2025‑05‑15)
### To Do
- [ ] Create repository & initial commit (2025‑05‑06)
- [ ] Setup GitHub Actions CI/CD pipeline (2025‑05‑07)
  - [ ] PHP unit tests & linting
  - [ ] JavaScript/TypeScript tests & linting
  - [ ] Build & deploy workflow
- [ ] Scaffold Laravel 12 monolith + SPA frontend
- [ ] Setup Docker & Docker Compose
- [ ] Configure PostgreSQL and .env.example
- [ ] Install Sanctum, Spatie Translatable
- [ ] Define database schema (ERD)

### In Progress
- [ ] Implement user authentication endpoints (register, login, logout)
- [ ] Seed initial roles (platform_admin, org_admin, donor)
- [ ] Build i18n middleware & language switcher

### Review/QA
- [ ] Code review: Auth controllers & tests

### Done
- [x] Add README.md, PLANNING.md, TASK.md stubs

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