=== WordPress Imager ===
Contributors: webaholic
Donate link: http://www.wordpress-imager.de/
Tags: images, image, picture, pictures, photo, photos, flickr
Requires at least: 2.5
Tested up to: 2.5
Stable tag: 0.9.5

WordPress Imager is a way to harmonically integrate flickr galleries into post and template of your WordPress blog.

== Description ==

Please checkout the screencast on howto install and use this Plugin in [english][screencast_en] or in [german][screencast_de]

[screencast_de]: http://wordpress-imager.de/screencast/
            "WordPress Flickr Integration"
[screencast_en]: http://wordpress-imager.de/screencast/lang/en/
            "WordPress Flickr Integration"

= Warning - it is beta =
Today WordPress Imager is **beta** (and I do not mean *beta* like in Googles *beta* stuff which simply seem to be perfect). There may be errors in it and there may be incompabilities which I do or don't know about.
Do not use it until it leaves beta or you're knowing what you do and what the word **backup** means!
Please inform me about every error you step over. I'll setup a forum for this soon - until then you may contact me via the [plugins homepage][plugin homepage].

[plugin homepage]: http://wordpress-imager.de
            "WordPress Flickr Integration"



= What it does =

WordPress Imager is a plugin designed for integration of images from services like flickr. In its first release it will only support flickr but integration other services is planned.

The integration will be as flexible as possible making use of WordPress 2.5 newest features: shortcodes (faster and more easy than they could ever be before) and media buttons.

You will be able to use all features in templates and in posts – only how you put them there will be slightly different.

You will be able to:
* Select a gallery of yours saved at flickr.
* Select any number of your images saved at flickr.
* Search flickr for images (from others) by license, description, title and combinations of those and select any number of this images.

Whenever you include images from others WordPress Imager will authomatically add the needed Attribution into the lightboxes footnote.

WordPress Imager supports blogs with multiple authors by saving authentification data for each user and only searches with the authentification for the logged in user.

WordPress Imager makes use of “lytebox” technologie to provide Lightboxes to show the lage picture (including the Image Description as footnote) and providing even slideshows in the Lightbox.

= Plans for the future =
*	Include upload capabilities for direct upload to flickr from the WordPress write panel
*	Include galleries of other people (will only show images which match specific licenses)
*	Add the possibility to share your account token with other users on the same blog (select which ones or just allow all)
*	Add more services.

= Known Issues =
*	There is trouble with pictures marked as being private. You can insert them but they won't show up, after the caching time is over (only when the user which put them in views the page they appear until chaching time is over again, then they disappear until he visits the page again...)
*	Searching images doesn't work.
*	Managing your flickr account isn't integrated now.

= Limitations =

WordPress Imager is inserting 2 Types of outgoing links:
*	One to the flickr-page for each picture in the pictures footnote
*	One to the homepage of WordPress Imager.

You may turn off the link to the plugins homepage, while you can not turn off the link to flickr since this is a requirement flickr sets for including images from flickr into your blog.

Since flickr doesn't save more then one authentification for one user combined with one "application" it is not possible to use WordPress Imager on more then one blog (or more then one author at the same blog) with the same flickr account - at least it isn't possible with the normal installation behaviour.
I added one field into the wp-admin-panel which shows the serialized authentification data which is stored for your user. You may copy it and post it into the same field at the other blog or when logged in with the other user and then may save this field. This seems to be the only way to have more then one blog or author account connected with one flickr account.

== Installation ==

1. Upload the whole directory 'wordpress-imager' (you'll find in the .zip) to the `/wp-content/plugins/` directory (please do not rename the folder)
2. Make cache folder and subfolders writeable for server (chmod 777)
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to Settings->WordPress Imager and make the needed authentification (this is saved seperatly for each user!)
5. Enter and save your default settings (They are **not** stored seperatly for each user since this is most likely somthing you'll want to have consitent around the blog). You can change most of them seperatly with each usage.

= Post integration =

1. go to write page
2. click the media button saying WordPress Imager
3. Choose from the following:
	*	Select a gallerie of your (presaved at flickrs) flickr galleries
	*	Select a bunch of your images you want to use as a gallery
	*	Fill in the search form, hit search and select any images you like
4. Change Settings as needed.
5. Hit Insert button

= Theme integration =

1. copy a shortcode generated like described above.
2. open your theme file
3. insert the shortcode like this: `do_shortcode('[wordpressimager ... /]');` into the theme file.

Thats it. A widget will be created soon but isn't available today - sorry.

== Frequently Asked Questions ==

= Will other image services will be integrated? =

Yes they will.
WordPress Imager wants to make integration of as many services as possible into WordPress as easy as possible.

= What does a service need to be integrated? =

An API would be good. The minimum however is XML-Feeds (if there is nothing better, like an API).

= How can I contribute? =

Glad you asked. There are plenty of ways you can contribute:

*	Translation to any other language than english and german would be highly appreciated
*	Any bug reports are highly appreciated
*	Telling me about other great image hosters with API would be nice.
*	When none of the above applies to you (or you want to contribute more) you may as well donate some money, since coding on this plugin was quite time consuming. You will find a paypal donation button on the [plugins homepage][plugin homepage].

[plugin homepage]: http://wordpress-imager.de
            "Remote Image Management Plugin"

== Changelog ==

0.9.5
*	PHP 4.2.x Compatibility (file_get_contents() emulation)
*	allow_url_fopen = false issues resolved IF CURL is available.

0.9.4
*	Some litte but annoying bugs fixed
*	"Mayor" Bug fixed which which did trigger flickr to not recognising the "frob" which is actually something like an authentification value only used for token transfer.

0.9.3

*	Support for Thumbnail size integrated (100px width images).
*	Added a Media Button Image (IMGr)

== Screenshots ==

Coming Soon