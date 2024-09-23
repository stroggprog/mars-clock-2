if [ "$(id -u)" -ne 0 ] || [ "$MC2_OPT" != "4" ] || [ ! -d ./.git ]; then
    echo "Please run './menu.sh' in the root of the repository"
    exit 1
fi
echo "Performing uninstall"

USERHOME="/home/$SUDO_USER"

# determine which browser has been installed
# we have a preference for firefox if both are available
#
if [ -f "/usr/bin/firefox" ]; then
    BROWSER="firefox"
elif [ -f "/usr/bin/chromium-browser" ]; then
    BROWSER="chromium-browser"
fi

# remove everything from the webspace
rm -r /var/www/*

# remove browser config if using desktop
# if we're using a 3.5" GPIO/SPI screen then we require LXDE to be installed
# otherwise, assume an official 7" touchscreen and chromium-browser is installed in kiosk mode
if [ -d "$USERHOME/.config/lxsession" ]; then
    AUTOSTART_LIVE="$USERHOME/.config/lxsession/LXDE/autostart"
    AUTOSTART_TEMP="$AUTOSTART_LIVE.old"
    cp "$AUTOSTART_TEMP" "$AUTOSTART_LIVE"
fi
grep -qxF 'www-data ALL=NOPASSWD:/sbin/shutdown' /etc/sudoers || echo 'www-data ALL=NOPASSWD:/sbin/shutdown' >> /etc/sudoers
sed -i '/www-data ALL=NOPASSWD:\/sbin\/shutdown/d' /etc/sudoers
if [ -f /etc/cron.d/leap_seconds ]; then
    rm /etc/cron.d/leap_seconds
fi
if [ -f $USERHOME/bin/screensaver.sh ]; then
    rm $USERHOME/bin/screensaver.sh
fi
if [ -d "$USERHOME/ban" ] && [ ! -n "$(ls -A "$USERHOME/ban")" ]; then
    rm -r "$USERHOME/bin"
fi
cp docs/index.lighttp.html /var/www/
chown www-data:www-data /var/www/*.html

echo "Uninstall is complete."
echo "If you wish to delete the repository, type from the parent directory:"
echo "sudo rm -r mars-clock-2"
