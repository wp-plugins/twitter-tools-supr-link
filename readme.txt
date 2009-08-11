=== Twitter Tools: Su.pr Links ===
Contributors: Jon Rogers
Donate link: http://su.pr/1PPfE2
Tags: twitter
Requires at least: 2.7
Tested up to: 2.8
Stable tag: 0.1.3

Makes the links that Twitter Tools posts to Twitter be API-created su.pr links so you can track the number of clicks and such via your su.pr account.

== Description ==

[Twitter Tools](http://wordpress.org/extend/plugins/twitter-tools/) is an excellent plugin for posting notifications of new blog posts to [Twitter](http://twitter.com/). However Twitter Tools just sends the URL to the new post normally which is then shortened by Twitter itself to a [bit.ly](http://bit.ly/) short link. This is done anonymously.

This plugin will replace the normal URLs sent by Twitter Tools to Twitter with su.pr URLs tied to your Stumble Upon account. You can then easily track the number of clicks from your Stumble Upon profile.

This plugin is based on [twitter-tools-bitly-links](http://wordpress.org/extend/plugins/twitter-tools-bitly-links/) by Viper007Bond

**Requirements**

* [Twitter Tools](http://wordpress.org/extend/plugins/twitter-tools/) be installed and activated
* PHP 5.2.0 or newer (PHP4 is dead anyway)
* WordPress 2.7 or newer

== Installation ==

###Manual Installation###

Extract all files from the ZIP file, **making sure to keep the file/folder structure intact**, and then upload it to `/wp-content/plugins/`.

###Automated Installation###

Visit Plugins -> Add New in your admin area and search for this plugin. Click "Install".

**See Also:** ["Installing Plugins" article on the WP Codex](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins)

###Plugin Usage###

Visit Settings -> Twitter Tools: bit.ly and fill in your login and API key.

== Frequently Asked Questions ==

= It's not working! =

Did you make sure to fill in your su.pr login and API key on the plugin's settings page?

== Screenshots ==

1. The settings page is where you need to add in your own su.pr login and API key - get one from su.pr, don't ask me!

== ChangeLog ==

= 0.1.0 =

* Initial release!
