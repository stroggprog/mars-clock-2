echo "Checking origin for updates..."

# drag the latest hashes down from the remote
git remote update

LOCAL=$(git rev-parse @)
REMOTE=$(git rev-parse @{u})
BASE=$(git merge-base @ @{u})

if [ $LOCAL = $REMOTE ]; then
    echo "...Everything is Up-to-date"
    exit 0
elif [ $LOCAL = $BASE ]; then
    echo "...Need to pull"
    exit 4
elif [ $REMOTE = $BASE ]; then
    echo "...Need to push, please send a pull request"
    exit 3
else
    echo "...Diverged, what did you do?"
    exit 2
fi
