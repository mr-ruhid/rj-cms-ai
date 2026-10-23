# 🤝 Contributing to RJ Commerce

First off, thank you for considering contributing to **RJ Commerce**! 🎉
It's people like you who make open-source software such a great community.

## 📑 Table of Contents

1. [Where do I go from here?](#1-where-do-i-go-from-here)
2. [Fork & create a branch](#2-fork--create-a-branch)
3. [Coding standards](#3-coding-standards)
4. [Commit messages](#4-commit-messages)
5. [Pull requests](#5-pull-requests)
6. [License](#6-license)

---

## 1. Where do I go from here?

Noticed a bug or have a feature request?

1. Check the [Issues](https://github.com/rj-nexus/rj-commerce/issues) tab to see if someone has already reported it.
2. If not, [open a new issue](https://github.com/rj-nexus/rj-commerce/issues/new) and describe it as clearly as you can.

## 2. Fork & create a branch

If you think you can fix it yourself, fork RJ Commerce and create a branch with a descriptive name.

A good branch name (where issue `#325` is the ticket you're working on):

```sh
git checkout -b feature/325-add-product-reviews
```

## 3. Coding standards

To keep the codebase consistent, please follow these standards:

| Area | Standard |
|------|----------|
| **PHP** | Follow [PSR-12](https://www.php-fig.org/psr/psr-12/). We use Laravel 12 conventions. |
| **Database** | All tables, routes, and helper functions must use the `rj_` prefix (e.g. `rj_products`, `rj_get_option()`). |
| **Translations** | All user-facing strings must be wrapped in the `__()` helper for multi-language support. |
| **Views** | Use Blade templating. File names must be in `kebab-case.blade.php`. |
| **Frontend** | We use Tailwind CSS v3. Avoid inline CSS unless absolutely necessary. |

## 4. Commit messages

We follow the [Conventional Commits](https://www.conventionalcommits.org/) specification. Format your messages like this:

| Type | Description |
|------|-------------|
| `feat(scope)` | A new feature |
| `fix(scope)` | A bug fix |
| `docs(scope)` | Documentation-only changes |
| `style(scope)` | Changes that don't affect the meaning of the code (white-space, formatting, etc.) |
| `refactor(scope)` | A code change that neither fixes a bug nor adds a feature |

**Example:**

```
feat(cart): implement coupon code application
```

## 5. Pull requests

Before submitting, please make sure that:

- [ ] Your code follows the coding standards above.
- [ ] `README.md` is updated if the interface changed (new shortcodes, new API endpoints, etc.).
- [ ] Your branch is up to date with the main repository.
- [ ] Your pull request has a clear title and a description of **what** you changed and **why**.

## 6. License

By contributing to RJ Commerce, you agree that your contributions will be licensed under its [MIT License](LICENSE).

---

💙 Thank you for helping make RJ Commerce better!