# Pages Management

This document explains how to manage static pages (such as Home, About, Contact, etc.) in RJ CMS.

## How to create a new page
1. Log in to the admin panel.
2. Go to **Pages** in the left sidebar.
3. Click the **Add New** button.
4. Enter a title for your page.
5. Choose your editing method:
   *   **Classic Editor:** Use a standard text editor for simple text and images.
   *   **RJBuilder:** Open the visual drag-and-drop builder to design complex, custom layouts.
   *   **Theme Template:** Select a predefined custom template provided by your active theme (e.g., Contact Page, Full Width).
6. Set the **Page Attributes**: If your theme supports it, you can assign a "Parent Page" to create a hierarchical menu structure.
7. Click **Publish** to make the page live, or **Save as Draft**.

## How to set the Homepage
By default, the latest posts are shown on the homepage. To change this:
1. Go to **Settings > Reading**.
2. Under "Your homepage displays", select **A static page**.
3. Choose your desired page from the **Homepage** dropdown.
4. Choose a page for the **Posts page** (where your blog posts will appear).
5. Click Save Changes.

## How to edit or delete a page
1. Go to **Pages**.
2. Hover over the page name to see the action buttons.
3. Click **Edit** to make changes, or **Trash** to move it to the trash. 
4. To permanently delete, click the **Trash** filter and select **Delete Permanently**.

## Features
*   **Page Builder:** Full RJBuilder integration for custom page designs.
*   **Theme Templates:** Assign custom PHP/Blade templates defined in your `theme.json` file.
*   **Hierarchy:** Create parent and child pages for structured navigation.
*   **SEO:** Dedicated fields for SEO titles, meta descriptions, and Open Graph tags.
*   **Multilingual:** Translate your pages into multiple languages using the language tabs.
