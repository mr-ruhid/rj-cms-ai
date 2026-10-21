# Menus Management

This document explains how to create, manage, and assign navigation menus in RJ CMS.

## How to create a new menu
1. Log in to the admin panel.
2. Go to **Appearance > Menus** in the left sidebar.
3. Under the "Create New Menu" section, enter a **Menu Name** (e.g., Main Menu).
4. Select a **Location** from the dropdown (Header, Footer, Mobile, or any custom location).
5. Click **Create Menu**.
6. You will be redirected to the menu editor.

## How to add items to a menu
1. In the menu editor, look at the left panel titled **Add Menu Item**.
2. Choose the type of item you want to add:
   *   **Custom Link:** Manually enter a label and URL (e.g., Home, `/`).
   *   **Page:** Select from your published pages. The label will auto-fill based on the page title.
   *   **Post:** Select from your published posts.
   *   **Category / Tag:** Link directly to a taxonomy archive.
3. Depending on the type, select the specific item from the dropdown.
4. You can set a **Parent** (to create dropdowns) and a **Target** (Same window or New window).
5. Click the **Add to Menu** button.
6. The item will appear in the **Menu Structure** panel on the right.

## How to organize and nest items
1. In the Menu Structure panel, hover over an item and click the drag handle (the parallel lines icon).
2. Drag the item up or down to change its order.
3. Drag the item slightly to the right to make it a **child** (dropdown) of the item above it.
4. The system automatically saves the order when you drop the item.

## How to assign a menu to a location
1. In the menu editor, look at the right panel titled **Menu Settings**.
2. Select the desired **Location** (e.g., Header).
3. Check the **Active Menu** toggle to make it live.
4. Click **Save Settings**.

## Features
*   **Drag & Drop:** Reorder items visually.
*   **Auto-fill:** Selecting a Page/Post automatically fills the label and URL.
*   **Multilingual:** Add translations for custom links using the Translations accordion.
*   **Custom Locations:** You can add new theme-specific locations using the "+ Add custom location" button on the Menus index page.
*   **Icons & CSS:** Custom classes and icons can be attached to individual links (via code or extensions).
