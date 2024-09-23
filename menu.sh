if [ "$(id -u)" -ne 0 ]; then
    # force run as sudo
    sudo $0
else
    if [ ! -d .git ]; then
        echo "Please run './menu.sh' in the root of the repository"
        exit 1
    fi
    RESULT=$(
    whiptail --title "Mars-Clock-2 Setup" --menu "" 12 48 4 \
        1 "Install"   \
        2 "Repair"  \
        3 "Update" \
        4 "Uninstall" 3>&2 2>&1 1>&3
    )

    if [ "$RESULT" = "" ]; then
        echo "backing out"
        exit 0
    fi
    if [ "$RESULT" = "1" ] || [ "$RESULT" = "2" ] || [ "$RESULT" = "3" ] || [ "$RESULT" = "4" ]; then
        export MC2_OPT=$RESULT
        sys/git_check.sh
        GIT_CHECK=$?

        # if 0, we are up to date
        # if 1, serious error, we didn't allow for a value of 1
        # if 2, local and remote have diverged
        # if 3, local repo has updates that need to be pushed to remote
        # if 4, we need to pull from origin
        #
        # only 4 and 0 are valid to continue as the others equate to an error
        # that the user must rectify themselves
        #
        if [ "$GIT_CHECK" -eq 4 ]; then
            git pull
            if [ $? -ne 0 ]; then
                echo "An error has occurred, please investigate."
                exit 1;
            fi
        elif [ "$GIT_CHECK" -ne 0 ]; then
            exit 1
        fi
        if [ "$RESULT" -eq 1 ]; then
            sys/install.sh
        elif [ "$RESULT" -eq 2 ]; then
            export MENU_CALLER=$0
            sys/repair.sh
        elif [ "$RESULT" -eq 3 ]; then
            sys/update.sh
        elif [ "$RESULT" -eq 4 ]; then
            sys/uninstall.sh
        fi
    fi
fi
