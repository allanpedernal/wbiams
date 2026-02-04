# Web-Based IP Address Management System (WBIAMS)

A web-based IP address management solution that allows authenticated users to record IP addresses with labels and comments, perform CRUD operations based on user roles, and maintain a secure, tamper-proof audit log.

## Features

- **Authentication & Authorization**
  - User registration and login with Laravel Fortify
  - API token authentication with Laravel Sanctum (JWT-like)
  - Automatic token renewal to prevent session expiration
  - Two-factor authentication support
  - Role-based access control (Regular User, Super Admin)

- **IP Address Management**
  - Record IPv4 and IPv6 addresses with labels and optional comments
  - View all recorded IP addresses (accessible to all authenticated users)
  - Edit IP address labels (owners and super-admins only)
  - Delete IP addresses (super-admins only)

- **Audit Logging**
  - Comprehensive activity logging using Spatie Activity Log
  - Tracks login/logout events
  - Records all IP address changes (create, update, delete)
  - View activity by user session or IP address lifetime
  - Tamper-proof audit logs (no delete functionality)
  - Audit log dashboard (super-admins only)

- **API Documentation**
  - Interactive Swagger/OpenAPI documentation
  - Try-it-out functionality for all endpoints
  - Bearer token authentication support
  - Auto-generated from PHP annotations

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.4)
- **Frontend**: Vue.js 3 with Inertia.js v2
- **Database**: MySQL 8.4
- **Authentication**: Laravel Fortify
- **Authorization**: Spatie Laravel Permission
- **Audit Logging**: Spatie Laravel Activity Log
- **Styling**: Tailwind CSS with shadcn/ui components
- **Containerization**: Docker via Laravel Sail

## Installation

### Prerequisites

- Docker and Docker Compose
- PHP 8.4+ (for local development without Docker)
- Composer
- Node.js 18+ and npm

### Using Docker (Recommended)

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd wbiams
   ```

2. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

3. Install PHP dependencies:
   ```bash
   composer install
   ```

4. Start the Docker containers:
   ```bash
   ./vendor/bin/sail up -d
   ```

5. Generate application key:
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

6. Run migrations and seed the database:
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```

7. Install frontend dependencies and build:
   ```bash
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run build
   ```

8. Access the application at `http://localhost`

### Local Development (Without Docker)

1. Clone the repository and install dependencies:
   ```bash
   git clone <repository-url>
   cd wbiams
   composer install
   npm install
   ```

2. Configure your `.env` file with your database credentials

3. Run migrations and seed:
   ```bash
   php artisan migrate --seed
   ```

4. Build frontend assets:
   ```bash
   npm run build
   ```

5. Start the development server:
   ```bash
   composer run dev
   ```

## Default Users

After seeding, the following users are available:

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@example.com | password |
| Regular User | (check database) | password |

## API Endpoints

> **Interactive Documentation**: Access the Swagger UI at [http://localhost/api/documentation](http://localhost/api/documentation) for interactive API testing.

### Token-based API Authentication (JWT-like)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/login` | Login and receive Bearer token (24h expiry) |
| GET | `/api/auth/user` | Get current user and token expiration |
| POST | `/api/auth/refresh` | Refresh token before expiration |
| POST | `/api/auth/logout` | Revoke current token |

**Example Usage:**
```bash
# Login and get token
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Use token for API requests
curl http://localhost/api/ip-addresses \
  -H "Authorization: Bearer {token}"
```

### API Endpoints (Token Authentication)

| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET | `/api/ip-addresses` | List all IP addresses | Authenticated |
| POST | `/api/ip-addresses` | Create new IP address | Authenticated |
| GET | `/api/ip-addresses/{id}` | View IP address details | Authenticated |
| PUT | `/api/ip-addresses/{id}` | Update IP address | Owner or Super Admin |
| DELETE | `/api/ip-addresses/{id}` | Delete IP address | Super Admin only |

### Web Endpoints (Session Authentication)

| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET | `/ip-addresses` | List all IP addresses | All authenticated users |
| POST | `/ip-addresses` | Create new IP address | All authenticated users |
| GET | `/ip-addresses/{id}` | View IP address details | All authenticated users |
| PUT | `/ip-addresses/{id}` | Update IP address | Owner or Super Admin |
| DELETE | `/ip-addresses/{id}` | Delete IP address | Super Admin only |

### Audit Logs (Super Admin Only)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/audit-logs` | List all audit logs |
| GET | `/audit-logs/{id}` | View audit log details |
| GET | `/audit-logs/user/{userId}` | View user's activity history |
| GET | `/audit-logs/ip-address/{id}` | View IP address history |
| GET | `/audit-logs/session/{sessionId}` | View session activities |

## Postman Collection

A Postman collection is included for API testing. Import `postman_collection.json` into Postman.

### Token-based API Usage (Recommended)

1. Import `postman_collection.json` into Postman
2. Set the `base_url` variable (default: `http://localhost`)
3. Run **"Login (Get Token)"** from the API Token Authentication folder
4. Token is automatically saved to `api_token` variable
5. Use API endpoints - token is automatically included

### Session-based Web Usage

1. Run **"Get CSRF Cookie"** from Web Session Authentication folder
2. Run **"Login"** to authenticate via session
3. Use Web endpoints - cookies are automatically included

## Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter=IpAddressTest

# Run with coverage
php artisan test --coverage
```

## Development

### Code Style

The project uses Laravel Pint for code formatting:

```bash
vendor/bin/pint
```

### Generate Wayfinder Routes

When adding new routes, regenerate TypeScript route definitions:

```bash
php artisan wayfinder:generate
```

### Frontend Development

```bash
npm run dev
```

## Project Structure

```
├── app/
│   ├── Actions/                    # Laravel Actions (Single-purpose classes)
│   │   ├── Auth/
│   │   │   ├── ApiLogin.php
│   │   │   ├── ApiLogout.php
│   │   │   ├── RefreshToken.php
│   │   │   └── GetCurrentUser.php
│   │   ├── IpAddress/
│   │   │   ├── ListIpAddresses.php
│   │   │   ├── CreateIpAddress.php
│   │   │   ├── ShowIpAddress.php
│   │   │   ├── UpdateIpAddress.php
│   │   │   └── DeleteIpAddress.php
│   │   └── AuditLog/
│   │       ├── ListAuditLogs.php
│   │       ├── ShowAuditLog.php
│   │       ├── ListUserActivities.php
│   │       ├── ListIpAddressHistory.php
│   │       └── ListSessionActivities.php
│   ├── Http/
│   │   └── Requests/
│   │       └── IpAddress/
│   │           ├── StoreIpAddressRequest.php
│   │           └── UpdateIpAddressRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   └── IpAddress.php
│   ├── Policies/
│   │   └── IpAddressPolicy.php
│   └── Listeners/
│       ├── LogSuccessfulLogin.php
│       └── LogSuccessfulLogout.php
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── RoleAndPermissionSeeder.php
├── doc/                            # Documentation
│   ├── ARCHITECTURE.md
│   └── API.md
├── resources/js/
│   ├── pages/
│   │   ├── IpAddresses/
│   │   │   ├── Index.vue
│   │   │   ├── Create.vue
│   │   │   ├── Edit.vue
│   │   │   └── Show.vue
│   │   └── AuditLogs/
│   │       ├── Index.vue
│   │       ├── Show.vue
│   │       ├── UserActivities.vue
│   │       ├── IpAddressHistory.vue
│   │       └── SessionActivities.vue
│   └── components/
├── tests/
│   └── Feature/
│       ├── IpAddressTest.php
│       └── AuditLogTest.php
├── compose.yaml
└── postman_collection.json
```

## Roles and Permissions

### Regular User
- View all IP addresses
- Create new IP addresses
- Edit their own IP addresses (label and comment only)
- Cannot delete any IP addresses

### Super Admin
- All regular user permissions
- Edit any IP address
- Delete any IP address
- Access audit log dashboard
- View all user activities

## Security Considerations

- Audit logs are immutable - no delete operations are exposed
- All changes to IP addresses are logged with user attribution
- Login/logout events are tracked with IP and user agent
- Role-based access control prevents unauthorized modifications
- Session management with optional two-factor authentication

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
