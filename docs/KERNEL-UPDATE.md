# KERNEL UPDATE
If you've done a DietPi kernel update that has caused the 3.5" screen to stop working, follow these instructions to get back up and running.

  1. ssh into the clock as the DietPi user (or whichever account you used to install the 3.5" screen)
  2. enter `cd LCD-show` to change to that folder
  3. enter `sudo ./MHS35-show 180`

At this point, you may see a lot  of errors about directories not existing or not found. Don't worry, just let the script run to conclusion. At the end, the script should reboot the machine. It may take a minute or three to display the clock, but it should have fixed the problem.

What was the problem? The DietPi update script which installed the new kernel didn't preserve the `dtparam` setting for the screen in the the startup config file. Running the script `MHS35-show 180` rebuilt the drivers (in case the build changed with the new kernel) and added the `dtparam` setting to the config file.
