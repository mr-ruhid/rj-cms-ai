# Plugins Management

This document explains how to extend the functionality of RJ CMS using plugins.

## How to install a new plugin
You have three ways to install a plugin:
1.  **Browse Community:** Go to **Plugins**, click **Browse Community**, and install directly from the official registry.
2.  **Upload ZIP:** Click the **Upload Plugin** button, select a `.zip` file containing a valid `plugin.json` and source code, and click Upload.
3.  **GitHub URL:** You can install directly from a GitHub repository by providing the repository URL.

## How to activate a plugin
1.  Go to **Plugins** in the admin sidebar.
2.  Find the plugin in the list.
3.  Click the **Activate** button. 
4.  Once activated, the plugin may add new menus to the sidebar, new widgets to RJBuilder, or new routes to your site.

## How to update a plugin
1.  Go to **Plugins**.
2.  Click the **Check for Updates** button at the top. RJ CMS will query the plugin's remote `plugin.json` file.
3.  If an update is available, an amber **Update** button will appear next to the plugin.
4.  Click Update. The system will download the new version, overwrite the old files, and run any new migrations automatically.
5.  It is highly recommended to back up your database before updating plugins.

## How to use the Plugin Editor
The Plugin Editor is an advanced tool for developers to modify plugin code directly from the browser.
1.  Go to **Plugins > Editor**.
2.  Select an active plugin from the dropdown.
3.  The file tree will load on the left. Click a file to open it in the Ace Editor.
4.  Make changes and click **Save**.
5.  The editor has a built-in **AI Code Review** button that analyzes your code for syntax errors, security issues, and best practices.

## Plugin Architecture (For Developers)
*   **Location:** Plugins are stored in the `rj-content/plugins/` directory.
*   **Manifest:** Every plugin must have a `plugin.json` file at its root. This file defines the plugin's name, slug, version, namespace, and service provider.
*   **Service Provider:** Plugins boot up via a standard Laravel Service Provider (e.g., `FormBuilderServiceProvider`).
*   **Hooks:** Plugins can interact with the core using the `HookRegistry` (Actions and Filters).
*   **Widgets:** Plugins can register custom RJBuilder widgets.
*   **Migrations:** Plugins can include their own database migrations. The system handles running these when the plugin is installed or updated.

## Form Builder Plugin
RJ CMS comes with a built-in Form Builder plugin (v1.1.1).
*   It adds a **Forms** menu to the sidebar.
*   It allows you to create forms with various field types (text, email, textarea, select).
*   It provides a `FormWidget` for RJBuilder, so you can embed forms into any page.
*   Submissions are stored in the database and can be viewed in the admin panel.
