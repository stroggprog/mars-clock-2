# Official 7" Touchscreen Instructions
Use these instructions if you are building a clock using an Official 7" Touchscreen.

1. Re-log into your RPI as the `dietpi` user and run:
```
sudo dietpi-software
```
Install the following (the dietpi index is also shown):
```
    [81] lighttp+PHP+SQLite
    [113] Chromium: web browser for desktop or autostart
```

After these have been installed, you should be offered the opportunity to select startup options. If not, run:
```
sudo dietpi-config
```

1. Select the `Autostart` option.
2. Select `Browser Kiosk: 11 : Chromium - dedicated use without desktop`

Configure to point at `http://localhost/`

**You can't use 'dedicated use without desktop' on an MHS screen!**

Complete the install by following the [Part III](part-three.md) instructions.
