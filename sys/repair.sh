if [ "$(id -u)" -ne 0 ] || [ "$MC2_OPT" != "2" ] || [ ! -d ./.git ]; then
    echo "Please run './menu.sh' in the root of the repository"
    exit 1
fi
echo "Removing clock"
rm -r /var/www/*

echo "Performing repair"
# sub-update
sys/sub_update.sh

echo "Repair complete."
