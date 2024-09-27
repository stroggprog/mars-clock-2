if [ "$(id -u)" -ne 0 ] || [ "$MC2_OPT" != "install" ] || [ ! -d ./.git ]; then
    echo "Please run './menu.sh' in the root of the repository"
    exit 1
fi

if [ -f /etc/cron.d/leap_seconds ]; then
    "mars-clock-2 is already installed. Please consider a repair or update"
    exit 1
fi
echo "Performing install"

# determine which browser has been installed
# we have a preference for firefox if both are available
#
if [ -f "/usr/bin/firefox" ]; then
    BROWSER="firefox"
elif [ -f "/usr/bin/chromium-browser" ]; then
    BROWSER="chromium-browser"
else
    echo "You must install firefox or chromium-browser before running this script"
    echo "If both are installed, firefox will be the preferred browser"
    exit 2
fi

##############
# test mode, exit!
#exit 0
USERHOME="/home/$SUDO_USER"
# remove default web page
if [ -f "/var/www/index.lighttp.html" ]; then
    rm /var/www/*.html
fi

# sub-update
sys/sub_update.sh

echo "Installing to use $BROWSER"
# if we're using a 3.5" GPIO/SPI screen then we require LXDE to be installed
# otherwise, assume an official 7" touchscreen and chromium-browser is installed in kiosk mode
if [ -d "$USERHOME/.config/lxsession" ]; then
    # setup LXDE autostart (3 steps)
    AUTOSTART_LIVE="$USERHOME/.config/lxsession/LXDE/autostart"
    AUTOSTART_TEMP="$AUTOSTART_LIVE.old"
    # 1. comment out screensaver and create a temporary file to work on
    sed -i 's/^@xscreensaver/#\0/' $AUTOSTART_LIVE

    # 2. next, we add the browser if it isn't already available in the file
    FIELDS="--kiosk -tab http://localhost"
    if [ "$BROWSER" = "firefox" ]; then
        FIELDS="--kiosk http://localhost"
    fi
    grep -qxF "@/usr/bin/$BROWSER $FIELDS" $AUTOSTART_LIVE || echo "@/usr/bin/$BROWSER $FIELDS" >> $AUTOSTART_LIVE

    ## 3. and add it if it doesn't exit
    SEEK_STR="@$USERHOME/bin/screensaver.sh"
    grep -qxF -- "$SEEK_STR" $AUTOSTART_LIVE || echo $SEEK_STR >> $AUTOSTART_LIVE

    echo "== Autostart"
    cat $AUTOSTART_LIVE
    echo "== End Autostart"
fi
# allow www-data to use the shutdown command
# this command won't add this line if it already exists
#
grep -qxF 'www-data ALL=NOPASSWD:/sbin/shutdown' /etc/sudoers || echo 'www-data ALL=NOPASSWD:/sbin/shutdown' >> /etc/sudoers

# setup cron job to fetch latest leap-second list
if [ ! -f /etc/cron.d/leap_seconds ]; then
    # get a random number between 0 and 59 (this is not a truly random number, but it'll work for us)
    CRON_MIN=$(shuf -i 0-59 -n 1)
    CRON_COMMAND="/var/www/sys/leap_seconds.sh"
    CRON="0 $CRON_MIN 28 * *"
    CRON_CAPTURE=">/dev/null 2>&1"
    CRON_USER="root"
    CRON_FILE_NAME="leap_seconds"
    CRON_PATH="/etc/cron.d/"
    CRON_FILE="$CRON_PATH$CRON_FILE_NAME"
    CRON_TZ="TZ=UTC"
    echo "Installing cron"
    echo "$CRON_TZ" > $CRON_FILE
    echo "$CRON $CRON_USER $CRON_COMMAND $CRON_CAPTURE" >> $CRON_FILE
    echo "CRON file looks like:"
    echo "== START"
    cat $CRON_FILE
    echo "== END"
else
    echo "cron already exists"
fi

if [ ! -d $USERHOME/bin ]; then
    mkdir $USERHOME/bin
fi
if [ ! -f $USERHOME/bin/screensaver.sh ]; then
    FILE="$USERHOME/bin/screensaver.sh"
    echo "#!/bin/bash" > $FILE
    echo "export DISPLAY=:0" >> $FILE
    echo "xset s off && xset -dpms" >> $FILE
    chmod +x $FILE
    chown $SUDO_USER:$SUDO_USER $FILE
fi

echo "Updating leap-second data"
/var/www/sys/leap_seconds.sh

echo "Installation is complete."
