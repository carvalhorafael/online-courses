# Online Courses

Online Courses is a WordPress plugin that owns a reusable "online courses" content domain. It registers the custom post type, taxonomy and essential checkout metadata needed to publish courses while leaving visual presentation to the active theme.

## What It Provides

- Custom post type: `course`
- Custom taxonomy: `course_category`
- REST-enabled metadata:
  - `_online_courses_checkout_url`
- A WordPress admin meta box for the course checkout URL
- An editable default block structure for new courses in the block editor
- Rewrite rules for `/cursos/`, `/cursos/{slug}/` and `/cursos/categoria/...`
- GitHub Releases update integration through the plugin `Update URI`

## What It Does Not Provide

This plugin does not render public templates and does not implement checkout, payments, enrollment, student areas or access control. Themes and integration plugins should consume the content domain and decide how to display or process it.

For example:

- A theme may provide `single-course.php` and `taxonomy-course_category.php`.
- A theme template may read `online_courses_get_checkout_url()` and render a checkout link.

## Course Editor Template

New courses start with an unlocked block editor structure for the main course content:

- What you will learn
- Course curriculum
- Requirements
- About this course
- Who this course is for
- Instructor

The template uses core WordPress blocks and is fully editable. It provides an authoring starting point only; public rendering remains the responsibility of the active theme.

## Public Contract

The plugin keeps these identifiers stable so existing WordPress content remains portable:

```php
online_courses_post_type(); // course
online_courses_taxonomy(); // course_category
online_courses_checkout_url_meta_key(); // _online_courses_checkout_url
online_courses_get_checkout_url( $post ); // sanitized checkout URL or empty string
```

## Installation

1. Download the latest `online-courses-X.Y.Z.zip` release asset.
2. In WordPress admin, go to Plugins > Add New > Upload Plugin.
3. Upload and activate the ZIP.
4. Flush permalinks if needed by visiting Settings > Permalinks and saving.

## Development

Requirements:

- PHP 8.1+
- Composer
- MySQL for WordPress integration tests
- Subversion for installing the WordPress PHPUnit test suite

Install dependencies:

```bash
composer install
```

Run unit tests:

```bash
composer test:unit
```

Install the WordPress test suite and run integration tests:

```bash
composer install:wp-tests
composer test:wordpress
```

Run the full test suite:

```bash
composer test
```

Build a public ZIP package:

```bash
composer package
```

## Release Flow

Releases are prepared from `develop` and published when the prepared version reaches `main`.

1. Run the `Prepare Release` workflow with `patch`, `minor`, `major` or an explicit version.
2. Merge the generated `release/vX.Y.Z` PR into `develop`.
3. Merge `develop` into `main`.
4. The `Release` workflow validates the plugin, creates tag `vX.Y.Z`, publishes a GitHub Release and uploads `online-courses-X.Y.Z.zip`.

## License

GPL-2.0-or-later.
