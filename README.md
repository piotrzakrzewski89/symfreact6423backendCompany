# symfreact6423backendCompany

docker compose exec php bin/console d:d:c --env=dev
docker compose exec php bin/console d:m:m --env=dev

docker compose exec php bin/console d:d:c --env=test
docker compose exec php bin/console d:m:m --env=test

fixtursy:
dphp bin/console doctrine:fixtures:load --env=dev
dphp bin/console doctrine:fixtures:load --env=test

testy:
dphp vendor/bin/phpunit --coverage-text

dphp vendor/bin/phpcs src --standard=PSR12 - code sniffer
dphp vendor/bin/phpcbf src - automatyczna naprawa