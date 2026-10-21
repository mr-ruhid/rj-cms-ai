# Themes & Appearance Management

This document explains how to install, activate, and customize themes in RJ CMS.

## How to install a new theme
You have three ways to install a theme:
1.  **Browse Community:** Go to **Appearance > Themes**, click the **Browse Community** button, and install a theme directly from the official RJ CMS registry.
2.  **Upload ZIP:** Click the **Upload Theme** button, select a `.zip` file containing a valid `theme.json` and theme files, and click Upload.
3.  **GitHub URL:** You can install directly from a GitHub repository if it follows the RJ CMS theme structure.

## How to activate a theme
1.  Go to **Appearance > Themes**.
2.  Browse your installed themes.
3.  Click the **Activate** button on the theme you want to use.
4.  Only one theme can be active at a time. The active theme will have a green "Active" badge.

## How to customize a theme (Customizer)
The Customizer allows you to change the look of your site without writing code.
1.  Go to **Appearance > Themes**.
2.  On the active theme, click the **Customize** button.
3.  The interface reads the `theme.json` file and generates settings dynamically.
4.  You will see sections like **Colors, Typography, Layout, Header, Footer, and Social Links**.
5.  Change values (e.g., pick a new primary color or change the font).
6.  Click **Save Changes** for the specific section. The frontend updates immediately.
7.  If you make a mistake, click **Restore Defaults** to reset the theme to its original state.

## How to use the Theme Editor
The Theme Editor is a built-in code editor for advanced users.
1.  Go to **Appearance > Editor**.
2.  Select the theme you want to edit from the top dropdown.
3.  The left panel shows the file tree (`.blade.php`, `.css`, `.js`, `.json`).
4.  Click a file to open it in the Ace Editor.
5.  Make your changes and press **Ctrl+S** (or click Save).
6.  **Safety Feature:** Every time you save a file, a backup is automatically created.
7.  If you break the site, go to the **Backups** tab in the editor to restore a previous version.
8.  The system validates `theme.json` (checks for valid JSON) and `.blade.php` files (checks for PHP syntax errors) before saving.

## Global Theme Options
Go to **Appearance > Options** to manage site-wide settings that persist even if you change themes:
*   **Site Identity:** Upload global Light/Dark logos, Favicon, and Open Graph image.
*   **Custom Code:** Inject Custom CSS, JavaScript, and HTML into the `<head>`, `<body>` start, or `<body>` end.
*   **Tracking:** Add Google Analytics (GA4), Google Tag Manager (GTM), or Facebook Pixel IDs.

## Features
*   **Child Themes:** Create a child theme to modify a parent theme without losing changes on updates.
*   **Theme Export:** Download any installed theme as a `.zip` file.
*   **Theme Updates:** Check for updates if the theme was installed from GitHub.
*   **Manifest (`theme.json`):** Defines theme metadata, supported features, custom templates, and customizer controls.
