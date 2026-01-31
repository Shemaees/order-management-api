# Order & Payment Management API

A Laravel-based RESTful API for managing orders and payments with a focus on clean architecture, extensibility, and maintainability.

##  Features

- **Authentication System** - JWT-based authentication
- **Order Management** - Full CRUD operations for orders with items
- **Payment Processing** - Multiple payment gateways with Strategy Pattern
- **Modular Architecture** - Self-contained modules for scalability
- **Comprehensive Testing** - Feature tests
- **API Documentation** - Complete API documentation with examples

##  Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Architecture](#architecture)
- [Testing](#testing)
- [API Documentation](#api-documentation)
- [Adding New Payment Gateway](#adding-new-payment-gateway)

##  Requirements

- PHP 8.4+
- Composer
- MySQL 8.0+ / PostgreSQL / SQLite
- Laravel 12.x

##  Installation

### 1. Clone the repository
```bash
git clone https://github.com/Shemaees/order-payment-api.git
cd order-payment-api
```

### 2. Install dependencies
```bash
composer install
```

### 3. Environment setup
```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

### 4. Configure database

Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=order_payment_api
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Run migrations and seeders
```bash
php artisan migrate --seed
```

### 6. Start the server
```bash
php artisan serve
```

API will be available at: `http://localhost:8000`

##  Configuration

### JWT Configuration
```env
JWT_SECRET=your_generated_secret
JWT_TTL=60
JWT_REFRESH_TTL=20160
```
### Payment Gateways

Configure payment gateway credentials:
```env
CREDIT_CARD_API_KEY=your_api_key
CREDIT_CARD_SECRET=your_secret
PAYPAL_CLIENT_ID=your_client_id
PAYPAL_SECRET=your_secret
```

## ️ Architecture

### Modular Structure
```
app/Modules/
├── Auth/
│   ├── DTOs/
│   ├── Services/
│   ├── Http/
│   └── Routes/
├── Order/
│   ├── Database/
│   │   ├── Migrations/
│   │   ├── Factories/
│   │   └── Seeders/
│   ├── DTOs/
│   ├── Models/
│   ├── Repositories/
│   ├── Services/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Routes/
│   └── Providers/
└── Payment/
    ├── Database/
    ├── DTOs/
    ├── Models/
    ├── Repositories/
    ├── Services/
    │   └── Gateways/
    ├── Http/
    └── Routes/
```

### Design Patterns Used

- **Repository Pattern** - Data access abstraction
- **Service Layer Pattern** - Business logic separation
- **Strategy Pattern** - Payment gateway implementation
- **DTO Pattern** - Data transfer objects
- **Factory Pattern** - Payment gateway factory

### Base Classes

All modules extend from base classes:

- `BaseDTO` - Data Transfer Object base
- `BaseRepository` - Repository base with common operations
- `BaseService` - Service base with exception handling

## 🧪 Testing

### Run all tests
```bash
php artisan test
```

### Run specific test suite
```bash
# Auth tests
php artisan test --filter=Auth

# Order tests
php artisan test --filter=Order

# Payment tests
php artisan test --filter=Payment
```

### Run with coverage
```bash
php artisan test --coverage
```

### Test Database

Tests use MySQL database by default. Configure in `phpunit.xml`:
```xml
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_DATABASE" value="order_management_api"/>
```

##  API Documentation

See [API_DOCUMENTATION.md](API_DOCUMENTATION.md) for complete API reference.

### Quick Start

1. **Register User**
```bash
POST /api/auth/register
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

2. **Login**
```bash
POST /api/auth/login
{
  "email": "john@example.com",
  "password": "password123"
}
```

3. **Create Order**
```bash
POST /api/order
Authorization: Bearer {token}
{
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "price": 50.00
    }
  ],
  "billing_address": "123 Street",
  "shipping_address": "456 Avenue"
}
```

4. **Process Payment**
```bash
POST /api/orders/{order_id}/payment
Authorization: Bearer {token}
{
  "payment_method": "credit_card",
  "amount": 100.00
}
```

##  Adding New Payment Gateway

### Step 1: Create Gateway Class

Create `app/Modules/Payment/Services/Gateways/YourGateway.php`:
```php
<?php

namespace App\Modules\Payment\Services\Gateways;

use App\Modules\Payment\DTOs\ProcessPaymentDTO;

class YourGateway extends AbstractPaymentGateway
{
    protected function getGatewayPrefix(): string
    {
        return 'YOUR';
    }

    public function process(ProcessPaymentDTO $dto): array
    {
        // Your payment processing logic
        
        return [
            'success' => true,
            'payment_id' => $this->generatePaymentId(),
            'payment_status' => PaymentStatusEnum::COMPLETED,
            'transaction_details' => [
                'gateway' => 'your_gateway',
                'message' => 'Payment processed',
            ],
        ];
    }

    public function refund(string $transactionId, float $amount): array
    {
        // Your refund logic
    }

    public function getStatus(string $transactionId): string
    {
        // Your status check logic
    }
}
```

### Step 2: Register in Factory

Edit `app/Modules/Payment/Services/Gateways/PaymentGatewayFactory.php`:
```php
private const GATEWAYS = [
    'credit_card' => CreditCardGateway::class,
    'paypal' => PayPalGateway::class,
    'stripe' => StripeGateway::class,
    'your_gateway' => YourGateway::class, // Add this line
];
```

### Step 3: Configure Environment

Add to `.env`:
```env
YOUR_GATEWAY_API_KEY=your_api_key
YOUR_GATEWAY_SECRET=your_secret
```

### Step 4: Test
```php
$dto = new ProcessPaymentDTO(
    order_id: 1,
    payment_method: 'your_gateway',
    amount: 100.00
);

$payment = $paymentService->processPayment($dto);
```

That's it! No changes needed in controllers, services, or routes.

##  Security

- JWT token authentication
- Password hashing with bcrypt
- Input validation on all requests
- SQL injection prevention via Eloquent ORM
- CSRF protection
- Rate limiting on API endpoints

##  Business Rules

### Orders

- Order must have at least one item
- Order can only be deleted if no payments exist
- Order status transitions:
    - `pending` → `completed` ✅
    - `pending` → `cancelled` ✅
    - `completed` → `cancelled` ✅
    - `completed` → `pending` ❌
    - `cancelled` → any ❌

### Payments

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/orders/{order_id}/payment` | Process payment for order | Yes |
| GET | `/api/payments/{id}` | Get payment details | Yes |
| GET | `/api/payments/order/{order_id}` | Get order payments | Yes |

#### Payment Business Rules

- Payments can only be processed for `completed` orders
- Supported payment methods: `credit_card`, `paypal`, `stripe`
- Payment amount must be greater than 0
- Each payment generates unique payment ID

##  Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Authors

- **Mahmoud Shemaees** - *Initial work* - [Shemaees](https://github.com/Shemaees)
