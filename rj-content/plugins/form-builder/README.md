# RJ CMS — Form Builder Plugin

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![RJ CMS](https://img.shields.io/badge/RJ%20CMS-%3E%3D1.0.0-purple)

A powerful, fully isolated form builder plugin for **RJ CMS**. Create custom forms, manage submissions, and embed them anywhere using RJBuilder — no core modifications required.

---

## Features

- **Drag & Drop Form Builder** — Create forms with a modern UI built on Alpine.js.
- **Multiple Field Types** — Text, email, phone, textarea, select, and checkbox.
- **Submissions Management** — View, read, and delete user submissions.
- **RJBuilder Integration** — Includes a dedicated "Form Widget" to embed your forms visually.
- **Fully Isolated Architecture** — Has its own routes, views, migrations, and assets. Does not interfere with the core system.
- **Extensible** — Built using RJ CMS Plugin API (Service Providers & Autoloaders).

---

## Requirements

- **RJ CMS** `>=1.0.0`
- **PHP** `>=8.2`
- **Composer** installed on the host server.

---

## Installation

### Method 1: Via RJ CMS Admin Panel (Recommended)
1. Download the latest `form-builder.zip` from the [Releases](https://github.com/mr-ruhid/rj-plugin-form-builder/releases) page.
2. Go to **Admin → Plugins → Upload Plugin**.
3. Select the ZIP file and click Upload.
4. Once uploaded, click **Activate** next to the plugin.

### Method 2: Manual Installation
1. Clone this repository or download the ZIP.
2. Extract it into `rj-content/plugins/form-builder/`.
3. Go to **Admin → Plugins**.
4. Click **Scan for Plugins** (if available) or refresh the page.
5. Click **Activate** on the Form Builder plugin.

---

## Usage

1. **Create a Form:** Go to **Admin → Plugins → Forms** and click "Create Form".
2. **Add Fields:** Use the built-in builder to add fields, set labels, and mark them as required.
3. **Embed in Builder:** Go to **RJBuilder**, add the **Form** widget to your page, and select the form you created from the dropdown.
4. **View Submissions:** Go to the Forms list and click the envelope icon to view user submissions.

---

## Architecture

This plugin is a great reference for building your own RJ CMS plugins. It demonstrates:

- **Service Provider:** `FormBuilderServiceProvider` extends the core `PluginServiceProvider`.
- **Routes:** Handled independently via `routes/admin.php` and `routes/frontend.php`.
- **Views:** Namespaced under `plugin-form-builder::`.
- **Migrations:** Auto-loaded via `src/Migrations/`.
- **Widgets:** Registered automatically using the `builder.widgets` filter.

---

## Development

If you want to contribute or customize this plugin:

1. Make sure you have RJ CMS installed locally.
2. Symlink or copy this folder to `rj-content/plugins/form-builder`.
3. Activate the plugin from the admin panel.
4. Modify the code. The core system will automatically pick up the changes.

---

## License

This project is licensed under the **MIT License**. See the [LICENSE](LICENSE) file for details.

---

## Credits

- **Author:** [mr-ruhid](https://github.com/mr-ruhid)
- Built for [RJ CMS](https://github.com/mr-ruhid/rj-cms-ai)