run-app:
	docker compose up -d

run-tests:
	docker compose exec php php artisan test