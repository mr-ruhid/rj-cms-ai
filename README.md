# RJ CMS

![Status](https://img.shields.io/badge/Status-In%20Development-orange)
![Version](https://img.shields.io/badge/Version-v1.0.0--alpha-blue)
![License](https://img.shields.io/badge/License-MIT-green)
![Laravel](https://img.shields.io/badge/Laravel-12-red)
![PHP](https://img.shields.io/badge/PHP-8.2-purple)

**RJ CMS** is a modern, self-hosted content management system built on **Laravel 12**. It is modular, secure, and deeply integrated with AI, and ships with a visual page builder and a fully isolated plugin and theme architecture.

RJ CMS is 100% self-hosted: you install it on your own server and keep full ownership of your data.

> **Development notice**
> RJ CMS is under active development. Expect bugs, incomplete features, and breaking changes. It is not recommended for production use yet. Feedback, bug reports, and contributions are very welcome.

---

## Table of Contents

- [Key Features](#key-features)
- [Tech Stack](#tech-stack)
- [Roadmap](#roadmap)
- [Requirements](#requirements)
- [Installation](#installation)
- [Ecosystem](#ecosystem)
- [Contributing](#contributing)
- [License](#license)
- [Credits](#credits)

---

## Key Features

- **RJBuilder (visual page builder):** Drag-and-drop editing with responsive controls, nested containers, custom CSS/JS, and dynamic tags.
- **Modular plugin system:** Plugins are fully isolated, with their own routes, views, migrations, and assets, and never touch the core. Updates are delivered through GitHub.
- **Theme architecture:** A builder-first theme system with custom templates, child themes, and dynamic asset injection.
- **AI integration (OpenRouter):** Built-in tools for generating SEO meta tags, translating content, writing text, and suggesting page layouts.
- **Multi-language support:** Built-in AZ, EN, RU, and TR locales plus dynamic locales. Posts, pages, categories, and SEO data can all be translated.
- **Advanced SEO:** Fine-grained control over titles, descriptions, Open Graph, Twitter cards, and schema markup, with AI-powered SEO analysis.
- **Role-based access control (RBAC):** Built-in roles, permissions, and activity logging.

---

## Tech Stack

| Layer          | Technology                              |
| -------------- | --------------------------------------- |
| Backend        | Laravel 12, PHP 8.2                     |
| Database       | MySQL                                   |
| Frontend       | Blade, Tailwind CSS v3, Alpine.js       |
| Build tool     | Vite                                    |
| AI engine      | OpenRouter API                          |

---

## Roadmap

RJ CMS is being developed in phases.

### Completed (Core and Phase 1)

- [x] Authentication (Laravel Breeze) and admin middleware
- [x] Settings management (General, AI, SEO, Security, and more)
- [x] Media library with drag-and-drop upload and image optimization
- [x] Categories and tags (multilingual)
- [x] Menu system (header, footer, custom locations)
- [x] Posts and pages CRUD with SEO and AI generation
- [x] RJBuilder core: drag and drop, 8 built-in widgets, responsive controls, history, animations
- [x] Theme system: active theme loading, view namespaces, custom page templates
- [x] Plugin system engine: dynamic autoloaders, service providers, isolated routes and views
- [x] Plugin IDE: built-in code editor with AI code review

### In Progress (Phases 2 and 3)

- [ ] Form Builder plugin (v1.1.0 released, under testing and refinement)
- [ ] Admin UI for the remaining sections (Post Types, Roles, Themes, Plugins)
- [ ] Frontend views: single post, archive, and loop card designs
- [ ] AI Center: dedicated AI management dashboard

### Planned (Phase 4)

- [ ] Role-based permissions middleware
- [ ] Maintenance mode
- [ ] RTL support
- [ ] Custom post type builder
- [ ] Custom fields (ACF alternative)
- [ ] Notification system

---

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL

---

## Installation

The steps below set up a local development environment.

1. **Clone the repository**

   ```bash
   git clone https://github.com/mr-ruhid/rj-cms-ai.git
   cd rj-cms-ai
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**

   ```bash
   npm install
   ```

4. **Configure the environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Then open `.env` and set your database credentials.

5. **Run migrations and seeders**

   ```bash
   php artisan migrate --seed
   ```

6. **Build assets and start the server**

   ```bash
   npm run dev
   php artisan serve
   ```

   Run each command in its own terminal, since `npm run dev` keeps running.

### Default Admin Credentials

| Field    | Value             |
| -------- | ----------------- |
| Email    | `admin@admin.com` |
| Password | `admin123`        |

> These credentials are for local development only. Change them immediately on any environment that is reachable from the internet.

---

## Ecosystem

- **Themes:** RJ CMS Registry (add the registry URL here)
- **Plugin, Form Builder:** [rj-plugin-form-builder](https://github.com/mr-ruhid/rj-plugin-form-builder)

---

## Contributing

Contributions are welcome. Please read the contributing guide in the Form Builder plugin repository for the workflow, coding standards (PSR-12), and pull request process, and open an issue before starting work on large changes.

To report a bug or request a feature, use the [issue tracker](https://github.com/mr-ruhid/rj-cms-ai/issues).

---

## License

RJ CMS core is open-source software licensed under the [MIT License](LICENSE).

---

## Credits

- **Creator:** Ruhid Javadov
- Built with Laravel, Tailwind CSS, and Alpine.js.
