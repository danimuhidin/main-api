cd mainapi.danimuhidin.my.id/ || exit

git reset --hard
git pull origin main

/usr/local/bin/composer install
/usr/local/bin/php artisan optimize
/usr/local/bin/php artisan config:clear

echo "Deployment finished at $(date)"