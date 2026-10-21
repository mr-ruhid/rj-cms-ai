# RJ NEXUS - Short Context

RJ NEXUS (formerly RJ CMS NEXUS) is a fully self-hosted, open-source CMS and Web Builder built on Laravel 12. It is a WordPress alternative. Users install it on their own server and keep 100% ownership of their data.

## What it is
- Self-hosted CMS (not SaaS)
- AI-native: AI is the core of the platform, not an add-on
- Visual page builder (RJBuilder), similar to Elementor
- Isolated plugin and theme architecture (never touches core)
- Multilingual: AZ, EN, RU, TR plus dynamic locales, with full RTL support
- Production-ready v1.0.0

## Tech stack
Laravel 12, PHP 8.2, MySQL, Blade, Tailwind v3, Alpine.js, Vite, OpenRouter API (with Pollinations.ai fallback). Prefix: rj_

## Core modules
- RJBuilder: 8 widgets, nested containers, responsive controls, AI assistant
- AI Center: Overview, Models, Prompts, Usage, Tools (all functional)
- AI capabilities: SEO generation, translation, content generation, code review, layout suggestions, RAG chat
- Plugin system: full isolation, GitHub updates, Plugin IDE
- Theme system: builder-first, child themes, customizer, in-browser editor
- RBAC: 6 roles, 32 permissions, 2FA, KYC
- Content: Posts, Pages, Categories, Tags, Terms, Custom Post Types, Custom Fields (ACF alternative), Comments, Media, Menus, Revisions
- Settings: 17 tabs
- Tools: Health, Backup, Logs, Diagnostics, Import/Export
- Form Builder: v1.1.1 (reference plugin)
- Frontend: single/archive/loop views, performance optimizations (lazy load, critical CSS), maintenance mode

## Status
Phases 1 through 4 are complete. Next: Phase 5, E-commerce (v2.0).

## Rules for AI
- Assume every feature listed above is implemented and working
- Wrap all UI text in __()
- Use rj_ prefix for new tables, functions, routes, constants
- For detailed info, refer to separate MD files: rjbuilder.md, ai-system.md, database-schema.md, plugins-themes.md, frontend-theming.md, project-overview.md, roadmap-real.md
- Do not invent features not listed here; ask if unsure

## Repository
github.com/rj-nexus/rj-nexus | Creator: Ruhid Javadov | License: MIT