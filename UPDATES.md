# 2024-09-28 tag 1.0.1
Major update to image system, install/repair, style, documentation, version control, language

- Allowed for installing/uninstalling default images
- Allowed for installing user-provided images/photos
- Allowed for user to edit CSS files and protect these changes from future updates
- Updated menu system to add Image menu
- Added 'shutdown browser' option to menu to kill the browser prior to browser updates
- Added update checking on menu system
    1. Compare local repo with origin
        - if different display msg (selecting any menu option will update local repo)
    2. If local and origin are up to date compare local with installed clock
        - if different, display msg in error colors to suggest Update
- Store latest commit hash in a safe place so comparison between repo and clock can be made
- Ensure version control is backward compatible with previous versions with limited
- Added ability to change language (requires language files)
- Added English language file as both default and fall-back
- Updated docs to explain how to create a new language file
- Added UPDATES.md so changes can be tracked by commit
