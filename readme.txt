=== BP Code Snippets ===
Contributors: imath
Donate link: http://imathi.eu/donations/
Tags: BuddyPress, sharing, code, snippets
Requires at least: 3.5.1
Tested up to: 3.5.1
Stable tag: 2.1
License: GPLv2

BuddyPress 1.6+ plugin to share & highlight code snippets

== Description ==

BP Code Snippets is a BuddyPress component to share snippets in your community. Important : since version 2.1 of this plugin <b>BuddyPress 1.6+ is required</b>.

In WordPress administration, under the BuddyPress menu, You can manage the look and feel of code snippets and disable/enable the component for groups / group forums / blogs.

http://vimeo.com/35811457

NB : Once the Super Admin / Admin activated the component for groups, Group Admins will be able to activate it for their group by choosing the wanted languages for code snippets in the Snippets sub menu of Admin tab.

== Installation ==

You can download and install BP Code Snippets using the built in WordPress plugin installer. If you download BP Code Snippets manually, make sure it is uploaded to "/wp-content/plugins/bp-code-snippets/".

Activate BP Code Snippets in the "Plugins" admin panel using the "Network Activate" (or "Activate" if you are not running a network) link.

== Frequently Asked Questions ==

= If you have any question =

Please add a comment <a href="http://imathi.eu/2012/01/30/bp-code-snippets-2-0/">here</a>

== Screenshots ==

1. The Snippets Public Directory view.
2. The group Administration for Snippets.
3. Modal window to attach a snippet to a forum topic.
4. Sharing toolbar.

== Changelog ==

= 2.1 =
* security fix : zClip is abandoned due to security hole
* finally fixes various bugs appearead since 1.6 BuddyPress upgrade.
* BuddyPress 1.7 ready !

= 2.0 =
* finally BuddyPress 1.5+ ready !
* Better integration with group forums and blog posts
* The Snippets directory now supports language category filtering (and searching)
* Snippets can now be favorited by members
* A widget can display the most favorited snippets
* You can allow people to embed snippets in an iframe
* You can ease the process of sharing snippets thanks to BP Bookmarklet plugin.

= 1.3.3 =
* adds the support of WP 3.1 network admin menu

= 1.3.2 =
* fixes a bug on activity filtering

= 1.3.1 =
* it's now possible to add snippets (without saving them in the group component) into regular posts or forum posts

= 1.3 =
* it's now possible to embed snippets into regular posts or forum posts

= 1.2.1 =
* fixes a bug in the display of Snippets Public Directory

= 1.2 =
* trouble in snippet comment count fixed
* widget : title is customizable
* widget : Recently commented display mode added
* widget : displays the snippets of public groups when not on a group single template
* widget : once on a group single template, displays the snippets of the group if public or if logged in user is member of the group
* Snippets Public Directory : on home page a snippet navigation has been added.
* RSS feeds added to member template / group template / Snippets public directory template / single snippet template for the comments feed
* In the home group, if you add to the groups/home.php or groups/activity.php template the tag bp_custom_group_boxes(), it will enable some stats for the snippets group (more infos can be found <a hred="http://imath.owni.fr/2010/07/29/bp-widgetized-home-4-group/">here</a>).

= 1.1 =
* Into the member area, under the snippets nav, all the snippets written by a user can be displayed
* A widget can be added to the sidebar to display the most commented or latest public snippets
* New languages are supported (Java, Java FX, Perl, Python, Ruby, ColdFusion, AppleScript, CPP, CSharp, VB, Bash)

= 1.0 =
* languages supported : PHP, Javascript, CSS, XML / Html, Actionscript, SQL
* Plugin birth..

== Upgrade Notice ==

= 2.1 =
Important : you'll need to upgrade to BuddyPress 1.6.4 before upgrading BP Code Snippets to version 2.1. Once BuddyPress is upgraded, make sure to back up all your config (database and files) before attempting to upgrade BP Code Snippets to version 2.1.

= 2.0 =
Important : you'll need to upgrade to BuddyPress 1.5 before upgrading BP Code Snippets to version 2.0. Once BuddyPress is upgraded, make sure to back up all your config (database and files) before attempting to upgrade BP Code Snippets to version 2.0.

= 1.3.3 =
If you disabled the snippets nav in admin panel, this upgrade will restore it. It will also add the Snippets Public Directory. Just in case : i advise you to back-up (prefix_)code_snippets and (prefix_)code_snippets_comments. You do not need this upgrade if you are not using WP 3.1.

= 1.3.2 =
If you disabled the snippets nav in admin panel, this upgrade will restore it. It will also add the Snippets Public Directory. If you want to disable these 2 options, go to the admin panel. Just in case : i advise you to back-up (prefix_)code_snippets and (prefix_)code_snippets_comments

= 1.3.1 =
If you disabled the snippets nav in admin panel, this upgrade will restore it. It will also add the Snippets Public Directory. If you want to disable these 2 options, go to the admin panel. Just in case : i advise you to back-up (prefix_)code_snippets and (prefix_)code_snippets_comments

= 1.3 =
If you disabled the snippets nav in admin panel, this upgrade will restore it. It will also add the Snippets Public Directory. If you want to disable these 2 options, go to the admin panel. Just in case : i advise you to back-up (prefix_)code_snippets and (prefix_)code_snippets_comments

= 1.2 =
If you disabled the snippets nav in admin panel, this upgrade will restore it. It will also add the Snippets Public Directory. If you want to disable these 2 options, go to the admin panel. Just in case : i advise you to back-up (prefix_)code_snippets and (prefix_)code_snippets_comments

= 1.1 =
No changes to the database, but just in case : i advise you to back-up (prefix_)code_snippets and (prefix_)code_snippets_comments

= 1.0 =
no upgrades, just a first install..
