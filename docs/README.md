# Installation Instructions

These installation instructions assume you are using the [DietPi](https://dietpi.com) operating system, and an MHS35 3.5" TFT LCD screen (hereafter called the MHS35) or an official 7" touchscreen.

Please read these instructions in their entirety before attempting to install. Familiarity with the process will help you to resolve any issues, should there be any.

Note that for the MHS35 these instructions will tell you to reboot frequently - don't skip a reboot, they're important to ensure stuff is setup for the next step.

## All Screens
The first step is to write an image of the DietPi operating system to an SD card. A 16Gb SD card is large enough (with room to spare) but a 32Gb card will last longer.

If you've never done this before, download an image from [DietPi.com](dietpi.com) and uncompress it. The complete docs on how to download, uncompress and install DietPi are found at [https://dietpi.com/docs/install/](https://dietpi.com/docs/install/)

After installing DietPi on the SD card, you may wish to pre-configure wifi by editing `dietpi-wifi.txt` in the boot partition of your SD card before you put it in the RPI (it's the smaller of the two partitions). You should also edit `dietpi.txt` in the same partition and change the following values as appropriate:
```
AUTO_SETUP_NET_ETHERNET_ENABLED=0
AUTO_SETUP_NET_WIFI_ENABLED=1

# WiFi country code: 2 capital letter value (e.g. GB US DE JP): https://en.wiki>
# - NB: This choice may be overridden if the WiFi access point sends a country >
AUTO_SETUP_NET_WIFI_COUNTRY_CODE=GB
```

After booting up:
  1. Configure country and keyboard settings as usual.
  2. Change passwords for both software and users
  3. Select ONLY `git` as software to install
  4. Select `Install Software` and allow the system to complete the install & update procedure

## Configuring MHS35
Please follow these instructions which are specific to installing for an MHS35 screen: [MHS35 Instructions](mhs35-instructions.md)

## Configuring Official 7" Touchscreen
Please follow these instructions which are specific to installing for the 7" screen: [Official 7" Touchscreen Instructions](seven-inch-instructions.md)

