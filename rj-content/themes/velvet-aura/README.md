<p align="center">
  <img src="https://raw.githubusercontent.com/rj-nexus/velvet-aura/main/assets/images/velvetlogo_dark.png" alt="VelvetAura Logo" width="250">
</p>

<h1 align="center">VelvetAura</h1>

<p align="center">
  A premium, modern e-commerce theme exclusively designed for RJ CMS and RJ Commerce. VelvetAura offers a luxurious, high-end shopping experience with deep RJBuilder integration.
</p>

## Requirements

- **PHP:** >= 8.2
- **RJ CMS:** >= 1.0.0
- **RJ Commerce Plugin:** Required for full e-commerce functionality.
- **Database:** MySQL

## Installation

1. Download the `velvet-aura.zip` file from the Releases tab.
2. Log in to your RJ CMS Admin Panel.
3. Navigate to **Appearance > Themes** and click **Upload Theme**.
4. Upload the ZIP file and click **Activate**.

*Alternatively, you can manually extract the theme into `rj-content/themes/velvet-aura` and activate it from the admin panel.*

## Features

- **3 Premium Header Styles:** Choose between Luxury Dark, Elegant Light, and Editorial designs.
- **3 Premium Footer Styles:** Includes Dark Luxury, Light Minimal, and Editorial designs.
- **RJBuilder Integration:** Custom widgets allow you to build pages, headers, and footers visually.
- **Dynamic Menus:** Fully integrated with RJ CMS menu system (Header and Footer locations).
- **Editable Announcement Bars:** Change the top bar text directly from the RJBuilder widget settings.
- **Custom Logo Support:** Override the default logo per header/footer widget instance.
- **E-commerce Ready:** Beautifully styled views for Shop, Product, Cart, and Checkout pages (when RJ Commerce is active).

## RJBuilder Widgets

VelvetAura comes with 4 custom widgets specifically designed for the RJBuilder. You can find them under the **Velvet Aura** category in the builder panel:

1. **Velvet Header:**
   - Select between 3 header styles.
   - Edit the announcement bar text.
   - Upload a custom logo override.
2. **Velvet Footer:**
   - Select between 3 footer styles.
   - Edit the company description and copyright text.
   - Upload a custom logo override.
   - Change the giant background text (Style 3 only).
3. **Hero Slider:**
   - Full-width, high-impact hero section.
   - Configurable background image, title, subtitle, and CTA button.
4. **Featured Products:**
   - Display a curated grid of products.
   - Filter by category, set the number of columns, and limit the number of items.

## Theme Structure

If you want to customize the theme, here is a quick overview of the directory structure:

- `assets/` - Contains images, logos, and other static assets.
- `src/` - Contains the `VelvetAuraServiceProvider` and custom Widget classes.
- `views/` - Contains the Blade templates.
  - `layouts/` - Main layout files.
  - `partials/headers/` - The 3 header styles.
  - `partials/footers/` - The 3 footer styles.
  - `widgets/` - Views for the custom RJBuilder widgets.
  - `commerce/` - Custom e-commerce views (if overriding RJ Commerce defaults).

## Screenshots

| Header Style 1 | Header Style 2 | Header Style 3 |
| :---: | :---: | :---: |
| ![Header 1](https://raw.githubusercontent.com/rj-nexus/velvet-aura/main/assets/images/header1.png) | ![Header 2](https://raw.githubusercontent.com/rj-nexus/velvet-aura/main/assets/images/header2.png) | ![Header 3](https://raw.githubusercontent.com/rj-nexus/velvet-aura/main/assets/images/header3.png) |

| Footer Style 1 | Footer Style 2 | Footer Style 3 |
| :---: | :---: | :---: |
| ![Footer 1](https://raw.githubusercontent.com/rj-nexus/velvet-aura/main/assets/images/footer1.png) | ![Footer 2](https://raw.githubusercontent.com/rj-nexus/velvet-aura/main/assets/images/footer2.png) | ![Footer 3](https://raw.githubusercontent.com/rj-nexus/velvet-aura/main/assets/images/footer3.png) |

## License

This theme is open-sourced software licensed under the [MIT License](LICENSE).
