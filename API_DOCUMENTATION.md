# Admin Panel API Documentation

This documentation provides details about the RESTful API endpoints available for the Admin Panel mobile application.

## Authentication

All API requests require authentication using Laravel Sanctum tokens.

### Login
```
POST /api/admin/login
```

**Request Body:**
```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "admin": {
      "id": 1,
      "name": "Admin Name",
      "email": "admin@example.com",
      // ... other admin fields
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz"
  }
}
```

### Logout
```
POST /api/admin/logout
```

**Headers:**
```
Authorization: Bearer YOUR_API_TOKEN
```

## Admin Profile

### Get Profile
```
GET /api/admin/profile
```

**Headers:**
```
Authorization: Bearer YOUR_API_TOKEN
```

## Customers

### Get All Customers
```
GET /api/admin/customers
```

**Headers:**
```
Authorization: Bearer YOUR_API_TOKEN
```

### Create Customer
```
POST /api/admin/customers
```

**Headers:**
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: multipart/form-data
```

**Request Body:**
```
name: Customer Name
father_name: Father Name
// ... other customer fields
```

### Get Customer
```
GET /api/admin/customers/{id}
```

**Headers:**
```
Authorization: Bearer YOUR_API_TOKEN
```

### Update Customer
```
PUT /api/admin/customers/{id}
```

**Headers:**
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: multipart/form-data
```

### Delete Customer
```
DELETE /api/admin/customers/{id}
```

**Headers:**
```
Authorization: Bearer YOUR_API_TOKEN
```

## Villages

### Get All Villages
```
GET /api/admin/villages
```

### Create Village
```
POST /api/admin/villages
```

### Get Village
```
GET /api/admin/villages/{id}
```

### Update Village
```
PUT /api/admin/villages/{id}
```

### Delete Village
```
DELETE /api/admin/villages/{id}
```

## Supports

### Get All Supports
```
GET /api/admin/supports
```

### Create Support
```
POST /api/admin/supports
```

### Get Support
```
GET /api/admin/supports/{id}
```

### Update Support
```
PUT /api/admin/supports/{id}
```

### Delete Support
```
DELETE /api/admin/supports/{id}
```

## Support Types

### Get All Support Types
```
GET /api/admin/support-types
```

### Create Support Type
```
POST /api/admin/support-types
```

### Get Support Type
```
GET /api/admin/support-types/{id}
```

### Update Support Type
```
PUT /api/admin/support-types/{id}
```

### Delete Support Type
```
DELETE /api/admin/support-types/{id}
```

## Support Categories

### Get All Support Categories
```
GET /api/admin/support-categories
```

### Create Support Category
```
POST /api/admin/support-categories
```

### Get Support Category
```
GET /api/admin/support-categories/{id}
```

### Update Support Category
```
PUT /api/admin/support-categories/{id}
```

### Delete Support Category
```
DELETE /api/admin/support-categories/{id}
```

## Error Responses

All error responses follow this format:
```json
{
  "status": "error",
  "message": "Error description"
}
```

Common HTTP status codes:
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 500: Internal Server Error