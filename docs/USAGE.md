# Configuration
Touching/tapping/clicking the gear icon in the top right of the screen will present you with various configuration options.

### Main Clock
The clock displays time for both the local time here on Earth and the time at the given location on Mars. The selected option will be the larger of the two clocks, and the smaller one will have an `e` or `m` next to it, denoting wher=ther the small clock is displaying Earth or Mars time: e.g. `10:42:27 e` means it is 10:42:27 here on Earth, and the larger clock is displaying Martian time.

### Clock From
You can set a location based on an Experiment (lander or rover), a Location, or by setting latitude/longitude. The clock needs to know which one of these you want to use as the basis for the clock, so you can choose it here.

### Options
Enabling `Info` will display the latitude, longitude and any other available information about the chosen location. The `Ls` value is always displayed.

Ls (pron. L-sub-ess) is where the sun is in regard to a fictitious mean centre based on the Martian alemma. This equates to the position of Mars in its orbit. If Ls=180, then it is half-way around its annual orbit.

### Save Changes
This button will save all the changes you have made to the options, including options you changed but didn't select, and then return to the clock.

### Return
This abandons any changes you made and returns to the clock. If you just want to force the clock to pick another background, you can come to the configure screen and then `Return` to get a new image. This will also force a reload of the scripts and json data.

### Power
This button will kill all processes currently running on the clock and power down as if you had typed `sudo shutdown now` at the terminal prompt.

## Deep Configuration Options
Some aspects of the clock require editing `/var/www/data/skin.json`.

    "seconds": true,
    "pulse": true,

The `seconds` option determines whether seconds are displayed. If this is false, then the `:` between the hours and minutes will pulse if `pulse` is true. If `seconds` is true, `pulse` is ignored.

    "interval": 100,

`Interval` determines the delay between updates of the clock in milliseconds. 100=10 times per second, 10=100 times per second, 500=twice per second.

    "bgimage": true,

`bgimage` determines whether a background image is displayed.

    "slide": {
        "slide": true,
        "interval": 300,
        "name": ""
    },

If `slide.slide` is true, then a slideshow will occur, with new slides being chosen at random every `slide.interval` seconds.
If `slide.slide` is false, and `slide.name` is the name of an image in `/var/www/images/slides`, then `slide.name` will be displayed as a static (unchanging) image.

No other values in skin.json should be manually changed, as they are set programmatically either by the startup script or one or more of the supplementary scripts, including (but not limited to) the cron job which updates the leap-second database. Changing any of the values except as described above may have unpredictable results.

You should take a copy of `skin.json` before editing it so you can always revert back if necessary. As a last resort, you can re-run `menu.sh` and select `Repair`.

### Language Files

New languages can be setup easily. For example, suppose we wanted to create an Icelandic language file (language code=is):

```
# create a folder using the Icelandic code (is)
sudo mkdir /var/www/lang/is

# copy across the default language file (or another that's similar to your chosen language)
# don't forget the trailing '/' on the destination
sudo cp /var/www/lang/en/settings.php /var/www/lang/is/

# edit the values in the file
sudo nano /var/www/lang/is/settings.php

# set correct owner for webspace
sudo chown -R www-data:www-data /var/www/lang
```

Your new language file will be available immediately, just go to the admin screen on the clock and select your language.

If you create a new language file, please go to the repo (https://github.com/stroggprog/mars-clock-2) and create a new issue. In the description section say what language (and language code) the file is for, and add your `settings.php` to the comment.

Note that language files do not need to be complete. If you only want to change some of the on-screen text, just modify those values. The additional values you can delete from the file, as they will automatically fall back to English.

Unfortunately it is not (yet) possible to provide translations for the menu system.

## Food for thought
If you have port-forwarding enabled on your router to port 80 on the clock, and either a static external IP address or use a dynamic hostname service, you can display the clock on your phone. I would recommend installing a firewall such as UFW, or accessing your home network solely via a VPN.

I have Pi-Hole acting as my DNS server, and I use a VPN to access my home network through a static IP address. In this way, my phone uses Pi-Hole as my DNS server whenever I'm at home and whenever I'm out in the world. In this way, I can just load up a browser on my phone and feed it the hostname of the machine my `mars-clock-2` is running on. It also gives me the ability to power the clock down from anywhere in the world.

Return to [README.md](../)
