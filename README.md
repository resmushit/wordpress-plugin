# reSmush.it Image Optimizer for Wordpress

Wordpress plugin for the **reSmush.it Image Optimization API**

## What is it ?

Use reSmush.it Image Optimizer for **FREE** to **optimize your pictures file sizes**. Improve your performances by using reSmush.it, the 10+ billion images API optimizer.

reSmush.it Image Optimizer allow to use **free Image optimization** based on [reSmush.it API](https://resmush.it/ "Image Optimization API, developped by Charles Bourgeaux"). reSmush.it provides image size reduction based on several advanced algorithms. The API accept JPG, PNG and GIF files up to **5MB**.

## Getting Started

This plugin includes a bulk operation to optimize all your pictures in 2 clicks ! Change your image optimization level to fit your needs !
This service is used by **thousands** of websites on different CMS (Drupal, Joomla, Magento, Prestashop...).

1. Upload `resmushit-image-optimizer` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. It will appear in the `Media` section of the WP Admin page.
4. All your new pictures will be automatically optimized !

### Prerequisites

This will require a quite standard PHP configuration

```
allow_url_fopen  must be set to On
CURL extension must be enabled
```

### Usage

Once installed, the plugin will be able to optimize you assets. It will gather all your attachments and it will optimize them one by one. **This operation can take a while**, especially if you have a lot of assets to optimize.

Currently, reSmush.it Plugin doesn't use CRON to execute, you must remain on the Optimization page to make the optimization run.


## Roadmap
- [X] Add EXIF preservation option
- [ ] Add a pre-requisites test before running
- [ ] Provide a deeper log level
- [X] Manage error when a picture is referenced in the DB but the file is missing on the server
- [ ] Manage error when there are server issues
- [ ] Enhance the old-jquery progress bar while optimizing
- [ ] Advanced option : move *unsmushed* pictures to another folder (not web accessible)
- [X] Advanced option : not preserve *unsmushed* pictures
- [X] Fix individual Optimize Button
- [X] Provide a "restore" option if plugin is deactivated
- [ ] Provide option to optimize only some image size
- [ ] Provide a S3 support
- [ ] Provide an Azure Storage support
- [ ] Provide a warning message when using reSmush.it with incompatibles plugins
- [ ] Add a link to the future GDPR and Privacy Policy Page on https://resmush.it
- [X] Wordpress 5.x support
- [X] Provide a CRON feature
- [ ] Support for Arabic file names : https://wordpress.org/support/topic/arabic-named-images-dont-get-optimized/#post-13607644
- [ ] Auto-replace when JPG files is lower than an optimized PNG file
- [ ] fix Optimize button KO : https://wordpress.org/support/topic/new-page-media-optimized-button-doesnt-work/#post-13607661
- [ ] SQL bug? : https://wordpress.org/support/topic/bug-311/
- [ ] Remove SQL entries and revert pictures if uninstalling the plugin
- [X] Create a WP-CLI plugin
 
## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/charlyie/resmushit-wordpress). 

## Authors

* **Charles Bourgeaux** - *Initial work* - [reSmush.it](https://resmush.it)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments
More informations on the plugin on the [Wordpress Plugin Page](https://fr.wordpress.org/plugins/resmushit-image-optimizer/ "Wordpress Plugin Page")

## Support us on our new platform
Support us through Ko-Fi !

[![Kofi](https://feed.resmush.it/images/kofi-button.png)](https://ko-fi.com/resmushit)
