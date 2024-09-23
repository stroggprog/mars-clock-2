# Sys
This folder contains applets to massage data.

  - library.php
  - massage-missions.php
  - massage-coordinates.php
  - lang-builder.php
  - leap_seconds.sh and leap_seconds.php

## library.php
contains a debug function and a sorting function used by other scripts in this folder.

## massage-missions.php
This collates data scraped from [https://philip-p-ide.uk/doku.php/blog/aardvaark/mars_missions](https://philip-p-ide.uk/doku.php/blog/aardvaark/mars_missions), which has been transformed from a javascript array into a json object. This is read, sorted and output as a new json file in the `/data` folder of the webspace. Any data not needed by the clock is discarded during this transformation process, and additional information, such as an index, is added.

## massage-coordinates
This is similar to `massage-missions.php`, except it uses location coordinates culled from Mars24.

## lang-builder.php
This program takes a list of languages found in `languages.dat`, and creates a json file `language.json`, which should then be copied to the root of the webspace (i.e. one folder up). There should already be a copy of that file in the root of the webspace, which you can simply edit to meet your needs.

The default language is set to English (`en`). If your browser is throwing up offers to translate the page for you, edit the `language.json` file in the root of the web space and change the `language` option to your language. The list of other languages is there simply to help you identify the correct code for your chosen language, and they can all be deleted - only the `language` key and value is required.

# Usage
These programs should be run from the `sys` folder, and from the command line, and PHP does not need to be invoked, the scripts self-reference their processor, so a call on the command-line would simply look like this:

```
massage-missions.php
```

On reading the input file, it will immediately write out a pretty-print version of the data, which at this point is otherwise unchanged. This is so that if there is an issue with the data, it is easier to read.

## leap_seconds
Updating of the leap seconds is crucial for accuracy of the clock, as Martian time doesn't use leap-seconds, whereas UTC does - and time calculations are based on UTC.

You should setup a cron job as the root user:
```
sudo crontab -e
```
The cronjob definition is:
```
0 16 28 * * /var/www/sys/leap_seconds.sh > /var/www/logs/error_leap-second.log
```

This invokes the job to run at 4pm (16th hour) on the 28th of every month (which would be after any update to the list we're fetching). If you want to have this run later, just change 16 to whatever suits you, but it must be on the 28th of the month, and no earlier than 4pm UTC.

You should also ensure `leap_seconds.sh` is executable:
```
sudo chmod +x leap_seconds.sh
```
The `sudo` is required because the file exists in the web-space, and therefore the owner is either root or www-data (hopefully the latter).
