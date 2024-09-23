if [ "$(id -u)" -ne 0 ] || [ "$MC2_OPT" = "" ] || [ ! -d .git ]; then
    echo "Please run './menu.sh' in the root of the repository"
    exit 1
fi

# blindly copy everything to the web root
cp -r . /var/www

# police stuff - protect and preserve
mv /var/www/sys/leap_seconds.sh /var/www/admin/leap_seconds.sh

# remove everything that's not needed
rm -r /var/www/.git
rm -r /var/www/docs
rm /var/www/*.sh
rm /var/www/sys/*.sh

# let out of protective custody
mv /var/www/admin/leap_seconds.sh /var/www/sys/leap_seconds.sh

# if the data folder already exists, we don't want to trample all over it
if [ ! -d "/var/www/data" ]; then
    echo installing data to folder "/var/www/data"
    mkdir /var/www/data
    cp -r /var/www/x-data/* /var/www/data
fi

# if slide folder already exists, don't trample all over it
if [ ! -d "/var/www/images/slides" ]; then
    echo installing data to folder "/var/www/images/slides"
    mkdir /var/www/images/slides
    cp -r /var/www/x-slide/* /var/www/images/slides
fi

# change ownership of everything to www-data (web user) except stuff in sys folder
# because www-data doesn't have execution rights
chown -R www-data:www-data /var/www
chown -R $USER:$USER /var/www/sys

# make a few scripts runnable from the command line just by invoking them
# (no need to call php first)
chmod +x /var/www/sys/*.php
chmod +x /var/www/sys/leap_seconds.sh

# ...except the library which is invoked by the other scripts
chmod -x /var/www/sys/library.php
