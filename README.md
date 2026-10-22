<div align="center">

<img src="https://raw.githubusercontent.com/rj-nexus/.github/main/assets/banner.svg" alt="RJ Nexus" width="100%"/>

<br/>

![Status](https://img.shields.io/badge/Status-Pre--Release-orange)
![Version](https://img.shields.io/badge/Version-v1.0.0--rc1-blue)
![License](https://img.shields.io/badge/License-MIT-green)
![Laravel](https://img.shields.io/badge/Laravel-12-red)
![PHP](https://img.shields.io/badge/PHP-8.2-purple)

<br/>

[Website](https://rj-nexus.github.io) &nbsp;|&nbsp; [RJ CMS Core](https://github.com/rj-nexus/rj-nexus) &nbsp;|&nbsp; [Packages](#-package-catalog) &nbsp;|&nbsp; [Contribute](#-contributing)

</div>

<img src="https://raw.githubusercontent.com/rj-nexus/.github/main/assets/divider.svg" alt="" width="100%"/>

## About RJ Nexus

RJ Nexus is the home of **RJ CMS** and its growing ecosystem. This organization hosts the CMS core and publishes the plugins, themes, widgets, and tools that extend it.

**RJ CMS** is a modern, self-hosted content management system built on **Laravel 12**. It is modular by design: plugins and themes are fully isolated from the core, so they can be installed, updated, and removed without touching it. You run it on your own server and keep full ownership of your data.

> **⚠️ Pre-Release Notice.** This is a **Pre-Release (v1.0.0-rc1)** version. Some bugs, incomplete features, and breaking changes are possible. Bug fixes, optimizations, and further improvements will land in the next update, which will be the official release version. Bug reports and feedback from the community are very welcome.

<img src="https://raw.githubusercontent.com/rj-nexus/.github/main/assets/divider.svg" alt="" width="100%"/>

## 📸 Screenshots

### Admin Interface & Core Features

<table>
  <tr>
    <td width="50%" align="center">
      <b>Dashboard</b><br/><br/>
      <img src="https://raw.githubusercontent.com/rj-nexus/rj-nexus.github.io/main/assets/image/screensholt/01.png" alt="Dashboard" width="100%"/>
    </td>
    <td width="50%" align="center">
      <b>AI Tools & Content Generator</b><br/><br/>
      <img src="https://raw.githubusercontent.com/rj-nexus/rj-nexus.github.io/main/assets/image/screensholt/02.png" alt="AI Tools" width="100%"/>
    </td>
  </tr>
  <tr>
    <td width="50%" align="center">
      <b>RJBuilder (Page Builder)</b><br/><br/>
      <img src="https://raw.githubusercontent.com/rj-nexus/rj-nexus.github.io/main/assets/image/screensholt/03.png" alt="RJBuilder" width="100%"/>
    </td>
    <td width="50%" align="center">
      <b>Posts & Pages Management</b><br/><br/>
      <img src="https://raw.githubusercontent.com/rj-nexus/rj-nexus.github.io/main/assets/image/screensholt/04.png" alt="Posts" width="100%"/>
    </td>
  </tr>
  <tr>
    <td width="50%" align="center">
      <b>Security & 2FA</b><br/><br/>
      <img src="https://raw.githubusercontent.com/rj-nexus/rj-nexus.github.io/main/assets/image/screensholt/05.png" alt="Security" width="100%"/>
    </td>
    <td width="50%" align="center">
      <b>Appearance & Themes</b><br/><br/>
      <img src="https://raw.githubusercontent.com/rj-nexus/rj-nexus.github.io/main/assets/image/screensholt/06.png" alt="Appearance" width="100%"/>
    </td>
  </tr>
  <tr>
    <td width="100%" colspan="2" align="center">
      <b>Media Library & Settings</b><br/><br/>
      <img src="https://raw.githubusercontent.com/rj-nexus/rj-nexus.github.io/main/assets/image/screensholt/07.png" alt="Media" width="70%"/>
    </td>
  </tr>
</table>

### Custom Error Pages

RJ CMS ships with responsive, standalone error pages for every major HTTP status code.

<table>
  <tr>
    <td width="33%" align="center"><b>403 Forbidden</b><br/><br/><img src="https://raw.githubusercontent.com/rj-nexus/rj-nexus.github.io/main/assets/image/screensholt/08.png" alt="403" width="100%"/></td>
    <td width="33%" align="center"><b>404 Not Found</b><br/><br/><img src="https://raw.githubusercontent.com/rj-nexus/rj-nexus.github.io/main/assets/image/screensholt/09.png" alt="404" width="100%"/></td>
    <td width="33%" align="center"><b>419 Page Expired</b><br/><br/><img src="https://raw.githubusercontent.com/rj-nexus/rj-nexus.github.io/main/assets/image/screensholt/10.png" alt="419" width="100%"/></td>
  </tr>
  <tr>
    <td width="33%" align="center"><b>429 Too Many Requests</b><br/><br/><img src="https://raw.githubusercontent.com/rj-nexus/rj-nexus.github.io/main/assets/image/screensholt/11.png" alt="429" width="100%"/></td>
    <td width="33%" align="center"><b>500 Server Error</b><br/><br/><img src="https://raw.githubusercontent.com/rj-nexus/rj-nexus.github.io/main/assets/image/screensholt/12.png" alt="500" width="100%"/></td>
    <td width="33%" align="center"><b>503 Maintenance</b><br/><br/><img src="https://raw.githubusercontent.com/rj-nexus/rj-nexus.github.io/main/assets/image/screensholt/13.png" alt="503" width="100%"/></td>
  </tr>
</table>

<img src="https://raw.githubusercontent.com/rj-nexus/.github/main/assets/divider.svg" alt="" width="100%"/>

## ✨ Key Features

- **🤖 AI Integration (OpenRouter):** Generate full blog posts, bulk translate content (preserving HTML), and audit SEO automatically.
- **🎨 RJBuilder (Page Builder):** Elementor-like visual builder with drag-and-drop, responsive controls, and dynamic tags.
- **🔐 Advanced Security:** Two-Factor Authentication (Google Authenticator), Email Verification, and role-based permissions.
- **🌍 Multi-language & RTL Support:** Built-in translations for 6 languages and automatic Right-to-Left rendering for Arabic, Persian, Hebrew, etc. (`InjectRtlDirection` middleware).
- **⚡ Performance First:** Lazy loading for images, optimized DB queries, and fast asset compilation via Vite.
- **🛠️ Maintenance Mode:** Animated 503 page with an easy toggle from the admin panel.
- **🧩 Modular Plugin System:** Plugins are fully isolated with their own routes, views, migrations, and assets. Updates are delivered through GitHub.
- **📝 Custom Error Pages:** Professionally designed 403, 404, 419, 429, 500, and 503 pages.

## 🌐 Supported Languages

| Flag | Language | File |
|:----:|----------|------|
| 🇦🇿 | Azərbaycan dili (Azerbaijani) | `lang/az.json` |
| 🇬🇧 | English | `lang/en.json` |
| 🇩🇪 | Deutsch (German) | `lang/de.json` |
| 🇪🇸 | Español (Spanish) | `lang/es.json` |
| 🇫🇷 | Français (French) | `lang/fr.json` |
| 🇵🇹 | Português (Portuguese) | `lang/pt.json` |

More languages will be added in the next version.

## 📦 What we publish

<table>
  <tr>
    <td width="33%" valign="top">
      <img src="https://raw.githubusercontent.com/rj-nexus/.github/main/assets/icon-plugins.svg" alt="" width="48" height="48"/>
      <h4>Plugins</h4>
      Isolated extensions with their own routes, views, migrations, and assets. Updates are delivered through GitHub.
    </td>
    <td width="33%" valign="top">
      <img src="https://raw.githubusercontent.com/rj-nexus/.github/main/assets/icon-themes.svg" alt="" width="48" height="48"/>
      <h4>Themes</h4>
      Builder-first themes with custom page templates, child themes, and dynamic asset injection.
    </td>
    <td width="33%" valign="top">
      <img src="https://raw.githubusercontent.com/rj-nexus/.github/main/assets/icon-widgets.svg" alt="" width="48" height="48"/>
      <h4>Widgets</h4>
      Building blocks for RJBuilder, the drag-and-drop page builder with responsive controls and nested containers.
    </td>
  </tr>
  <tr>
    <td width="33%" valign="top">
      <img src="https://raw.githubusercontent.com/rj-nexus/.github/main/assets/icon-ai.svg" alt="" width="48" height="48"/>
      <h4>AI tools</h4>
      Integrations built on the OpenRouter-powered AI layer: SEO generation, translation, writing, and layout suggestions.
    </td>
    <td width="33%" valign="top">
      <img src="https://raw.githubusercontent.com/rj-nexus/.github/main/assets/icon-docs.svg" alt="" width="48" height="48"/>
      <h4>Documentation</h4>
      Guides and references for building plugins and themes on top of RJ CMS.
    </td>
    <td width="33%" valign="top">
      <img src="https://raw.githubusercontent.com/rj-nexus/.github/main/assets/icon-contribute.svg" alt="" width="48" height="48"/>
      <h4>Community</h4>
      Open issues, pull requests, and feedback from everyone building with RJ CMS.
    </td>
  </tr>
</table>

<img src="https://raw.githubusercontent.com/rj-nexus/.github/main/assets/divider.svg" alt="" width="100%"/>

## 🧩 Package Catalog

| Package | Type | Status | Repository |
|---------|------|--------|------------|
| RJ CMS | Core | `v1.0.0-rc1` (Pre-Release) | [rj-nexus/rj-nexus](https://github.com/rj-nexus/rj-nexus) |
| Form Builder | Plugin | `v1.1.1` | [rj-nexus/rj-plugin-form-builder](https://github.com/rj-nexus/rj-plugin-form-builder) |
| Official themes | Theme | Coming soon | - |
| More plugins | Plugin | Coming soon | - |

## 🛠️ Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 12, PHP 8.2 |
| Database | MySQL |
| Frontend | Blade, Tailwind CSS v3, Alpine.js |
| Build tool | Vite |
| AI engine | OpenRouter API |

<img src="https://raw.githubusercontent.com/rj-nexus/.github/main/assets/divider.svg" alt="" width="100%"/>

## 🚀 Installation

Follow these steps to set up RJ CMS in your local development environment.

1. **Clone the repository**

   ```bash
   git clone https://github.com/rj-nexus/rj-nexus.git
   cd rj-nexus
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

   Open `.env` and configure your database credentials.

5. **Run migrations and seeders**

   ```bash
   php artisan migrate --seed
   ```

6. **Build assets and start the server**

   ```bash
   npm run build
   php artisan serve
   ```

### Default Admin Credentials

| Field | Value |
|-------|-------|
| Email | `admin@admin.com` |
| Password | `admin123` |

> **⚠️ Warning:** Change these credentials immediately on any production environment.

<img src="https://raw.githubusercontent.com/rj-nexus/.github/main/assets/divider.svg" alt="" width="100%"/>

## 🗺️ Roadmap

### ✅ Completed (v1.0.0-rc1)

- [x] Authentication (Laravel Breeze) and admin middleware
- [x] Settings management (General, AI, SEO, Security, and more)
- [x] Media library with drag-and-drop upload and image optimization
- [x] Categories and tags (multilingual)
- [x] Menu system (header, footer, custom locations)
- [x] Posts and pages CRUD with SEO and AI generation
- [x] RJBuilder core: drag and drop, widgets, responsive controls
- [x] Theme system: active theme loading, view namespaces
- [x] Plugin system engine: dynamic autoloaders, isolated routes
- [x] AI Tools: Content Generator and Bulk Translate (direct DB integration)
- [x] Security: Two-Factor Authentication (2FA) and Email Verification
- [x] Performance: lazy load for images, LCP optimization
- [x] Localization: RTL support, automatic language switching, 6 languages (🇦🇿 🇬🇧 🇩🇪 🇪🇸 🇫🇷 🇵🇹)
- [x] Maintenance Mode: custom animated 503 page
- [x] Custom Error Pages: 403, 404, 419, 429, 500, 503

### 🔄 Next Version (Official Release)

- [ ] Bug fixes and stability improvements
- [ ] Performance optimizations
- [ ] Improved SEO tools
- [ ] Additional languages
- [ ] Critical CSS generation
- [ ] KYC (Know Your Customer) identity verification module
- [ ] Remaining built-in theme views

### 📅 Planned (Post-Release)

- [ ] Custom post type builder
- [ ] Custom fields (ACF alternative)
- [ ] Advanced notification system

<img src="https://raw.githubusercontent.com/rj-nexus/.github/main/assets/divider.svg" alt="" width="100%"/>

## 🤝 Contributing

Contributions of every size are welcome, from bug reports to whole new plugins and themes.

1. Open an issue before starting work on a large change.
2. Fork the repository and create a feature branch.
3. Follow PSR-12 coding standards.
4. Open a pull request with a clear description.

Want your plugin or theme listed here? Open an issue in [rj-nexus/rj-nexus](https://github.com/rj-nexus/rj-nexus) with the repository link and a short description.

<img src="https://raw.githubusercontent.com/rj-nexus/.github/main/assets/divider.svg" alt="" width="100%"/>

<div align="center">

Created by **Ruhid Javadov** &nbsp;|&nbsp; Released under the MIT License &nbsp;|&nbsp; [rj-nexus.github.io](https://rj-nexus.github.io)

</div>