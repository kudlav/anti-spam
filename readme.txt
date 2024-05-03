=== Anti-spam Reloaded ===
Contributors: kudlav, webvitaly
Tags: spam, spammer, comment, comments, comment-spam, antispam, anti-spam, block-spam, spam-free, spambot, spam-bot, bot
Requires at least: 3.3
Tested up to: 6.5
Stable tag: 6.5
Requires PHP: 5.6
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

No spam in comments. No captcha.

== Description ==

This is fork of successful Anti-spam plugin v5.5 written by webvitalii, for more info visit [GitHub Fork](https://github.com/kudlav/anti-spam/).
From version 5.6 maintained by kudlav.

* **[GitHub](https://github.com/kudlav/anti-spam/)**

Anti-spam Reloaded plugin blocks 100% of automatic spam messages in comments section and also blocks all trackbacks.No captcha required.

Plugin is simple and easy to use: just install it and it just works.

Blocked comments can be stored in the Spam area and converted to regular comments if needed.

Anti-spam Reloaded plugin is GDPR compliant and does not store any other user data except of the behaviour mentioned above.

**Plugin blocks spam only in comments section.**.
Plugin does not block manual spam (submitted by spammers manually via browser).


== Installation ==

1. Install and activate the plugin on the Plugins page
2. Try to submit a comment on your site being logged out
3. Enjoy life without spam in comments


= Settings (optional) =

In "Settings => Anti-spam Reloaded" you can enable saving blocked comments as spam in the spam section (disabled by default).
Saving blocked comments is useful for testing and debug purpose. You can easily mark comment as "not spam" if some of the comments were blocked by mistake.

You hide the info block with total spam blocked counter in the admin comments section in the "Screen Options" section of comments page.
The visibility option for this info block is saved per user (enabled by default).


== Compatibility ==

All modern browsers and IE11+ are supported.
Anti-spam Reloaded plugin works with disabled JavaScript. Users with disabled JavaScript should manually fill current year before submitting the comment.

Server compatibility:

* Wordpress 3.3 - 6.5
* PHP 5.6 - 8.2
* Doesn't use jQuery

Plugin is incompatible with:

* Disqus
* Jetpack Comments
* AJAX Comment Form
* bbPress

If site has caching plugin enabled and cache is not cleared or if theme does not use 'comment_form' action
and there is no plugin inputs in comments form - plugin tries to add hidden fields automatically using JavaScript.


== How does it work? ==

The blocking algorithm is based on 2 methods: 'invisible js-captcha' and 'invisible input trap' (aka honeypot technique).

= 'invisible js-captcha' =

The 'invisible js-captcha' method is based on fact that bots does not have JavaScript on their user-agents.
Extra hidden field is added to comments form.
It is the question about the current year.
If the user visits site, than this field is answered automatically with JavaScript, is hidden by JavaScript and CSS and invisible for the user.
If the spammer will fill year-field incorrectly - the comment will be blocked because it is spam.

= 'invisible input trap' =

The 'invisible input trap' method is based on fact that almost all the bots will fill inputs with name 'email' or 'url'.
Extra hidden field is added to comments form.
This field is hidden for the user and user will not fill it.
But this field is visible for the spammer.
If the spammer will fill this trap-field with anything - the comment will be blocked because it is spam.

== Screenshots ==

1. Plugin will count number of blocked comments, blocked comments can be saved.
2. Plugin settings page
3. Spam comments will not be proceeded.

== Changelog ==

= 6.5 - 2021-07-02 =
* Improve accessibility - assign label to the year input

= 6.4 - 2020-08-12 =
* Bugfix: JavaScript wasn't on some pages invoked (use DOMContentLoaded listener on document again)


= 6.3 - 2020-07-31 =
* Compatibility with WordPress 5.5 (Use `comment` or empty string for the `comment_type`)
* Use only DOMContentLoaded listener for autofill and auto-hide (affect 1% of browsers)

= 6.2 - 2020-06-12 =
* Internationalize plugin
* Czech translation
* Minify JS file, use wp_enqueue versioning
* Dont use extract() in antispamrel_check_comment
* Fix undefined variables

= 6.1 - 2020-04-22 =
* Security improvements
* Hide antispam version from HTML

= 6.0 - 2020-04-20 =
* New maintainer kudlav - updated links and docs
* Sanitizing and cleaner HTML output
* Changed prefixes
* Code + Readme simplified
* Use let instead of var in JS

= 5.6 - 2020-04-17 =
* New maintainer kudlav - updated links and docs
* Removed links to paid version
* Stat info is no longer warning

Previous versions can be found in original Anti-spam [repository](https://github.com/webvitalii/anti-spam/).
