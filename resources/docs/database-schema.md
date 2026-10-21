# RJ NEXUS - Database Schema

> All tables use the `rj_` prefix (except Laravel defaults). MySQL 8+, database `rj_cms`.

## 1. Naming Rules

- Every CMS table is prefixed `rj_`
- Laravel defaults are kept unchanged: `users`, `sessions`, `cache`, `jobs`, `migrations`, `password_reset_tokens`, `failed_jobs`, `job_batches`, `cache_locks`
- Translation tables: `rj_{entity}_translations`
- Pivot tables: `rj_{a}_{b}` (e.g. `rj_post_category`, `rj_role_permission`)

## 2. Core Content Tables

| Table | Purpose | Key Columns |
|---|---|---|
| `rj_posts` | Posts and pages | id, post_type, status, author_id, blocks, edit_mode, published_at |
| `rj_post_translations` | Post/page per locale | post_id, locale, title, slug, excerpt, content, seo_title, seo_description, og_image |
| `rj_categories` | Categories | id, parent_id, slug, status |
| `rj_category_translations` | Category per locale | category_id, locale, name, description |
| `rj_tags` | Tags | id, slug |
| `rj_tag_translations` | Tag per locale | tag_id, locale, name |
| `rj_terms` | Custom taxonomy terms | id, taxonomy_id, parent_id |
| `rj_term_translations` | Term per locale | term_id, locale, name, description |
| `rj_taxonomies` | Custom taxonomies | id, slug, hierarchical |
| `rj_post_types` | Custom post types | id, slug, supports |
| `rj_custom_fields` | ACF-alternative definitions | id, post_type, slug, type, config |
| `rj_custom_field_values` | Custom field values | field_id, post_id, locale, value |
| `rj_post_category` | Post-Category pivot | post_id, category_id |
| `rj_post_tag` | Post-Tag pivot | post_id, tag_id |
| `rj_post_term` | Post-Term pivot | post_id, term_id |
| `rj_revisions` | Post revisions | id, post_id, content, author_id |
| `rj_comments` | Comments | id, post_id, author_id, parent_id, status, content |
| `rj_media` | Media library | id, filename, path, mime, size, alt, caption |

## 3. RJBuilder Tables

| Table | Purpose |
|---|---|
| `rj_templates` | Builder templates (header / footer / page / popup / block): name, slug, type, content, status, is_default, conditions, settings |
| `rj_global_widgets` | Reusable widgets |

## 4. AI Tables

| Table | Purpose |
|---|---|
| `rj_ai_logs` | AI call history (model, tokens, cost, duration, status) |
| `rj_ai_prompts` | Prompt library |
| `rj_knowledge` | RAG knowledge base |
| `rj_knowledge_translations` | RAG translations |

## 5. Auth & Permissions

| Table | Purpose |
|---|---|
| `users` | Laravel default plus `role` column |
| `rj_roles` | 6 default roles |
| `rj_permissions` | 32 permissions |
| `rj_role_permission` | 96 role-permission assignments |
| `rj_user_role` | User-role pivot |
| `password_reset_tokens` | Laravel default |
| `sessions` | Laravel default |

## 6. Menus

| Table | Purpose |
|---|---|
| `rj_menus` | Menu containers |
| `rj_menu_items` | Menu items (nested, with translations) |

## 7. Forms (Plugin Reference)

| Table | Purpose |
|---|---|
| `rj_forms` | Form definitions |
| `rj_form_fields` | Form fields |
| `rj_form_submissions` | Submitted entries |

## 8. Plugins, Themes, Widgets

| Table | Purpose |
|---|---|
| `rj_plugins` | Installed plugins, status, version |
| `rj_themes` | Installed themes, active state |
| `rj_theme_customizations` | Per-theme customizer data |
| `rj_widgets` | Uploaded widgets library |
| `rj_custom_fonts` | Custom font uploads |
| `rj_custom_icons` | Custom icon uploads |

## 9. System

| Table | Purpose |
|---|---|
| `rj_options` | Global options (key-value, cached) |
| `rj_notifications` | Notification queue |
| `rj_notification_settings` | Per-user preferences |
| `rj_activity_log` | User action audit trail |

## 10. Key Relationships

- `rj_posts` to `rj_post_translations` (1:N, per locale)
- `rj_posts` to `rj_post_category` to `rj_categories` (M:N)
- `rj_posts` to `rj_post_tag` to `rj_tags` (M:N)
- `rj_posts` to `rj_comments` (1:N)
- `rj_posts` to `rj_revisions` (1:N)
- `rj_posts` to `rj_custom_field_values` to `rj_custom_fields` (M:N via values)
- `rj_templates` to `rj_global_widgets` (referenced by content JSON)
- `users` to `rj_user_role` to `rj_roles` to `rj_role_permission` to `rj_permissions`
- `rj_taxonomies` to `rj_terms` to `rj_term_translations`

## 11. Translation Pattern

Every translatable entity has:

- A main table: `rj_{entity}`
- A translation table: `rj_{entity}_translations` with a `locale` column
- Translatable fields: `name`, `slug`, `description`, `content`, `excerpt`, `seo_title`, `seo_description`, `og_image`, `meta`

**RJBuilder translation:** only for `templateType === 'page'`. Translatable keys inside the content JSON: `text`, `alt`, `caption`, `title`, `subtitle`, `description`, `label`, `button_text`, `content`, `placeholder`.

## 12. Models (40+)

All models live under `app/Models/`. Table mapping examples:

| Model | Table |
|---|---|
| `Post` | `rj_posts` |
| `PostTranslation` | `rj_post_translations` |
| `Template` | `rj_templates` |
| `Knowledge` | `rj_knowledge` |
| `AiLog` | `rj_ai_logs` |
| `AiPrompt` | `rj_ai_prompts` |
| `Form` | `rj_forms` |

## 13. Migrations Order (Critical)

1. Laravel defaults (`0001_01_01_*`)
2. `add_role_to_users_table`
3. `yarat_setting_tabili` (options)
4. `yarat_kateqoriya_tabili` (categories)
5. `yarat_tags_tabili` (tags)
6. `yarat_templates_tabili` (builder)
7. `yarat_menus_tabili` and `elave_et_translations_menu_items`
8. `pop_up_rj_builder`
9. Forms tables (`form_tabili`, `form_tabili_field`, `form_tabili_submission`, `yarat_global_form_vidceti`)
10. Custom fonts and icons
11. Posts and translations
12. Comments, revisions
13. Roles, post types, taxonomies, custom fields
14. Terms, notifications, activity log
15. Plugins, widgets, themes
16. SEO fields, category translations
17. AI tables (logs, prompts, knowledge)

**Never reorder.** Foreign keys depend on this order.

## 14. Seeding

| Seeder | Purpose |
|---|---|
| `AdminUserSeeder` | Default admin user (`admin@admin.com` / `admin123`) |
| `PermissionSeeder` | 6 roles, 32 permissions |
| `PostTypeSeeder` | `post`, `page` |
| `TaxonomySeeder` | `category`, `tag` |
| `OptionSeeder` | 56 default options |
| `GlobalStylesSeeder`, `BreakpointSettingsSeeder` | Global styles and breakpoints |
| `AiPromptSeeder`, `KnowledgeSeeder` | AI core |
| `MenuSeeder`, `PageSeeder`, `PostSeeder`, `TemplateSeeder` | Demo data |

Change the default admin password immediately after installation.
