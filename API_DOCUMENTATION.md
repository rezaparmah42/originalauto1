# Original Shargh Mobile API Documentation

## Authentication

### Headers

- `Content-Type: application/json`
- `Accept: application/json`
- `Authorization: Bearer <token>` for protected endpoints

### Login

POST `/api/auth/login`

Request:
```json
{
  "login": "09123456789",
  "password": "secret123"
}
```

Response:
```json
{
  "status": "ok",
  "token": "<bearer-token>",
  "user": {
    "id": 1,
    "name": "Ali",
    "phone": "09123456789",
    "email": "ali@example.com",
    "role": "customer"
  }
}
```

### Register

POST `/api/auth/register`

Request:
```json
{
  "name": "Ali",
  "phone": "09123456789",
  "email": "ali@example.com",
  "password": "secret123",
  "password_confirm": "secret123"
}
```

Response:
```json
{
  "status": "ok",
  "token": "<bearer-token>",
  "user": {
    "id": 1,
    "name": "Ali",
    "phone": "09123456789",
    "email": "ali@example.com",
    "role": "customer"
  }
}
```

### Logout

POST `/api/auth/logout`

Request: empty body

Response:
```json
{
  "status": "ok",
  "message": "Logged out successfully"
}
```

## Vehicles

### List vehicles

GET `/api/vehicles`

Headers:
- `Authorization: Bearer <token>`

Response:
```json
{
  "status": "ok",
  "vehicles": [
    {
      "id": 10,
      "brand_name": "Toyota",
      "model": "Corolla",
      "year": "2019",
      "engine": "1.8L",
      "vin": "XXXXXXXXXXXXXXXXX",
      "mileage": 45000,
      "created_at": "2026-08-07 12:00:00"
    }
  ]
}
```

### Get vehicle profile

GET `/api/vehicles/{id}`

Headers:
- `Authorization: Bearer <token>`

Response:
```json
{
  "status": "ok",
  "vehicle": {
    "id": 10,
    "brand_name": "Toyota",
    "model": "Corolla",
    "year": "2019",
    "engine": "1.8L",
    "vin": "XXXXXXXXXXXXXXXXX",
    "mileage": 45000,
    "maintenance_status": "healthy",
    "service_history": [],
    "diagnostic_history": []
  }
}
```

## Diagnostics

### List diagnostics

GET `/api/diagnostics`

Headers:
- `Authorization: Bearer <token>`

Response:
```json
{
  "status": "ok",
  "diagnostics": []
}
```

### Analyze codes

POST `/api/diagnostics/analyze`

Request:
```json
{
  "vehicle_id": 10,
  "dtc_codes": ["P0301", "P0171"]
}
```

Response:
```json
{
  "status": "ok",
  "vehicle_id": 10,
  "analysis": [
    {
      "code": "P0301",
      "title": "Misfire Detected",
      "description": "Cylinder misfire",
      "severity": "high",
      "possible_causes": "Spark plug, fuel system",
      "recommended_actions": "Check spark plugs"
    }
  ],
  "health_score": 70,
  "recommendations": [],
  "summary": "..."
}
```

## Repairs

### List repairs

GET `/api/repairs`

Headers:
- `Authorization: Bearer <token>`

Response:
```json
{
  "status": "ok",
  "repairs": []
}
```

### Get repair details

GET `/api/repairs/{id}`

Headers:
- `Authorization: Bearer <token>`

Response:
```json
{
  "status": "ok",
  "repair": {
    "id": 15,
    "booking_id": 20,
    "diagnosis": "Brake pad wear",
    "repair_notes": "Replace front pads",
    "cost": 250,
    "status": "in_progress"
  }
}
```

## Orders

### List orders

GET `/api/orders`

Headers:
- `Authorization: Bearer <token>`

Response:
```json
{
  "status": "ok",
  "orders": []
}
```

### Get order details

GET `/api/orders/{id}`

Headers:
- `Authorization: Bearer <token>`

Response:
```json
{
  "status": "ok",
  "order": {
    "id": 100,
    "user_id": 1,
    "total": 500,
    "status": "pending",
    "payment_status": "pending"
  },
  "items": []
}
```

## Payments

### Get payment details

GET `/api/payments/{id}`

Headers:
- `Authorization: Bearer <token>`

Response:
```json
{
  "status": "ok",
  "payment": {
    "id": 30,
    "order_id": 100,
    "amount": 500,
    "status": "paid"
  }
}
```

## Notifications

### List notifications

GET `/api/notifications`

Headers:
- `Authorization: Bearer <token>`

Response:
```json
{
  "status": "ok",
  "notifications": []
}
```

### Mark notification read

POST `/api/notifications/read`

Request:
```json
{
  "notification_id": 42
}
```

Response:
```json
{
  "status": "ok",
  "message": "Notification marked as read"
}
```
