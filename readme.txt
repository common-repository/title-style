=== Plugin Name ===
Contributors: kari.patila
Tags: titles, formatting
Requires at least: 2.0.0
Tested up to: 2.8.4
Stable tag: 0.1.1

This plugin adds emphasis around certain words in post titles.

== Description ==

You can use this plugin to wrap lowercase words or words like "the", "of" or "a" in em, span or div elements with custom class names. Helpful if you want to automatically style parts of your headlines.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `titlestyle` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

OR

Use WordPress' "Add New" feature from the Plugins menu.

== Changelog ==

= 0.1.1 =
* Fixes a bug where titles already containing markup would get messed up

= 0.1 =
* Moved the plugin to the WordPress plugin directory

== TODO ==

1. Proximity detection so words that are next to each other will be wrapped inside the same HTML element.