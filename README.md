# Arbiter Coffee Hub Modernization

## Key Features & Improvements
- Password hashing (bcrypt) and CSRF protection for all forms
- Rate limiting and account lockout for failed logins
- Audit logging (triggers, admin UI)
- Advanced search/filtering in admin (users, products)
- Robust error handling and user notifications
- Admin reports using SQL views, export to CSV/PDF
- Automation: daily DB backup (`backup_db.bat`), order archiving (`archive_old_orders.sql`)
- Code refactoring: reusable DB helpers in `includes/db_util.php`
- PHPDoc and inline documentation (in progress)
- Unit/integration testing (recommended: Pest, PHPUnit)

## Usage
- **Database**: Import the schema, advanced SQL, and run `login_attempts.sql` for rate limiting.
- **Backup**: Schedule `backup_db.bat` with Windows Task Scheduler.
- **Archiving**: Run `archive_old_orders.sql` monthly to archive old orders.
- **Audit Logs**: View in Admin > Audit Logs.
- **Reports**: Admin > Reports, export as CSV/PDF.
- **DB Helpers**: Use `db_fetch_one`, `db_fetch_all`, `db_execute`, `db_last_insert_id` from `includes/db_util.php` for all DB access.

## Best Practices
- Always use prepared statements for DB queries.
- Use the provided DB helpers for consistency and maintainability.
- Document new functions/classes with PHPDoc.
- Write tests for all critical features and edge cases.

## Testing
- Manual and automated tests recommended for registration, login, account lockout, search/filter, and reporting.

---
For more details, see inline comments in the codebase.
