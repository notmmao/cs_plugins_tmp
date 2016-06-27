=== Plugin Name ===
Contributors: cxThemes
Tags: woocommerce, email cart, create cart, cart, email order
Requires at least: 3.0.1
Tested up to: 3.6
Stable tag: 1.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Email Cart for WooCommerce is a plugin which allows you to create and send a WooCommerce Cart to a customer's email address containing a link to the cart.

== Description ==

What it Does
WooCommerce Email Cart is a super useful plugin which allows customers and admins to send a pre-populated WooCommerce Cart to any email address ready and waiting for checkout. The receiver can then checkout straight away or continue shopping, adding to the cart or editing what is already in it.

Admins are also able to select whether the link goes to the Cart page or the Checkout page and a live output of the link is always available for to copy and use in any other application, such as a newsletter, support email, ad or promotion.

Great for:
* Customers sharing their cart with others for interest or to checkout for them
* An easy, once-off, wish list type functionality for customers
* Over-the-phone or email, "manual" orders for admins
* Customers contacting support who are having trouble finding products or using your site
* Not having to create a new customer manually in admin for an over-the-phone or email customer to set up a new Order for them
* Encouraging phone / email customers to checkout through the usual front-end where all your shipping and discount plugins are effective
* Adding links to newsletters, ads or communications to quickly add products or sets of products to customers' carts with one click. Increasing sales!

Happy Conversions!


== Documentation ==

Please see the included PDF for full instructions on how to use this plugin.
 

== Changelog ==

1.12
* Refactor the way carts are encoded and decoded, to deal with the chnages to the cart in WC2.2
* Fixed bugs that cause incorrect totals, tax totals and other calcualtions after chnages to the cart in WC2.2
* Added link to Email Cart Settings (Settings>Email Cart), also available by clicking the cog icon in the top right of Email Cart page

1.11
* Added styling to email product list table
* Fixed formatted price and added correct tax rate name
* Fixed backwards compatibility on nonce_field
* Fixed update default settings on activate plugin cuasing empty message body
* Fixed tax not showing in email - WC2.0 and below
* Fixed deprecated function issue
* Fixed post variable not set notices
* Fixed settings page compatibility - WC 2.0 and below

1.10
* Added default email template setting so you can customize the default message on the backend and frontend carts
* Added default From address setting that defaults to WooCommerce from address or can be overidden
* Added Tax and Totals to email
* Added sending information to the Send a Copy email so admin can see what the customer or store manager sent
* Moved settings to its own settings page
* Fixed delete line on backend add to cart

1.09
* Added optional CC and BCC fields to Back and Front end forms that can be turned on/off in the General Settings Tab
* Added Send a Copy field to General Settings Tab for permanent BCC so admins can keep track of User actiity

1.08
* Fixed compatibility issue with WC2.1
* Fixed double attribute_ being added to the cart URL
* Fixed formatting issue with the front-end cart
* Changed the front-end call to action to be .button

1.07
* Added filter to add CC or BCC to email
* Updated UpdateChecker class
* Various small bug fixes

1.06
* Added ability to send Variable Products set to "Any" attribute from Email Cart on the front-end Cart Page. Previously these products would be omitted (www.your-site.com/cart/#email-cart)
* Added en_US.mo and en_US.mo files to use for language conversion
* Updated language support for previously disabled text areas

1.05
* Added ability to deep link to Email Cart using www.yoursite.com/cart/#email-cart - allows users to create their own custom buttons linking to Email Cart

1.04
* Updated the Email Cart back-end with a great new looking UI
* 1.41 Check all missing text is language translatable

1.03
* Added ability to Send Cart from the front end WooCommerce Cart
* Added WordPress multilingual support (we invite you to send us your language files. please. thanks)

1.02
* Added ability to add complex variable products to the cart
* Added variable product attributes to the cart in the email to improve the information sent to the user
* Added count to a product item in the query string in order to reduce length. Compatible with older query
* Fixed minor PHP notices

1.01
* Changed form layout for better UI
* Added a drop-down to the form which allows the selection of either the Cart or Checkout page as the landing page
* Added an update-able Share link to the Form
* Fixed Bug: Link now clears cart before adding products to cart and displaying cart/checkout page

1.00
* Initial release
