=== Online Courses ===
Contributors: carvalhorafael
Tags: custom-post-type, courses, content
Requires at least: 6.4
Tested up to: 6.5
Requires PHP: 8.1
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Reusable WordPress content domain for online courses.

== Description ==

Online Courses registers a portable WordPress content domain for publishing online courses. It owns the custom post type, taxonomy and essential checkout URL metadata while allowing themes to handle presentation.

The plugin registers:

* `course` custom post type.
* `course_category` taxonomy.
* Checkout URL metadata for each course.

== Installation ==

1. Upload the plugin ZIP through Plugins > Add New > Upload Plugin.
2. Activate Online Courses.
3. Save Settings > Permalinks if rewrite rules need to be refreshed.

== Frequently Asked Questions ==

= Does this plugin render the public course pages? =

No. The active theme should provide templates and styling. This plugin owns the portable content model.

= Does this plugin process checkout or enrollment? =

No. It stores the course checkout URL only. Payment, enrollment, student areas and access control belong to separate systems.

== Changelog ==

= 0.1.0 =

* Initial public plugin foundation.
