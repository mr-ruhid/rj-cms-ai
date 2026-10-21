# RJ NEXUS - Frontend & Theming

> Frontend rendering, theme system integration and performance.

## 1. Frontend Architecture

The frontend is rendered via Blade and the active theme. Every route resolves through `ThemeResolver`:

1. Check the active theme's template for the current route
2. Check the parent theme (child theme setup)
3. Fall back to the core default views

## 2. Layouts

| Layout | Used by |
|---|---|
| `frontend.layout` | Public site |
| `admin.layouts.app` | Admin panel |
| `rjbuilder.layout` | Builder canvas |
| `admin.settings.layout` | Settings pages |

## 3. Frontend Views (all implemented)

### 3.1 Single Post View

`frontend/posts/single.blade.php`

- Renders via builder or classic content
- SEO meta injected
- Comments integrated
- Related posts

### 3.2 Archive View

`frontend/posts/archive.blade.php`

- Category, tag, taxonomy and author archives
- Pagination
- Filter support

### 3.3 Loop Card Designs

`frontend/partials/loop-card.blade.php`

- Reusable in PostsWidget
- Variants: grid, list, masonry, carousel

### 3.4 Page View

`frontend/pages/single.blade.php`

- RJBuilder content rendering
- Full SEO support

### 3.5 Home / Preview

- `frontend/home.blade.php`
- `frontend/preview.blade.php` (builder preview)

### 3.6 Form Submission

- `frontend/forms/*.blade.php`
- Backed by `FormSubmitController`

## 4. Theme Resolution

`ThemeResolver`:

- Determines the active theme from `rj_themes`
- Handles child theme inheritance
- Registers view namespaces
- Loads theme assets

`PopupResolver`:

- Injects popups into the frontend render
- Based on `rj_templates` with `type=popup` and their conditions

## 5. Rendering Engine

The `rj-render.php` helper renders RJBuilder content JSON into HTML.

- `rj_render_blocks($blocks)`: render a block array
- `rj_render_widget($widget)`: render a single widget
- Every widget has its own renderer

## 6. SEO in Frontend

Every page injects:

- `<title>` from `seo_title`
- Meta description
- Open Graph tags
- Twitter card tags
- Schema.org JSON-LD
- Canonical URL
- Robots meta

AI-generated SEO data flows into these fields via `AiSeoService`.

## 7. Multilingual Frontend

- Locale is switched via session or URL prefix
- All content queries pull from `*_translations` tables by current locale
- Falls back to the default locale if a translation is missing
- Built-in language switcher UI
- Full RTL support for RTL locales

## 8. Assets

- **CSS:** Tailwind v3 compiled via Vite (`resources/css/app.css`)
- **JS:** Alpine.js (`resources/js/app.js`)
- **Theme assets:** injected by `ThemeManager` per active theme
- **Child theme overrides:** loaded after the parent
- **`rj_*` color classes:** driven by the `rj_css_variables()` helper

## 9. Performance (implemented)

- Lazy image loading (native `loading="lazy"`)
- Critical CSS inlined per route
- Vite asset bundling and cache busting
- `Cache::rememberForever('rj_options')` for options
- Query optimization in `PostService`
- Fragment caching for PostsWidget
- Database indexes on foreign keys and slugs

## 10. Menus in Frontend

`MenuWidget` renders menus via the `rj_menu_tree()` helper:

- Header menu
- Footer menu
- Custom location menus
- Mobile behavior (collapse, drawer, etc.)
- Dropdown support

## 11. Widgets in Frontend

All 8 builder widgets render via `rj_render_widget()`:

- HeadingWidget
- TextWidget
- ImageWidget
- LogoWidget
- MenuWidget
- ContainerWidget
- FormWidget
- PostsWidget

## 12. Global Styles in Frontend

`rj_global_color_css()` outputs CSS variables for:

- Colors (`--rj-color-*`)
- Fonts (`--rj-font-*`)
- Spacing (`--rj-space-*`)
- Breakpoints

## 13. Theme Customizer (Frontend-aware)

Customizer options (from `theme.json`) affect:

- Colors (via CSS variables)
- Typography
- Layout structure
- Header / footer variants
- Widget visibility

Changes are stored in `rj_theme_customizations`.

## 14. Custom Fonts & Icons

- Custom fonts are registered in `<head>` via the `CustomFont` model
- Custom icons are available as an SVG sprite or per-icon class

## 15. Maintenance Mode

When enabled (Settings, Advanced):

- Frontend routes return a 503 view
- Admin routes bypass it (except logout)
- Allowed IPs are configurable
- `Retry-After` header is sent

## 16. RTL Support

- Automatic RTL stylesheet when `app()->getLocale()` is an RTL locale
- `dir="rtl"` applied to `<html>`
- All builder spacing and dimensions flip horizontally
- Icons are mirrored where appropriate
- The admin panel supports an RTL toggle per user

## 17. Frontend Conventions for AI

**Do:**

- Use `frontend.layout` for public views
- Wrap UI text in `__()`
- Use `rj_*` Tailwind color classes
- Pull content via model translation methods
- Inject SEO via layout partials
- Use `rj_render_blocks()` for builder content

**Do not:**

- Hardcode strings
- Bypass `ThemeResolver`

## 18. Frontend File Map

```text
resources/views/frontend/
├── layouts/app.blade.php
├── posts/
│   ├── single.blade.php
│   ├── archive.blade.php
│   └── partials/loop-card.blade.php
├── pages/
│   └── single.blade.php
├── forms/
│   └── submit.blade.php
├── home.blade.php
└── preview.blade.php
```
