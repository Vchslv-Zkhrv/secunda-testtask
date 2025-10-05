env:
	cp .env.example .env
build:
	docker compose build
install:
	docker compose exec php composer install -o
generate-app-key:
	docker compose exec php php artisan key:generate
up:
	docker compose up -d
down:
	docker compose down --remove-orphans
migrate:
	docker compose exec php php artisan migrate
seed:
	docker compose exec php php artisan db:seed
apikey:
	docker compose exec php php artisan app:api-key:generate
swagger:
	docker compose exec php php artisan l5-swagger:generate
test:
	docker compose exec php ./vendor/bin/phpunit

setup:
	$(MAKE) --ignore-errors env;
	$(MAKE) build;
	$(MAKE) up;
	$(MAKE) install;
	$(MAKE) generate-app-key;
	$(MAKE) migrate;
	$(MAKE) seed;
	$(MAKE) apikey;
	$(MAKE) swagger;
	$(MAKE) test;
	@echo "Quick setup complete"
