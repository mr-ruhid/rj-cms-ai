# RJ Commerce

A complete, self-hosted e-commerce solution for RJ CMS. RJ Commerce provides a robust backend for managing products, orders, and storefronts, alongside a flexible frontend architecture that integrates seamlessly with your themes.

## Requirements

- **PHP:** >= 8.2
- **RJ CMS:** >= 1.0.0
- **Database:** MySQL

## Installation

1. Upload the `rj-commerce` folder to your RJ CMS installation at `rj-content/plugins/rj-commerce`.
2. Log in to your RJ CMS Admin Panel.
3. Navigate to **Plugins** and activate **RJ Commerce**.
4. Upon activation, the required database tables (`rj_products`, `rj_orders`, `rj_coupons`, etc.) will be migrated automatically.
5. Go to **Commerce > Settings** in the admin menu to configure your store details, assign shop pages, and set up payment methods.

## Features

- **Products Management:** Support for physical and digital products, variations, stock management, and galleries.
- **Orders:** Full order lifecycle management (Pending, Processing, Completed, Cancelled, Refunded, Failed).
- **Categories & Brands:** Hierarchical categories with multi-language support, and brand management.
- **Attributes:** Product attributes and variation generation.
- **Reviews:** Customer product reviews with admin moderation (approve/reject/delete).
- **Coupons:** Discount code management.
- **Shipping:** Shipping zones and methods configuration.
- **Payments:** Built-in support for Cash on Delivery (COD), Stripe, PayPal, Razorpay, and Crypto.
- **Multi-language:** Built-in translation support for products, categories, and brands.
- **REST API:** Comprehensive API for external integrations.
- **RJBuilder Widgets:** Out-of-the-box widgets for your page builder (Product Grid, Categories, Single Product, Cart).
- **Modern UI:** Premium, responsive, and accessible frontend design (Vanilla JS & Tailwind CSS).

## Theme Integration

RJ Commerce looks for views in your active theme before falling back to its default views. To customize the frontend, create the following files in your theme directory:

- `resources/views/commerce/shop/index.blade.php` - Shop Archive
- `resources/views/commerce/shop/single.blade.php` - Single Product Page
- `resources/views/commerce/cart/index.blade.php` - Cart Page
- `resources/views/commerce/checkout/index.blade.php` - Checkout Page
- `resources/views/commerce/checkout/success.blade.php` - Order Success Page
- `resources/views/partials/commerce/products.blade.php` - Shortcode Products Grid
- `resources/views/partials/commerce/categories.blade.php` - Shortcode Categories Grid

## RJBuilder Widgets

If you are using the RJBuilder, you can drag and drop the following commerce elements into your pages:

- **Product Grid:** Displays a customizable grid of products with filtering options.
- **Single Product:** Highlights a specific product with a detailed card.
- **Categories:** Displays a grid of active product categories.
- **Cart:** Displays a cart icon with a live item count or a mini-cart summary.

## Shortcodes

You can use the following shortcodes in your pages or posts:

### `[rj_products]`
Displays a grid of products.
- `category` (string): Filter by category slug.
- `brand` (string): Filter by brand slug.
- `limit` (int): Number of products to show (default: 12).
- `columns` (int): Number of columns (default: 4).
- `orderby` (string): `latest`, `price_low`, `price_high`.
- `format` (string): `html` (default) or `json` (outputs raw JSON data for JS).

### `[rj_categories]`
Displays a grid of product categories.
- `limit` (int): Number of categories (default: 10).
- `columns` (int): Number of columns (default: 4).
- `format` (string): `html` or `json`.

### `[rj_cart]`
Renders the shopping cart.

### `[rj_checkout]`
Renders the checkout form.

## Helper Functions

Use these helpers in your theme files:

- `rj_commerce_url('shop'|'cart'|'checkout')`: Returns the URL for the assigned commerce pages.
- `rj_commerce_get_cart_count()`: Returns the total number of items currently in the cart.

## REST API Documentation

The plugin exposes a REST API for external applications (e.g., mobile apps, headless storefronts).

**Base URL:** `/api/commerce/v1`

### Authentication
All API requests must include the following headers:
- `X-Public-Key`: Your API Public Key (configured in Commerce > Settings > API).
- `Authorization: Bearer <your_secret_key>`: Your API Secret Key.

*Note: API access must be enabled in the settings.*

### Endpoints
- `GET /orders` - List all orders.
- `GET /orders/{order}` - Get a specific order.
- `PUT /orders/{order}/status` - Update order status.
- `PUT /orders/{order}/shipping` - Update order shipping details.
- `GET /products` - List all products.
- `GET /products/{slug}` - Get a specific product by slug.
- `GET /categories` - List product categories.
- `GET /brands` - List product brands.

## Screenshots

| Storefront | Product Page |
| :---: | :---: |
| ![Storefront](https://raw.githubusercontent.com/rj-nexus/rj-commerce/main/assets/images/screenshot01.png) | ![Product Page](https://raw.githubusercontent.com/rj-nexus/rj-commerce/main/assets/images/screenshot02.png) |

| Shopping Cart | Checkout |
| :---: | :---: |
| ![Shopping Cart](https://raw.githubusercontent.com/rj-nexus/rj-commerce/main/assets/images/screenshot03.png) | ![Checkout](https://raw.githubusercontent.com/rj-nexus/rj-commerce/main/assets/images/screenshot04.png) |

| Order Success | Admin Dashboard |
| :---: | :---: |
| ![Order Success](https://raw.githubusercontent.com/rj-nexus/rj-commerce/main/assets/images/screenshot05.png) | ![Admin Dashboard](https://raw.githubusercontent.com/rj-nexus/rj-commerce/main/assets/images/screenshot06.png) |

## License

This plugin is open-sourced software licensed under the [MIT License](LICENSE).
