# Task Management API
### Cytonn Software Engineering Internship — Coding Challenge 2026
**Author:** Nathanael Kamau  
**GitHub:** https://github.com/Lavezzamavl/task-management-api  
**Live URL:** https://web-production-91be1.up.railway.app  

---

## Tech Stack
- **Backend:** PHP / Laravel 11
- **Database:** MySQL 8.0
- **Frontend:** Vanilla JS + HTML/CSS
- **Hosting:** Railway

---

## Features
- ✅ Create tasks with duplicate title+date protection
- ✅ List tasks sorted by priority (high → medium → low), then due date
- ✅ Optional status filter on listing
- ✅ Strict forward-only status progression (pending → in_progress → done)
- ✅ Delete only `done` tasks (403 Forbidden otherwise)
- ✅ Bonus: Daily report grouped by priority × status
- ✅ Clean frontend dashboard (Vanilla JS)

---

## Local Setup

### Prerequisites
- PHP 8.2+
- Composer
- MySQL 8.0+ or XAMPP

### Steps
```bash
# 1. Clone the repo
git clone https://github.com/Lavezzamavl/task-management-api.git
cd task-management-api

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env
php artisan key:generate

# 4. Configure .env with your MySQL credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=

# 5. Create database
mysql -u root -p -e "CREATE DATABASE task_management;"

# 6. Run migrations
php artisan migrate

# 7. Start server
php artisan serve
```

Open `http://127.0.0.1:8000/dashboard.html` for the frontend UI.

---

## Database
- **Database used:** MySQL
- **SQL dump file:** `dump.sql` (included in submission ZIP)
- Import with: `mysql -u root -p task_management < dump.sql`

---

## Live Deployment (Railway)
- **Live API URL:** https://web-production-91be1.up.railway.app
- **Frontend UI:** https://web-production-91be1.up.railway.app/dashboard.html

---

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/tasks` | Create a task |
| GET | `/api/tasks` | List all tasks |
| GET | `/api/tasks?status=pending` | Filter by status |
| PATCH | `/api/tasks/{id}/status` | Update status |
| DELETE | `/api/tasks/{id}` | Delete a done task |
| GET | `/api/tasks/report?date=YYYY-MM-DD` | Daily report |

---

## Example API Requests

### Create Task
```bash
curl -X POST https://web-production-91be1.up.railway.app/api/tasks \
  -H "Content-Type: application/json" \
  -d '{"title":"Fix login bug","due_date":"2026-04-05","priority":"high"}'
```

### List Tasks
```bash
curl https://web-production-91be1.up.railway.app/api/tasks
```

### Update Status
```bash
curl -X PATCH https://web-production-91be1.up.railway.app/api/tasks/1/status \
  -H "Content-Type: application/json" \
  -d '{"status":"in_progress"}'
```

### Delete Task
```bash
curl -X DELETE https://web-production-91be1.up.railway.app/api/tasks/1
```

### Daily Report
```bash
curl "https://web-production-91be1.up.railway.app/api/tasks/report?date=2026-04-05"
```

---

## Business Rules

| Rule | Behaviour |
|------|-----------|
| Duplicate title+date | 422 rejected |
| Past due date | 422 rejected |
| Status progression | pending → in_progress → done only |
| Skip/revert status | 422 not allowed |
| Delete non-done task | 403 Forbidden |
| Delete done task | 200 OK |

---

## Project Structure
```
task-management-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/TaskController.php
│   │   └── Requests/
│   │       ├── StoreTaskRequest.php
│   │       └── UpdateTaskStatusRequest.php
│   └── Models/Task.php
├── database/
│   ├── migrations/
│   └── dump.sql
├── public/
│   └── dashboard.html        ← Frontend UI
├── routes/
│   └── api.php
├── nixpacks.toml             ← Railway config
├── start.sh                  ← Railway startup script
└── README.md
```
