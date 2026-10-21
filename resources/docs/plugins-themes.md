# RJ NEXUS - Plugins & Themes Architecture

> Strict isolation model. Plugins and themes never modify core.

## 1. Isolation Principle (Absolute)

Plugins and themes NEVER touch core code. They operate in complete isolation with their own routes, views, migrations and assets.

If a plugin needs to change behavior, it uses hooks and filters, not core edits.

## 2. Plugin System

### 2.1 Plugin Location

`plugins/{plugin-slug}/`

### 2.2 Plugin Structure

```text
plugins/{plugin-slug}/
├── plugin.json            # Manifest
├── src/
│   ├── Providers/         # Service providers
│   ├── Controllers/
│   ├── Models/
│   └── Services/
├── routes/
│   ├── web.php
│   └── admin.php
├── resources/views/
├── database/migrations/
├── assets/
└── lang/
```

### 2.3 plugin.json Manifest

```json
{
  "name": "Form Builder",
  "slug": "form-builder",
  "version": "1.1.1",
  "author": "RJ NEXUS",
  "description": "...",
  "requires": { "rj-nexus": "^1.0" },
  "providers": ["RJ\\Plugins\\FormBuilder\\FormBuilderServiceProvider"]
}
```

### 2.4 Plugin Lifecycle

| Stage | What happens |
|---|---|
| Install | Upload ZIP or install via registry. `PluginManager::install()` runs migrations and registers the provider |
| Activate | Register service provider, load routes and views, register hooks |
| Deactivate | Unregister provider, keep data |
| Uninstall | Roll back migrations, delete files |

### 2.5 Dynamic Autoloader

`PluginManager` registers a PSR-4 autoloader per plugin dynamically. Nothing needs to be added to `composer.json`.

### 2.6 Isolated Routes

Each plugin loads its own `routes/web.php` and `routes/admin.php`, namespaced automatically.

### 2.7 Isolated Views

Views are registered under the namespace `{plugin-slug}::view.name`.

### 2.8 Isolated Migrations

Migrations run in plugin context via `PluginManager` and are tracked separately in `rj_plugins`.

### 2.9 GitHub Updates

The `GitHubRegistry` service checks GitHub for updates. Users see update notifications in the admin panel.

### 2.10 Plugin IDE

Built-in code editor with:

- File tree
- Syntax highlighting
- AI code review (`AiCodeReviewerService`)
- Safe edit / preview / save

### 2.11 Reference Plugin

Form Builder v1.1.1 is the canonical example plugin and uses every isolation feature correctly.

## 3. Theme System

### 3.1 Theme Location

`themes/{theme-slug}/`

### 3.2 Theme Structure

```text
themes/{theme-slug}/
├── theme.json             # Manifest
├── layouts/               # Blade layouts
├── templates/             # RJBuilder templates
├── partials/              # Reusable partials
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── lang/
└── child/                 # Optional child themes
```

### 3.3 theme.json Manifest

```json
{
  "name": "Default Theme",
  "slug": "default",
  "version": "1.0.0",
  "author": "RJ NEXUS",
  "supports": ["rjbuilder", "menus", "widgets"],
  "customizer": {}
}
```

### 3.4 Builder-First Architecture

Themes are built around RJBuilder templates. A theme defines:

- Layout structure
- Template hierarchy
- Default builder templates
- Customizer options

### 3.5 Child Themes

Child themes inherit from the parent and override only the changed files. `theme.json` declares `"parent": "default"`.

### 3.6 Dynamic Asset Injection

- Theme assets are injected via `ThemeManager`
- Only the active theme's assets are loaded
- Child theme assets override parent assets

### 3.7 Template Resolver

`ThemeResolver` and `PopupResolver` decide which template renders which route:

1. Check the active theme template
2. Check the parent theme
3. Fall back to the core default

### 3.8 Theme Customizer

- Driven by the `theme.json` schema
- Colors, fonts, spacing, layout options
- Live preview
- Saved to `rj_theme_customizations`
- Backed by `ThemeCustomizerService`

### 3.9 Theme Editor

In-browser file editor for theme files, backed by `ThemeEditorService` and `ThemeEditorController`.

### 3.10 Global Styles

Global palette, fonts and spacing are stored in `rj_options` and exposed via `rj_global_colors()`, `rj_global_fonts()`, etc.

### 3.11 Custom Fonts & Icons

- `rj_custom_fonts`: upload custom fonts
- `rj_custom_icons`: custom icon packs
- Managed via `CustomFontController` and `CustomIconController`

## 4. Hooks & Filters

### 4.1 HookRegistry

Defined in `app/Helpers/hooks.php`:

- `add_action($hook, $callback, $priority)`
- `do_action($hook, ...$args)`
- `remove_action($hook, $callback)`
- `add_filter($hook, $callback, $priority)`
- `apply_filters($hook, $value, ...$args)`
- `remove_filter($hook, $callback)`

### 4.2 ShortcodeRegistry

- `add_shortcode($tag, $callback)`
- `do_shortcode($content)`

10 default shortcodes are registered in `app/Helpers/shortcodes.php`.

## 5. Registry

The RJ NEXUS Registry is the central theme and plugin marketplace. It is configured via the registry URL option and is GitHub-backed.

## 6. Rules for AI Assistants

When generating plugin or theme code:

**Do:**

- Place ALL plugin files under `plugins/{slug}/`
- Place ALL theme files under `themes/{slug}/`
- Use `plugin.json` / `theme.json` manifests
- Register service providers in the manifest, never in core
- Use hooks and filters to modify behavior
- Namespace views as `{slug}::view`

**Do not:**

- Edit core files from plugin or theme code
- Add plugin classes to `composer.json`
