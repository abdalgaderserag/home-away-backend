cp ./env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
docker compose exec app php artisan optimize
docker compose exec app php artisan config:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan icons:cache
docker compose exec app php artisan filament:optimize
docker compose exec app php artisan event:cache