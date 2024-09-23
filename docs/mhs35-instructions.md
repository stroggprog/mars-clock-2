# MHS35 Instructions
Use these instructions if you are building a clock using the MHS35 3.5" TFT Touchscreen.

1. Re-log into your RPI as the `dietpi` user.
2. run:
```
# install utilities required by the screen's scripts
sudo apt install apt-utils build-essential

# install the screen's scripts and prep them for running
sudo rm -rf LCD-show
git clone https://github.com/goodtft/LCD-show.git
chmod -R 755 LCD-show
cd LCD-show/

# run the screen driver builder script with parameter
sudo ./MHS35-show 180
```
Note the last line is different from instructions you will see elsewhere, in that it instructs the driver to rotate the screen display by 180 degrees. After the script has built the drivers, it will reboot the computer.

3. Log in as `dietpi` again and run
```
sudo dietpi-software
```
Dietpi (the OS, not the user) can't run Chromium in kiosk mode on an MHS display, it'll just get errors. The workaround is to install a desktop and run the browser from there. Install the following (the dietpi index is also shown):
```
    [23] LXDE
    [81] lighttp+PHP+SQLite
    [67] Firefox
```
You can install Chromium instead of Firefox, but Firefox handles the small screen better.

After these have been installed, you should be offered the opportunity to select startup options. If not, run:
```
sudo dietpi-config
```
This is very important: **Do NOT select Browser Kiosk!**

Instead, select `Desktops: [2] Automatic Login`
Then select `Ok` and let it do its thing.

Back-out to the terminal prompt.

Complete the install by following the [Part III](part-three.md) instructions.
