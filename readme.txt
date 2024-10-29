=== Plugin Name ===
Contributors: mariohillercodecastlede
Tags: update, security
Requires at least: 3.0.1
Tested up to: 3.7
Stable tag: trunk

Collects information to list information on appcastle.net for easy overview and updating of your own wordpress installations.

== Description ==

Collects information to list information on appcastle.net for easy overview and updating of your own wordpress installations.

== Installation ==

There are three ways of installing this plugin.

= using plugin directory ( most common ) =

1. log into your wordpress backend
1. go to "Plugins" - "installed Plugins"
1. click on "add Plugins"
1. search for appcastle.net
1. install and activate plugin

= using zip file =

1. download plugin from https://plugin.appcastle.net/appcastle_wordpress.zip
1. log into your wordpress backend
1. go to "Plugins" - "installed Plugins"
1. click on "add Plugins"
1. click on "upload"
1. choose zip file from hard disk
1. install and activate plugin

= using ftp =

1. download plugin from https://plugin.appcastle.net/appcastle_wordpress.zip
1. unzip file
1. upload directory to /plugins folder of wordpress installation
1. log into your wordpress backend
1. go to "Plugins" - "installed Plugins"
1. activate plugin

= register at appcastle.net =

1. go to https://register.appcastle.net/de/registration
1. create account
1. wait for password mail and set password
1. log in and go to "Wordpress Watch" and add name and address of wordpress installation
1. generate token and add to configuration in wordpress

== Frequently Asked Questions ==

= What about security? =

Nobody will be able to log into your wordpress account. All information retrieved from your server
are: wordpress version, plugins, themes and used space of disk and database. The connection is
secured by a generated API token, which you are able to change at any time.

= Where to I enter the API token? =

1. Log into your wordpress backend.
1. Go to "settings" - "appcastle.net settings"

== Changelog ==

= 0.7 =
* Corrected readout of available wordpress version

== Upgrade Notice ==

= 0.7.21 =
All versions prior 0.7.21 will not read the correct wordpress version.