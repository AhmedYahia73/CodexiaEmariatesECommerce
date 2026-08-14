# E-Commerce API — Documentation

## Overview

A fully-featured headless e-commerce REST API built with **Laravel 13** and **Laravel Sanctum**. Designed to power mobile or web storefronts with a separate admin panel. All content supports **bilingual responses (Arabic / English)** via a `local` parameter.

---

## Key Features

### Multi-language Support
Every piece of content (product names, descriptions, categories, variations, coupons, cities, zones) is stored as a JSON object with `en` and `ar` keys. The client sends `local=en` or `local=ar` and receives translated data accordingly.

### Role-based Authentication
- **Admin** — full control over the platform (products, orders, users, settings, coupons, etc.)
- **User** — can browse, place orders, manage addresses, and apply coupons

Authentication is handled via Laravel Sanctum tokens. Each route group is protected by a dedicated middleware (`AdminMiddleware` / `UserMiddleware`).

---

## Modules

### Products
- Full CRUD with main image upload (converted to WebP automatically)
- Supports product **variations** (e.g. Size, Color) each with multiple **options** and individual pricing
- **Gallery** management — add or delete multiple images per product
- Discount system with a date range (`discount_from` / `discount_to`); `final_price` and `is_discounted` are computed automatically
- Status toggle (active / inactive)

### Categories
- Hierarchical — supports parent categories and subcategories (self-referencing)
- Image upload with WebP conversion
- Status toggle

### Orders
- Users place orders with multiple products, each with selected options
- Order totals (price, discount, coupon discount, final price) are calculated server-side — clients cannot manipulate prices
- Attach a **receipt image** (for manual payment methods)
- Admin can change **order status**: `pending → inprogress → delivered / faild_delivered / return`
- Admin can change **payment status**: `pending → approve / reject`
- Full order detail view including user info, address, payment method, and itemized products with options

### Coupons
- Code-based coupons with two discount types: **percentage** or **fixed value**
- Configurable limits: total usage limit (`usage_limit`) and per-user limit (`user_usage_limit`)
- Date range validity (`from` / `to`)
- Optional maximum discount cap (`max_discount`)
- `users_count` is tracked automatically — cannot be set manually
- Rate limiting on coupon check endpoint: **3 attempts per 3 minutes** per user

### Addresses
- Users manage their own addresses (CRUD)
- Each address is linked to a **City** and a **Zone**
- Dropdown lists for cities and zones with local translation
- Google Maps link generated automatically from `lat` / `lng`

### Cities & Zones
- Admin manages cities and zones with status toggle
- Zone carries a delivery price used in order calculation

### Payment Methods
- Admin manages available payment methods with icon, description, and status
- Users see only active methods when placing orders

### Settings
- Single-row site settings (brand name, logo, contact info, social links, map coordinates, min order value)
- Smart upsert: creates a new record if none exists, updates otherwise
- Logo upload with WebP conversion

### Image Handling (trait)
- All uploaded images are automatically **converted to WebP** at 80% quality using Intervention Image
- Update operations **delete the old image** from storage before saving the new one
- Dedicated methods: `upload_image`, `update_image_v2`, `uploadFile_v2`, `deleteImage`

---

## API Structure

| Prefix | Middleware | Description |
|--------|-----------|-------------|
| `/api/login` | none | Authentication |
| `/api/user/home/*` | none | Public browsing (categories, products) |
| `/api/user/orders/lists` | none | Public order lists data |
| `/api/user/*` | `auth:sanctum + user` | User actions (addresses, orders, coupon check) |
| `/api/admin/*` | `auth:sanctum + admin` | Admin panel |

---

## Tech Stack

- **Laravel 13**
- **Laravel Sanctum** (token authentication)
- **Intervention Image v3** (WebP conversion)
- **MySQL** (with JSON columns for multilingual fields)
- **Laravel Rate Limiter** (coupon abuse protection)
