# Product Requirements Document (PRD)

## Project Title
PCA Hybridization Portal System

## Version
v2.0 (Updated)

## Prepared For
Undergraduate Thesis / Capstone Project

## Prepared By
Marc Arron

---

## 1. Purpose of the Document
This Product Requirements Document (PRD) specifies the complete functional and non-functional requirements of the PCA Hybridization Portal System. It serves as the authoritative reference for system design, development, testing, deployment, and thesis defense.

---

## 2. Project Overview

### 2.1 System Description
The PCA Hybridization Portal System is a secure, web-based information system designed to manage coconut hybridization activities across multiple Philippine Coconut Authority (PCA) field sites. It supports role-based and field-based access control, structured data recording across four operational modules, automated Excel and PDF report generation, and a comprehensive audit trail.

### 2.2 Background and Rationale
Agricultural hybridization data at PCA field sites is often recorded manually using spreadsheets, resulting in fragmented records, limited traceability, and weak reporting. This system centralizes all field data — hybrid seedling distribution, monthly seednut harvest, nursery operations, and pollen production — while enforcing strict access control to preserve data integrity and accountability.

### 2.3 Objectives
- Digitize and centralize PCA coconut hybridization records across field sites
- Enforce role-based access (COS/Agriculturist, Senior Agriculturist, PCDM/Division Chief I) and field-based data isolation
- Support multi-field operations (Loay and Balilihan farms)
- Provide manual data entry with carry-forward logic from prior months
- Generate PCA-branded Excel exports (`.xlsx`) and PDF reports with official headers, logos, and approval footers
- Maintain a complete audit trail of all system actions
- Deliver role-specific dashboards with real-time statistics and data visualizations

---

## 3. Scope

### 3.1 In Scope
- User authentication and authorization with session timeout
- Field-specific, role-based dashboards with charts and statistics
- Four field data modules: Hybrid Distribution, Monthly Harvest, Nursery Operations, Pollen Production
- Hybridization record management (CRUD with submission/validation workflow)
- PCA-formatted Excel export with logo, headers, and signature footer
- PDF report generation (per-farm and consolidated)
- In-app notifications
- Comprehensive audit logging
- User profile management

### 3.2 Out of Scope
- Mobile application (native iOS/Android)
- AI-based prediction or analytics
- Public-facing data access or APIs
- Email or SMS notification delivery

---

## 4. Stakeholders

| Stakeholder | Role Title | Responsibility |
|------------|------------|----------------|
| Farm Supervisor | COS / Agriculturist | Field-level data entry, monitoring, and field-specific exports |
| Admin | Senior Agriculturist | Data validation, consolidated reporting, cross-field data review |
| Super Admin | PCDM / Division Chief I | Full system control, user governance, audit log review |
| PCA Management | — | Oversight and program evaluation |
| Thesis Panel | — | System assessment and evaluation |

---

## 5. User Roles and Permissions

### 5.1 Supervisor (COS / Agriculturist)
- Assigned to a single field site (Loay or Balilihan)
- Create, update, and delete field data records (own field only)
- Create and manage hybridization records (draft → submit workflow)
- Export Excel reports (own field only)
- View field-specific dashboard with statistics and charts

**Restrictions:**
- Cannot access other field sites' data
- Cannot manage users
- Cannot validate hybridization records

### 5.2 Admin (Senior Agriculturist)
- View and filter data from all field sites
- Validate or request revision on submitted hybridization records
- Generate consolidated PDF and Excel reports across all farms
- Access comparative analytics on the admin dashboard
- Delete any hybridization record

**Restrictions:**
- Cannot create or delete users
- Cannot view audit logs or system administration

### 5.3 Super Admin (PCDM / Division Chief I)
- Full system access
- Create, update, activate/deactivate user accounts
- Assign users to roles and field sites (inline or AJAX role update)
- View complete audit logs and system status
- Access system-level dashboard with user counts, activity logs, and record summaries

**Restrictions:**
- No direct data entry responsibilities

---

## 6. Field Sites

| Field Site | Description |
|-----------|-------------|
| Loay Farm | PCA hybridization field site in Loay, Bohol |
| Balilihan Farm | PCA hybridization field site in Balilihan, Bohol |

Each field site has isolated data access for supervisors. Admins and Super Admins can view and filter across all sites.

---

## 7. Functional Requirements

### 7.1 Authentication and Authorization

| Requirement | Detail |
|------------|--------|
| Login | Secure login via username and password |
| Role-Based Access Control | Three roles: supervisor, admin, superadmin — enforced via decorators |
| Field-Based Data Filtering | Supervisors see only their assigned field site's data |
| Session Timeout | Auto-logout after 30 minutes of inactivity (`SESSION_COOKIE_AGE=1800`) |
| Session Security | Session expires at browser close; cache-control headers prevent back-button access after logout |
| CSRF Protection | Django CSRF middleware enabled for all POST requests |
| Password Validation | Enforced via Django's built-in password validators (similarity, length, common, numeric) |

---

### 7.2 Dashboards

#### Supervisor Dashboard
- Field-specific record counts (distribution, harvest, nursery, pollen)
- Monthly seednut production trend chart (current year)
- Seedlings distributed vs. planted comparison chart
- Quick-action links to data entry and export
- Recent unread notifications

#### Admin Dashboard
- Multi-field overview with aggregate statistics
- Per-farm record counts and comparisons
- Comparative charts across Loay and Balilihan
- Supervisor activity monitoring
- Report generation controls

#### Super Admin Dashboard
- Total user counts broken down by role
- Active vs. inactive user summary
- System-wide record counts across all data modules
- Recent audit log entries
- User management quick links

---

### 7.3 Field Data Modules

The system manages four primary field data modules, each supporting full CRUD operations, list views with filtering, and Excel export.

#### 7.3.1 Hybrid Distribution
Tracks the distribution of hybrid coconut seedlings to farmers.

**Data Fields:**
- Region, Province, District, Municipality, Barangay
- Farmer Name (Last Name, First Name, M.I.)
- Gender (Male / Female)
- Farm Location (Barangay, Municipality, Province)
- No. of Seedlings Received
- Date Received
- Type / Variety
- No. of Seedlings Planted
- Date Planted
- Remarks

**Features:**
- Batch entry: add multiple farmer rows in a single form submission
- List view with totals row (sum of Seedlings Received and Seedlings Planted)
- Filter by field site, year, and month
- Excel export with totals and PCA formatting

#### 7.3.2 Monthly Harvest (On-Farm Hybrid Seednut Production)
Records monthly seednut production data across partner farms.

**Data Fields:**
- Farm Location
- Name of Partner / Farm
- Area (Ha.)
- Age of Palms (Years)
- No. of Hybridized Palms
- Variety / Hybrid Crosses (supports multiple varieties per record via child model)
- Seednuts Produced (OPV / Hybrid) — per variety
- Monthly Production (Jan–Dec columns, per record)
- Remarks

**Features:**
- Parent-child data model: one harvest record → multiple `HarvestVariety` child records
- Monthly carry-forward via AJAX: auto-populates farm data from the preceding month
- Year-based record segregation (auto-creates new records per year)
- List view with variety expansion
- Excel export per farm with multi-variety layout

#### 7.3.3 Nursery Operations and Terminal Reports
Tracks communal nursery establishment and terminal (end-of-cycle) reports.

**Data Fields:**
- Region / Province / District
- Barangay / Municipality
- Proponent Entity Name
- Representative
- Target No. of Seednuts

**Batch-level fields (child model — `NurseryBatch`):**
- No. of Seednuts Harvested
- Date Harvested
- Date Seednuts Received
- Source of Seednuts
- Type / Variety
- No. of Seednuts Sown
- Date Seednut Sown
- No. of Seedlings Germinated
- No. of Ungerminated Seednuts
- No. of Culled Seedlings
- No. of Good Seedlings @ 1 ft tall
- No. of Ready to Plant (Polybagged)
- No. of Seedlings Dispatched
- Remarks

**Features:**
- Two report types: Monthly Report and Terminal Report (separate list views)
- Parent-child data model: one nursery operation → multiple `NurseryBatch` records
- Full CRUD with dynamic batch row management
- Excel export per farm

#### 7.3.4 Pollen Production and Inventory
Tracks pollen production, receipt from other centers, and weekly utilization.

**Data Fields:**
- Month (label)
- Pollen Variety
- Ending Balance from Previous Month
- Pollens Received: Source, Date, Amount
- Weekly Utilization: Week 1–5
- Total Utilization
- Ending Balance
- Remarks

**Features:**
- Carry-forward via AJAX: auto-populates ending balance from the prior month
- Filter by field site, year, and month
- Excel export per farm with weight formatting

---

### 7.4 Hybridization Record Management

Core hybridization experiment/cross records with a submission and validation workflow.

**Data Fields:**
- Field Site (auto-assigned for supervisors)
- Crop Type
- Parent Line A
- Parent Line B
- Hybrid Code (unique)
- Date Planted
- Growth Status: Seedling → Vegetative → Flowering → Fruiting → Harvested
- Notes
- Status: Draft → Submitted → Validated / Needs Revision
- Admin Remarks (validation feedback)
- Field Images (multiple per record, via `RecordImage` child model)

**Workflow:**
1. Supervisor creates record (Draft)
2. Supervisor submits for validation
3. Admin validates or requests revision (with remarks)
4. Notifications sent to supervisor on status change

**Permissions:**
- Supervisors: create, edit (own draft/revision only), submit, delete own drafts
- Admins: view all, validate, delete any record
- Super Admins: full access

---

### 7.5 Reporting and Exporting

#### Excel Export (`.xlsx`)
- Available for all four field data modules: Distribution, Harvest, Nursery, Pollen
- Terminal Reports exported separately from Nursery Operations
- **PCA-branded formatting:**
  - Official PCA + Department of Agriculture logo in header
  - Title rows with report name, field site name, and "as of" date
  - Green-styled column headers
  - Auto-sized columns with data borders
  - Prepared by / Reviewed by / Noted by signature footer block
- Per-farm worksheet separation (one sheet per field site)
- Totals row for numeric columns (e.g., Seedlings Received, Seedlings Planted)
- Filterable by field site, year, and month before export

#### PDF Reports
- Generated via ReportLab with landscape letter format
- Per-farm and consolidated options
- Includes PCA logo, table headers, data rows, and approval sections
- Available for hybridization records, distribution, harvest, nursery, and pollen data

#### Report History
- All generated reports (PDF, CSV, Excel) are tracked in the `Report` model
- Download history accessible from the Reports page

---

### 7.6 In-App Notifications
- **Notification triggers:**
  - Hybridization record submitted for validation
  - Record validated or revision requested
  - Report generation completion
- Bell icon in navbar with unread count badge
- Mark individual or all notifications as read
- Full notification history page

---

### 7.7 Audit Logging
All significant system events are recorded in the `AuditLog` model:

| Action Type | Description |
|-----------|-------------|
| Login | User login with IP address |
| Logout | User logout |
| Create | New record creation (any module) |
| Update | Record modification |
| Delete | Record deletion |
| Submit | Hybridization record submission |
| Validate | Admin validation action |
| Revision | Admin revision request |
| Report | Report generation |
| User Management | User create, edit, toggle active |

Each log entry records: user, action, model name, object ID, JSON details, IP address, and timestamp.

---

### 7.8 User Profile
- View profile page with username, role, and assigned field site
- Password change functionality
- Profile information display

---

## 8. Non-Functional Requirements

### 8.1 Security
| Measure | Implementation |
|---------|----------------|
| Role-Based Access Control | Custom `@role_required` and `@field_access_required` decorators |
| Field-Based Data Isolation | QuerySet filtering by `field_site` based on user profile |
| CSRF Protection | Django CSRF middleware on all forms |
| Password Security | Hashed passwords via Django auth; 4-rule password validation |
| Session Management | 30-min inactivity timeout; session expires on browser close |
| Cache Control | `CacheControlMiddleware` prevents cached page access after logout |
| 403 Forbidden Page | Custom `403.html` template for unauthorized access attempts |

### 8.2 Performance
- Optimized database queries with `select_related` and `prefetch_related`
- Response time target: under 3 seconds for standard operations

### 8.3 Scalability
- Support for additional field sites (data model is site-agnostic)
- Support for increased data volume across years
- Year-based automatic record segregation

### 8.4 Usability
- Consistent Bootstrap-based responsive layout across all roles
- Dynamic form rows (add/remove) for batch and child record entry
- AJAX carry-forward for Monthly Harvest and Pollen Production
- Date filter controls (year + month dropdowns) on all list views
- Confirmation modals before destructive actions (delete)

### 8.5 Reliability
- Database backup strategy (PostgreSQL via Supabase)
- Error handling with user-friendly flash messages
- Comprehensive audit logging for accountability

---

## 9. System Architecture

### 9.1 Technology Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, JavaScript, Bootstrap 5 |
| Backend | Python 3, Django 5.x |
| Database | PostgreSQL (via Supabase) with SQLite fallback for local development |
| ORM | Django ORM with `dj-database-url` for connection configuration |
| Excel Generation | `openpyxl` (`.xlsx` with images, styles, formulas) |
| PDF Generation | `ReportLab` (tables, logos, landscape layout) |
| Environment Config | `python-dotenv` for `DATABASE_URL` and secrets |
| Time Zone | Asia/Manila (`Asia/Manila`) |

### 9.2 Django Application Architecture

```
pca_portal/          ── Project configuration (settings, URLs, WSGI)
├── accounts/        ── User model, roles, authentication, notifications, user management
├── dashboard/       ── Role-based dashboard views and templates
├── field_data/      ── 4 field data modules (models, views, forms, exports)
├── hybridization/   ── Core hybridization records and validation workflow
├── reports/         ── Report generation (PDF + Excel) and download history
├── audit/           ── Audit logging model and views
├── templates/       ── Global templates (base, 403, per-app subdirectories)
├── static/          ── Static assets (CSS, JS)
├── assets/          ── Logo images and branding resources
└── media/           ── User-uploaded files (Excel uploads, images, reports)
```

---

## 10. Database Design Overview

### Core Models

| App | Model | Description |
|-----|-------|-------------|
| `accounts` | `FieldSite` | PCA field site (Loay, Balilihan) |
| `accounts` | `UserProfile` | Extends Django User with role and field site assignment |
| `accounts` | `Notification` | In-app notification (message, link, read status) |
| `field_data` | `ExcelUpload` | Tracks uploaded Excel files with type and record count |
| `field_data` | `HybridDistribution` | Seedling distribution records per farmer |
| `field_data` | `MonthlyHarvest` | Monthly seednut production with Jan–Dec columns |
| `field_data` | `HarvestVariety` | Child: variety-level data for a harvest record |
| `field_data` | `NurseryOperation` | Nursery operation or terminal report header |
| `field_data` | `NurseryBatch` | Child: batch/variety detail for a nursery operation |
| `field_data` | `PollenProduction` | Pollen production and weekly utilization records |
| `hybridization` | `HybridizationRecord` | Core hybridization cross record with lifecycle status |
| `hybridization` | `RecordImage` | Field images attached to a hybridization record |
| `reports` | `Report` | Generated report file metadata (PDF/CSV/Excel) |
| `audit` | `AuditLog` | System event log with JSON details and IP tracking |

### Key Relationships
- `UserProfile` → one-to-one with Django `User`; foreign key to `FieldSite`
- `HybridDistribution`, `MonthlyHarvest`, `NurseryOperation`, `PollenProduction` → all foreign key to `FieldSite`
- `MonthlyHarvest` → one-to-many `HarvestVariety`
- `NurseryOperation` → one-to-many `NurseryBatch`
- `HybridizationRecord` → one-to-many `RecordImage`; foreign key to `FieldSite` and `User`
- All field data models → optional foreign key to `ExcelUpload`
- `AuditLog` → foreign key to `User`; stores `model_name` and `object_id` for flexible referencing

---

## 11. Assumptions and Constraints

### Assumptions
- Internet connectivity is available at field sites
- Users have basic computer literacy and browser access
- PCA report formats (Excel templates) remain consistent
- Two field sites are active (extensible to more)

### Constraints
- Limited budget (academic project)
- Academic timeline (one semester)
- Local development with SQLite; production via PostgreSQL on Supabase

---

## 12. Risks and Mitigation

| Risk | Mitigation |
|-----|------------|
| Unauthorized access | Role-based + field-based access control; session timeout; cache control |
| Data loss | PostgreSQL on Supabase with managed backups; local SQLite for dev |
| User entry errors | Form validation; confirmation modals; carry-forward auto-population |
| Cross-field data leakage | Field site filtering enforced at QuerySet level for supervisors |
| Session hijacking | CSRF protection; session expiry at browser close; 30-min timeout |

---

## 13. Success Metrics
- Accurate and complete digitization of all four field data modules
- Zero cross-field data access violations
- Successful PCA-branded Excel and PDF report generation
- Full audit trail with no gaps in logged actions
- Role-appropriate dashboard rendering for all three user types
- Carry-forward functionality working across month and year transitions
- Positive thesis panel evaluation

---

## 14. Approval
This PRD is considered approved upon acceptance by the thesis adviser and panel.

---

**End of Document**
