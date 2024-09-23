# Mars-Clock-2
This is a complete rewrite of the original [marsclock](https://github.com/stroggprog/marsclock) (formerly hosted at [marsclock](https://github.com/phil-ide/marsclock)). The purpose is to:
1. Abandon use of external libraries (that might require breaking security updates)
2. Work with both 7" and 3.5" touchscreens
3. Simplify framework by ridding Electron and Nodejs
4. Can be displayed and configured on a phone (requires network access to machine hosting the clock)
5. Create a slideshow of background images

The application runs as a simple web application, backended by _lighttpd_ and PHP, and front-ended by _firefox_ or _chromium-browser_ in kiosk mode, with most of the work performed in the browser via javascript.

The background images can be replaced or added to, so could, for example, be a set of family photos.

This configuration makes it much simpler to maintain, and easier to fork so you can add your own extensions and enhancements.

![mars-clock-2 running on an MHS35 3.5“ screen](/images/mars-clock2a.png)

The detailed instructions tell you how to prepare your Raspberry Pi for installing the clock, whether you are using an `MHS35 3.5" TFT Touchscreen` or an `Official 7" Touchscreen`.

Installation and maintenance of the clock is simplified by a comprehensive set of scripts fronted by a whiptail menu system, making installing, uninstalling, repairing and updating the clock as simple as possible. Once the RPI has been prepared (browser installed etc - see [Installation Instructions](docs/README.md)), just clone the repository and run `./menu.sh` from the root of the repository.

![mars-clock-2 running on an MHS35 3.5“ screen](/images/mars-clock2b.png)

[Installation Instructions](docs/README.md)
