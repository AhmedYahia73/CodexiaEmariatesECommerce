# 📦 ECommerce API Documentation

> **Base URL:** `http://your-domain.com/api`
> **Authentication:** Laravel Sanctum (Bearer Token)
> **Content-Type:** `application/json` *(unless uploading files → use `multipart/form-data`)*
> **Localization:** Most endpoints require a `local` query param → `en` or `ar`

---

## 📌 Table of Contents

1. [Authentication](#1-authentication)
2. [Public – User Endpoints](#2-public--user-endpoints-no-auth-required)
3. [Admin Endpoints](#3-admin-endpoints-requiresauth--admin-role)
   - [Dashboard / Home](#31-dashboard--home)
   - [Admins Management](#32-admins-management)
   - [Users Management](#33-users-management)
   - [Categories](#34-categories)
   - [Products](#35-products)
   - [Orders (Admin)](#36-orders-admin)
   - [Coupons](#37-coupons)
   - [Cities](#38-cities)
   - [Zones](#39-zones)
   - [Payment Methods](#310-payment-methods)
   - [Services](#311-services)
   - [About (Admin)](#312-about-admin)
   - [Settings](#313-settings)
   - [Contact (Admin)](#314-contact-admin)
4. [Authenticated User Endpoints](#4-authenticated-user-endpoints-requiresauth--user-role)
   - [Addresses](#41-addresses)
   - [Cart](#42-cart)
   - [Orders (User)](#43-orders-user)

---

## 🔐 Authentication Header

For all protected routes, add this header:

```
Authorization: Bearer {token}
```

---

## 1. Authentication

### 🔑 Login

| Field  | Value          |
|--------|----------------|
| **URL** | `POST /api/login` |
| **Auth** | None |
| **Content-Type** | `application/json` |

#### Request Body

| Field      | Type   | Required | Description       |
|------------|--------|----------|-------------------|
| `email`    | string | ✅        | User email        |
| `password` | string | ✅        | User password     |

#### Example Request

```json
{
  "email": "admin@example.com",
  "password": "password123"
}
```

#### Response `200 OK`

```json
{
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "role": "admin",
    "phone": "+1234567890",
    "image": "users/photo.jpg",
    "email_verified_at": "2024-01-01T00:00:00.000000Z",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  },
  "token": "1|abcdefghijklmnopqrstuvwxyz"
}
```

#### Error Responses

| Code  | Description            |
|-------|------------------------|
| `400` | Validation error       |
| `401` | Invalid credentials    |

---

## 2. Public – User Endpoints (No Auth Required)

### 2.1 Get All Products

| Field   | Value                               |
|---------|-------------------------------------|
| **URL** | `GET /api/user/home/all_products`   |
| **Auth** | None                               |

#### Query Params

| Param   | Type   | Required | Description               |
|---------|--------|----------|---------------------------|
| `local` | string | ✅        | Language: `en` or `ar`    |

#### Response `200 OK`

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "Product Name",
      "description": "Product description",
      "image": "http://domain.com/storage/products/img.jpg",
      "price": 100.00,
      "discount": 10.00,
      "final_price": 90.00,
      "category_id": 2,
      "category_name": "Category Name"
    }
  ],
  "last_page": 5,
  "per_page": 15,
  "total": 70
}
```

---

### 2.2 Get Parent Categories

| Field   | Value                                    |
|---------|------------------------------------------|
| **URL** | `GET /api/user/home/parent-categories`   |
| **Auth** | None                                    |

#### Query Params

| Param   | Type   | Required | Description            |
|---------|--------|----------|------------------------|
| `local` | string | ✅        | Language: `en` or `ar` |

#### Response `200 OK`

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "Electronics",
      "description": "Electronic products",
      "image": "http://domain.com/storage/categories/img.jpg"
    }
  ],
  "last_page": 1,
  "total": 5
}
```

---

### 2.3 Get Sub-Categories

| Field   | Value                                 |
|---------|---------------------------------------|
| **URL** | `GET /api/user/home/sub-categories`   |
| **Auth** | None                                 |

#### Query Params

| Param         | Type    | Required | Description                       |
|---------------|---------|----------|-----------------------------------|
| `local`       | string  | ✅        | Language: `en` or `ar`            |
| `category_id` | integer | ✅        | ID of the parent category         |

#### Response `200 OK`

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 3,
      "name": "Phones",
      "description": "Mobile phones",
      "image": "http://domain.com/storage/categories/img.jpg"
    }
  ],
  "total": 3
}
```

---

### 2.4 Get Products by Category

| Field   | Value                         |
|---------|-------------------------------|
| **URL** | `GET /api/user/home/products` |
| **Auth** | None                         |

#### Query Params

| Param         | Type    | Required | Description                 |
|---------------|---------|----------|-----------------------------|
| `local`       | string  | ✅        | Language: `en` or `ar`      |
| `category_id` | integer | ✅        | ID of the category          |

#### Response `200 OK`

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "iPhone 15",
      "description": "Latest iPhone",
      "image": "http://domain.com/storage/products/img.jpg",
      "price": 999.00,
      "discount": 50.00,
      "final_price": 949.00
    }
  ],
  "total": 10
}
```

---

### 2.5 Get Product Details

| Field   | Value                                  |
|---------|----------------------------------------|
| **URL** | `GET /api/user/home/product/{id}`      |
| **Auth** | None                                  |

#### URL Params

| Param | Type    | Required | Description |
|-------|---------|----------|-------------|
| `id`  | integer | ✅        | Product ID  |

#### Query Params

| Param   | Type   | Required | Description            |
|---------|--------|----------|------------------------|
| `local` | string | ✅        | Language: `en` or `ar` |

#### Response `200 OK`

```json
{
  "product": {
    "id": 1,
    "name": "iPhone 15",
    "description": "Latest iPhone",
    "image": "http://domain.com/storage/products/img.jpg",
    "price": 999.00,
    "discount": 50.00,
    "final_price": 949.00,
    "category": "Phones",
    "variations": [
      {
        "id": 1,
        "name": "Color",
        "options": [
          { "id": 1, "name": "Black", "price": 0 },
          { "id": 2, "name": "White", "price": 10 }
        ]
      }
    ]
  },
  "gallery": [
    { "id": 1, "image": "http://domain.com/storage/products/gallery/img1.jpg" },
    { "id": 2, "image": "http://domain.com/storage/products/gallery/img2.jpg" }
  ]
}
```

---

### 2.6 Get Payment Methods List (Public)

| Field   | Value                       |
|---------|-----------------------------|
| **URL** | `GET /api/user/orders/lists` |
| **Auth** | None                       |

#### Query Params

| Param   | Type   | Required | Description            |
|---------|--------|----------|------------------------|
| `local` | string | ✅        | Language: `en` or `ar` |

#### Response `200 OK`

```json
{
  "payment_methods": [
    {
      "name": "Credit Card",
      "description": "Pay with credit card",
      "icon": "http://domain.com/storage/payment_methods/card.png"
    }
  ]
}
```

---

### 2.7 Get Footer Data

| Field   | Value                    |
|---------|--------------------------|
| **URL** | `GET /api/user/footer`   |
| **Auth** | None                    |

#### Query Params

| Param   | Type   | Required | Description            |
|---------|--------|----------|------------------------|
| `local` | string | ✅        | Language: `en` or `ar` |

#### Response `200 OK`

```json
{
  "data": {
    "brand_name": "My Store",
    "phone": "+971501234567",
    "wattsapp": "+971501234567",
    "email": "info@store.com",
    "address": "Dubai, UAE",
    "lat": "25.2048",
    "lng": "55.2708",
    "facebook": "https://facebook.com/store",
    "insta": "https://instagram.com/store",
    "tiktok": "https://tiktok.com/@store",
    "ios_app": "https://apps.apple.com/...",
    "android_app": "https://play.google.com/...",
    "logo": "http://domain.com/storage/settings/logo.png",
    "min_order": 50
  }
}
```

---

### 2.8 Get About Page

| Field   | Value                  |
|---------|------------------------|
| **URL** | `GET /api/user/about`  |
| **Auth** | None                  |

#### Query Params

| Param   | Type   | Required | Description            |
|---------|--------|----------|------------------------|
| `local` | string | ✅        | Language: `en` or `ar` |

#### Response `200 OK`

```json
{
  "data": {
    "title": "About Our Store",
    "content": "We are a leading e-commerce platform...",
    "image": "http://domain.com/storage/about/img.jpg"
  }
}
```

---

### 2.9 Get Services

| Field   | Value                     |
|---------|---------------------------|
| **URL** | `GET /api/user/services`  |
| **Auth** | None                     |

#### Query Params

| Param   | Type   | Required | Description            |
|---------|--------|----------|------------------------|
| `local` | string | ✅        | Language: `en` or `ar` |

#### Response `200 OK`

```json
{
  "data": [
    {
      "id": 1,
      "name": "Free Delivery",
      "description": "Free delivery on orders above 100",
      "icon": "http://domain.com/storage/services/delivery.png"
    }
  ]
}
```

---

### 2.10 Submit Contact Form

| Field   | Value                   |
|---------|-------------------------|
| **URL** | `POST /api/user/contact` |
| **Auth** | None                   |
| **Content-Type** | `application/json` |

#### Request Body

| Field     | Type   | Required | Description          |
|-----------|--------|----------|----------------------|
| `f_name`  | string | ✅        | First name           |
| `l_name`  | string | ✅        | Last name            |
| `phone`   | string | ✅        | Phone number         |
| `email`   | string | ✅        | Email address        |
| `title`   | string | ✅        | Subject / title      |
| `content` | string | ✅        | Message content      |

#### Example Request

```json
{
  "f_name": "John",
  "l_name": "Doe",
  "phone": "+971501234567",
  "email": "john@example.com",
  "title": "Inquiry about order",
  "content": "I would like to know the status of my order..."
}
```

#### Response `200 OK`

```json
{
  "success": "You contact success"
}
```

---

## 3. Admin Endpoints (Requires Auth + Admin Role)

> All admin routes are prefixed with `/api/admin`
> Required header: `Authorization: Bearer {admin_token}`

---

### 3.1 Dashboard / Home

#### Get Dashboard Stats

| Field   | Value               |
|---------|---------------------|
| **URL** | `GET /api/admin/home` |
| **Auth** | Admin Bearer Token |

#### Response `200 OK`

```json
{
  "products": 150,
  "categories": 20,
  "users": 500,
  "year": 2025,
  "monthly_orders": [
    { "month": 1, "orders_count": 45, "orders_total": 12500.00 },
    { "month": 2, "orders_count": 60, "orders_total": 18000.00 }
  ],
  "best_products": [
    { "id": 1, "product": { "en": "iPhone 15", "ar": "ايفون 15" }, "count": 120 }
  ]
}
```

---

### 3.2 Admins Management

#### Get All Admins (Paginated)

| Field   | Value                    |
|---------|--------------------------|
| **URL** | `GET /api/admin/admins`  |
| **Auth** | Admin Bearer Token      |

#### Response `200 OK`

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "Super Admin",
      "email": "admin@store.com",
      "phone": "+971501234567",
      "role": "admin",
      "image": "users/admin.jpg",
      "email_verified_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "total": 5,
  "per_page": 15
}
```

---

#### Get Admins List (Dropdown)

| Field   | Value                         |
|---------|-------------------------------|
| **URL** | `GET /api/admin/admins/list`  |
| **Auth** | Admin Bearer Token           |

#### Response `200 OK`

```json
[
  { "id": 1, "name": "Super Admin", "email": "admin@store.com", "phone": "+971" }
]
```

---

#### Create Admin

| Field   | Value                    |
|---------|--------------------------|
| **URL** | `POST /api/admin/admins` |
| **Auth** | Admin Bearer Token      |
| **Content-Type** | `multipart/form-data` |

#### Request Body

| Field      | Type   | Required | Description             |
|------------|--------|----------|-------------------------|
| `name`     | string | ✅        | Admin full name         |
| `email`    | string | ✅        | Unique email address    |
| `password` | string | ✅        | Min 8 characters        |
| `phone`    | string | ✅        | Phone number            |
| `image`    | file   | ❌        | Profile image (jpg/png) |

#### Response `201 Created`

```json
{
  "id": 5,
  "name": "New Admin",
  "email": "newadmin@store.com",
  "phone": "+971501234567",
  "role": "admin",
  "image": "users/newadmin.jpg"
}
```

---

#### Get Admin by ID

| Field   | Value                       |
|---------|-----------------------------|
| **URL** | `GET /api/admin/admins/{id}` |
| **Auth** | Admin Bearer Token         |

#### URL Params

| Param | Type    | Required | Description |
|-------|---------|----------|-------------|
| `id`  | integer | ✅        | Admin ID    |

#### Response `200 OK`

```json
{
  "id": 1,
  "name": "Admin Name",
  "email": "admin@store.com",
  "phone": "+971501234567",
  "role": "admin",
  "image": "users/admin.jpg"
}
```

---

#### Update Admin

| Field   | Value                        |
|---------|------------------------------|
| **URL** | `POST /api/admin/admins/{id}` |
| **Auth** | Admin Bearer Token          |
| **Content-Type** | `multipart/form-data` |

#### URL Params

| Param | Type    | Required | Description |
|-------|---------|----------|-------------|
| `id`  | integer | ✅        | Admin ID    |

#### Request Body (all optional)

| Field      | Type   | Required | Description         |
|------------|--------|----------|---------------------|
| `name`     | string | ❌        | Admin full name     |
| `email`    | string | ❌        | Unique email        |
| `password` | string | ❌        | Min 8 characters   |
| `phone`    | string | ❌        | Phone number        |
| `image`    | file   | ❌        | Profile image       |

#### Response `200 OK`

```json
{
  "id": 1,
  "name": "Updated Admin",
  "email": "admin@store.com",
  "phone": "+971501234567"
}
```

---

#### Delete Admin

| Field   | Value                          |
|---------|--------------------------------|
| **URL** | `DELETE /api/admin/admins/{id}` |
| **Auth** | Admin Bearer Token            |

#### Response `200 OK`

```json
{ "message": "Deleted successfully" }
```

---

#### Toggle Admin Status

| Field   | Value                                      |
|---------|--------------------------------------------|
| **URL** | `POST /api/admin/admins/{id}/change-status` |
| **Auth** | Admin Bearer Token                        |

#### Response `200 OK`

```json
{ "active": true }
```

---

### 3.3 Users Management

> Same endpoints as Admins but prefixed with `/api/admin/users`

| Method   | URL                                    | Description              |
|----------|----------------------------------------|--------------------------|
| `GET`    | `/api/admin/users`                     | List all users (paginated) |
| `GET`    | `/api/admin/users/list`                | Dropdown list            |
| `POST`   | `/api/admin/users`                     | Create user              |
| `GET`    | `/api/admin/users/{id}`               | Get user by ID           |
| `POST`   | `/api/admin/users/{id}`               | Update user              |
| `DELETE` | `/api/admin/users/{id}`               | Delete user              |
| `POST`   | `/api/admin/users/{id}/change-status` | Toggle user status       |

#### Create/Update User Body

| Field      | Type   | Required (Create) | Description         |
|------------|--------|-------------------|---------------------|
| `name`     | string | ✅                 | User full name      |
| `email`    | string | ✅                 | Unique email        |
| `password` | string | ✅                 | Min 8 characters    |
| `phone`    | string | ✅                 | Phone number        |
| `image`    | file   | ❌                 | Profile image       |

---

### 3.4 Categories

| Method   | URL                                          | Description               |
|----------|----------------------------------------------|---------------------------|
| `GET`    | `/api/admin/categories`                      | List all (paginated)      |
| `GET`    | `/api/admin/categories/list`                 | Active categories (dropdown) |
| `POST`   | `/api/admin/categories`                      | Create category           |
| `GET`    | `/api/admin/categories/{id}`                | Get by ID                 |
| `POST`   | `/api/admin/categories/{id}`                | Update                    |
| `DELETE` | `/api/admin/categories/{id}`                | Delete                    |
| `POST`   | `/api/admin/categories/{id}/change-status`  | Toggle status             |

#### Create/Update Category Body

| Field             | Type    | Required (Create) | Description                         |
|-------------------|---------|-------------------|-------------------------------------|
| `name`            | object  | ✅                 | Multilingual object                 |
| `name.en`         | string  | ✅                 | Name in English                     |
| `name.ar`         | string  | ✅                 | Name in Arabic                      |
| `description`     | object  | ✅                 | Multilingual object                 |
| `description.en`  | string  | ✅                 | Description in English              |
| `description.ar`  | string  | ✅                 | Description in Arabic               |
| `image`           | file    | ❌                 | Category image                      |
| `category_id`     | integer | ❌                 | Parent category ID (for subcategory)|
| `status`          | boolean | ❌                 | Active/Inactive (default: true)     |

#### Example Request (Create Category)

```json
{
  "name": {
    "en": "Electronics",
    "ar": "إلكترونيات"
  },
  "description": {
    "en": "Electronic products and gadgets",
    "ar": "المنتجات الإلكترونية والأجهزة"
  },
  "status": 1
}
```

#### Get Category Response `200 OK`

```json
{
  "id": 1,
  "name": { "en": "Electronics", "ar": "إلكترونيات" },
  "description": { "en": "Electronic products", "ar": "منتجات إلكترونية" },
  "image": "categories/electronics.jpg",
  "status": 1,
  "category_id": null,
  "parentCategory": null,
  "subcategories": [
    { "id": 2, "name": { "en": "Phones", "ar": "هواتف" } }
  ]
}
```

---

### 3.5 Products

| Method   | URL                                              | Description                    |
|----------|--------------------------------------------------|--------------------------------|
| `GET`    | `/api/admin/products?local=en`                  | List all (paginated)           |
| `GET`    | `/api/admin/products/list?local=en`             | Dropdown list                  |
| `GET`    | `/api/admin/products/categories?local=en`       | Categories for products dropdown |
| `POST`   | `/api/admin/products`                            | Create product                 |
| `GET`    | `/api/admin/products/{id}`                      | Get by ID                      |
| `POST`   | `/api/admin/products/{id}`                      | Update product                 |
| `DELETE` | `/api/admin/products/{id}`                      | Delete product                 |
| `POST`   | `/api/admin/products/{id}/change-status`        | Toggle status                  |
| `POST`   | `/api/admin/products/{id}/gallery`              | Add gallery images             |
| `DELETE` | `/api/admin/products/gallery/{id}`              | Delete gallery image           |
| `POST`   | `/api/admin/products/{id}/variations`           | Add variation                  |
| `DELETE` | `/api/admin/products/variations/{id}`           | Delete variation               |
| `POST`   | `/api/admin/products/variations/{variationId}/options` | Add option to variation |
| `DELETE` | `/api/admin/products/options/{id}`              | Delete option                  |

#### Create Product Body (`multipart/form-data`)

| Field                            | Type    | Required | Description                      |
|----------------------------------|---------|----------|----------------------------------|
| `name`                           | object  | ✅        | Multilingual name                |
| `name[en]`                       | string  | ✅        | Name in English                  |
| `name[ar]`                       | string  | ✅        | Name in Arabic                   |
| `description`                    | object  | ✅        | Multilingual description         |
| `description[en]`                | string  | ✅        | Description in English           |
| `description[ar]`                | string  | ✅        | Description in Arabic            |
| `category_id`                    | integer | ✅        | Category ID                      |
| `image`                          | file    | ✅        | Main product image               |
| `price`                          | numeric | ✅        | Base price (min: 0)              |
| `discount`                       | numeric | ❌        | Discount amount                  |
| `discount_from`                  | date    | ❌        | Discount start date              |
| `discount_to`                    | date    | ❌        | Discount end date                |
| `status`                         | boolean | ✅        | Active/Inactive                  |
| `variations`                     | array   | ❌        | Product variations               |
| `variations[0][name][en]`       | string  | ✅*       | Variation name (English)         |
| `variations[0][name][ar]`       | string  | ✅*       | Variation name (Arabic)          |
| `variations[0][options][0][name][en]` | string | ✅*  | Option name (English)            |
| `variations[0][options][0][name][ar]` | string | ✅*  | Option name (Arabic)             |
| `variations[0][options][0][price]`    | numeric | ✅* | Option additional price          |
| `gallery`                        | array   | ❌        | Gallery images array             |
| `gallery[0]`                     | file    | ❌        | Gallery image file               |

*Required when variations/options array is provided

#### Get Product Response `200 OK`

```json
{
  "id": 1,
  "name": { "en": "iPhone 15", "ar": "ايفون 15" },
  "description": { "en": "Latest iPhone", "ar": "أحدث ايفون" },
  "image": "products/iphone.jpg",
  "price": 999.00,
  "discount": 50.00,
  "discount_from": "2024-01-01",
  "discount_to": "2024-12-31",
  "status": 1,
  "category_id": 2,
  "category": {
    "id": 2,
    "name": { "en": "Phones", "ar": "هواتف" }
  },
  "variations": [
    {
      "id": 1,
      "name": { "en": "Color", "ar": "اللون" },
      "options": [
        { "id": 1, "name": { "en": "Black", "ar": "أسود" }, "price": 0 },
        { "id": 2, "name": { "en": "White", "ar": "أبيض" }, "price": 10 }
      ]
    }
  ],
  "gallery": [
    { "id": 1, "image": "products/gallery/img1.jpg" }
  ]
}
```

#### Add Gallery Images Body (`multipart/form-data`)

| Field       | Type  | Required | Description         |
|-------------|-------|----------|---------------------|
| `images`    | array | ✅        | Array of image files |
| `images[0]` | file  | ✅        | Image file          |

#### Add Variation Body

| Field     | Type   | Required | Description              |
|-----------|--------|----------|--------------------------|
| `name`    | object | ✅        | Multilingual name        |
| `name.en` | string | ✅        | Variation name (English) |
| `name.ar` | string | ✅        | Variation name (Arabic)  |

#### Add Option Body

| Field     | Type    | Required | Description              |
|-----------|---------|----------|--------------------------|
| `name`    | object  | ✅        | Multilingual name        |
| `name.en` | string  | ✅        | Option name (English)    |
| `name.ar` | string  | ✅        | Option name (Arabic)     |
| `price`   | numeric | ✅        | Additional price (min:0) |

---

### 3.6 Orders (Admin)

| Method | URL                                        | Description                  |
|--------|--------------------------------------------|------------------------------|
| `GET`  | `/api/admin/orders?local=en`              | List orders (paginated, filterable) |
| `GET`  | `/api/admin/orders/{id}?local=en`         | Get order details            |
| `POST` | `/api/admin/orders/{id}/payment-status`   | Change payment status        |
| `POST` | `/api/admin/orders/{id}/status`           | Change order status          |

#### List Orders Query Params

| Param            | Type   | Required | Values                                                   |
|------------------|--------|----------|----------------------------------------------------------|
| `local`          | string | ✅        | `en` or `ar`                                             |
| `status`         | string | ❌        | `pending`, `inprogress`, `delivered`, `faild_delivered`, `return` |
| `payment_status` | string | ❌        | `pending`, `approve`, `reject`                           |

#### List Orders Response `200 OK`

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "price": 999.00,
      "discount": 50.00,
      "coupon_discount": 0,
      "final_price": 949.00,
      "payment_status": "pending",
      "status": "pending",
      "user": "John Doe",
      "payment_method": "Credit Card",
      "receipt_url": "http://domain.com/storage/orders/receipt.jpg"
    }
  ],
  "total": 50
}
```

#### Get Order Details Response `200 OK`

```json
{
  "id": 1,
  "price": 999.00,
  "discount": 50.00,
  "coupon_discount": 0,
  "final_price": 949.00,
  "receipt_url": "http://domain.com/storage/orders/receipt.jpg",
  "payment_status": "pending",
  "status": "pending",
  "coupon": { "id": 1, "name": "Summer Sale" },
  "payment_method": {
    "id": 1,
    "name": "Credit Card",
    "icon": "http://domain.com/storage/payment_methods/card.png"
  },
  "user": {
    "id": 5,
    "name": "John Doe",
    "phone": "+971501234567",
    "email": "john@example.com",
    "image": "http://domain.com/storage/users/john.jpg"
  },
  "address": {
    "address": "123 Main St",
    "floor": "3",
    "street": "King St",
    "building_number": "10",
    "additional_data": "Near the park",
    "lat": "25.2048",
    "lng": "55.2708",
    "map": "http://maps.google.com/...",
    "city": "Dubai",
    "zone": "Business Bay"
  },
  "order_products": [
    {
      "id": 1,
      "price": 999.00,
      "discount": 50.00,
      "final_price": 949.00,
      "count": 1,
      "product": {
        "id": 1,
        "name": "iPhone 15",
        "description": "Latest iPhone",
        "image": "http://domain.com/storage/products/iphone.jpg"
      },
      "options": [
        {
          "id": 1,
          "name": "Black",
          "price": 0,
          "variation": "Color"
        }
      ]
    }
  ]
}
```

#### Change Payment Status Body

| Field            | Type   | Required | Values                        |
|------------------|--------|----------|-------------------------------|
| `payment_status` | string | ✅        | `pending`, `approve`, `reject` |

#### Response `200 OK`

```json
{ "payment_status": "approve" }
```

#### Change Order Status Body

| Field    | Type   | Required | Values                                                    |
|----------|--------|----------|-----------------------------------------------------------|
| `status` | string | ✅        | `pending`, `inprogress`, `delivered`, `faild_delivered`, `return` |

#### Response `200 OK`

```json
{ "status": "delivered" }
```

---

### 3.7 Coupons

| Method   | URL                          | Description              |
|----------|------------------------------|--------------------------|
| `GET`    | `/api/admin/coupons`         | List all (paginated)     |
| `GET`    | `/api/admin/coupons/list`    | Dropdown list            |
| `POST`   | `/api/admin/coupons`         | Create coupon            |
| `GET`    | `/api/admin/coupons/{id}`   | Get by ID                |
| `POST`   | `/api/admin/coupons/{id}`   | Update coupon            |
| `DELETE` | `/api/admin/coupons/{id}`   | Delete coupon            |

#### Create/Update Coupon Body

| Field               | Type    | Required (Create) | Description                                  |
|---------------------|---------|-------------------|----------------------------------------------|
| `name`              | object  | ✅                 | Multilingual name                            |
| `name.en`           | string  | ✅                 | Coupon name (English)                        |
| `name.ar`           | string  | ✅                 | Coupon name (Arabic)                         |
| `code`              | string  | ✅                 | Unique coupon code                           |
| `discount`          | numeric | ✅                 | Discount value (amount or percentage)        |
| `type`              | string  | ✅                 | `precentage` or `value`                      |
| `usage_limit`       | integer | ❌                 | Total usage limit                            |
| `user_usage_limit`  | integer | ❌                 | Per-user usage limit                         |
| `from`              | date    | ❌                 | Valid from (format: `Y-m-d`)                 |
| `to`                | date    | ❌                 | Valid to (format: `Y-m-d`, must be >= from)  |
| `max_discount`      | numeric | ❌                 | Maximum discount cap (for percentage type)   |

#### Example Request

```json
{
  "name": { "en": "Summer Sale", "ar": "تخفيضات الصيف" },
  "code": "SUMMER25",
  "discount": 25,
  "type": "precentage",
  "usage_limit": 100,
  "user_usage_limit": 1,
  "from": "2025-06-01",
  "to": "2025-08-31",
  "max_discount": 200
}
```

---

### 3.8 Cities

| Method   | URL                                        | Description            |
|----------|--------------------------------------------|------------------------|
| `GET`    | `/api/admin/cities`                        | List all (paginated)   |
| `GET`    | `/api/admin/cities/list`                   | Active cities (dropdown) |
| `POST`   | `/api/admin/cities`                        | Create city            |
| `GET`    | `/api/admin/cities/{id}`                  | Get by ID              |
| `POST`   | `/api/admin/cities/{id}`                  | Update city            |
| `DELETE` | `/api/admin/cities/{id}`                  | Delete city            |
| `POST`   | `/api/admin/cities/{id}/change-status`    | Toggle status          |

#### Create/Update City Body

| Field     | Type    | Required (Create) | Description         |
|-----------|---------|-------------------|---------------------|
| `name`    | object  | ✅                 | Multilingual name   |
| `name.en` | string  | ✅                 | City name (English) |
| `name.ar` | string  | ✅                 | City name (Arabic)  |
| `status`  | boolean | ❌                 | Active/Inactive     |

---

### 3.9 Zones

| Method   | URL                                        | Description             |
|----------|--------------------------------------------|-------------------------|
| `GET`    | `/api/admin/zones`                         | List all with city (paginated) |
| `GET`    | `/api/admin/zones/list`                    | Active cities list      |
| `POST`   | `/api/admin/zones`                         | Create zone             |
| `GET`    | `/api/admin/zones/{id}`                   | Get by ID               |
| `POST`   | `/api/admin/zones/{id}`                   | Update zone             |
| `DELETE` | `/api/admin/zones/{id}`                   | Delete zone             |
| `POST`   | `/api/admin/zones/{id}/change-status`     | Toggle status           |

#### Create/Update Zone Body

| Field     | Type    | Required (Create) | Description            |
|-----------|---------|-------------------|------------------------|
| `name`    | object  | ✅                 | Multilingual name      |
| `name.en` | string  | ✅                 | Zone name (English)    |
| `name.ar` | string  | ✅                 | Zone name (Arabic)     |
| `price`   | numeric | ✅                 | Delivery price for zone |
| `city_id` | integer | ✅                 | City ID                |
| `status`  | boolean | ❌                 | Active/Inactive        |

#### Get Zone Response `200 OK`

```json
{
  "id": 1,
  "name": { "en": "Business Bay", "ar": "خليج الأعمال" },
  "price": 25.00,
  "status": 1,
  "city_id": 1,
  "city": {
    "id": 1,
    "name": { "en": "Dubai", "ar": "دبي" }
  }
}
```

---

### 3.10 Payment Methods

| Method   | URL                                                  | Description           |
|----------|------------------------------------------------------|-----------------------|
| `GET`    | `/api/admin/payment-methods`                         | List all (paginated)  |
| `GET`    | `/api/admin/payment-methods/list`                    | Dropdown list         |
| `POST`   | `/api/admin/payment-methods`                         | Create method         |
| `GET`    | `/api/admin/payment-methods/{id}`                   | Get by ID             |
| `POST`   | `/api/admin/payment-methods/{id}`                   | Update method         |
| `DELETE` | `/api/admin/payment-methods/{id}`                   | Delete method         |
| `POST`   | `/api/admin/payment-methods/{id}/change-status`     | Toggle status         |

#### Create Payment Method Body (`multipart/form-data`)

| Field             | Type    | Required | Description                    |
|-------------------|---------|----------|--------------------------------|
| `name`            | object  | ✅        | Multilingual name              |
| `name.en`         | string  | ✅        | Method name (English)          |
| `name.ar`         | string  | ✅        | Method name (Arabic)           |
| `description`     | object  | ✅        | Multilingual description       |
| `description.en`  | string  | ✅        | Description (English)          |
| `description.ar`  | string  | ✅        | Description (Arabic)           |
| `icon`            | file    | ✅ (Create) | Method icon image            |
| `status`          | boolean | ✅ (Create) | Active/Inactive              |

---

### 3.11 Services

| Method   | URL                           | Description          |
|----------|-------------------------------|----------------------|
| `GET`    | `/api/admin/services`         | List all (paginated) |
| `POST`   | `/api/admin/services`         | Create service       |
| `GET`    | `/api/admin/services/{id}`   | Get by ID            |
| `POST`   | `/api/admin/services/{id}`   | Update service       |
| `DELETE` | `/api/admin/services/{id}`   | Delete service       |

#### Create Service Body (`multipart/form-data`)

| Field             | Type   | Required | Description                 |
|-------------------|--------|----------|-----------------------------|
| `name`            | object | ✅        | Multilingual name           |
| `name.en`         | string | ✅        | Service name (English)      |
| `name.ar`         | string | ✅        | Service name (Arabic)       |
| `description`     | object | ✅        | Multilingual description    |
| `description.en`  | string | ✅        | Description (English)       |
| `description.ar`  | string | ✅        | Description (Arabic)        |
| `icon`            | file   | ✅        | Service icon image          |

---

### 3.12 About (Admin)

#### Get About

| Field   | Value                   |
|---------|-------------------------|
| **URL** | `GET /api/admin/about`  |
| **Auth** | Admin Bearer Token     |

#### Response `200 OK`

```json
{
  "id": 1,
  "title": { "en": "About Us", "ar": "من نحن" },
  "content": { "en": "We are...", "ar": "نحن..." },
  "image": "about/about.jpg"
}
```

---

#### Update About

| Field   | Value                    |
|---------|--------------------------|
| **URL** | `POST /api/admin/about`  |
| **Auth** | Admin Bearer Token      |
| **Content-Type** | `multipart/form-data` |

#### Request Body

| Field        | Type   | Required | Description              |
|--------------|--------|----------|--------------------------|
| `title.en`   | string | ✅        | Title (English)          |
| `title.ar`   | string | ✅        | Title (Arabic)           |
| `content.en` | string | ✅        | Content (English)        |
| `content.ar` | string | ✅        | Content (Arabic)         |
| `image`      | file   | ❌        | About page image         |

---

### 3.13 Settings

#### Get Settings

| Field   | Value                       |
|---------|-----------------------------|
| **URL** | `GET /api/admin/settings`   |
| **Auth** | Admin Bearer Token         |

#### Response `200 OK`

```json
{
  "id": 1,
  "brand_name": { "en": "My Store", "ar": "متجري" },
  "logo": "settings/logo.png",
  "phone": "+971501234567",
  "wattsapp": "+971501234567",
  "email": "info@store.com",
  "address": "Dubai, UAE",
  "lat": "25.2048",
  "lng": "55.2708",
  "facebook": "https://facebook.com/store",
  "insta": "https://instagram.com/store",
  "tiktok": "https://tiktok.com/@store",
  "ios_app": "https://apps.apple.com/...",
  "android_app": "https://play.google.com/...",
  "min_order": 50
}
```

---

#### Update Settings

| Field   | Value                        |
|---------|------------------------------|
| **URL** | `POST /api/admin/settings`   |
| **Auth** | Admin Bearer Token          |
| **Content-Type** | `multipart/form-data` |

#### Request Body

| Field            | Type    | Required (1st time) | Description                    |
|------------------|---------|---------------------|--------------------------------|
| `brand_name`     | object  | ✅                   | Multilingual brand name        |
| `brand_name.en`  | string  | ✅                   | Brand name (English)           |
| `brand_name.ar`  | string  | ✅                   | Brand name (Arabic)            |
| `logo`           | file    | ❌                   | Store logo image               |
| `phone`          | string  | ✅                   | Contact phone                  |
| `wattsapp`       | string  | ✅                   | WhatsApp number                |
| `email`          | string  | ✅                   | Contact email                  |
| `address`        | string  | ❌                   | Physical address               |
| `lat`            | string  | ❌                   | Latitude                       |
| `lng`            | string  | ❌                   | Longitude                      |
| `facebook`       | string  | ❌                   | Facebook page URL              |
| `insta`          | string  | ❌                   | Instagram page URL             |
| `tiktok`         | string  | ❌                   | TikTok page URL                |
| `ios_app`        | string  | ❌                   | iOS App Store link             |
| `android_app`    | string  | ❌                   | Google Play link               |
| `min_order`      | numeric | ✅                   | Minimum order amount           |

---

### 3.14 Contact (Admin)

#### Get New Messages (Unread)

| Field   | Value                       |
|---------|-----------------------------|
| **URL** | `GET /api/admin/contact`    |
| **Auth** | Admin Bearer Token         |

#### Query Params

| Param    | Type   | Required | Description            |
|----------|--------|----------|------------------------|
| `search` | string | ❌        | Search in name/email/phone/title/content |

#### Response `200 OK`

```json
{
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "f_name": "John",
        "l_name": "Doe",
        "phone": "+971501234567",
        "email": "john@example.com",
        "title": "Inquiry",
        "content": "I want to know...",
        "status": 0,
        "created_at": "2024-08-01T10:00:00.000000Z"
      }
    ],
    "total": 15
  }
}
```

---

#### Get Contact History (Read Messages)

| Field   | Value                             |
|---------|-----------------------------------|
| **URL** | `GET /api/admin/contact/history`  |
| **Auth** | Admin Bearer Token               |

#### Query Params

| Param    | Type   | Required | Description    |
|----------|--------|----------|----------------|
| `search` | string | ❌        | Search term    |

---

#### Mark Message as Read

| Field   | Value                                 |
|---------|---------------------------------------|
| **URL** | `GET /api/admin/contact/read/{id}`    |
| **Auth** | Admin Bearer Token                   |

#### URL Params

| Param | Type    | Required | Description   |
|-------|---------|----------|---------------|
| `id`  | integer | ✅        | Message ID    |

#### Response `200 OK`

```json
{ "success": "You read success" }
```

---

## 4. Authenticated User Endpoints (Requires Auth + User Role)

> All user routes are prefixed with `/api/user`
> Required header: `Authorization: Bearer {user_token}`

---

### 4.1 Addresses

#### Get Available Cities (Dropdown)

| Field   | Value                              |
|---------|------------------------------------|
| **URL** | `GET /api/user/addresses/cities`   |
| **Auth** | User Bearer Token                 |

#### Query Params

| Param   | Type   | Required | Description            |
|---------|--------|----------|------------------------|
| `local` | string | ✅        | Language: `en` or `ar` |

#### Response `200 OK`

```json
[
  { "id": 1, "name": "Dubai" },
  { "id": 2, "name": "Abu Dhabi" }
]
```

---

#### Get Zones by City (Dropdown)

| Field   | Value                            |
|---------|----------------------------------|
| **URL** | `GET /api/user/addresses/zones`  |
| **Auth** | User Bearer Token               |

#### Query Params

| Param     | Type    | Required | Description            |
|-----------|---------|----------|------------------------|
| `local`   | string  | ✅        | Language: `en` or `ar` |
| `city_id` | integer | ✅        | City ID                |

#### Response `200 OK`

```json
[
  { "id": 1, "name": "Business Bay", "price": 25.00 },
  { "id": 2, "name": "Downtown", "price": 15.00 }
]
```

---

#### Get User Addresses

| Field   | Value                       |
|---------|-----------------------------|
| **URL** | `GET /api/user/addresses`   |
| **Auth** | User Bearer Token          |

#### Query Params

| Param   | Type   | Required | Description            |
|---------|--------|----------|------------------------|
| `local` | string | ✅        | Language: `en` or `ar` |

#### Response `200 OK`

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "address": "123 Main St",
      "floor": "3",
      "street": "King St",
      "building_number": "10",
      "additional_data": "Near the park",
      "lat": "25.2048",
      "lng": "55.2708",
      "map": "http://maps.google.com/...",
      "city": "Dubai",
      "zone": "Business Bay"
    }
  ]
}
```

---

#### Create Address

| Field   | Value                        |
|---------|------------------------------|
| **URL** | `POST /api/user/addresses`   |
| **Auth** | User Bearer Token           |
| **Content-Type** | `application/json` |

#### Request Body

| Field             | Type   | Required | Description               |
|-------------------|--------|----------|---------------------------|
| `address`         | string | ❌        | Address text              |
| `lat`             | string | ✅        | Latitude                  |
| `lng`             | string | ✅        | Longitude                 |
| `floor`           | string | ✅        | Floor number              |
| `street`          | string | ✅        | Street name               |
| `building_number` | string | ❌        | Building number           |
| `city_id`         | integer| ✅        | City ID                   |
| `zone_id`         | integer| ✅        | Zone ID                   |
| `additional_data` | string | ❌        | Additional directions     |

#### Response `201 Created`

```json
{
  "id": 3,
  "address": "456 Side St",
  "floor": "5",
  "street": "Sheikh Zayed Rd",
  "building_number": "22",
  "lat": "25.2048",
  "lng": "55.2708",
  "city_id": 1,
  "zone_id": 2,
  "user_id": 5
}
```

---

#### Get Address by ID

| Field   | Value                           |
|---------|---------------------------------|
| **URL** | `GET /api/user/addresses/{id}`  |
| **Auth** | User Bearer Token              |

#### URL Params

| Param | Type    | Required | Description |
|-------|---------|----------|-------------|
| `id`  | integer | ✅        | Address ID  |

#### Query Params

| Param   | Type   | Required | Description            |
|---------|--------|----------|------------------------|
| `local` | string | ✅        | Language: `en` or `ar` |

---

#### Update Address

| Field   | Value                            |
|---------|----------------------------------|
| **URL** | `POST /api/user/addresses/{id}`  |
| **Auth** | User Bearer Token               |
| **Content-Type** | `application/json`  |

#### Request Body (all optional)

| Field             | Type    | Required | Description           |
|-------------------|---------|----------|-----------------------|
| `address`         | string  | ❌        | Address text          |
| `lat`             | string  | ❌        | Latitude              |
| `lng`             | string  | ❌        | Longitude             |
| `floor`           | string  | ❌        | Floor number          |
| `street`          | string  | ❌        | Street name           |
| `building_number` | string  | ❌        | Building number       |
| `city_id`         | integer | ❌        | City ID               |
| `zone_id`         | integer | ❌        | Zone ID               |
| `additional_data` | string  | ❌        | Additional directions |

---

#### Delete Address

| Field   | Value                              |
|---------|------------------------------------|
| **URL** | `DELETE /api/user/addresses/{id}`  |
| **Auth** | User Bearer Token                 |

#### Response `200 OK`

```json
{ "message": "Deleted successfully" }
```

---

### 4.2 Cart

#### Get Cart

| Field   | Value                  |
|---------|------------------------|
| **URL** | `GET /api/user/cart`   |
| **Auth** | User Bearer Token     |

#### Query Params

| Param   | Type   | Required | Description            |
|---------|--------|----------|------------------------|
| `local` | string | ✅        | Language: `en` or `ar` |

#### Response `200 OK`

```json
{
  "cart": [
    {
      "cart_product_id": 1,
      "count": 2,
      "product": {
        "id": 1,
        "name": "iPhone 15",
        "description": "Latest iPhone",
        "image": "http://domain.com/storage/products/iphone.jpg",
        "price": 999.00,
        "discount": 50.00,
        "final_price": 949.00,
        "is_discounted": true
      },
      "variations": [
        {
          "variation_id": 1,
          "variation_name": "Color",
          "selected_option": {
            "option_id": 2,
            "option_name": "Black",
            "price": 0
          }
        }
      ]
    }
  ]
}
```

---

#### Add to Cart

| Field   | Value                   |
|---------|-------------------------|
| **URL** | `POST /api/user/cart`   |
| **Auth** | User Bearer Token      |
| **Content-Type** | `application/json` |

#### Request Body

| Field                         | Type    | Required | Description                 |
|-------------------------------|---------|----------|-----------------------------|
| `local`                       | string  | ✅        | Language: `en` or `ar`      |
| `product_id`                  | integer | ✅        | Product ID                  |
| `count`                       | integer | ✅        | Quantity (min: 1)           |
| `variations`                  | array   | ❌        | Selected variations         |
| `variations[0].variation_id`  | integer | ✅*       | Variation ID                |
| `variations[0].option_id`     | integer | ✅*       | Selected option ID          |

*Required when variations array is provided

#### Example Request

```json
{
  "local": "en",
  "product_id": 1,
  "count": 2,
  "variations": [
    { "variation_id": 1, "option_id": 2 }
  ]
}
```

#### Response `201 Created`

```json
{
  "message": "Added to cart",
  "item": {
    "cart_product_id": 5,
    "count": 2,
    "product": { ... },
    "variations": [ ... ]
  }
}
```

---

#### Update Cart Item Quantity

| Field   | Value                        |
|---------|------------------------------|
| **URL** | `POST /api/user/cart/{id}`   |
| **Auth** | User Bearer Token           |
| **Content-Type** | `application/json` |

#### URL Params

| Param | Type    | Required | Description      |
|-------|---------|----------|------------------|
| `id`  | integer | ✅        | Cart product ID  |

#### Request Body

| Field   | Type    | Required | Description            |
|---------|---------|----------|------------------------|
| `local` | string  | ✅        | Language: `en` or `ar` |
| `count` | integer | ✅        | New quantity (min: 1)  |

#### Response `200 OK`

```json
{
  "message": "Cart updated",
  "item": { ... }
}
```

---

#### Remove Cart Item

| Field   | Value                           |
|---------|---------------------------------|
| **URL** | `DELETE /api/user/cart/{id}`    |
| **Auth** | User Bearer Token              |

#### Response `200 OK`

```json
{ "message": "Removed from cart" }
```

---

#### Clear Cart

| Field   | Value                         |
|---------|-------------------------------|
| **URL** | `DELETE /api/user/cart/clear` |
| **Auth** | User Bearer Token            |

#### Response `200 OK`

```json
{ "message": "Cart cleared" }
```

---

### 4.3 Orders (User)

#### Place Order

| Field   | Value                          |
|---------|--------------------------------|
| **URL** | `POST /api/user/orders/make`   |
| **Auth** | User Bearer Token             |
| **Content-Type** | `multipart/form-data` |

#### Request Body

| Field                  | Type    | Required | Description                         |
|------------------------|---------|----------|-------------------------------------|
| `payment_method_id`    | integer | ✅        | Payment method ID                   |
| `address_id`           | integer | ✅        | Delivery address ID                 |
| `cart_product_ids`     | array   | ✅        | Array of cart product IDs to order  |
| `cart_product_ids[0]`  | integer | ✅        | Cart product ID                     |
| `coupon_code`          | string  | ❌        | Discount coupon code                |
| `receipt`              | file    | ❌        | Payment receipt image               |

#### Example Request

```json
{
  "payment_method_id": 1,
  "address_id": 2,
  "cart_product_ids": [1, 3, 5],
  "coupon_code": "SUMMER25"
}
```

#### Response `200 OK`

```json
{
  "message": "Order placed successfully",
  "order_id": 42
}
```

#### Error Responses

| Code  | Description                          |
|-------|--------------------------------------|
| `400` | Validation error / No valid cart items / Below minimum order |
| `429` | Too many coupon check attempts       |

---

#### Check Coupon

| Field   | Value                                 |
|---------|---------------------------------------|
| **URL** | `POST /api/user/orders/check-coupon`  |
| **Auth** | User Bearer Token                    |
| **Content-Type** | `application/json`         |

> ⚠️ **Rate Limited**: 3 attempts per 3 minutes per user

#### Request Body

| Field         | Type    | Required | Description                          |
|---------------|---------|----------|--------------------------------------|
| `coupon_code` | string  | ✅        | Coupon code to validate              |
| `amount`      | numeric | ✅        | Current cart total to apply discount |

#### Example Request

```json
{
  "coupon_code": "SUMMER25",
  "amount": 500.00
}
```

#### Response `200 OK`

```json
{
  "coupon_discount": 125.00,
  "amount": 375.00
}
```

#### Error Responses

| Code  | Description               |
|-------|---------------------------|
| `400` | Invalid or expired coupon |
| `429` | Too many attempts         |

---

#### Get Order History

| Field   | Value                     |
|---------|---------------------------|
| **URL** | `GET /api/user/orders`    |
| **Auth** | User Bearer Token        |

#### Query Params

| Param   | Type   | Required | Description            |
|---------|--------|----------|------------------------|
| `local` | string | ✅        | Language: `en` or `ar` |

#### Response `200 OK`

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 42,
      "price": 999.00,
      "discount": 50.00,
      "coupon_discount": 125.00,
      "final_price": 824.00,
      "payment_status": "pending",
      "status": "pending",
      "payment_method": "Credit Card",
      "receipt_url": "http://domain.com/storage/orders/receipt.jpg",
      "created_at": "2025-08-01T10:00:00.000000Z"
    }
  ],
  "total": 10
}
```

---

#### Get Order Details

| Field   | Value                        |
|---------|------------------------------|
| **URL** | `GET /api/user/orders/{id}`  |
| **Auth** | User Bearer Token           |

#### URL Params

| Param | Type    | Required | Description |
|-------|---------|----------|-------------|
| `id`  | integer | ✅        | Order ID    |

#### Query Params

| Param   | Type   | Required | Description            |
|---------|--------|----------|------------------------|
| `local` | string | ✅        | Language: `en` or `ar` |

#### Response `200 OK`

```json
{
  "id": 42,
  "price": 999.00,
  "discount": 50.00,
  "coupon_discount": 125.00,
  "final_price": 824.00,
  "receipt_url": "http://domain.com/storage/orders/receipt.jpg",
  "payment_status": "pending",
  "status": "pending",
  "created_at": "2025-08-01T10:00:00.000000Z",
  "coupon": { "id": 1, "name": "Summer Sale" },
  "payment_method": {
    "id": 1,
    "name": "Credit Card",
    "icon": "http://domain.com/storage/payment_methods/card.png"
  },
  "address": {
    "address": "123 Main St",
    "floor": "3",
    "street": "King St",
    "building_number": "10",
    "additional_data": "Near the park",
    "lat": "25.2048",
    "lng": "55.2708",
    "city": "Dubai",
    "zone": "Business Bay"
  },
  "order_products": [
    {
      "id": 1,
      "price": 999.00,
      "discount": 50.00,
      "final_price": 824.00,
      "count": 1,
      "product": {
        "id": 1,
        "name": "iPhone 15",
        "description": "Latest iPhone",
        "image": "http://domain.com/storage/products/iphone.jpg"
      },
      "options": [
        {
          "id": 1,
          "name": "Black",
          "price": 0,
          "variation": "Color"
        }
      ]
    }
  ]
}
```

---

## 📋 Global Error Responses

| Code  | Description                              |
|-------|------------------------------------------|
| `400` | Validation failed – returns `errors` object |
| `401` | Unauthenticated – invalid/missing token  |
| `403` | Unauthorized – wrong role               |
| `404` | Resource not found                       |
| `429` | Too many requests (rate limited)         |
| `500` | Internal server error                    |

### Validation Error Format

```json
{
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

---

## 📌 Notes

- **Multilingual Fields**: Fields like `name`, `description`, `title`, `content` are stored as JSON objects with `en` and `ar` keys. When creating via `multipart/form-data`, use bracket notation: `name[en]`, `name[ar]`.
- **Pagination**: Paginated responses follow Laravel's default pagination structure with `current_page`, `data`, `last_page`, `per_page`, `total`.
- **File Uploads**: Whenever the request includes file fields (images), use `Content-Type: multipart/form-data`.
- **Status Toggle**: `change-status` endpoints toggle the current status (active ↔ inactive).
- **Order Statuses**: `pending` → `inprogress` → `delivered` / `faild_delivered` / `return`
- **Payment Statuses**: `pending` → `approve` / `reject`

---

*Generated for ECommerce API – Laravel Sanctum Authentication*
