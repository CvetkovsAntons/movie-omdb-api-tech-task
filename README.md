# Movie OMDB API – Tech Task

Backend service built with Laravel.

Frontend SPA built with Vue 3 and Bootstrap.

The application uses Redis for caching and is fully dockerized.

---

## Running the Project

The project runs entirely inside Docker.

### Option 1 (recommended)

```bash
make run-app
``` 
### Option 2
```bash
docker compose up -d
```

---

## Running tests

### Option 1 (recommended)

```bash
make run-tests
``` 
### Option 2
```bash
docker compose exec php php artisan test
```

---

## Using the Application

After starting the project, open the frontend in your browser:

http://localhost:5173
