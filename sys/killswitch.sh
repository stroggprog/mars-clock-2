if [ "$(id -u)" -ne 0 ] || [ "$MC2_OPT" != "killswitch" ] || [ ! -d ./.git ]; then
    echo "Please run './menu.sh' in the root of the repository"
    exit 1
fi

line () {
    echo "--------------------"
}

press_any_key () {
    echo "Press any key to continue..."
    read -n 1 -s
    echo ""
}

askYN () {
    read -r -n 1 -s -p "$1" response
    response=${response,,}    # tolower
    if [ "$response" = "y" ]; then
        echo "y" 1>&2
        echo "1"
    else
        echo "n" 1>&2
        echo "0"
    fi
}

# determine which browser has been installed
# we have a preference for firefox if both are available
#
if [ -f "/usr/bin/firefox" ]; then
    BROWSER="firefox-esr"
elif [ -f "/usr/bin/chromium-browser" ]; then
    BROWSER="chromium"
else
    echo "You must install firefox or chromium-browser before running this script"
    exit 2
fi
line
echo "This will shutdown the browser"
result="$(askYN "Do you wish to continue? [y/N] ")"
if [ "$result" = "1" ]; then
    killall -w $BROWSER
    echo "Thine browser hath gone to the great bit-bucket in the sky"
    press_any_key
fi
