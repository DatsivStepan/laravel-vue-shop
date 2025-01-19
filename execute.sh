#!/bin/bash

PROJECT_FOLDER=/home/intellicart/project
branch=develop
npmbuild=dev

# pull code from repository
pull_code()
{
    echo ">>> Pulling Code from '$1' branch..."
    git pull
    git checkout $1
    echo ""
}

# run migrations and clear generated data
upgrade()
{
    echo ">>> Composer Process..."
    composer install --ignore-platform-reqs
    echo ""

    echo ">>> Central Migration Process..."
    php artisan migrate
    echo ""

    echo ">>> Tenans Migration Process..."
    php artisan tenants:migrate
    echo ""

    echo ">>> Generate Lang file for front Process..."
    php artisan VueTranslation:generate
    echo ""

    echo ">>> Tenans Seed Process..."
    php artisan tenants:seed
    echo ""

    echo ">>> Generate Storage link for media files"
    php artisan storage:link
    echo ""

    echo ">>> Cleaning Process..."
    make clean
    echo ""
}

# build project
build()
{
    echo ">>> NPM Install Process..."
    npm install
    echo ""

    echo ">>> NPM Build '$1' ..."
    npm run $1
    echo ""
}


# open project directory
cd $PROJECT_FOLDER

# run commands
if [ "$1" = "pullcode" ]; then

    if [ "$2" != "" ]; then
      branch=$2
    fi

    pull_code $branch
fi

if [ "$1" = "upgrade" ]; then
    upgrade
fi

if [ "$1" = "build" ]; then
    if [ "$2" != "" ]; then
      npmbuild=$2
    fi
    build $npmbuild
fi

exit
