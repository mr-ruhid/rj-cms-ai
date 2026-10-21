# Media Library Management

This document explains how to manage images, videos, and files in RJ CMS.

## How to upload media
1. Go to **Media** in the left sidebar.
2. Click the **Add New** button.
3. Drag and drop your files into the upload area, or click to browse your computer.
4. Supported formats typically include: JPG, PNG, GIF, SVG, WEBP, MP4, PDF, DOCX.
5. Once uploaded, the file is stored in the `storage/app/public` directory and can be used across your site.

## How to edit media details
1. Go to **Media**.
2. Click on an image or file to open its details panel.
3. You can edit the following fields:
   *   **Title:** For internal organization.
   *   **Alt Text:** Important for SEO and accessibility. Describe what the image shows.
   *   **Caption:** Displayed below the image on the frontend (optional).
   *   **Description:** For internal reference.
4. Click **Save**.

## How to insert media into posts or pages
1. When editing a post/page or using **RJBuilder**, click on an **Image** widget or use the Classic Editor's image button.
2. Click **Select Image** to open the Media Picker modal.
3. Choose an existing image from the library or upload a new one.
4. The URL will be automatically inserted.

## Media Cleanup (Optimization)
RJ CMS includes a tool to find and delete unused media files to save disk space.
1. Go to **Media > Media Cleanup** (or **Tools > Media Cleanup**).
2. The system scans your database and the `rj_media` table.
3. It lists files that are not attached to any post, page, or template.
4. Review the list and click **Delete Unused Files** to remove them permanently.

## AI Features
*   If AI features are enabled, RJ CMS can automatically generate **Alt Text** for images during upload.
*   This improves SEO scores and accessibility without manual effort.

## Features
*   **Drag & Drop:** Fast and intuitive uploading.
*   **Bulk Delete:** Select multiple files and delete them at once.
*   **File Organization:** Filter by type (Images, Videos, Audio, Documents).
*   **Image Sizes:** RJ CMS automatically generates thumbnail, medium, and large versions of uploaded images for better performance.
