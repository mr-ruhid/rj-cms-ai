# Settings Management

This document explains the various configuration options available in the RJ CMS Settings panel.

## General Settings
*   **Site Title & Tagline:** The name of your website and a short description.
*   **Site URL:** The main address of your site (used for routing and links).
*   **Timezone:** Set your local timezone for dates and scheduled posts.
*   **Admin Language:** Change the default language of the admin panel.

## Writing Settings
*   **Default Post Category:** The category assigned to posts if you don't select one.
*   **Default Editor:** Choose between Classic Editor or RJBuilder for new posts.
*   **AI Auto-generate SEO:** Toggle automatic AI generation of meta tags when publishing.

## Reading Settings
*   **Homepage Display:** Choose whether the homepage shows your latest blog posts or a static page.
*   **Posts Per Page:** The maximum number of posts to show on archive pages.
*   **Search Engine Visibility:** Toggle to discourage search engines from indexing the site (useful for staging).

## Discussion Settings
*   **Allow Comments:** Globally enable or disable comments on new posts.
*   **Moderation:** Hold comments for manual approval before they appear.
*   **AI Spam Filter:** Use AI to automatically analyze and flag spam comments.

## Media Settings
*   **Image Sizes:** Define default dimensions for Thumbnail, Medium, and Large image sizes.
*   **Upload Organization:** Choose whether to organize uploads into month/year folders.
*   **AI Image Alt Text:** Automatically generate alt text for uploaded images using AI.

## Permalinks Settings
*   **Post URL Structure:** Choose the format (e.g., `/blog/%postname%/`).
*   **Category & Tag Base:** Customize the prefix for taxonomy archives (e.g., `/category/`).

## Language Settings
*   **Active Languages:** Add, remove, or reorder languages (AZ, EN, RU, TR, etc.).
*   **AI Translation:** Use the AI translator to automatically translate the entire site's JSON files to a new language.

## SEO Settings
*   **Meta Tags:** Default title and description formats.
*   **Sitemap:** Enable/disable auto-generated XML sitemaps.
*   **Robots.txt:** Customize the virtual robots.txt content.

## Email / SMTP Settings
*   **Mailer:** Choose between PHP Mail, SMTP, or Log.
*   **SMTP Host & Port:** Credentials for your mail server.
*   **Test Email:** Send a test email to verify your configuration.

## Security Settings
*   **Login Limits:** Limit failed login attempts to prevent brute-force attacks.
*   **2FA:** Enable Two-Factor Authentication for admin accounts.
*   **reCAPTCHA:** Add Google reCAPTCHA keys to protect forms.

## Cache Settings
*   **Clear Data Cache:** Flushes the application cache and database cache.
*   **Clear View Cache:** Compiles Blade templates to improve performance.
*   **Clear Config Cache:** Caches the `.env` and configuration files.

## Social Login Settings
*   Configure OAuth credentials (Client ID & Secret) for Google, Facebook, GitHub, Apple, and other providers to allow users to log in via social accounts.

## Notification Settings
*   **Admin Email:** Where system alerts are sent.
*   **Event Toggles:** Choose which events trigger an email (New Comment, New User, Update Available, Failed Login, Form Submission).

## API & Integrations
*   **API Keys:** Generate Public and Private keys to allow external applications (mobile apps, Zapier, etc.) to access your site via the REST API.

## AI Settings
*   **OpenRouter API Key:** Enter your API key to enable advanced AI features (OpenRouter).
*   **Default Model:** Choose the primary AI model (e.g., `openai/gpt-4o-mini`).
*   **Task-Specific Models:** Assign different models for SEO, Translation, and Builder tasks.

## Advanced Settings
*   **Custom Code:** Inject Custom CSS, JS, and HTML globally (available in Appearance > Options).
*   **Debug Mode:** Enable or disable detailed error reporting (keep OFF in production).
*   **Maintenance Mode:** Put the site into maintenance mode for updates.
