=== ZeroBounce Email Verification & Validation ===
Contributors: zerobounce
Tags: email validation, email verifier, email verification, email tester, email checker
Requires at least: 4.4
Tested up to: 6.4.3
Stable tag: 1.1.2
Requires PHP: 7.0
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

ZeroBounce validates emails on your WordPress site in real-time, blocking invalid and risky emails to improve deliverability and reduce bounce rates.

== Description ==

Need an email validation tool to block invalid and high-risk emails on your WordPress website?

The ZeroBounce email verification plugin assists users by validating email addresses entered into your registration forms, comments sections, eCommerce shops, and more. Install the plugin, connect your API key, and select the forms you want to monitor with email validation.

== Key Features ==

* **Automated real-time email validation** - Automatically prevent selected email types from creating accounts, leaving comments, or signing up
* **Detect more than 30+ email address types** - Including invalid, abuse, disposable, spam trap, toxic domains, catch-all, and more
* **Choose what to accept** - Create your own rules for email validation and disallow emails based on status
* **Email verification for 9 form types** - Easily select which forms you want to protect with email validation
* **Fast manual email validation** - Verify any email address in the tools section using our interactive form
* **Email validation API logs** - Keep track of monthly email verifications, including status, sub-status, IP, date, and credits used

== Benefits ==

* **Keep your email list clean & accurate**
* **Reduce your email bounce rate**
* **Boost your inbox placement**
* **Protect your email sender reputation**
* **Improve email deliverability**
* **Eliminate fraudulent, untrustworthy shoppers**
* **Block spammers and spoofers**
* **Keep your comments section clean**

== Supported Forms/Plugins ==

* Contact Form 7
* WPForms
* Ninja Forms
* Formidable Forms
* WooCommerce
* WordPress Post Comments
* WordPress Registration
* MC4WP: Mailchimp for WordPress
* Gravity Forms
* Fluent Forms
* WS Forms
* Mailster Forms
* Forminator Forms

And more support is being added gradually.

== Installation ==
 
1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to the plugin Settings screen and set your API Key. You can find your [api key here](https://www.zerobounce.net/members/API).
4. Select which forms you would like to validate, click the relevant checkboxes.

That's it! You are now automatically validating emails and reducing abuse, spam and more.

== Frequently Asked Questions ==

= What is email verification? =

Email verification is the process of determining if an email address is valid and in use. Using an email verifier, you can identify and block invalid and high-risk email types such as disposable emails, spam traps, and abuse emails.

= Why do I need to verify emails? =

Email verification is necessary for businesses to ensure your email list contains valid data. Whenever someone signs up on your forms with an invalid or high-risk email address, it leads to future issues, including higher email bounce rates, more spam placement, and a potential email blacklisting.

Using the ZeroBounce plugin, you can block low-quality email addresses and protect your email deliverability.

= How does the ZeroBounce email verification plugin work? =

Our email validation plugin integrates with popular forms, including WordPress registration, post comments, WooCommerce, and more. The plugin “hooks” into the form and verifies incoming email address submissions in real time. Once our email verifier confirms the status using our API, it will allow or block the email address based on your preferences.

= Is the email verification plugin free? =

There is no charge to install and use the ZeroBounce email verification plugin. However, you will require credits to validate incoming email addresses on your forms. The rate is 1 ZeroBounce credit to verify 1 email address.

You can test the email verification plugin and clean emails regularly by creating a free account. All users get instant access to 100 free monthly email verifications at no cost. Visit https://www.zerobounce.net/members/signin/register to get started.

= What happens if I run out of email verification credits? =

Your forms and the plugin will continue to function normally. However, you cannot verify incoming emails until you acquire more credits.

The email verifier plugin provides monthly credit usage charts and will notify you when your email validation credits run low.

== Screenshots ==

1. Dashboard
2. Settings
3. Tools
4. Logs

== Changelog ==

= 1.1.2 =
* Fixed minor bugs for validation

= 1.1.0 =
* Added support for bulk validation
* Removed credits graph

= 1.0.27 =
* Added French translation

= 1.0.26 =
* Added support for translations

= 1.0.25 =
* Updated API Key validation

= 1.0.24 =
* Added support for typos - Did you mean

= 1.0.23 =
* Fixed typos

= 1.0.22 =
* Moved ZeroBounce button, in Admin Menu, under Plugins

= 1.0.21 =
* Updated the performance of APIKey validation

= 1.0.20 =
* Updated Validation Status graph to status logs for 'Block Free Services'

= 1.0.19 =
* Fixed status related bugs
* Updated plugin tags

= 1.0.18 =
* Added support for BWS Forms (multi-language forms)
* Added status for 'Block Free Services'

= 1.0.17 =
* Added support for U.S.A. API
* Added support for Wordpress Registration Forms
* Added support for Wordpress Comment Forms
* Added support for WS Forms
* Added support for Mailster Forms
* Added support for Forminator Forms
* Added option to block email addresses from free email services

= 1.0.16 =
* Added support for Wordpress Multisite

= 1.0.15 =
* Added custom error field for validator

= 1.0.14 =
* Fixed error message on admin tools page for email validation

= 1.0.13 =
* Added support for Fluent Forms

= 1.0.12 =
* Fixed potential XSS bug on saving API Key
* Updated for WordPress 6.3

= 1.0.11 =
* Fixed CSS bug

= 1.0.10 =
* Fixed and Improved Gravity Forms support up to latest version (2.7.8)
* Gravity Forms logs are clickable (takes you to respective form)

= 1.0.9 =
* Bug fixes and improvements

= 1.0.8 =
* Bug fixes and improvements

= 1.0.7 =
* Bug fixes and improvements

= 1.0.6 =
* Fixed bug in Gravity Forms validation

= 1.0.5 =
* Fixed bug in admin settings with validation status
* Updated images

= 1.0.4 =
* Fixed bug where email needed url enconding
* Fixed typo when activating first time plugin

= 1.0.3 =
* Fixed bug where WooCommerce validation was not activating
* Fixed typo in Admin settings page
* Improved API Key settings page input
* Updated plugin description URLs
* Improved message notification when no API Key is set
* Added API Key validation when saving it
* When activating the plugin for the first time, all supported forms are checked
* When activating the plugin for the first time, the default validation statuses are set
* Updated tools page and removed empty box for now

= 1.0.2 =
* Added support for MC4WP: Mailchimp for WordPress
* Added support for Gravity Forms

= 1.0.1 =
* Full rewamp of the plugin

= 1.0.0 =
* Initial release

== Upgrade Notice == 

 