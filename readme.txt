=== reSmush.it : the original free image compressor and optimizer plugin  ===
Contributors: ShortPixel, resmushit
Tags: image, optimizer, image optimization, resmush.it, smush, jpg, png, gif, optimization, compression, Compress, Images, Pictures, Reduce Image Size, Smush, Smush.it, free image optimization
Requires at least: 4.0.0
Tested up to: 6.4.3
Stable tag: 0.4.14
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

reSmush.it is the FREE image compressor and optimizer plugin - use it to optimize your images and improve the SEO and performance of your website.

== Description ==
The reSmush.it Image Optimizer is a **free WordPress image compressor and optimizer** plugin which allows you to smush your website's images so that they load faster.
The plugin is super easy to use (just 2 clicks!), supports JPG, PNG and GIF image formats and can be used to bulk optimize current(past) images and automatically optimize all new images.
You can also adjust the optimization levels and exclude certain images.
Since its launch more than 9 years ago, reSmush.it has become the preferred choice for WordPress image optimization as it allows you to smush the images for free.
reSmush.it image optimization service works on various CMS platforms (WordPress, Drupal, Joomla, Magento, Prestashop, etc.) and is used by **more than 400,000 websites** worldwide.

reSmush.it has earned the reputation of being the best free, fast and easy image optimization plugin out there :-)

**Features:**
- Free bulk image compressor
- Automatic image optimization on upload
- Keep or remove EXIF data
- Image quality selector
- Powerful and free image optimizer API
- Customizable settings for image quality
- Automatic image optimization with CRON
- Backup and restore original images
- Image statistics
- File logging for developers


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

Yes, by default the optimization level is set to 92. However, you can further optimize your images by reducing the optimization level.

= Can I return to my original images? =

Yes, by excluding/reverting this asset, you will have your original image available again.

= Is it possible to exclude some images from the optimizer? =

Yes, since version 0.1.2, you can easily exclude an asset from the optimizer.

= Am I at risk of losing my existing images? =

No! reSmush.it Image Optimizer creates a copy of the original images and performs optimizations only on the copies.

= Is it free? =

Yes ! Absolutely free, the only restriction is that the images must not be larger than 5 MB.

== Screenshots ==

1. The simple interface

== Changelog ==
= 0.4.14 =
* Fix Optimize button in listing

== Changelog ==
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
