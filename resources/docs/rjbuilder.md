# RJ NEXUS - RJBuilder (Visual Page Builder)

> Complete architecture of RJBuilder, the visual drag-and-drop page builder. Fully implemented.

## 1. Purpose

RJBuilder is the Elementor-class visual page builder of RJ NEXUS. The theme architecture is builder-first: themes are built around RJBuilder templates.

## 2. Template Types

The `type` column in `rj_templates` accepts:

| Type | Purpose |
|---|---|
| `header` | Site header |
| `footer` | Site footer |
| `page` | Page content |
| `popup` | Popup / modal |
| `block` | Reusable block |

## 3. Database

Table: `rj_templates`

Columns: `name`, `slug`, `type`, `content` (JSON), `status`, `is_default`, `conditions`, `settings`

### Content JSON Structure

```json
{
  "blocks": [
    { "id": "blk_xxx", "type": "heading", "settings": {}, "children": [] },
    { "id": "blk_yyy", "type": "container", "settings": { "columns": "2" }, "children": [null, { "type": "widget" }] }
  ]
}
```

**Critical rule:** empty slots in `children` arrays are `null`, NOT nested containers. A column is either `null` (empty) or a widget object. A nested container inside a slot is not allowed.

## 4. Builder UI (3 Panels)

### Left Panel

- Add Element
- Sections
- Global Widgets
- Navigator
- History
- Settings
- AI Assistant

### Center Panel (Canvas)

- Desktop / Tablet / Mobile viewport toggle
- Real-time rendering
- Drag-drop, hover, click-to-edit

### Right Panel (Edit Element)

- Content tab
- Style tab
- Motion tab (animations)

## 5. Partials Structure

Location: `rjbuilder/partials/`

| File | Purpose |
|---|---|
| `styles.blade.php` | All builder CSS |
| `tag-picker.blade.php` | Dynamic tag modal |
| `ai-panel.blade.php` | AI assistant modal |
| `fields/` | Reusable field partials (label, text, color, font, dimensions, post-type, terms) |
| `scripts/` | JS modules (data, state, actions, render, panels, fields, index) |

## 6. Widget List (8 widgets)

### 6.1 HeadingWidget

`text`, `level`, `align`, `color`, `font_size`, `font_weight`, `line_height`, `letter_spacing`, `text_transform`, `dims`

### 6.2 TextWidget

`text`, `size`, `align`, `color`, `font_size`, `font_weight`, `line_height`, `letter_spacing`, `text_transform`, `dims`

### 6.3 ImageWidget

`url`, `alt`, `link`, `target`, `width` (auto / full / half / third / custom), `height`, `object_fit`, `align`, `rounded`, `opacity`, `box_shadow`, `dims`

### 6.4 LogoWidget

`url`, `alt`, `link`, `target`, `width` (auto / full / xs / sm / md / lg / xl / custom), `height`, `align`

Default: `width=full` (fills the column completely).

### 6.5 MenuWidget

`menu_id`, `layout`, `align`, `mobile_behavior`, `gap`, `font_size`, `font_weight`, `text_color`, `hover_color`, `hover_style`, `active_color`, `dropdown_bg`, `dropdown_border`, `dims`

### 6.6 ContainerWidget

`width`, `columns`, `column_widths`, `keep_tablet`, `keep_mobile`, `flex_direction`, `justify_content`, `align_items`, `flex_wrap`, `gap`, `padding_dim`, `margin_dim`, `min_height`, `border`, `border_radius`

### 6.7 FormWidget

`form_id`, `layout`, `label_position`, `field_bg`, `field_border_color`, `field_text_color`, `button_bg`, `button_text_color`, `rounded`, `dims`

### 6.8 PostsWidget

`post_type`, `layout` (grid / list / masonry / carousel), `columns`, `limit`, `order_by`, `order`, `categories`, `tags`, `exclude_ids`, `show_image`, `image_ratio`, `show_excerpt`, `excerpt_length`, `show_date`, `show_author`, `show_categories`, `show_read_more`, `read_more_text`, `gap`, `card_style`, `card_bg`, `card_radius`, `title_color`, `title_size`, `text_color`, `meta_color`, `hover_lift`, `pagination`, `dims`

## 7. Styling Capabilities (all implemented)

- Dimensions: px / % / em / rem / vh / vw, with link and responsive support
- Flexbox: direction, justify, align, wrap
- Column widths: `dimWidths` auto-completes (a single `30` becomes `[30, 70]`)
- Min height: custom
- Hover styles: color, background, border
- Typography: full control
- Border and radius
- Background: color, image, gradient
- Box shadow
- Align self and order
- Global styles
- Breakpoint settings
- Responsive per field: D/T/M toggle on every field
- Nested drag-drop into `null` slots
- CSS ID
- `keep_tablet` / `keep_mobile` toggles

## 8. Responsive Behavior

- Per-field D/T/M toggle
- Auto-keep for Header / Footer / Popup: `keep_mobile`, `keep_tablet`, `align_items: center`
- Breakpoints are configurable via `BreakpointSettingsSeeder`

## 9. Dynamic Tags

- `tag-picker.blade.php` modal
- Insert dynamic values (post title, site name, current year, etc.) into any text field
- Fully integrated with the AI panel

## 10. AI Assistant

- Layout suggestions
- Content generation
- Translation
- SEO metadata

Integrated via `ai-panel.blade.php` and backed by `AiBuilderService`, `OpenRouterService` and `FreeAiService`.

## 11. Global Widgets

- Reusable widgets stored in `rj_global_widgets`
- Managed via `GlobalWidgetController`
- Injected into any template
- Edits propagate across all usages

## 12. Template Library

- Pre-built template blocks
- Managed via `TemplateLibraryController` and `TemplateLibraryService`
- Import / export supported

## 13. PostsWidget Preview

- Real preview using `rj_preview_posts` (not mock data)
- Layouts: grid, list, masonry, carousel
- Pagination supported

## 14. Conventions

- Builder views live in `resources/views/admin/rjbuilder/` and `resources/views/rjbuilder/partials/`
- Layout: `rjbuilder.layout`
- Widgets are registered via the widget registry
- All builder state lives in Alpine.js stores
