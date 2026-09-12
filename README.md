# HEHA Agency Platform

A multi-service platform for HEHA Agency: house-item moving, loans, guidance, and a shop
for merchandise, phones, and cases. **Python (Flask) powers the backend API**; **PHP renders
the frontend** and talks to that API over HTTP (JSON + JWT).

## Why this split works well

- The Flask API is stateless and only deals with data (users, requests, loans, sessions,
  products, orders) — it doesn't know or care about HTML.
- PHP pages are thin: they call the API via `config.php`'s `api_request()` helper, then
  render the JSON as HTML forms/tables. Sessions in PHP hold the JWT and cart, not business data.
- Because the two layers only talk over HTTP, you can host them on different
  servers/ports, and even swap the frontend later (e.g. to a JS SPA) without touching the backend.

## Architecture

```
heha_agency/
├── backend/                  # Python / Flask REST API
│   ├── app.py                 # app factory, registers all blueprints
│   ├── config.py               # env-driven config (secret keys, DB URL)
│   ├── extensions.py           # db, jwt, bcrypt singletons
│   ├── models.py               # User, MovingRequest, Loan, GuidanceSession, Product, Order, OrderItem
│   ├── seed.py                  # loads sample merchandise/phone/case products
│   ├── requirements.txt
│   └── routes/
│       ├── auth.py             # /api/auth/register, /api/auth/login
│       ├── moving.py           # /api/moving/requests
│       ├── loans.py            # /api/loans
│       ├── guidance.py         # /api/guidance
│       └── shop.py             # /api/products, /api/orders
│
└── frontend/                  # PHP pages (server-rendered)
    ├── config.php              # API_BASE_URL + api_request() cURL helper + session helpers
    ├── includes/
    │   ├── header.php / footer.php / nav.php
    ├── index.php               # HOME — the required hub linking to every sector
    ├── login.php / register.php / logout.php
    ├── moving.php               # request a move + view your requests
    ├── loans.php                # apply for a loan + view your applications
    ├── guidance.php              # book a guidance session + view your bookings
    ├── shop.php                  # browse merchandise / phones / cases, add to cart
    ├── cart.php                   # session-based cart
    ├── checkout.php                # places the order via the API
    └── assets/css/style.css
```

## The "home page is the hub" requirement

`index.php` is the single page that links to Moving, Loans, Guidance, and Shop — and the
same links also live in the persistent nav bar (`includes/nav.php`) that's included on
every page, so users always have a way back to the hub and across sectors. Each sector page
(`moving.php`, `loans.php`, `guidance.php`, `shop.php`) is a standalone PHP file with its own
URL, so it can be linked/bookmarked directly, but discovery of all sectors happens from the
home page as required.

## Data model (SQLAlchemy, SQLite by default — swap to MySQL/Postgres by changing `DATABASE_URL`)

| Table              | Purpose                                                        |
|---------------------|-----------------------------------------------------------------|
| `users`             | Accounts (customers/admins), bcrypt-hashed passwords            |
| `moving_requests`   | Pickup/dropoff address, item list, date, status, quote           |
| `loans`             | Amount, purpose, term, interest rate, status                    |
| `guidance_sessions` | Topic, preferred date, notes, status                             |
| `products`          | `category` = `merchandise` \| `phone` \| `case`, price, stock    |
| `orders` / `order_items` | Shop checkout records                                        |

## API summary

| Method | Endpoint                | Auth | Purpose |
|--------|--------------------------|------|---------|
| POST   | `/api/auth/register`     | No   | Create account, returns JWT |
| POST   | `/api/auth/login`        | No   | Returns JWT |
| GET/POST | `/api/moving/requests` | Yes  | List / create moving requests |
| GET/POST | `/api/loans`          | Yes  | List / create loan applications |
| GET/POST | `/api/guidance`       | Yes  | List / create guidance bookings |
| GET    | `/api/products?category=`| No  | List products, optional filter |
| POST   | `/api/orders`             | Yes | Place an order (checkout) |
| GET    | `/api/orders`              | Yes | List your past orders |

## Running it locally

### 1. Backend (Python)
```bash
cd backend
python3 -m venv venv && source venv/bin/activate
pip install -r requirements.txt
python seed.py          # creates tables + sample products
python app.py            # runs on http://localhost:5000
```

### 2. Frontend (PHP)
```bash
cd frontend
php -S localhost:8000
```
Then visit `http://localhost:8000/index.php`. If the backend runs elsewhere, set:
```bash
export HEHA_API_URL="http://your-backend-host:5000/api"
```

## Notes / next steps for production

- Replace the default `SECRET_KEY`/`JWT_SECRET_KEY` and move them to real environment
  variables or a secrets manager.
- Swap SQLite for MySQL/Postgres by setting `DATABASE_URL` (e.g.
  `mysql+pymysql://user:pass@host/heha`).
- Add an admin area (Flask routes + PHP pages) to approve/reject loans, update moving
  quotes/status, and manage product stock — the models already support this; only the
  admin-facing routes/pages are missing.
- Add real payment integration (M-Pesa/Stripe/etc.) in `checkout.php` before hitting
  `/api/orders`, and validate stock/price server-side as already done in `shop.py`.
- Put the JWT in an HttpOnly cookie instead of the PHP session if you later split the
  frontend onto a different domain, to avoid CSRF/session-fixation issues.
