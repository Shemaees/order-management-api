# API Documentation

Base URL: `http://order-management-api.test/api`

All API requests must include:
```
Accept: application/json
Content-Type: application/json
```

Protected endpoints require:
```
Authorization: Bearer {token}
```

---

##  Table of Contents

- [Authentication](#authentication)
- [Orders](#orders)
- [Payments](#payments)
- [Error Handling](#error-handling)

---

##  Authentication

### Register User

Create a new user account.

**Endpoint:** `POST /auth/register`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Success Response (201):**
```json
{
  "status": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "email_verified_at": null,
      "created_at": "2026-01-29 20:00:00",
      "updated_at": "2026-01-29 20:00:00"
    },
    "access": {
      "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
      "type": "Bearer",
      "expired_at": "2026-01-29T21:00:00.000000Z"
    }
  }
}
```

**Validation Errors (422):**
```json
{
  "status": false,
  "message": "Validation errors",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

---

### Login

Authenticate user and receive JWT token.

**Endpoint:** `POST /auth/login`

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Success Response (200):**
```json
{
  "status": true,
  "message": "User logged in successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "access": {
      "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
      "type": "Bearer",
      "expired_at": "2026-01-29T21:00:00.000000Z"
    }
  }
}
```

**Invalid Credentials (401):**
```json
{
  "status": false,
  "message": "Invalid credentials"
}
```

---

### Get User Profile

Get authenticated user information.

**Endpoint:** `GET /auth/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "status": true,
  "message": "User retrieved successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": null,
    "created_at": "2026-01-29 20:00:00",
    "updated_at": "2026-01-29 20:00:00"
  }
}
```

---

### Refresh Token

Get a new JWT token.

**Endpoint:** `POST /auth/refresh`

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "status": true,
  "message": "Token refreshed successfully",
  "data": {
    "access": {
      "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
      "type": "Bearer",
      "expired_at": "2026-01-29T21:00:00.000000Z"
    }
  }
}
```

---

### Logout

Invalidate current token.

**Endpoint:** `POST /auth/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (201):**
```json
{
  "status": true,
  "message": "User logged out successfully"
}
```

---

##  Orders

### List Orders

Get paginated list of user's orders with optional filters.

**Endpoint:** `GET /orders`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| status | string | No | Filter by status: `pending`, `confirmed`, `cancelled` |
| from_date | date | No | Filter from date (YYYY-MM-DD) |
| to_date | date | No | Filter to date (YYYY-MM-DD) |
| per_page | integer | No | Items per page (default: 15) |
| page | integer | No | Page number (default: 1) |

**Example Request:**
```
GET /order?status=pending&per_page=10&page=1
```

**Success Response (200):**
```json
{
  "status": true,
  "data": {
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "status": "pending",
        "total": 150.00,
        "sub_total": 140.00,
        "tax": 14.00,
        "discount": 4.00,
        "billing_address": "123 Billing St",
        "shipping_address": "456 Shipping Ave",
        "notes": "Please handle with care",
        "created_at": "2026-01-29 20:00:00",
        "updated_at": "2026-01-29 20:00:00",
        "items": [
          {
            "id": 1,
            "order_id": 1,
            "product": {
              "id": 1,
              "name": "Product A",
              "description": "Product A description",
              "stock": 100,
              "status": "active",
              "image": "product-a.jpg",
              "created_at": "2026-01-29 20:00:00"
            },
            "quantity": 2,
            "price": 50.00,
            "discount": 0.00,
            "total": 100.00
          }
        ]
      }
    ],
    "links": {
      "first": "http://localhost:8000/api/orders?page=1",
      "last": "http://localhost:8000/api/orders?page=2",
      "prev": null,
      "next": "http://localhost:8000/api/orders?page=2"
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 2,
      "per_page": 15,
      "to": 1,
      "total": 25,
      "path": "http://localhost:8000/api/orders"
    }
  }
}
```

---

### Create Order

Create a new order with items.

**Endpoint:** `POST /orders`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "price": 50.00
    },
    {
      "product_id": 2,
      "quantity": 1,
      "price": 30.00
    }
  ],
  "billing_address": "123 Billing St, City, Country",
  "shipping_address": "456 Shipping Ave, City, Country",
  "notes": "Please handle with care",
  "tax_percentage": 10,
  "discount_percentage": 5
}
```

**Field Descriptions:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| items | array | Yes | Array of order items |
| items.*.product_id | integer | Yes | Product ID |
| items.*.quantity | integer | Yes | Quantity (min: 1) |
| items.*.price | number | Yes | Product price |
| items.*.discount | number | No | Item discount (default: 0) |
| billing_address | string | Yes | Billing address |
| shipping_address | string | Yes | Shipping address |
| notes | string | No | Order notes |
| tax_percentage | number | No | Tax percentage (default: 0) |
| discount_percentage | number | No | Discount percentage (default: 0) |

**Success Response (200):**
```json
{
  "status": true,
  "data": {
    "id": 1,
    "user_id": 1,
    "status": "pending",
    "total": 140.85,
    "sub_total": 130.00,
    "tax": 13.00,
    "discount": 6.50,
    "billing_address": "123 Billing St",
    "shipping_address": "456 Shipping Ave",
    "notes": "Please handle with care",
    "items": [
      {
        "id": 1,
        "product": {
          "id": 1,
          "name": "Product A",
          "description": "Product A description",
          "stock": 100,
          "status": "active",
          "image": "product-a.jpg",
          "created_at": "2026-01-29 20:00:00"
        },
        "quantity": 2,
        "price": 50.00,
        "discount": 0.00,
        "total": 100.00
      },
      {
        "id": 2,
        "product": {
          "id": 2,
          "name": "Product B",
          "description": "Product B description",
          "stock": 50,
          "status": "active",
          "image": "product-b.jpg",
          "created_at": "2026-01-29 20:00:00"
        },
        "quantity": 1,
        "price": 30.00,
        "discount": 0.00,
        "total": 30.00
      }
    ]
  }
}
```

**Validation Errors (422):**
```json
{
  "status": false,
  "message": "Validation errors",
  "errors": {
    "items": ["The items field is required."],
    "items.0.product_id": ["The selected product is invalid."]
  }
}
```

---

### Get Order Details

Get specific order details.

**Endpoint:** `GET /orders/{order}`

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "status": true,
  "data": {
    "id": 1,
    "user_id": 1,
    "status": "confirmed",
    "total": 150.00,
    "items": [...],
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    }
  }
}
```

**Unauthorized (401):**
```json
{
  "status": false,
  "message": "Unauthorized"
}
```

**Not Found (404):**
```json
{
  "status": false,
  "message": "Order not found"
}
```

---

### Update Order

Update existing order.

**Endpoint:** `PUT /orders/{order}`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "notes": "Updated note",
  "items": [
    {
      "product_id": 1,
      "quantity": 3,
      "price": 50.00
    }
  ]
}
```

**Note:** All fields are optional. Only provided fields will be updated.

**Success Response (200):**
```json
{
  "status": true,
  "data": {
    "id": 1,
    "notes": "Updated note",
    "items": [...]
  }
}
```

**Business Rule Error (400):**
```json
{
  "status": false,
  "message": "Cannot update a cancelled order"
}
```

---

### Delete Order

Delete an order (only if no payments exist).

**Endpoint:** `DELETE /orders/{order}`

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "status": true,
  "data": "Order deleted successfully"
}
```

**Cannot Delete (400):**
```json
{
  "status": false,
  "message": "Cannot delete order with existing payments"
}
```

---

### Update Order Status

Update order status.

**Endpoint:** `PATCH /orders/{order}/status`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "status": "completed"
}
```

**Valid Statuses:**
- `pending`
- `completed`
- `cancelled`

**Status Transition Rules:**

| From | To | Allowed |
|------|----|---------| 
| pending | completed | ✅ |
| pending | cancelled | ✅ |
| completed | cancelled | ✅ |
| completed | pending | ❌ |
| cancelled | any | ❌ |

**Success Response (200):**
```json
{
  "status": true,
  "data": "Order status updated successfully"
}
```

**Invalid Transition (400):**
```json
{
  "status": false,
  "message": "Cannot change status of a cancelled order"
}
```

---

##  Payments

### Process Payment

Process a payment for an order.

**Endpoint:** `POST /orders/{order_id}/payment`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "payment_method": "credit_card",
  "amount": 150.00,
  "currency": "USD"
}
```

**Field Descriptions:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| payment_method | string | Yes | `credit_card`, `paypal`, or `stripe` |
| amount | number | Yes | Payment amount (min: 0.01) |
| currency | string | No | Currency code (default: USD) |

**Supported Payment Methods:**
- `credit_card`
- `paypal`
- `stripe`

**Success Response (200):**
```json
{
  "status": true,
  "message": "Payment processed successfully",
  "data": {
    "id": 1,
    "order_id": 1,
    "payment_id": "CC_ABC123XYZ456",
    "payment_method": "credit_card",
    "payment_status": "successful",
    "amount": 150.00,
    "currency": "USD",
    "transaction_details": {
      "gateway": "credit_card",
      "message": "Payment processed successfully",
      "card_last_four": "****1234",
      "reference_number": "REF_ABC123",
      "processed_at": "2026-01-29T20:30:00.000000Z"
    },
    "paid_at": "2026-01-29 20:30:00"
  }
}
```

**Payment Failed (400):**
```json
{
  "status": false,
  "message": "Payment failed",
  "data": {
    "id": 1,
    "payment_id": "CC_ABC123XYZ456",
    "payment_status": "failed",
    "transaction_details": {
      "gateway": "credit_card",
      "error": "Card declined",
      "error_code": "CC_DECLINED"
    }
  }
}
```

**Order Not Confirmed (400):**
```json
{
  "status": false,
  "message": "Payments can only be processed for confirmed orders"
}
```

**Validation Errors (422):**
```json
{
  "status": false,
  "message": "Validation errors",
  "errors": {
    "payment_method": [
      "Invalid payment method. Supported methods: credit_card, paypal, stripe"
    ],
    "amount": ["Payment amount must be greater than 0"]
  }
}
```

---

### Get Payment Details

Get specific payment details.

**Endpoint:** `GET /payments/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "status": true,
  "message": "Payment retrieved successfully",
  "data": {
    "id": 1,
    "order_id": 1,
    "payment_id": "CC_ABC123XYZ456",
    "payment_method": "credit_card",
    "payment_status": "successful",
    "amount": 150.00,
    "currency": "USD",
    "transaction_details": {...},
    "paid_at": "2026-01-29 20:30:00",
    "order": {
      "id": 1,
      "status": "confirmed",
      "total": 150.00
    }
  }
}
```

---

### Get Order Payments

Get all payments for a specific order.

**Endpoint:** `GET /payments/order/{order_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "status": true,
  "message": "Order payments retrieved successfully",
  "data": [
    {
      "id": 1,
      "order_id": 1,
      "payment_id": "CC_ABC123",
      "payment_method": "credit_card",
      "payment_status": "successful",
      "amount": 150.00,
      "currency": "USD",
      "transaction_details": {
        "gateway": "credit_card",
        "message": "Payment processed successfully",
        "card_last_four": "****1234"
      },
      "paid_at": "2026-01-29 20:30:00",
      "created_at": "2026-01-29 20:30:00",
      "updated_at": "2026-01-29 20:30:00"
    }
  ]
}
```

---

##  Error Handling

### Standard Error Response Format
```json
{
  "status": false,
  "message": "Error description",
  "errors": {} // Only for validation errors
}
```

### HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request / Business Logic Error |
| 401 | Unauthorized |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Server Error |

### Common Error Examples

**Validation Error (422):**
```json
{
  "status": false,
  "message": "Validation errors",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

**Unauthorized (401):**
```json
{
  "status": false,
  "message": "Unauthenticated"
}
```

**Business Logic Error (400):**
```json
{
  "status": false,
  "message": "Cannot delete order with existing payments"
}
```

**Not Found (404):**
```json
{
  "status": false,
  "message": "Resource not found"
}
```

---

##  Rate Limiting

API endpoints are rate-limited to:
- **60 requests per minute** per IP address

Rate limit headers:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
```

When exceeded:
```json
{
  "message": "Too Many Attempts."
}
```

---

##  Pagination

List endpoints return paginated results with Laravel's default structure:

**Complete Response Structure:**
```json
{
  "status": true,
  "message": "Success",
  "data": {
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "status": "pending",
        "total": 150.00,
        "items": [...]
      }
    ],
    "links": {
      "first": "http://localhost:8000/api/orders?page=1",
      "last": "http://localhost:8000/api/orders?page=7",
      "prev": null,
      "next": "http://localhost:8000/api/orders?page=2"
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 7,
      "per_page": 15,
      "to": 15,
      "total": 100,
      "path": "http://localhost:8000/api/orders"
    }
  }
}
```

**Pagination Fields Breakdown:**

| Field | Type | Description |
|-------|------|-------------|
| `data` | array | Array of items on current page |
| `links` | object | Navigation links |
| `links.first` | string | URL to first page |
| `links.last` | string | URL to last page |
| `links.prev` | string|null | URL to previous page |
| `links.next` | string|null | URL to next page |
| `meta` | object | Pagination metadata |
| `meta.current_page` | integer | Current page number |
| `meta.from` | integer | First item number on page |
| `meta.last_page` | integer | Last page number |
| `meta.per_page` | integer | Items per page |
| `meta.to` | integer | Last item number on page |
| `meta.total` | integer | Total number of items |
| `meta.path` | string | Base URL for pagination |

**Query Parameters:**
- `per_page` - Items per page (max: 100)
- `page` - Page number

---

##  Notes

- Dates should be in `YYYY-MM-DD` format
- Amounts are in decimal format with 2 decimal places
- JWT tokens expire after 60 minutes (configurable)
- Refresh tokens are valid for 2 weeks
