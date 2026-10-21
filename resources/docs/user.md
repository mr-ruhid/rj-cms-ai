## Users & Roles Management

This document explains how to manage users, assign roles, and control access permissions in RJ CMS.

## How to create a new user

1. Log in to the admin panel as an Administrator or Super Admin.
2. Go to **Users** in the left sidebar.
3. Click the **Add New** button.
4. Fill in the required details:
   - **Name:** The user's display name.
   - **Email:** A valid email address for login.
   - **Password:** A secure password (you can use the generate button).
   - **Role:** Assign a role to this user (e.g., Editor, Author, Subscriber).
5. Click **Save**. An invitation email can optionally be sent if configured in settings.

## How to edit or delete a user

1. Go to **Users**.
2. Hover over the user you want to manage.
3. Click **Edit** to update their details or change their role.
4. Click **Delete** to remove the user. Note: You cannot delete the primary Super Admin account.

## Understanding Roles & Permissions

RJ CMS uses a robust Role-Based Access Control (RBAC) system.

### Default Roles

- **Super Admin:** Has absolute control over the entire system, including settings, plugins, and user management. This role cannot be deleted.
- **Administrator:** Has access to almost all features but might be restricted from critical developer tools.
- **Editor:** Can publish, edit, and delete posts, including those written by others.
- **Author:** Can publish and manage their own posts, but cannot edit others' posts.
- **Contributor:** Can write and manage their own posts, but cannot publish them (requires review).
- **Subscriber:** Can only read content and manage their own profile.

### How to create a Custom Role

1. Go to **Settings > Roles** (or click on the **Roles** menu in the sidebar).
2. Click **Add New Role**.
3. Enter a **Role Name** and a **Slug** (e.g., `manager`).
4. Set the **Level** (hierarchy). Super Admin is level 100. Lower numbers mean fewer privileges.
5. Under the **Permissions** section, check the boxes for the specific actions this role is allowed to perform (e.g., `create posts`, `manage settings`, `upload media`).
6. Click **Create Role**.

### How to assign users to a role

1. Go to **Roles**.
2. Find the role and click **Edit**.
3. You can add users directly to the role, or users can be assigned a role from their user edit page.
4. Note: The system links users to roles via a pivot table (`rj_user_role`) for flexibility.

## User Profile

Every user can access their own profile by clicking their name in the top right corner of the admin panel.

- **Update Profile:** Change name, email, and profile picture.
- **Update Password:** Change the account password securely.
- **Two-Factor Authentication (2FA):** If enabled by the admin, users can set up 2FA for extra security.

## Security Features

- **Role Enforcement:** If a user tries to access a page they don't have permission for (e.g., an Author trying to access Settings), they will receive a 403 Unauthorized error.
- **Super Admin Protection:** The `super_admin` role is hardcoded into the system to prevent lockouts. Its permissions cannot be altered.
- **Session Management:** Users can be logged out of all devices if a security breach is suspected.
