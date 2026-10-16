<div align="center">

# 🎨 RJ Default

**The official default theme for [RJ CMS](https://github.com/mr-ruhid/rj-cms-ai)**

Clean, minimal, and builder-first. Designed to work seamlessly with **RJBuilder** — the drag-and-drop page builder included with RJ CMS.

![RJ CMS](https://img.shields.io/badge/RJ%20CMS-%3E%3D1.0.0-blue)
![PHP](https://img.shields.io/badge/PHP-%3E%3D8.2-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green)

</div>

---

## ✨ Features

- **Builder-first** — Works with RJBuilder headers, footers, pages, and popups
- **Single post** — Hero section, featured image, tags, related posts
- **Single page** — Full-width builder blocks or classic content
- **Archive** — Grid layout with pagination and category filter
- **Blog cards** — Reusable loop card partial
- **Popup support** — Automatic popup rendering from Popup Builder
- **Form handling** — AJAX form submission with validation
- **Responsive** — Mobile, tablet, and desktop optimized

---

## 📋 Requirements

| Requirement | Version |
|---|---|
| RJ CMS | `>=1.0.0` |
| PHP | `>=8.2` |
| Laravel | `12` |

---

## 📦 Installation

### Via RJ CMS admin panel

1. Go to **Appearance → Themes → Browse Community**
2. Find **RJ Default** in the registry
3. Click **Install**, then **Activate**

### Manual

1. Download the latest release ZIP
2. Go to **Appearance → Themes → Upload Theme**
3. Select the ZIP file
4. Click **Activate**

### Via GitHub URL

1. Go to **Appearance → Themes → Browse Community**
2. Paste the repository URL into the **From GitHub** field
3. Click **Install**

---

## 🗂 Structure

```text
rj-theme-default/
├── theme.json                  # theme manifest
├── README.md
├── LICENSE
├── screenshot.png
├── views/
│   ├── layout.blade.php        # base HTML layout
│   ├── single-post.blade.php
│   ├── single-page.blade.php
│   ├── archive.blade.php
│   └── partials/
│       ├── popup.blade.php
│       └── loop-card.blade.php
└── assets/
    ├── css/
    └── js/
```

### Views

| File | Purpose |
|---|---|
| `layout.blade.php` | Base HTML — head, body, form handler, popup include |
| `single-post.blade.php` | Individual blog post view |
| `single-page.blade.php` | Static page view (supports builder blocks) |
| `archive.blade.php` | Blog archive with pagination |
| `partials/loop-card.blade.php` | Reusable post card |
| `partials/popup.blade.php` | Popup Builder integration |

### Assets

Add your CSS and JS files to `assets/css/` and `assets/js/`, then register them in `theme.json`:

```json
{
    "assets": {
        "css": ["assets/css/style.css"],
        "js": ["assets/js/script.js"]
    }
}
```

RJ CMS automatically injects them into the layout head and body.

---

## 🎛 Customization

### Colors and fonts

RJ Default respects **Global Colors** and **Global Fonts** from your RJ CMS settings. No code changes needed — just update them from **Settings → Global Styles**.

### Child themes

Create a child theme by adding a `parent` field to your `theme.json`:

```json
{
    "name": "My Child Theme",
    "slug": "my-child-theme",
    "parent": "rj-default",
    "version": "1.0.0",
    "author": "Your Name"
}
```

Child themes override parent views and assets. Place the child theme in `rj-content/themes/my-child-theme/`.

---

## 🛠 Development

### Local workflow

1. Edit views directly in `themes/default/views/`
2. Refresh your browser — no build step needed
3. To test as a standalone theme, activate it from **Appearance → Themes**

### Publishing to the registry

1. Create a GitHub release with a ZIP asset
2. Add your repository to `rj_cms_theme_and_plugin_list`
3. Wait for review

See [CONTRIBUTING.md](CONTRIBUTING.md) for full details.

---

## 📄 License

MIT — see [LICENSE](LICENSE) for details.

## 🙏 Credits

Built for [RJ CMS](https://github.com/mr-ruhid/rj-cms-ai)

Font: [Inter](https://fonts.google.com/specimen/Inter) (via Google Fonts)