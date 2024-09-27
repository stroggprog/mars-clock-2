# drag the latest hashes down from the remote
git remote update >/dev/null

LOCAL=$(git rev-parse @)
REMOTE=$(git rev-parse @{u})
BASE=$(git merge-base @ @{u})

if [ $LOCAL = $REMOTE ]; then
    exit 0
elif [ $LOCAL = $BASE ]; then
    exit 4
elif [ $REMOTE = $BASE ]; then
    exit 3
else
    exit 2
fi
