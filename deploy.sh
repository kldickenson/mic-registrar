#!/usr/bin/env bash

# Pulls code from provided branch name.
git pull origin $1

# Switch to PHP 7.
source /opt/rh/rh-php70/enable

# Run any database updates.
/afs/umich.edu/group/itd/umweb/bin/drush-8.x/drush updatedb -y

# Rebuild entities.
/afs/umich.edu/group/itd/umweb/bin/drush-8.x/drush entup -y

# Rebuild cache
/afs/umich.edu/group/itd/umweb/bin/drush-8.x/drush cr