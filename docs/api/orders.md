# Order API Documentation

## Overview
The Order API provides comprehensive RESTful endpoints for managing orders in the B2B-B2C e-commerce system. It supports both authenticated user orders and guest checkout functionality.

## Base URL
```
/api/v1/orders
```

## Endpoints

### 1. List Orders
**GET** `/api/v1/orders`

Lists orders for the authenticated user with filtering and pagination.

**Authentication:** Required
**Parameters:**
- `status` (optional): Filter by order status
- `from_date` (optional): Filter orders from date (YYYY-MM-DD)
- `to_date` (optional): Filter orders to date (YYYY-MM-DD)
- `per_page` (optional): Items per page (default: 15)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "order_number": "ORD-2025-001",
      "status": {
        "value": "pending",
        "label": "Pending",
        "color": "warning",
        "icon": "heroicon-o-clock"
      },
      "total_amount": 299.99,
      "currency": "TRY",
      "created_at": "2025-01-15T10:30:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 73
  }
}
```

### 2. Get Order Details
**GET** `/api/v1/orders/{order}`

Retrieves detailed information about a specific order.

**Authentication:** Required
**Authorization:** User can view their own orders, admins can view all orders

**Response:**
```json
{
  "data": {
    "id": 1,
    "order_number": "ORD-2025-001",
    "status": {
      "value": "processing",
      "label": "Processing",
      "color": "info",
      "icon": "heroicon-o-cog"
    },
    "customer_type": "B2C",
    "subtotal_amount": 250.00,
    "discount_amount": 25.00,
    "tax_amount": 45.00,
    "shipping_cost": 15.00,
    "total_amount": 285.00,
    "currency": "TRY",
    "payment_status": "paid",
    "payment_method": "card",
    "items": [
      {
        "id": 1,
        "quantity": 2,
        "price": 125.00,
        "discounted_price": 112.50,
        "total_price": 225.00,
        "product": {
          "id": 10,
          "name": "Safety Helmet",
          "sku": "SH-001"
        }
      }
    ],
    "shipping_address": {
      "name": "John Doe",
      "email": "john@example.com",
      "address": "123 Main St",
      "city": "Istanbul",
      "country": "TR"
    },
    "created_at": "2025-01-15T10:30:00Z"
  }
}
```

### 3. Create Order (Checkout)
**POST** `/api/v1/orders`

Creates a new order from the user's cart through the checkout process.

**Authentication:** Required

**Request Body:**
```json
{
  "shipping_name": "John Doe",
  "shipping_email": "john@example.com",
  "shipping_phone": "+90555123456",
  "shipping_address": "123 Main Street",
  "shipping_city": "Istanbul",
  "shipping_country": "TR",
  "billing_same_as_shipping": true,
  "payment_method": "card",
  "payment_data": {
    "card_number": "4111111111111111",
    "card_expiry": "12/26",
    "card_cvv": "123",
    "cardholder_name": "John Doe"
  },
  "notes": "Please deliver during business hours"
}
```

**Response:**
```json
{
  "message": "Order created successfully",
  "data": {
    "id": 1,
    "order_number": "ORD-2025-001",
    "status": {
      "value": "pending",
      "label": "Pending"
    },
    "total_amount": 285.00
  }
}
```

### 4. Guest Checkout
**POST** `/api/v1/orders/guest-checkout`

Processes checkout for guests without requiring authentication.

**Authentication:** Not required

**Request Body:**
```json
{
  "cart_data": {
    "items": [
      {
        "product_id": 10,
        "product_variant_id": 25,
        "quantity": 2,
        "price": 125.00,
        "discounted_price": 112.50
      }
    ],
    "total_amount": 225.00,
    "subtotal_amount": 225.00
  },
  "shipping_name": "Jane Doe",
  "shipping_email": "jane@example.com",
  "shipping_address": "456 Oak Street",
  "shipping_city": "Ankara",
  "shipping_country": "TR",
  "billing_same_as_shipping": true,
  "payment_method": "card",
  "payment_data": {
    "card_number": "4111111111111111",
    "card_expiry": "12/26",
    "card_cvv": "123",
    "cardholder_name": "Jane Doe"
  }
}
```

### 5. Update Order Status
**PATCH** `/api/v1/orders/{order}/status`

Updates the status of an order (admin only).

**Authentication:** Required
**Authorization:** Admin, Manager, or Customer Service roles

**Request Body:**
```json
{
  "status": "shipped",
  "notes": "Order dispatched via DHL",
  "tracking_number": "DHL123456789",
  "shipping_carrier": "DHL",
  "estimated_delivery_at": "2025-01-20",
  "notify_customer": true
}
```

### 6. Cancel Order
**POST** `/api/v1/orders/{order}/cancel`

Cancels an order if it's in a cancellable state.

**Authentication:** Required
**Authorization:** Users can cancel their own orders, admins can cancel any order

**Request Body:**
```json
{
  "reason": "Customer requested cancellation"
}
```

### 7. Process Payment
**POST** `/api/v1/orders/{order}/payment`

Processes payment for an unpaid order.

**Authentication:** Required
**Authorization:** Users can pay for their own orders

**Request Body:**
```json
{
  "payment_method": "card",
  "payment_amount": 285.00,
  "payment_data": {
    "card_number": "4111111111111111",
    "card_expiry": "12/26",
    "card_cvv": "123",
    "cardholder_name": "John Doe"
  }
}
```

### 8. Order Tracking
**GET** `/api/v1/orders/{order_number}/tracking`

Retrieves tracking information for an order using the order number.

**Authentication:** Not required (but order access is validated)

**Response:**
```json
{
  "data": {
    "order_number": "ORD-2025-001",
    "status": "shipped",
    "status_label": "Shipped",
    "tracking_number": "DHL123456789",
    "shipping_carrier": "DHL",
    "estimated_delivery": "2025-01-20T00:00:00Z",
    "shipped_at": "2025-01-18T14:30:00Z",
    "history": [
      {
        "status": "pending",
        "status_label": "Pending",
        "notes": "Order created",
        "changed_at": "2025-01-15T10:30:00Z"
      },
      {
        "status": "processing",
        "status_label": "Processing",
        "notes": "Payment confirmed",
        "changed_at": "2025-01-15T10:35:00Z"
      }
    ]
  }
}
```

### 9. Estimate Checkout
**POST** `/api/v1/orders/estimate-checkout`

Estimates costs for checkout before creating an order.

**Authentication:** Required (for cart access)

**Request Body:**
```json
{
  "shipping_country": "TR",
  "shipping_city": "Istanbul",
  "payment_method": "card"
}
```

**Response:**
```json
{
  "data": {
    "subtotal": 250.00,
    "discount": 25.00,
    "shipping_cost": 15.00,
    "tax_amount": 45.00,
    "final_total": 285.00,
    "currency": "TRY",
    "estimated": true
  }
}
```

### 10. User Order Summary
**GET** `/api/v1/orders/user/summary`

Retrieves order statistics and summary for the authenticated user.

**Authentication:** Required

**Response:**
```json
{
  "data": {
    "total_orders": 15,
    "total_spent": 4250.00,
    "recent_orders": [
      {
        "id": 1,
        "order_number": "ORD-2025-001",
        "status": "delivered",
        "total_amount": 285.00,
        "created_at": "2025-01-15T10:30:00Z"
      }
    ],
    "status_counts": {
      "pending": 2,
      "processing": 1,
      "shipped": 3,
      "delivered": 8,
      "cancelled": 1
    }
  }
}
```

## Error Responses

### Validation Error (422)
```json
{
  "message": "Checkout validation failed",
  "errors": [
    "Shipping name is required",
    "Invalid card number"
  ],
  "warnings": [
    "International shipping may take 7-14 business days"
  ]
}
```

### Authorization Error (403)
```json
{
  "message": "This action is unauthorized."
}
```

### Not Found Error (404)
```json
{
  "message": "Order not found."
}
```

### Business Logic Error (400)
```json
{
  "message": "Order cannot be cancelled at this stage"
}
```

## Order Status Flow

1. **Pending** → Processing, Cancelled
2. **Processing** → Shipped, Cancelled
3. **Shipped** → Delivered
4. **Delivered** → Final state
5. **Cancelled** → Final state

## Payment Methods

- **card**: Credit/debit card payment
- **credit**: B2B credit payment (authenticated users only)
- **bank_transfer**: Bank transfer payment

## Rate Limiting

- Authenticated requests: 60 requests per minute
- Guest checkout: 10 requests per minute
- Order tracking: 30 requests per minute