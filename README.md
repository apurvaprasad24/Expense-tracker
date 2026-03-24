# ExpenseIQ — Smart Expense Tracker

A full-stack expense tracking web application with real-time budget alerts, spending analytics, and an interactive dashboard.

## 🛠 Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, Vanilla JavaScript, Chart.js |
| Backend | PHP 8+ (REST API) |
| Database | MySQL |
| Server | Apache (XAMPP) |

## Features

- **Dashboard** — Monthly spending overview with donut + line charts
- **Add Expenses** — Categorized expense logging with notes
- **Budget Management** — Per-category monthly budget limits with progress bars
- **Real-Time Alerts** — Toast notifications when 80% or 100% of budget is used
- **Filter & Delete** — Browse all expenses by category, delete with one click
- **Responsive Design** — Works on desktop and tablet

## Getting Started

### Prerequisites
- XAMPP (or any Apache + PHP + MySQL stack)
- PHP 8.0+
- MySQL 5.7+

### Setup

1. **Clone the repo**
   ```bash
   git clone https://github.com/yourusername/expense-tracker.git
   ```

2. **Move to XAMPP's htdocs**
   ```bash
   mv expense-tracker/ /path/to/xampp/htdocs/
   ```

3. **Import the database**
   - Open `http://localhost/phpmyadmin`
   - Create a new database called `expense_tracker`
   - Import `database.sql`

4. **Configure DB credentials** (if needed)
   - Edit `api/config.php`
   - Default: host=`localhost`, user=`root`, password=`` (empty)

5. **Run it**
   - Start Apache + MySQL in XAMPP Control Panel
   - Visit `http://localhost/expense-tracker`

## Project Structure

```
expense-tracker/
├── index.html          # Main SPA frontend
├── database.sql        # DB schema + seed data
└── api/
    ├── config.php      # DB connection
    ├── expenses.php    # CRUD API (GET/POST/DELETE)
    ├── stats.php       # Analytics & chart data
    └── budgets.php     # Budget management (GET/PUT)
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/expenses.php` | Fetch expenses (optional `?category=Food&limit=50`) |
| `POST` | `/api/expenses.php` | Add new expense |
| `DELETE` | `/api/expenses.php?id=1` | Delete expense by ID |
| `GET` | `/api/stats.php` | Get chart data, totals, budget status |
| `GET` | `/api/budgets.php` | Get all budgets |
| `PUT` | `/api/budgets.php` | Update budget limit |

## 📸 Screenshots

> *(Add screenshots of your dashboard, add expense form, and budget page here)*

## Author

**Apurva Prasad** — [LinkedIn](https://www.linkedin.com/in/apurva-prasad-029a39274) · [GitHub](https://github.com/apurvaprasad24)
