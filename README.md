# Mars-Clock-2
This is a complete rewrite of the original [marsclock](https://github.com/stroggprog/marsclock) (formerly hosted at [marsclock](https://github.com/phil-ide/marsclock)). The purpose is to:
1. Abandon use of external libraries (that might require breaking security updates)
2. Work with both 7" and 3.5" touchscreens
3. Simplify framework by ridding Electron and Nodejs
4. Can be displayed and configured on a phone (requires network access to machine hosting the clock)
5. Create a slideshow of background images
6. Provide external menu system to manage clock software and images

The application runs as a simple web application, backended by _lighttpd_ and PHP, and front-ended by _firefox_ or _chromium-browser_ in kiosk mode, with most of the work performed in the browser via javascript.

The background images can be replaced or added to, so could, for example, be a set of family photos.

This configuration makes it much simpler to maintain, and easier to fork so you can add your own extensions and enhancements.

![mars-clock-2 running on an MHS35 3.5“ screen](/images/mars-clock2a.png)

The detailed instructions tell you how to prepare your Raspberry Pi for installing the clock, whether you are using an `MHS35 3.5" TFT Touchscreen` or an `Official 7" Touchscreen`.

![mars-clock-2 running on an MHS35 3.5“ screen](/images/mars-clock2b.png)

![configuration options](/images/mars-clock2c.png)

Installation and maintenance of the clock is simplified by a comprehensive set of scripts fronted by a whiptail menu system, making installing, uninstalling, repairing and updating the clock as simple as possible. Once the RPI has been prepared (browser installed etc - see [Installation Instructions](docs/README.md)), just clone the repository and run `./menu` from the root of the repository.

![mars-clock-2 whiptail menu system](/images/mars-clock2d.png)

The menu system automatically checks for and downloads any updates and alerts you if the upstream repository is newer, or if the local repo has been updated and the clock needs updating - this final step is left up to you so you have some control over the process.

I have chosen to use a rolling-release paradigm rather than a point-release, hence there are no 'releases' available. A consequence of this is that each update must be backwardly-compatible with earlier updates. Therefore, if there are any breaking changes, the update system will correctly manage the transfer between an old, incompatible release and the new one.

At various times when there has been a number of updates or a major enhancement/change has taken place on the furthest-upstream repository (https://github.com/stroggprog/mars-clock-2), a new tag will be created in the format of a release e.g. "1.0.4".

I've been asked by what 'furthest-upstream repository' means. I have a ForgeJo repo system on my intranet. I push to that, it pushes to GitHub. It also has the capability to push to other sites, which I may do in the future, but at the moment I don't see the point.

[Installation Instructions](docs/README.md)

[Clock Usage Instructions](docs/USAGE.md)

[Installer Menu Usage Instructions](docs/MENU.md)

[When to Reboot the Clock](docs/REBOOTING.md)

