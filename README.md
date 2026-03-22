# Portfolio API

Production-grade REST API built with **PHP 8.4**, **Symfony 7.2**, **PostgreSQL**, **Nginx**, and **Docker**, following **Hexagonal Architecture** organized by **bounded context**.

## Architecture

```
app/                     # Symfony framework files
├── config/              # Symfony configuration
├── public/              # Web entry point
├── var/                 # Cache, logs
└── templates/           # Twig templates

src/
├── Domain/User/         # Business rules (bounded context)
│   ├── User.php         # Aggregate root
│   ├── Email.php, UserId.php, HashedPassword.php
│   ├── UserRepositoryInterface.php
│   ├── Exception/       # Domain-specific exceptions
│   └── Service/         # Domain service interfaces (ports)
│
├── Service/User/        # Use case orchestration
│   ├── CreateUserUseCase.php, GetUserUseCase.php, ...
│   └── DTO/             # Data transfer objects
│
├── Infrastructure/      # Adapters (external implementations)
│   ├── Repository/      # PDO repositories (raw SQL)
│   ├── Security/        # JWT authentication, password hashing
│   └── Symfony/         # Kernel, event listeners
│
├── Rest/                # HTTP layer
│   ├── HealthController.php
│   └── User/            # REST controllers (thin, no business logic)
│
├── Model/               # Request/Response models
│   ├── Request/User/    # Validated request objects
│   └── Response/
│       ├── User/        # User-specific response objects
│       └── ErrorResponse.php
│
└── Converter/User/      # Entity <-> DTO <-> Response mappers

chart-values/            # Helm chart values (cronjobs, monitoring, redis)
docs/                    # API documentation
liquibase/               # Database changelogs
mailpit/                 # Local email testing data
xdebug/                  # Xdebug configuration
```

### Key Principles

- **Domain isolation** -- Domain layer has zero framework dependencies
- **Dependency Inversion** -- All external dependencies are injected via interfaces (ports)
- **Bounded Context** -- Code organized by domain concept (User), not by layer type
- **Clean Controllers** -- Controllers only validate input, call a use case, and return a response
- **No entity exposure** -- Entities are never returned in API responses; DTOs and Response models are used instead

## Tech Stack

| Component | Technology |
|-----------|-----------|
| Language | PHP 8.4 |
| Framework | Symfony 7.2 |
| Database | PostgreSQL 16 |
| Persistence | Native PDO (raw SQL) |
| Auth | JWT (LexikJWTAuthenticationBundle) |
| API Docs | OpenAPI / Swagger (NelmioApiDocBundle) |
| Static Analysis | PHPStan (level max) |
| Code Style | ECS (PSR-12 + Symfony) |
| Testing | PHPUnit 11 |
| Containers | Docker + Docker Compose |
| Web Server | Nginx |
| Email (dev) | Mailpit |
| Cache | Redis |
| CI | GitHub Actions |

## Getting Started

### Prerequisites

- Docker and Docker Compose

### Setup

```bash
# Clone the repository
git clone <repository-url>
cd portafolio

# Full setup (build, install, create DB schema, generate JWT keys)
make setup
```

The API will be available at `http://localhost:8080`.

### Manual Setup

```bash
make build          # Build Docker images
make up             # Start containers
make install        # Install Composer dependencies
make db-create      # Create database schema
make jwt-keys       # Generate JWT key pair
```

## API Endpoints

### Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/login` | Authenticate and receive JWT token |

### Users (JWT protected)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/users` | Create a new user |
| GET | `/api/users` | List users (paginated) |
| GET | `/api/users/{id}` | Get a user by ID |
| PUT | `/api/users/{id}` | Update a user |
| DELETE | `/api/users/{id}` | Delete a user |

### System

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/health` | Health check |
| GET | `/api/doc` | Swagger UI |
| GET | `/api/doc.json` | OpenAPI spec (JSON) |

### Pagination & Filtering

```
GET /api/users?page=1&limit=10&name=john&email=example.com
```

### Example: Authentication Flow

```bash
# 1. Create a user
curl -X POST http://localhost:8080/api/users \
  -H "Content-Type: application/json" \
  -d '{"name": "John Doe", "email": "john@example.com", "password": "securepassword"}'

# 2. Login to get JWT token
curl -X POST http://localhost:8080/api/login \
  -H "Content-Type: application/json" \
  -d '{"username": "john@example.com", "password": "securepassword"}'

# 3. Use token in subsequent requests
curl http://localhost:8080/api/users \
  -H "Authorization: Bearer <your-jwt-token>"
```

## Development

### Available Commands

```bash
make up              # Start containers
make down            # Stop containers
make bash            # Shell into PHP container
make logs            # View container logs

make test            # Run all tests
make test-unit       # Run unit tests only
make test-integration # Run integration tests only
make test-functional # Run functional tests only
make test-coverage   # Generate HTML coverage report

make phpstan         # Run static analysis
make ecs             # Check code style
make ecs-fix         # Fix code style automatically
make quality         # Run all quality checks (phpstan + ecs + tests)

make db-create       # Create database schema
make db-test-create  # Create test database and schema
make db-reset        # Drop and recreate database schema
make cache-clear     # Clear Symfony cache
```

### Services

| Service | URL |
|---------|-----|
| API | http://localhost:8080 |
| Swagger UI | http://localhost:8080/api/doc |
| Mailpit UI | http://localhost:8025 |
| PostgreSQL | localhost:5432 |
| Redis | localhost:6379 |

### Code Quality Standards

- **PHPStan** level max -- strict static analysis
- **ECS** with PSR-12 and Symfony coding standards
- **Three test layers** -- Unit, Integration, Functional
- All checks run automatically in CI via GitHub Actions

## Error Handling

All errors return a consistent JSON format:

```json
{
    "error": "validation_error",
    "message": "Invalid request data.",
    "details": [
        {
            "field": "email",
            "message": "Please provide a valid email address."
        }
    ]
}
```

## License

MIT
