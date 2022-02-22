# MP Membership Limit

Adds ability to MemberPress to limit membership sign-ups.

**Version:** 0.1

**Requires WordPress Version:** 4.8 or higher, PHP 7+

**Compatible up to:** 5.9.1

**Minimum Memberpress Version:** 1.9.19


## Description

A client using MemberPress for their membership site also needed the ability to sell "tickets" to live events they hold that may also include online content that should only be available to attendees, so the "ticket" sale also had to include a membership for access to the restricted website content.

This plugin adds a Limit meta box to the _MemberPress > Memberships_ editor, a new column to the Memberships management page, and a new settings entry under the MemberPress admin menu (only visible to those with manage settings capability).

## Installation

1. Download the GitHub archive as a ZIP file.  
2. Unzip it, knock the “-master” off the folder’s filename
3. FTP upload it into your WordPress blog’s _~/wp-content/plugins/_ folder.
4. Go to your _Dashboard > Plugins_ and activate “MP Membership Limit”
5. Go to _Dashboard > MemberPress > Membership Limits_ to configure it.


## Settings

**Limit Label:** 
* "Sign-Up"
* "Ticket"
* "Seat"

You can change what the limit is called in the meta box, management column, and if displayed under the sign-up form.  Change it to whatever terminology makes sense for your membership site.  The default is "Sign-Up."

**Show on Form:** 
* Show Limit
* Show Available

You can check neither, either, or both.  These add text in the following formats beneath the sign-up form on the front-end:

* Nothing (neither box is checked)
* Registration is limited to X \{$limit_label\}s (Show Limit only)
* X \{$limit_label\}s Available (Show Available only)
* X of Y \{$limit_label\}s Available (both checked)

Default is neither box is checked.

**Fill Rule:** 
* Pending and Completed
* Completed Only

Determines which transaction status will "fill" a sign-up.

If you select "Pending and Completed" then both those paying online and offline will be drawing from the same stock of sign-ups, and when an offline payment is marked as "complete" the overall count of sign-ups won't change.

If you select "Completed Only" then transactions with "Pending" status won't be counted as "filled" sign-ups until they are marked "complete."  That means it is possible to accidentally oversell sign-ups, though that's only a problem if you actually do have a specific limited capacity.

**Validation "Sold Out" Message**

It is possible two people might be filling out the sign-up form at the same time, but if it's for the last sign-up available only the person who submits their registration first will get it.  This is the error message that is displayed to anyone else who submits the form after the sign-ups have already sold-out.

**Sign-Up Form "Sold Out" Message**

Instead of the sign-up form users will see this error message followed by a grayed-out, dummy (non-functional) version of the sign-up form.

**Multi-Tier Pricing:** 

To make sure all users are purchasing from the same stock of available sign-ups you should _use the MemberPress Coupons_ feature to create different pricing tiers.

You don't have to actually send the coupon codes out to your users because you can create a link/button to a sign-up form with the coupon's discount already applied.  Then use MemberPress show/hide shortcodes to selectively show a members-only link/button to your Members and hide it from everyone else.

If you used different membership products for pricing tiers each of them would be drawing from a different stock of available sign-ups.  The only circumstance under which you'd intentionally do this is to RESERVE a certain number of slots for a specific group.



## Changelog

Version 0.1

Initial public release.

## Developers

K.M. Hansen @kmhcreative - Lead Developer
http://www.kmhcreative.com

## Resources

Based on this Gist snippet: [https://gist.github.com/cartpauj/fec536f652794a01fc61dc55a2b83c31](https://gist.github.com/cartpauj/fec536f652794a01fc61dc55a2b83c31)

