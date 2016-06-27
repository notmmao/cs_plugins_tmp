=== WP Booklet ===
Contributors: binarystash01
Donate link: http://www.binarystash.com
Tags: flip book, flipbook, booklet
Requires at least: 3.9
Tested up to: 4.2.7
Stable tag: 2.1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows creation of flip books using the jQuery Booklet plugin

== Description ==

**NOTICE TO USERS OF WP BOOKLET 1.x**: *WP Booklet has been rewritten and requires old booklets to be imported. Use Booklets->Import to import them after installation.*

WP Booklet 2 makes creating brochures and magazine-like pages easy. It comes with built-in themes for casual users and allows more advanced users to add their own.

This plugin is the successor to WP Booklet 1.x and shares most of its features.

*   PDF uploads
*   Bulk image uploads
*   Page thumbnails
*   Compatibility with mobile devices
*	Full responsiveness
*	Page popups
*	Bulk importer for WP Booklet 1.x booklets

Credits:
Icon made by [Freepik](http://www.freepik.com) from [www.flaticon.com](http://www.flaticon.com) is licensed under [CC BY 3.0](http://creativecommons.org/licenses/by/3.0/)

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Create your flip books under 'Booklets 2'.
4. Copy the provided shortcode and paste it on pages or widgets.

== Frequently Asked Questions ==

= My old booklets are gone after update. What must I do? =
No need to panic, they can be restored via Booklets->Import.

= How do I change the style of my flip book? =

1. Under your theme folder, create a folder named `wpbooklet`.
2. Copy one of the provided themes under `/wp-content/plugins/wp-booklet2/themes/booklet` to `wpbooklet`. For example, `/wp-content/themes/my-theme/wpbooklet/light`.
3. Change the name of your custom theme. For example, `/wp-content/themes/my-theme/wpbooklet/light` to `/wp-content/themes/my-theme/wpbooklet/customtheme`.
4. Update the classes in `/wp-content/themes/my-theme/wpbooklet/customtheme/style.css` and `/wp-content/themes/my-theme/wpbooklet/booklet.php`.
5. Customize the look of your theme.
6. Your new theme should appear under themes menu.

= Do you provide flip book templates? = 

Yes. Two built-in themes are provided.

= How do I upload PDFs? =

Use the "Upload PDF" button beside the "Add pages" button. After upload, your PDF will be processed automatically.

= Why can't I see the "Upload PDF" button? =

WP Booklet relies on PDFInfo, Ghostscript, and Imagemagick for PDF conversion. If you can't see the button, then at least one of them is missing or misconfigured. If they are configured properly, you should see their versions under Booklet->Settings.

You also need to ensure that your uploads folder is writable by the web server. Check this status under Booklet->Settings.

If problems persist despite having proper configurations for the items above, please contact your web administrator or hosting provider.

= How do I upload images in bulk? =

On the media gallery popup, hold "CTRL" on your keyboard while selecting images.

== Screenshots ==

1. Light theme
2. Dark theme
3. Settings page
4. Import page
5. Booklet editor 

== Changelog ==

= 2.1.4 =
* Fixed warnings when a booklet is published

= 2.1.3 =
* Removed transparency during PDF conversion

= 2.1.2 =
* Added page indicator to PDF uploader

= 2.1.1 =
* PDF upload now terminates once maxiumum PHP execution time is reached.

= 2.1.0 =
* Booklet images now preload.

= 2.0.9 =
* Buttons to auto-calculate optimal booklet dimensions were added.
* Shortcode display now auto-selects the text when clicked.

= 2.0.8 =
* Added checking for 'exec'

= 2.0.7 =
* Popups are now scrollable on small screens

= 2.0.6 =
* Code improvements

= 2.0.5 =
* Fixed hidden publishing actions on other post types

= 2.0.4 =
* Restored popups
* Fixed completion message always appearing on the importer page

= 2.0.3 =
* Fixed installation error

= 2.0.2 =
* Fixed booklet editor messages
* Fixed shortcode

= 2.0.1 =
* Fixed booklet editor

= 2.0 =
* Added ability to customize and add new themes
* Shrunk buttons
* Removed popups

= 1.1.6 =
* Fixed unlimited PDF pages upload

= 1.1.5 =
* Fixed settings page

= 1.1.4 =
* Corrected thumbnail sizes

= 1.1.3 = 
* Corrected booklet dimensions
* Synced popups with booklet

= 1.1.2 = 
* Fixed PDF capability detection
* Added popups
* UI enhancements 
* Fixed warnings on admin pages

= 1.1.1 = 
* Removed "Use of undefined constant manage_booklet_custom_columns" warning

= 1.1.0 = 
* Fixed shortcode

= 1.0.9 = 
* PDF testing now involves creating a PDF file and checking for its existence.

= 1.0.8 = 
* Fixed empty pages when PDF has less than 10 pages and PDF limit is on

= 1.0.7 = 
* UI enhancements
* Fixed padding reverting to 10px when set to 0
* Added test for uploads folder's writability

= 1.0.6 = 
* UI enhancements
* Added ability to show thumbnailed previews
* Added ability to upload PDFs
* Added ability to close booklets
* Added ability to upload images in bulk

= 1.0.5 = 
* UI enhancements
* Added ability to add page links

= 1.0.4 =
* Fixed a bug on themes with multiple jQuery versions

= 1.0 =
* Stable version

== Upgrade Notice ==

= 2.1.4 =
* Fixed warnings when a booklet is published

= 2.1.3 =
* Removed transparency during PDF conversion

= 2.1.2 =
* Added page indicator to PDF uploader

= 2.1.1 =
* PDF upload now terminates once maxiumum PHP execution time is reached.

= 2.1.0 =
* Booklet images now preload.

= 2.0.9 =
* Buttons to auto-calculate optimal booklet dimensions were added.
* Shortcode display now auto-selects the text when clicked.

= 2.0.8 =
* Added checking for 'exec'

= 2.0.7 =
* Popups are now scrollable on small screens

= 2.0.6 =
* Code improvements

= 2.0.5 =
* Fixed hidden publishing actions on other post types

= 2.0.4 =
* Restored popups
* Fixed completion message always appearing on the importer page

= 2.0.3 =
* Fixed installation error

= 2.0.2 =
* Fixed booklet editor messages
* Fixed shortcode

= 2.0.1 =
* Fixed booklet editor

= 2.0 =
* Added ability to customize and add new themes
* Shrunk buttons
* Removed popups

= 1.1.6 =
* Fixed unlimited PDF pages upload

= 1.1.5 =
* Fixed settings page

= 1.1.4 =
* Corrected thumbnail sizes

= 1.1.3 = 
* Corrected booklet dimensions
* Synced popups with booklet

= 1.1.2 = 
* Fixed PDF capability detection
* Added popups
* UI enhancements 
* Fixed warnings on admin pages

= 1.1.1 = 
* Removed "Use of undefined constant manage_booklet_custom_columns" warning

= 1.1.0 = 
* Fixed shortcode

= 1.0.9 = 
* PDF testing now involves creating a PDF file and checking for its existence.

= 1.0.8 = 
* Fixed empty pages when PDF has less than 10 pages and PDF limit is on

= 1.0.7 = 
* UI enhancements
* Fixed padding reverting to 10px when set to 0
* Added test for uploads folder's writability

= 1.0.6 = 
* UI enhancements
* Added ability to show thumbnailed previews
* Added ability to upload PDFs
* Added ability to close booklets
* Added ability to upload images in bulk

= 1.0.5 = 
* The ability to add page links was added.

= 1.0.4 =
* Fixed a bug on themes with multiple jQuery versions

= 1.0 =
Stable version
