# Notes on Rebooting

After a reboot, the screen may come to a login screen and appear to stop, awaiting your input. If you are using the MHS35 screen (and therefore loading a desktop) please wait up to two minutes for the desktop to load. I am not sure why this long delay occurs but assume it is something to do with the LCD screen drivers. After the desktop loads, the clock will launch a few seconds later.

# When to Reboot

  - After an `apt upgrade` has upgraded any of the following:
    - Linux kernel and/or headers
    - any `Xorg` files
    - the web browser the clock is using
  - Whenever `dietpi-update` tells you to reboot

# When a Reboot isn't necessary

After updating the clock (via the menu in an `ssh` terminal): simply tap on the gear icon in the top right of the screen to load the admin screen, then hit the `Return` button. This forces all the scripts to reload, so a reboot is not required.
