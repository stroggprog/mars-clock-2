if [ "$(id -u)" -ne 0 ] || [ "$MC2_OPT" != "3" ]; then
    echo "Please run './menu.sh' in the root of the repository"
    exit 1
fi
echo "Performing update"
# sub-update
sys/sub_update.sh

echo ""
echo "You should touch the gear icon on the screen and click the 'Return' button to force the clock to reload the scripts"