## Event Booking Backend (Laravel 11)

This is a production-grade **Event Booking System backend** built on **Laravel 11** and **PHP 8.2**.  
It supports authentication with Sanctum, role-based access (admin, organizer, customer), event and ticket management, bookings, and payments.

### Requirements

- **PHP 8.2+**
- **Composer**
- **MySQL** (or another database supported by Laravel)
- **Node.js** (only required if you run the front-end tooling, not needed for API usage)

### Installation

1. **Clone the repository**

```bash
git clone <your-repo-url> event-booking-backend
cd event-booking-backend
```

2. **Install PHP dependencies**

```bash
composer install
```

3. **Copy environment file**

```bash
cp .env.example .env
```

4. **Configure database**

Update the following variables in `.env` to point to your MySQL (or other) database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_booking
DB_USERNAME=root
DB_PASSWORD=secret
```

Or use Sqlite database, the file is attached to the repo:

```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

5. **Generate application key**

```bash
php artisan key:generate
```

6. **Run migrations and seeders**

```bash
php artisan migrate --seed
```

This will create:

- **2 admins**
- **3 organizers**
- **10 customers**
- **5 events**
- **15 tickets**
- **20 bookings**

Some bookings will have associated payments.

7. **Run the development server**

```bash
php artisan serve
```

The API will be available at `http://127.0.0.1:8000`.

### Testing

Run the test suite with:

```bash
php artisan test
```

### Authentication & Roles

- **Sanctum** is used for API token authentication.
- `users` table contains a `role` column with values:
  - `admin`
  - `organizer`
  - `customer`

Use the seeded users (or register new ones via API) and log in to obtain an API token for authenticated requests.

### Postman Collection

A Postman collection is included at `postman/event-booking.postman_collection.json`.  
Import it into Postman and set the `base_url` variable (e.g. `http://127.0.0.1:8000`) to start calling the APIs.

### Seeding Summary

The main `DatabaseSeeder` uses factories to create:

- Admins, organizers, and customers with roles.
- Events for organizers.
- Tickets attached to events.
- Bookings for customer users, incrementing ticket `sold` counts.
- Optional payments for a subset of bookings.

You can re-seed the database at any time with:

```bash
php artisan migrate:fresh --seed
```


