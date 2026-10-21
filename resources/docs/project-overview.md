# RJ CMS — Project Overview

> Source of Truth for project identity, status, and capabilities.
> All AI assistants MUST reference this document. Features listed here ARE implemented.

## 1. Project Identity

| Field | Value |
|---|---|
| Name | RJ CMS |
| Version | 1.0.0 |
| Type | Self-hosted CMS (100% data ownership, no SaaS) |
| License | MIT |
| Creator | Ruhid Javadov |
| Philosophy | Modular · Secure · AI-native · Self-hosted |
| Positioning | Modern WordPress alternative with deep AI integration |

## 2. Current Status: PRODUCTION READY ✅

RJ CMS v1.0.0 is a complete, production-ready CMS. All core systems, AI modules, RJBuilder, plugin/theme architecture, and frontend rendering are fully implemented and stable.

## 3. Complete Feature Matrix

### 3.1 Core CMS ✅
- Posts CRUD (multilingual, SEO, revisions, block editor)
- Pages CRUD (multilingual, RJBuilder-driven)
- Categories & Tags (multilingual, hierarchical)
- Terms & Taxonomies (custom taxonomy support)
- Comments (moderation, threading, AI-assisted)
- Media library (drag-drop, optimization, cleanup)
- Menus (drag-drop builder, multi-location, auto-fill)

### 3.2 RJBuilder ✅
Visual page builder with 8 widgets, nested containers, responsive controls (D/T/M), history, animations, AI assistant, global widgets, template library, dynamic tags. Types: header, footer, page, popup, block.

### 3.3 AI System ✅
- AI Center dashboard (Overview, Models, Prompts, Usage, Tools) — fully functional
- OpenRouter integration (primary provider)
- FreeAiService (Pollinations.ai fallback)
- RAG knowledge base (rj_knowledge + translations)
- AI SEO (meta, OG, Twitter, Schema.org)
- AI translation (AZ/EN/RU/TR + dynamic locales)
- AI content generation
- AI code review (Plugin IDE)
- AI layout suggestions in Builder
- AI health monitoring
- AI comment moderation

### 3.4 Plugin System ✅
Fully isolated architecture. Plugins have own routes, views, migrations, assets — never touch core. GitHub-based updates. Plugin IDE with AI code review. Form Builder v1.1.1 (reference plugin).

### 3.5 Theme System ✅
Builder-first architecture. Custom templates, child themes, dynamic asset injection, theme customizer (theme.json), in-browser theme editor, global styles, breakpoints, custom fonts & icons.

### 3.6 Users & Security ✅
- RBAC: 6 roles, 32 permissions, 96 role-permission assignments
- 2FA (Two-Factor Authentication) — implemented
- KYC (Know Your Customer) verification — implemented
- Activity logging
- AdminMiddleware (super_admin + super-admin support)
- Session management, password reset, email verification

### 3.7 Internationalization ✅
- Built-in locales: AZ, EN, RU, TR
- Dynamic locale system (unlimited)
- RTL (Right-to-Left) support — fully implemented
- Per-entity translation tables (rj_{entity}_translations)
- Translation UI for all entities

### 3.8 Settings (17 tabs) ✅
General · Writing · Reading · Discussion · Media · Permalinks · Language · Colors · SEO · Email · Security · Backup · Cache · Social · Notifications · API & Integrations · AI · Advanced.

### 3.9 Tools ✅
Site Health, AI Health, Backup (DB + Files), Log Viewer, Diagnostics, Import/Export (JSON/WXR/CSV).

### 3.10 Appearance ✅
Themes, Customize (theme.json), Widgets (Library), Menus, Editor, Colors (Global Palette), Options (Site Settings).

### 3.11 Frontend ✅
Single post view, archive view, loop card designs, page rendering, form submission, theme-driven layouts, performance optimizations (lazy load, critical CSS).

### 3.12 Additional ✅
Maintenance mode · Notification system · Custom Post Types · Custom Fields (ACF alternative) · Updates system · RJ About · Support (Tickets/Chat/FAQ).

## 4. Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12.69.3 |
| PHP | 8.2.12 |
| Database | MySQL (rj_cms) |
| Frontend | Blade + Tailwind CSS v3 |
| JS | Alpine.js |
| Auth | Laravel Breeze |
| AI (primary) | OpenRouter API |
| AI (fallback) | FreeAiService (Pollinations.ai) |
| Build | Vite |
| Prefix | rj_ |

## 5. Architecture Principles

1. Self-hosted first — no SaaS dependency
2. Modular isolation — plugins/themes never touch core
3. AI-native — AI in every layer
4. rj_ prefix everywhere — tables, functions, routes, constants
5. Translation-first — all UI in __(), all entities translatable
6. Builder-first theming — themes built around RJBuilder

## 6. Key Statistics

- 60+ controllers (Admin, Settings, Auth)
- 40+ models with rj_ prefixed tables
- 25+ services (AI, backup, import/export, plugins, themes)
- 17 settings tabs
- 6 roles, 32 permissions
- 8 builder widgets
- 4 built-in locales + dynamic
- 10 default shortcodes

## 7. Repository

- GitHub: https://github.com/rj-nexus/rj-nexus
- Ecosystem: RJ CMS Registry

## 8. AI Assistant Rules

When answering about RJ CMS:
- ✅ Assume ALL listed features are fully implemented
- ✅ Reference correct file paths and architecture
- ✅ Use rj_ prefix for new code
- ✅ Write UI texts in __()
- ✅ Refer to `database-schema.md`, `ai-system.md`, `rjbuilder.md`, `plugins-themes.md`, `frontend-theming.md` for details
- ❌ Never say "not implemented" or "planned" for listed features
- ❌ Do not invent features NOT listed — ask instead