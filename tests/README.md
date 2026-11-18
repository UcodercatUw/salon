# Tests

Unit tests for the model layer (PHPUnit 11).

By default the suite runs on a separate **file-based SQLite database**
(`tests/.tmp/salon_test.sqlite`) which is recreated on every run. MySQL is not
required, and the working `salon_db` is never touched.

## Requirements

- PHP 8.2+ with `pdo_sqlite` (default mode)
- Composer dependencies installed (`composer install`)

## Run

```bash
vendor/bin/phpunit
```

From the XAMPP environment on Windows:

```bash
php vendor/bin/phpunit
```

Expected result:

```
OK (28 tests, 79 assertions)
```

## Optional: run against MySQL

To validate the same tests on the real MySQL schema, start MySQL (XAMPP Control
Panel → MySQL → Start) and run:

```bash
SALON_TEST_USE_MYSQL=1 vendor/bin/phpunit
```

This mode creates and drops a dedicated database **`salon_db_test`**; `salon_db`
is not affected. Connection can be overridden with `SALON_DB_HOST`,
`SALON_DB_USER` and `SALON_DB_PASS`.

## Coverage

| Test file | What it verifies |
|-----------|------------------|
| `ModelRatingTest.php` | inserting and updating a review, the one-review-per-user rule, average rating, per-service and per-master statistics, distinct master names |
| `ModelUserTest.php` | registration, password hashing, the `user` role, registration date, unique login and e-mail, lookup by login and by display name |
| `ModelEnrollmentTest.php` | booking creation with `confirmed` status, date normalisation (`T` → space, seconds), per-user booking list, rejection of unknown service/user |
| `ModelServicesTest.php` | category filtering, category names in the list, single service lookup, category order, master name suggestions |

## Structure

- `bootstrap.php` — prepares the test database (SQLite by default, MySQL behind
  `SALON_TEST_USE_MYSQL=1`), provides `TestDb::reset()` and `TestDb::seedBasics()`.
- `schema.sqlite.sql` — schema for the default SQLite mode.
- `schema.sql` — schema for the MySQL mode (mirrors `salon_db`).
