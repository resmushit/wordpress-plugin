# reSmush.it Image Optimizer for Wordpress

Wordpress plugin for the **reSmush.it Image Optimization API**

## What is it ?

Use reSmush.it Image Optimizer for **FREE** to **optimize your pictures file sizes**. Improve your performances by using reSmush.it, the 3 billion images API optimizer.

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
- [ ] Add EXIF preservation option
- [ ] Add option to delete *unsmushed* pictures
- [ ] Add a pre-requisites test before running
- [ ] Provide a deeper log level
- [ ] Manage error when a picture is referenced in the DB but the file is missing on the server
- [ ] Enhance the old-jquery progress bar while optimizing
- [ ] Fix individual Optimize Button
- [ ] Provide a "restore" option if plugin is deactivated
- [ ] Provide option to optimize only some image size
- [ ] Provide a S3 support
- [ ] Provide a warning message when using reSmush.it with incompatibles plugins
- [ ] Add a link to the future GPDR and Privacy Policy Page on https://resmush.it


## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/charlyie/resmushit-wordpress). 

## Authors

* **Charles Bourgeaux** - *Initial work* - [reSmush.it](https://resmush.it)

This plugin has initially been developped by [Maecia Agency](https://www.maecia.com/ "Maecia Drupal & Wordpress Agency"), Paris.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments
More informations on the plugin on the [Wordpress Plugin Page](https://fr.wordpress.org/plugins/resmushit-image-optimizer/ "Wordpress Plugin Page")

## Support on Beerpay
Hey dude! Help me out for a couple of :beers:!

[![Beerpay](https://beerpay.io/charlyie/resmushit-wordpress/badge.svg?style=beer-square)](https://beerpay.io/charlyie/resmushit-wordpress)  [![Beerpay](https://beerpay.io/charlyie/resmushit-wordpress/make-wish.svg?style=flat-square)](https://beerpay.io/charlyie/resmushit-wordpress?focus=wish)
