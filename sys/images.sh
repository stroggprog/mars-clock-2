if [ "$(id -u)" -ne 0 ] || [ "$MC2_OPT" != "images" ] || [ ! -d ./.git ]; then
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

count_files () {
    local count="$(ls -1q $1/* 2>/dev/null | wc -l)"
    echo $count
}

change_owner_wwww () {
    chown -R www-data:www-data /var/www/images/slides
}

delete_images () {
    line
    active_slides="$(count_files "/var/www/images/slides")"
    default_slides="$(count_files "/var/www/x-slide")"
    diff_slides="$(($active_slides - $default_slides))"
    echo "There are $active_slides images in the slides folder"
    echo "There are $default_slides default slides available to reinstall"
    if [ $active_slides -gt 0 ]; then
        echo "This will delete all images in the slides folder"
        result="$(askYN "Do you wish to continue? [y/N] ")"
        if [ "$result" = "1" ]; then
            rm /var/www/images/slides/* 2>/dev/null
            recount_slides="$(count_files "/var/www/images/slides")"
            diff_slides="$(($active_slides - $recount_slides))"
            echo "$diff_slides deleted"
            echo "$recount_slides remaining"
        else
            echo "Aborting 'Delete Images'"
        fi
    fi
    press_any_key
}

restore_default_slides () {
    line
    default_slides="$(count_files "/var/www/x-slide")"
    echo "There are $default_slides default slides available to reinstall"
    if [ $default_slides -gt 0 ]; then
        echo "This will restore the default images to the slides folder"
        result="$(askYN "Do you wish to continue? [y/N] ")"
        if [ "$result" = "1" ]; then
            cp -n /var/www/x-slide/* /var/www/images/slides/
            change_owner_wwww
            echo "$default_slides copied (existing files were skipped)"
        fi
    fi
    press_any_key
}

copy_user_images () {
    line
    active_slides="$(count_files "/var/www/images/slides")"
    user_slides="$(count_files "user_images")"
    echo "There are $active_slides images in the slides folder"
    echo "There are $user_slides available to install"
    if [ $user_slides -gt 0 ]; then
        echo "This will install these in the slides folder"
        result="$(askYN "Do you wish to continue? [y/N] ")"
        if [ "$result" = "1" ]; then
            cp -n user_images/* /var/www/images/slides/
            change_owner_wwww
            new_active_slides="$(count_files "/var/www/images/slides")"
            copied_files="$(($new_active_slides - $active_slides))"
            not_copied="$(($user_slides - $copied_files))"
            echo "$copied_files copied"
            echo "$not_copied skipped (already in slides folder)"
            echo "There are now $new_active_slides images in the slides folder"
        fi
    fi
    press_any_key
}


RESULT="undecided"
while [ -n "$RESULT" ]; do
    RESULT=$(
    whiptail --title "Mars-Clock-2 Images" --menu "Choose an option" --cancel-button "Back" 12 48 3 \
        "1" "Delete Images"   \
        "2" "Restore Default Images"  \
        "3" "Copy User Images"  3>&2 2>&1 1>&3
    )
    if [ ! -n "$RESULT" ]; then
        MC2_OPT="Bye-bye"
    elif [ "$RESULT" -eq 1 ]; then
        delete_images
    elif [ "$RESULT" -eq 2 ]; then
        restore_default_slides
    elif [ "$RESULT" -eq 3 ]; then
        copy_user_images
    fi
done
