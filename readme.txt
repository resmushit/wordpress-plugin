=== reSmush.it : The original free image compressor and optimizer plugin  ===
Contributors: ShortPixel, resmushit
Donate link: https://ko-fi.com/resmushit
Tags: image, optimizer, image optimization, smush, free image optimization
Requires at least: 4.0.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

reSmush.it is the FREE image compressor and optimizer plugin - use it to optimize your images and improve the SEO and performance of your website.

== Description ==
The reSmush.it Image Optimizer is a **free WordPress image compressor and optimizer** plugin which allows you to smush your website's images so that they load faster.
The plugin is **super easy to use** (just 2 clicks!), supports JPG, PNG and GIF image formats and can be used to bulk optimize current (past) images and automatically optimize all new images.
You can also adjust the optimization levels and exclude certain images.
Since its launch more than 9 years ago, <a href="https://resmush.it" target="_blank">reSmush.it</a> has become the preferred choice for WordPress image optimization as it allows you to smush the images for free.
reSmush.it image optimization service works on <a href="https://resmush.it/tools/" target="_blank">various CMS platforms</a> (WordPress, Drupal, Joomla, Magento, Prestashop, etc.) and is used by **more than 400,000 websites** worldwide.
reSmush.it has earned the reputation of being the best free, fast and easy image optimization plugin out there :-)

**Features:**
- Free bulk image compressor
- Automatic image optimization on upload
- Keep or remove EXIF data
- Image quality selector
- Exclude images from optimization
- Powerful and free image optimizer API
- Customizable settings for image quality
- Automatic image optimization with CRON
- Backup and restore original images
- Image statistics
- File logging for developers

**Other plugins by [ShortPixel](https://shortpixel.com):**

* [FastPixel Caching](https://wordpress.org/plugins/fastpixel-website-accelerator/) - WP Optimization made easy
* [ShortPixel Image Optimizer](https://wordpress.org/plugins/shortpixel-image-optimiser/) - Image optimization & compression for all the images on your website, including WebP & AVIF delivery
* [ShortPixel Adaptive Images](https://wordpress.org/plugins/shortpixel-adaptive-images/) - On-the-fly image optimization & CDN delivery
* [Enable Media Replace](https://wordpress.org/plugins/enable-media-replace/) - Easily replace images or files in Media Library
* [reGenerate Thumbnails Advanced](https://wordpress.org/plugins/regenerate-thumbnails-advanced/) - Easily regenerate thumbnails
* [Resize Image After Upload](https://wordpress.org/plugins/resize-image-after-upload/) - Automatically resize each uploaded image
* [WP SVG Images](https://wordpress.org/plugins/wp-svg-images/) - Secure upload of SVG files to Media Library 

== Installation ==

1. Upload `resmushit-image-optimizer` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. All your new pictures will be automatically optimized !


== Frequently Asked Questions ==

= How does reSmush.it Image Optimizer compare to other image optimization plugins (e.g. Smush, Imagify, TinyPNG, Kraken, EWWW, Optimole)?

reSmush.it Image Optimizer offers advanced image optimization and provides many of the premium features you'll find in competing services for free.
Plus, we've earned a reputation for being the best free, fast and easy image optimization plugin out there:-)

= How great is reSmush.it? =

Since we have optimized more than 25,000,000,000 images, we have acquired new skills. Our service is still in development to bring you new useful features.

= What about WebP and next-generation image formats? =

We are working on a new offer to give you the best of these new features. Please be patient, it will be coming soon :)

= Is there a function "Optimize on upload"? =

Absolutely, this function is activated for all newly added images and can be deactivated if desired.

= Is there a CRON function? =

Yes, you can optimize your images with cronjobs for large (and also for small) media libraries.

= Can I choose an optimization level? =

Yes, by default the optimization level is set to "Balanced". However, you can further optimize your images by changing the optimization level to one of the other four options.

= Can I return to my original images? =

Yes, by restoring an image, you will have your original image available again.

= Is it possible to exclude some images from the optimizer? =

Yes, since version 0.1.2, you can easily exclude an image from the optimizer.

= Am I at risk of losing my existing images? =

No! reSmush.it Image Optimizer creates a copy of the original images and performs optimizations only on the copies.

= Is it free? =

Yes ! Absolutely free, the only restriction is that the images must not be larger than 5 MB.

= Where do I report security bugs found in this plugin? =

Please report security bugs found in the source code of the reSmush.it Image Optimizer plugin through the [Patchstack Vulnerability Disclosure Program](https://patchstack.com/database/vdp/resmushit-image-optimizer). The Patchstack team will assist you with verification, CVE assignment, and notify the developers of this plugin.

== Screenshots ==

1. The simple interface

== Changelog ==

= 1.0.4 =
Release date September 23, 2024
* Tweak: After changing the optimization level, the plugin no longer suggests to re-optimize the entire Media Library;
* Fix: The backup files are correctly removed after restore in all situations;
* Fix: The exclusion check is hidden if the image is already optimized;
* Fix: More links without the correct namespace can now be translated;
* Fix: The texts in the settings have been improved;

= 1.0.3 =
Release date June 7, 2024
* Tweak: Added 5 compression options to choose from and a filter instead of the numeric quality selector;
* Fix: Restore and Force Optimize options are no longer displayed for images without backups;
* Fix: Internationalization now also works for ShortPixel modules (thanks @alexclassroom);
* Fix: Links, texts and mobile layout in the settings have been improved;

= 1.0.2 =
Release date March 29, 2024
* Fix: The new image size after compressing/restoring the image is now saved correctly in WordPress;
* Tweak: Updated the settings page and added more support links and a "Rate Us" button;

= 1.0.1 =
Release date March 12, 2024
* Fix: A PHP Notice was displayed in the logs for certain settings;
* Tweak: Updated the settings page and added a "Support Us" button.

= 1.0.0 =
Release date February 22, 2024
* New: The plugin settings have been moved to the "Settings" menu and the layout has been updated;
* New: Added a Restore button next to each item in the Media Library;
* New: An improved logging mechanism has been added;
* New: The columns of the Media Library have been restructured and the layout and texts have been updated;
* New: A dedicated reSmush.it box has been added on the image edit screen, with all the plugin actions;
* Compat: The plugin is compatible and tested with PHP versions up to 8.3;
* Fix: The exclude function can now be used directly in the list view of the Media Library;
* Fix: The bulk restore function has been improved and should now work better;
* Fix: The wording of the plugin has been updated and the JS messages have been converted to translatable strings;
* Fix: The languages folder has been removed so that the plugin can be translated via the <a href="https://translate.wordpress.org/projects/wp-plugins/resmushit-image
-optimizer/" target="_blank">Translate WordPress project</a>;
* Fix: The JS part has been updated to fix the deprecated jQuery JSON parser;
* Fix: The jQuery events are now associated with the "click" action instead of "mouseup";
* Fix: The plugin actions of files that cannot be processed (PDF, SVG, etc.) have been removed;

= 0.4.14 =
* Fix Optimize button in listing

= 0.4.13 =
* Patreon new message

= 0.4.12 =
* Patreon display message :(

= 0.4.11 =
* Missing image

= 0.4.10 =
* Partnership with Shortpixel
* fix crash bug when uploading non Image document in library (while log enabled)

= 0.4.9 =
* Compatibility with WP 6.1.0
* Compatible with PHP 8.1.X
* Fixed issue on Undefined array key "file" in .../resmushit.php on line 114

= 0.4.8 =
* Incorrect library imported (fix `PHP Fatal error: Uncaught Error: Undefined constant “SECURE_AUTH_COOKIE” in /wp-includes/pluggable.php:923`)

= 0.4.7 =
* Security fixes : CSRF protection for Ajax Calls

= 0.4.6 =
* Security fixes : protection in a WP's way

= 0.4.5 =
* Security fixes : prevent XSS breachs

= 0.4.4 =
* Avoid SSL verifications if certificate of remote endpoints fails.
* Security fixes : escape POST, and admin user check for AJAX requests

= 0.4.3 =
* Compatibility with WP 6.0.1
* Security fix issues (https://www.pluginvulnerabilities.com/2022/02/01/wordpress-plugin-security-review-resmush-it-image-optimizer/)
	* force int to ID in some SQL requests
	* check that user is connected as admin/contributor for AJAX actions
	* Message to indicate that there's no collection of data in contacting remote feed service

= 0.4.2 =
* Compatibility with PHP8+WP 5.8.2

= 0.4.1 =
* Official support of WP-CLI
* Fix cron context optimization

= 0.4.0 =
* New option to restore all original pictures

= 0.3.12 =
* Fix : Default value assignment
* Test on WP 5.7.1

= 0.3.11 =
* Fix : Optimize button not working when creating a new post
* Fix : Default value of variables incorrectly initialized
* Test on WP 5.5.1

= 0.3.10 =
* hotfix : deprecated function used

= 0.3.9 =
* Fix : OWASP & Security fix

= 0.3.8 =
* Fix : Fix warning in variable not set (metadata)
* Fix : Add an extension uppercase check

= 0.3.7 =
* Fix : CSS+JS load on every admin page, now restricted to reSmush.it pages & medias
* Fix : Links verification format for admin menu

= 0.3.6 =
* Fix : cron multiple run issue.

= 0.3.5 =
* New header image, new WP description for plugin page.

= 0.3.4 =
* Issue in version number

= 0.3.3 =
* Fix double cron launch. Timeout added
* Fix "Reduce by 0 (0 saved)" message if statistics are disabled
* Return error if attachment file not found on disk

= 0.3.2 =
* Fix variable check (generate notice)

= 0.3.1 =
* Fix log write (permission issue)
* Fix "Reduce by 0 (0 saved)" error. Optimize single attachment while "Optimize on upload" is disabled

= 0.3.0 =
* Add Backup deletion option
* Add script to delete old backups
* Changed JS inclusion

= 0.2.5 =
* Add Preserve Exif Feature

= 0.2.4 =
* Fix issue on SQL request for table prefix different from 'wp_'

= 0.2.3 =
* Version number issue

= 0.2.2 =
* Fix settings automatically reinitialized.

= 0.2.1 =
* Complete French translation
* Plugin translation fix

= 0.2.0 =
* Add CRON feature
* Code refactoring
* Fix issue for big Media library, with a limitation while fetching attachments
* Fix log path issues

= 0.1.23 =
* Add Settings link to Plugin page
* Limit reSmush.it options to image attachments only
* Fix `RESMUSHIT_QLTY is not defined`

= 0.1.22 =
* Fix on attachment metadata incorrectly returned (will fix issues with other media libraries)

= 0.1.21 =
* Wordpress 5.0 compatibility

= 0.1.20 =
* Fix PHP errors with PHP 7.2
* Code refacto

= 0.1.19 =
* Fix JS on "Optimize" button for a single picture
* Provide a new "Force Optimization" for a single picture

= 0.1.18 =
* Avoid `filesize () : stat failed` errors if a picture file is missing
* Log check file permissions
* Check extensions on upload (avoid using reSmush.it API if it's not a picture)
* Increase API Timeout for big pictures (10 secs)

= 0.1.17 =
* Fix bug (non-working optimization) on bulk upload when "Optimize on upload" isn't selected
* New header banner for 4 billionth images optimized

= 0.1.16 =
* Add correction for allow_url_fopen support
* News feed loaded from a SSL URL

= 0.1.15 =
* Log rotate if file too big

= 0.1.14 =
* Tested up to Wordpress 4.9.5
* New contributor (resmushit)
* Translation completion

= 0.1.13 =
* Tested up to Wordpress 4.9.1
* New header banner for 3 billionth images optimized :)

= 0.1.12 =
* Tested up to Wordpress 4.8.1

= 0.1.11 =
* New header banner for 2 billionth images optimized :)

= 0.1.10 =
* Slovak translation fix

= 0.1.9 =
* Slovak translation fix

= 0.1.8 =
* Italian translation added (thanks to Cristian R.)
* Description minor correction

= 0.1.7 =
* Slovak translation added (thanks to Martin S.)

= 0.1.6 =
* Bug fix when images uploaded > 5MB
* List of files above 5MB
* Translation minor corrections

= 0.1.5 =
* Error management if webservice not reachable
* Filesize limitation increased from 2MB to 5MB

= 0.1.4 =
* CSS Fixes

= 0.1.3 =
* Translation correction
* News feed images correction

= 0.1.2 =
* Delete also original file when deleting an attachment
* Exclusion of an attachment of the reSmush.it optimization (checkboxes)
* Adding french translation
* Code optimizations
* 4.6.x check
* Minor bugs corrections

= 0.1.1 =
* Optimize on upload
* Statistics
* Log services
* Interface rebuild
* News feed from feed.resmush.it

= 0.1.0 =
* plugin base
* bulk optimizer
