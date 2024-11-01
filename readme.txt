=== Space Manager ===
Contributors: dan.imbrogno
Tags: widget, space, content block, space manager
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 1.2.3

Allows for management of template spaces using the TinyMCE editor.

== Description ==

Wordpress' built-in text widget is great, but it's not all that user-friendly. Space Manager let's you use the familiar Wordpress' post editor to create "Spaces" and organize them into "Zones". You can drop these Zones into any sidebar and customize how you want to display them.

== Installation ==

1. Upload `space-manager` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Under the Settings menu click on "Space Manager"
1. Create a "Zone"
1. Create a "Space"
1. Under the Appearance menu click on "Widgets"
1. Drag the "Space Manager" widget to a sidebar

== Frequently Asked Questions ==

== Screenshots ==
1. Setup zones to group your spaces logically
2. Create spaces within the zones
3. Use the built in tinyMCE editor to drop images into your spaces, just like writing a post.
4. Display the spaces in any sidebar and customize the appearance

== Changelog ==

= 1.2.3 =
Fixed fatal php error caused by function checkUpdate on plugin activation

= 1.2.2 =
Added more descriptive class names
Changed structure of output functions to make them more useful when overridden
Passed some helpful variables into filters

= 1.2.1 =
Made manager variable available to widget subclasses.
Added over-rideable spaceName and spaceContent functions.

= 1.2 =
Changed static reference to beforeZone, beforeSpace, beforeSpaceName, afterZone, afterSpace, afterSpaceName so that they can be overridden by sub classes.

= 1.1 =
Fixed problem that caused non administrators to be locked out of the admin

= 1.0 =
Initial plugin creation


== Extending ==

This plugin can easily be extended. Check out Ad Space Manager to learn how to create your own extension of the Space Manager plugin.