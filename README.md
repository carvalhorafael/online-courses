# Online Courses

Online Courses is a WordPress plugin that owns a reusable "online courses" content domain. It registers the custom post type, taxonomy and essential checkout metadata needed to publish courses while leaving visual presentation to the active theme.

## What It Provides

- Custom post type: `course`
- Custom taxonomy: `course_category`
- REST-enabled metadata:
  - `_online_courses_checkout_url`
- A WordPress admin meta box for the course checkout URL
- Editable course blocks for the block editor
- A default block structure for new courses
- Rewrite rules for `/cursos/`, `/cursos/{slug}/` and `/cursos/categoria/...`
- GitHub Releases update integration through the plugin `Update URI`

## What It Does Not Provide

This plugin does not render public templates and does not implement checkout, payments, enrollment, student areas or access control. Themes and integration plugins should consume the content domain and decide how to display or process it.

For example:

- A theme may provide `single-course.php` and `taxonomy-course_category.php`.
- A theme template may read `online_courses_get_checkout_url()` and render a checkout link.

## Course Editor Template

New courses start with an unlocked block editor structure using Online Courses blocks for the main course content:

- `online-courses/learning-outcomes`
- `online-courses/course-curriculum`
- `online-courses/requirements`
- About this course
- `online-courses/audience-fit`
- `online-courses/instructor-bio`

The custom blocks store editable course content and render Design System-compatible markup on the front end. The active theme remains responsible for page composition, layout, spacing and loading the visual system.

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
