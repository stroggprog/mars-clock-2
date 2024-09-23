# Part Three Instructions

Rule #1: Always know where you are:

```
cd ~/
```

I usually put my projects in a Projects folder, it helps keep my home folder clean. If you don't feel the need skip this next step:

```
mkdir Projects
cd Projects
```

Clone the clock repository and run the installer. `x.x.x` is the latest release version, check the repo url to see the latest release version. Alternately you can omit `--branch x.x.x` entirely to get the latest bleeding edge version, which should have no breaking changes (but no promises on that count).
```
git clone https://github.com/stroggprog/mars-clock-2 --branch x.x.x
cd mars-clock-2
sudo ./menu.sh
```

There are four options (plus `Cancel` of course):

    1. Install
    2. Re-Install
    3. Update
    4. Uninstall

All of the above options will check to see if the local copy of the repository is behind the upstream repository, and if so will `git pull` the latest version before performing the chosen action. This may seem odd if you are uninstalling, but if there were any errors in the scripts they may be corrected by a more recent update.

## Install

This will do a lot of things.
  - Copy files to the root of the web space
  - If using a desktop, it will set autostart options to run the browser in kiosk mode
  - Install a cron job to perform updates of the leap-second database
  - Enable certain scripts in the webspace to become executable
  - Grant permission to the web user (www-data) to power off the computer
  - Force an immediate update of the leap-second database
  - Create the `/data` folder in the root of the web space and populate it

Install should only be used once on a clean system. Deleting the contents of the webspace is insufficient to prepare the way for a new installation, as the install scripts will do much more than copy files to the webspace. If your clock needs 'rebuilding', use the `Repair` option, which will perform a full update of the clock without re-performing all the other installation steps.

Install will recognise whether you have a desktop installed and setup autostart options to launch your chosen browser in kiosk mode once the desktop is up and running. If you are using a 7" screen and have chosen Chromium in kiosk mode without desktop, these settings will already be configured.

The installation procedure is verbose (although not excessively so), so you can see what it is doing.

After installation is complete, type `sudo reboot` at the command prompt and wait for the magic to happen. If you are using a desktop, note the MHS35 screen will be blank for a long time, then the desktop will appear, and after a few more moments the clock will appear. There is nothing that can be done about this long boot-up sequence.

## Repair
This will delete everything in the webspace and repopulate it. Other factors of the installation, such as the cron job, are unaffected.

To force the clock to use new scripts and any updated json file, click the gear icon in the top-right corner of the screen, then the `Return` button in the bottom right of the screen.

## Update
This will update all files in the webspace except the `/data` folder (preserving any changes/settings you have made there).

To force the clock to use new scripts and any updated json file, click the gear icon in the top-right corner of the screen, then the `Return` button in the bottom right of the screen.

## Uninstall
This will attempt to reverse everything done by the installation script. It will not remove a desktop, browser or web server, but it will leave a simple `index.html` in the root of the webspace, so navigating to the web server will display something intelligent.

The cron job and desktop autostart functions will be removed/reversed as appropriate, and screensaver settings (if changed) will be set back to factory default.

# Other Points to Note
## First run
If you chose Firefox as your browser - recommended on MHS35 screens - the first run will (rather annoyingly) ask you some setup questions then display the default blank page. Just select 'skip' for each question (you may have to scroll down to see the questions), then force a reboot via `ssh` and the problem will have been fixed.

## Screen Blanking
There are two events which we want to be able to control: the screensaver and the screen blanking.
The install script created a file called `screensaver.sh`, which is placed in a folder `~/bin`.
Here's what the contents of the shell script look like:
```
#!/bin/bash
export DISPLAY=:0
xset s off && xset -dpms
```

`xset s off` turns off the screen saver, and `xset -dpms` disables DPMS (EnergyStar) features.

Note that you can also use this to enable the screensaver and re-enable DPMS, e.g.:
```
# re-enable screen saver
xset s on

# screen saver set to 5 mins (in seconds)
xset s 300

# re-enable DMPS
xset +dpms
```

The `s` parameter is quite versatile. Run `man xset` for more information. On DietPi, you may need to install `man` first:
```
sudo apt install man-db
```

## Shutdown capability
As part of the installation process, the web user (www-data) is given permission to shutdown the computer as if it had typed:
```
sudo shutdown now
```
This is the only `sudo` permission it is given. If you don't want it to have this ability, either edit `install.sh` before running it and comment out the line that looks like this:

```
    grep -qxF 'www-data ALL=NOPASSWD:/sbin/shutdown' /etc/sudoers || echo 'www-data ALL=NOPASSWD:/sbin/shutdown' >> /etc/sudoers
```
...or you can edit `/etc/sudoers` file after running `install.sh` and removing the last line, which should look like this:
```
www-data ALL=NOPASSWD:/sbin/shutdown
```

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


## Images

You can add your own images (perhaps family photos) to the folder `/var/www/images/slide`. You will have to do this in a roundabout way. If you didn't change the default ssh server from Dropbear, firstly ssh into the machine then run

```
sudo dietpi-software
```

Select the SSH server on the main menu and change it to OpenSSH. Once that is installed, create a folder in your home directory to accept images:

```
mkdir ~/images
```

Now you can use a program such as FileZilla to transfer your images to this folder. Once you have done that, ssh into the computer again.

```
# if you want to remove the default slides
sudo rm -r /var/www/images/slides/*

# copy your own files over
sudo cp ~/images/* /var/www/images/slides/

# next we have to pass ownership to www-data
sudo chown -R www-data:www-data /var/www/images/slides
```

On any update operation, the slide folder will remain intact. However, on a repair operation it will be rebuilt (repair assumes everything is broken), therefore it will be necessary to copy your own images over again.

## Food for thought
If you have port-forwarding enabled for a(ny) port to port 80 on the clock, and either a static external IP address or use a dynamic hostname service, you can display the clock on your phone. I would recommend installing a firewall such as UFW, or accessing your home network solely via a VPN.

I have Pi-Hole acting as my DNS server, and I use a VPN to access my home network through a static IP address. In this way, my phone uses Pi-Hole as my DNS server whenever I'm at home and whenever I'm out in the world. In this way, I can just load up a browser on my phone and feed it the hostname of the machine my `mars-clock-2` is running on. It also gives me the ability to power the clock down from anywhere in the world.

Return to [README.md](../)
