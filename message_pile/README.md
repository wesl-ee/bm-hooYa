# MicroTXT 💻

A tiny [textboard style](https://en.wikipedia.org/wiki/Textboard) software written in PHP, no database required.

It is meant to be simple to use, host, and develop.

## Features

* ✅ Only PHP 5.x+ required (No database server, only sqlite3!)
* ✅ Hidden/unlisted threads (start your thread title with .)
* ✅ MOTDs
* ✅ Less than 300kb uncompressed
* ✅ Markdown for parent posts
* ✅ No JavaScript required (JavaScript is used minimally but only to increase usability)

## Installing

MicroTXT is only tested in a Linux environment, however, it should work on Windows/Unix with little to no modification.

Simply download and place the files in your PHP 5+ enabled website directory, and edit php/settings.php to your liking. You should probably also change rules.txt and faq.txt too.

**You also need the GD PHP library installed on your PHP instance if you want to use the included captcha**

**You also need Sqlite3 installed!**

## Configuring

Just edit php/settings.php

To disable the captcha just change $captcha to false, or to make the captcha appear every time, change $postsBeforeCaptcha to 0.

## Warnings

This is new, there may be some issues with it.

Don't rely on it for huge communities, it doesn't scale for very high traffic projects (it's not meant to).

Change the salt in settings.php, otherwise tripcodes may be easier to brute force.

**Prior to version 1.2, salts were not being applied to tripcodes, resulting in potentially easy to brute force tripcodes when bad passwords were used**

## Demo

You can use the demo board [on my website](https://chaoswebs.net/mt/).

## Contributing

I will accept pull requests if they fix bugs or improve the software in a way I think fits the goals of the project.

Try to follow the coding style of existing code, and comment any non-simple code.

### Bug Reports

Well structured & polite bug reports are appreciated. Please try to include the following information in any bug reports:

* PHP version
* Web server version
* Operating system version
* MicroTXT Version (specified in settings.php)
* What you have tried so far
* Screenshots are helpful, but not necessarily required.

## Development Roadmap & Planned features

* Better post & reply formatting
* Admin panel for setup, configuration, and moderation
* Easy to use installation script (For Linux)
* Perhaps a Docker container if there is demand

## Contacting me

You can get in touch with me [here](https://chaoswebs.net/contact)

## Donate 💲

If you want to support development, a dollar or two would be appreciated.

Bitcoin: 3GKzFQyfE35U6Gi9XeN3xGQ3tMZy3x2ByQ

[Or, donate another way](https://chaoswebs.net/donate)
