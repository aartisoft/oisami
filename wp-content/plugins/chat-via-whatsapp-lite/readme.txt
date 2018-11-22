=== Chat via WhatsApp ( Lite ) ===
Contributors: ks4wp
Tags: whatsapp, chat
Requires at least: 4.7
Tested up to: 4.9.1
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple and effective customer support channel using WhatsApp.

== Description ==
Ease the communication with your customers by using this Click to Chat plugin as your support channel. With this plugin, you can display your WhatsApp account simply by adding your number, name, and title on the settings page; no coding required!

= How does it work? =
1. A call-to-action toggle is placed at the bottom of your site page with an editable text.
2. Once the toggle is clicked, a box will be displayed showing your WhatsApp account with your title and name on it.
3. Click that name and the plugin will take the user to your WhatsApp account on [http://web.whatsapp.com](http://web.whatsapp.com) if your users are on desktop, or open the WhatsApp app if they're on their mobile device.

[See the live preview](http://lite.satudua.co/lite-wptwa/)

You can also display a link that when clicked will open WhatsApp chat by using a `[whatsapp]` shortcode. This shortcode accepts the following attributes:

* **number** to set the WhatsApp number.
* **auto_text** to set a pre-populated text when user open WhatsApp app on their phone or on the web.
* **text_color** to set the button's text color.
* **background_color** to set the button's background color.
* **text_color_on_hover** to set the text color when the button is being hovered.
* **background_color_on_hover** to set the background color when the button is being hovered.
* **icon**, which if set to 'no' will remove the icon from the button.
* **display**, which if set to block will take the full with for the button.

And the text between the `[whatsapp]...[/whatsapp]` tags will be used as the button's text, like the follwing example:

`[whatsapp number="628157227657" background_color="#3a589e"  text_color="white" background_color_on_hover="#25D366" auto_text="Hello..." icon="no" display="block"]A full width chat button with no icon[/whatsapp]`

To add a button in a widget, use the Text widget and put the shortcode in it.

= This lite version is pretty basic. Do you have a premium one? =
Yes. With the premium version, you'll get the following features:

* Display multiple accounts.
* Set a photo/avatar for each account.
* Set availability by time and days for each account.
* Set a pre-populated text for each account.
* Edit the colors of the toggle and the box.
* Support for WPML.
* Page targeting.
* Auto-display based on time delay, inactivity, or scroll length.

You can buy the premium version on [Codecanyon](https://codecanyon.net/item/whatsapp-click-to-chat-for-wordpress/20248537?ref=Satu_Dua). Pay only once and get free lifetime updates.

== Installation ==
Upload the Click to Chat for WhatsApp plugin to your blog, Activate it, and then go to its settings page. There, you need to add your WhatsApp number, your name, and your title (optional). Once you save the settings, go to the front-end of your site and refresh the page.

== Frequently Asked Questions ==

= I have installed the plugin but nothing shows up on my page. What happen?  =

Make sure to insert your WhatsApp number and your name; those two fields are required. After that, flush your cache if you're using a caching plugin and then refresh the page.

= The toggle is displayed but nothing happened when clicked. What's wrong? =

When this happens, it's usually caused by other plugin generating JavaScript errors and break your site. Try to deactivate all the plugins except this one and then activate the rest one by one and see which one prevents this plugin to work. If the problem persists, let me know by creating a new topic on the Support page. Don't forget to leave the URL to your site for me to look.

== Screenshots ==
1. The widget on the front-end
2. The settings page

== Changelog ==

= 1.0 =
* Initial release.

= 1.0 =
* Bug fix: hiding on larger or smaller screen didn't work.