# Ecommerce Project

## Overview
This repository is a modular ecommerce platform with a React frontend and a PHP (Laravel) backend. The backend is organized into domain-focused modules (accounts, products, cart, orders, payments, inventory, reviews, comments, shipments, etc.) and served through a Laravel app located under `framwork/internal`.

### Key pieces
- **Frontend**: `frontend/` (React + Vite, Tailwind). UI pages for browsing products, cart, checkout, orders, and admin screens.
- **Backend (Laravel)**: `framwork/internal/` (routes, controllers, migrations, config, storage/logs).
- **Domain modules**: Top-level folders such as `products/`, `orders/`, `payments/`, `inventory/`, `cart/`, `reviews/`, `comments/`, `shipments/`, `accounts/`, etc. Each module typically contains:
  - `api/` controllers
  - `application/` commands/queries/handlers
  - `domains/` models/contracts
  - `infrastructure/` repositories/gateways

### Payments
Payments are handled via the `payments` module. The current integration is Paymob, and the payment flow is:
1. Checkout creates an order.
2. Paymob intention is created to obtain a `client_secret`.
3. User is redirected to the Paymob unified checkout URL.
4. Paymob webhook updates the order status.

### Status model
Orders use a constrained status set: `pending`, `confirmed`, `shipped`, `delivered`, `cancelled`.  
If the payment gateway returns “paid”, it is mapped to a valid order status (e.g., `confirmed`).

---

If you want setup/run instructions, environment variables, or API docs added here, tell me and I’ll extend this README.
