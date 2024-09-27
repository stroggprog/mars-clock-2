# Menu system
To run the menu system, `ssh` into your clock's machine and navigate to the repository:

```
# first time we need to clone the repo
git clone https://github.com/stroggprog/mars-clock-2

# change directory to the root of the repo
cd mars-clock-2

# run the menu
sudo ./menu
```

There are several options (plus `Cancel` of course):

    1 Install
    2 Repair
    3 Update
    4 Uninstall
    5 Manage Images
    6 Shutdown Browser

All of the above options will check to see if the local copy of the repository is behind the upstream repository, and if so will `git pull` the latest version before performing the chosen action. This may seem odd if you are uninstalling, but if there were any errors in the scripts they may be corrected by a more recent update.

Note that the `git pull` will not perform an update of the clock, it only fetches the latest version of the repository from the source. Use the `3 Update` option to perform an actual update of the clock.

## Install

This will do a lot of things.
  - Copy files to the root of the web space
  - If using a desktop, it will set autostart options to run the browser in kiosk mode
  - Install a cron job to perform updates of the leap-second database
  - Enable certain scripts in the webspace to become executable
  - Grant permission to the web user (www-data) to power off the computer
  - Force an immediate update of the leap-second database
  - Create the `/data` folder in the root of the web space and populate it
  - Create the `/images/slides` folder in the root of the webspace and populate it
  - Create the `/style` folder in the root of the webspace and populate it
  - Create the `/lang` folder in the root of the webspace and populate it

Install should only be used once on a clean system. Deleting the contents of the webspace is insufficient to prepare the way for a new installation, as the install scripts will do much more than copy files to the webspace. If your clock needs 'rebuilding', use the `Repair` option, which will perform a full update of the clock without re-performing all the other installation steps.

Install will recognise whether you have a desktop installed and setup autostart options to launch your chosen browser in kiosk mode once the desktop is up and running. If you are using a 7" screen and have chosen Chromium in kiosk mode without desktop, these settings will already be configured.

The installation procedure is verbose (although not excessively so), so you can see what it is doing.

After installation is complete, type `sudo reboot` at the command prompt and wait for the magic to happen. If you are using a desktop, note the MHS35 screen will be blank for a long time, then the desktop will appear, and after a few more moments the clock will appear. There is nothing that can be done about this long boot-up sequence.

When using the firefox web browser, you may see the browser but not the clock the first time you run it. Scroll down the page that is shown and select 'skip' on each of the options until the 'first run' setup of firefox is complete, then reboot the machine.

## Repair
This will delete everything in the webspace and repopulate it, returning the clock to a vanilla state. Other external factors of the installation, such as the cron job, are unaffected.

If you have made changes to any of the `/data`, `/style`, `/lang` or `/images/slides` folders, note that they will be trashed. If you have installed your own images, you will have to re-install them. If you deleted the default set of images before installing your own images, remember to do that before re-installing your own images. The handling of images should be done through the `Manage Images` option of the menu system.

To force the clock to use new scripts and any updated json file, click the gear icon in the top-right corner of the screen, then the `Return` button in the bottom right of the screen.

## Update
This will update all files in the webspace. The following folders will not be updated to preserve any changes you have made:

  - /data
  - /images/slides
  - /style
  - /lang

To force the clock to use new scripts and any updated json file, click the gear icon in the top-right corner of the screen, then the `Return` button in the bottom right of the screen.

Actually, `/lang` is a funny one. The folder won't be recreated, but any changes to language files provided by the repository will be overwritten. If you have added new language files though, they will still exist. If you want to modify an existing language, such as `'en'`, you can copy it to a new folder with a different code and edit it.

## Uninstall
This will attempt to reverse everything done by the installation script. It will not remove a desktop, browser or web server, but it will leave a simple `index.html` in the root of the webspace, so navigating to the web server will display something intelligent.

The cron job and desktop autostart functions will be removed/reversed as appropriate, and screensaver settings (if changed) will be set back to factory default.

## Shutdown Browser

You should update the operating system at regular intervals. At least once per month, preferably once a week. This ensures your system receives the latest security patches. When you SSH into the clock, you will be presented with the DietPi welcome screen, which will tell you if there are any updates to perform. If there are any updates to the browser you are using, you may need to shutdown the browser before performing the update.

You can check if any of the browser files need updating by running the following command:

```
sudo apt list --upgradable
```

Alternatively, you can run this command:

```
# just fetches the latest update info and displays anything upgradable
sudo apt update
```

These will tell you what updates are available. If any of the files listed mention your browser, then you should shutdown the clock using the `Shutdown Browser` option in `./menu.sh` in the clock repository's root. Once it has shutdown your browser, run:

```
sudo apt upgrade
```

Note that an `apt` update/upgrade will not update the clock. That is performed by `./menu.sh`

This will perform the actual upgrade. Once the upgrade has finished, and if you shutdown the browser, just `sudo reboot` to get everything up and running again.

It is possible to relaunch the browser from an SSH session into the session running the XServer (required by Chromium in kiosk mode and any desktop that might be running), however, it will display a lot of errors. The reason is that access to the GPU is not enabled in an SSH session, and the browsers (although they will run fine without a GPU) will attempt to access the GPU via XServer libraries. Running applications from a different session also has the potential to break things so badly, the operating system may need reinstalling. Hence it is safer to reboot.

## Manage Images

This will present you with a new menu
```
    1 Delete Images
    2 Restore Default Images
    3 Copy User Images
```

Note that all of these options will display data and ask you to confirm before taking action.

### Delete Images

This will delete all the images in the `/images/slides` folder in the web root. All of them. A copy of the default set is still available for restoring.

### Restore Default Images

This will copy the default set of images (slides) to the `/images/slides` folder. It does not delete any files that might exist in that folder first, so this is an 'add' procedure.

### Copy User Images

There is a folder in the repository name `user_images`. If you want to add your own images/photos, you should use an ftp program to transfer them into this folder, then use this menu option to transfer them to `/images/slides` in the web root. Note that this is a copy operation, so if you delete the images from the clock, you can re-install them using this option.

You should always use this option and not perform the operation yourself, as it also changes ownership of the files to the www-data user as it does so. Be aware that since there will be two copies of files in this folder, you should use an SD card large enough to store them all.

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

Return to [README.md](../)
