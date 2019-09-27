# WP Headless Previews
Architecture for a headless wordpress app that supports private content previewing (paired with Nuxt in this example).

## How it works
By default, private content is invisible to the Wordpress REST API. Having the ability to preview content privately is important to content editors, and this system adds support for that feature.

An additional permalink field is added so that Wordpress knows where to go for previews:

![Front End Domain Field](https://github.com/chris-geelhoed/wp-headless-previews/blob/master/readme-images/front-end-domain-field.jpg)

Once this is in place, post permalinks will point to the app's front end:

![Altered Permalink](https://github.com/chris-geelhoed/wp-headless-previews/blob/master/readme-images/public-page.jpg)

And if the page is set as private, a token will be appended to the url to allow for the page to be previewed (See the url shown at the bottom of the following image). The front end of the headless app can read this token and use it to fetch protected content. Private pages are not viewable without this.


![Altered Permalink With Token](https://github.com/chris-geelhoed/wp-headless-previews/blob/master/readme-images/private-page-with-token.jpg)

## Setup

### Wordpress
The `wp` directory houses a Wordpress installation. There are 3 plugins included:
1. JWT Authentication for WP-API - This plugin provides authentication for the Wordpress rest API. It is available on the Wordpress plugin directory for free.
2. Headless WP - This is a small plugin that alters the Wordpress admin to connect with a decoupled front end like Nuxt.
3. Classic Editor - This is just a personal preference.

Aside from those plugins, the Wordpress installation can be totally standard. Reference the example `.htaccess` and `wp-config.php` files - there are a few variables that must be provided for this system to work.

### Nuxt (Front Facing Part)
Nuxt has been set to call Wordpress for content. See the example `.env` file - you'll need to specific the url of the Wordpress site there.
