# Movie OMDB API – Tech Task

Backend service built with Laravel.

Frontend SPA built with Vue 3 and Bootstrap.

The application uses Redis for caching and is fully dockerized.

---

## Environment setup

Before starting the project, create a `.env` file:

```bash
cp .env.example .env
``` 

Then open the .env file and set your OMDb API token:

```dotenv
OMDB_API_TOKEN=your_api_token_here
```

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
