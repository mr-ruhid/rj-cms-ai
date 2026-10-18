# Contributing to RJ CMS Form Builder

First off, thank you for considering contributing to the Form Builder Plugin! Contributions from people like you are what make the RJ CMS ecosystem such a great tool.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
  - [Reporting Bugs](#reporting-bugs)
  - [Suggesting Enhancements](#suggesting-enhancements)
  - [Pull Requests](#pull-requests)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Commit Message Guidelines](#commit-message-guidelines)
- [Testing](#testing)
- [License](#license)

## Code of Conduct

Be respectful and constructive. We welcome contributors of all backgrounds and experience levels. Harassment, personal attacks, and discriminatory behavior will not be tolerated.

## How Can I Contribute?

### Reporting Bugs

Before opening a new issue, please search the existing [Issues](https://github.com/mr-ruhid/rj-plugin-form-builder/issues) to make sure the bug has not already been reported.

If you can't find an existing issue, open a new one and include:

- **A clear, descriptive title.**
- **Steps to reproduce** the problem, in order.
- **Expected behavior** vs. **actual behavior**.
- **Environment details**: RJ CMS version, plugin version, PHP version, Laravel version, database, browser.
- **Logs or screenshots** (e.g. `storage/logs/laravel.log`, browser console errors).
- **A code sample or test case** that demonstrates the problem, if possible.

> **Security vulnerabilities:** please do **not** report them in public issues. Contact the maintainer privately instead.

### Suggesting Enhancements

- Search existing issues first to avoid duplicates.
- Open a new issue with the `enhancement` label.
- Describe the behavior you would like to see, the problem it solves, and why it would be useful to other users.
- If possible, include mockups, examples, or alternative approaches you considered.

### Pull Requests

1. **Fork** the repository and create your branch from `main`:
   ```bash
   git checkout -b feature/my-feature
   ```
   Suggested branch prefixes: `feature/`, `fix/`, `docs/`, `refactor/`.
2. **Make your changes**, keeping each pull request focused on a single topic.
3. **Add or update tests** for any new or changed behavior.
4. **Make sure the test suite passes** and your code follows PSR-12 (see [Testing](#testing)).
5. **Update documentation** (README, comments, translations) if your change affects it.
6. **Open the pull request** against `main` with:
   - A clear description of what changed and why.
   - A link to the related issue (e.g. `Closes #42`).
   - Screenshots for any UI changes.

## Development Setup

### Requirements

- A working RJ CMS installation
- PHP and Composer (versions matching your RJ CMS installation)
- Git

### Installation

1. Clone your fork into your RJ CMS installation's plugins directory:
   ```bash
   cd rj-content/plugins
   git clone https://github.com/<your-username>/rj-plugin-form-builder.git form-builder
   ```
2. Go to the **RJ CMS Admin Panel → Plugins** and **activate** the plugin.
3. Make your changes inside the cloned `form-builder` folder.

RJ CMS uses dynamic PSR-4 autoloading for plugins, so your changes are reflected immediately. There is no need to run `composer dump-autoload`.

### Keeping Your Fork Up to Date

```bash
git remote add upstream https://github.com/mr-ruhid/rj-plugin-form-builder.git
git fetch upstream
git rebase upstream/main
```

## Coding Standards

- Follow **Laravel** conventions and the **PSR-12** coding standard.
- Wrap all UI text in `__('String')` so it can be translated.
- Keep the plugin **isolated**. Do not modify core RJ CMS files to make your feature work. Use the provided hooks instead (`rj_add_filter`, `rj_add_action`).
- Follow the existing naming conventions:
  - Controllers: `PascalCaseController.php`
  - Views: `kebab-case.blade.php`
- Validate and sanitize all user input, and escape all output in Blade views.
- Keep methods small and focused, and add docblocks where the intent isn't obvious.

## Commit Message Guidelines

Write clear, meaningful commit messages in the imperative mood:

```
Add file upload field to form builder
Fix validation error on empty select options
Update Azerbaijani translations
```

- Keep the first line under 72 characters.
- Add a longer description after a blank line when the change needs explanation.
- Reference issues where relevant (`Fixes #12`).

## Testing

Before submitting a pull request, run the checks locally:

```bash
# Code style (PSR-12)
./vendor/bin/phpcs --standard=PSR12 .

# Automatically fix style issues
./vendor/bin/phpcbf --standard=PSR12 .

# Test suite
php artisan test
```

Please also manually verify your change in the admin panel, including edge cases and empty states.

## License

By contributing, you agree that your contributions will be licensed under the project's [MIT License](LICENSE).

Thank you for helping make RJ CMS Form Builder better! 🚀