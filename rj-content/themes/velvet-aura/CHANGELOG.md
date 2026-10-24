# Changelog

All notable changes to the VelvetAura theme will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0-beta.1] - 2026-10-24

### Added
- Initial beta release of the VelvetAura theme.
- 3 Premium Header styles (Luxury Dark, Elegant Light, Editorial).
- 3 Premium Footer styles (Dark Luxury, Light Minimal, Editorial).
- Dynamic menu support for header and footer locations.
- RJBuilder custom widgets:
  - Velvet Header (with style selector, custom logo, and editable announcement text).
  - Velvet Footer (with style selector, custom logo, description, copyright, and editable background text).
  - Hero Slider (full-width slider with custom text and CTA button).
  - Featured Products (customizable product grid with category filtering).
- Deep integration with RJ Commerce plugin (premium views for shop, single product, cart, and order success).
- `image_select` field type support for RJBuilder widget settings (allows visual style selection).
- Dynamic auto-discovery of theme widget categories in the core WidgetRegistry.
- Full localization support using standard Laravel `__()` helper.

### Security
- Implemented CC BY-ND 4.0 License (Commercial use allowed, code modification strictly prohibited).
