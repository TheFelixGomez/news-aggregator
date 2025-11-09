# NewsApp: A Personalized News Aggregator App

NewsApp is a modern, full-stack news aggregator built with Laravel and React. It scrapes articles from multiple major news sources (NewsAPI, The Guardian, The New York Times), stores them locally, and provides users with a clean, fast, and personalized news feed based on their preferred sources, categories, and authors.

## Features

  - **User Authentication:** Users can register and log in to save their preferences.
  - **Local Data Scraping:** A scheduled command scrapes and normalizes data from 3 different APIs, storing it locally for fast access.
  - **Personalized Dashboard:** The main dashboard is a custom feed built from the user's saved preferences.
  - **Advanced Article Search:** A dedicated search page allows filtering all local articles by keyword, source, category, and date range.
  - **Preference Management:** A settings page allows users to pick their favorite sources, categories, and authors.
  - **Mobile-Responsive Design:** The frontend is built with Tailwind CSS for a fully responsive experience.
  - **Dark Mode:** Full support for light and dark modes, synced with user preference.

## Tech Stack

  - **Backend:** Laravel 12
  - **Frontend:** React 19
  - **Tools:** Tailwind CSS, Inertia.js, Vite
  - **Database:** MySQL
  - **Containerization:** Docker & Docker Compose

-----

## How to Run with Docker

This project is fully containerized using Docker.

### Prerequisites

  * [Docker Desktop](https://www.docker.com/products/docker-desktop/) (or Docker Engine + Docker Compose)

### 1. Environment Setup

1.  Copy the example environment file:

    ```sh
    cp .env.example .env
    ```

2.  **IMPORTANT:** Edit the `.env` file and set your database credentials. These *must* match the `db` service in `docker-compose.yml`.

    ```env
    # Set the host to the service name from docker-compose.yml
    DB_HOST=db
    DB_PORT=3306

    # Choose your database name, user, and passwords
    DB_DATABASE=news_aggregator
    DB_USERNAME=news_user
    DB_PASSWORD=your_secure_password
    DB_ROOT_PASSWORD=your_very_secure_root_password
    ```

3.  Add your API keys for the news sources to your `.env` file:

    ```env
    NEWS_API_KEY=your_key_here
    GUARDIAN_API_KEY=your_key_here
    NYT_API_KEY=your_key_here
    ```

### 2. Build and Run the Containers

From your project's root directory, run:

```sh
docker-compose up -d --build
```

This will build the `app` image (running `npm run build` in the process) and start all four services in the background.

### 3. Post-Installation Commands

The first time you run the app, you need to run these commands to finalize the Laravel setup.

```sh
# Generate the application key
docker-compose exec app php artisan key:generate

# Run database migrations
docker-compose exec app php artisan migrate

# Link the storage directory
docker-compose exec app php artisan storage:link
```

And the following command to run the initial data scraping:

```sh
# Fetch initial articles from the news sources (since the scheduler is disabled by default)
docker-compose exec app php artisan news:fetch-articles

# (Optional) If you want to enable the schedule go to console.php and uncomment the scheduler line
```

### 4. Access Your Application

You can now access the application in your browser at:
**[http://localhost:8080](http://localhost:8080)**

### Other Useful Docker Commands

  * **Run Artisan Commands:**

    ```sh
    # Example: Manually fetch articles
    docker-compose exec app php artisan news:fetch-articles
    ```

  * **View Logs (in real-time):**

    ```sh
    docker-compose logs -f
    ```

  * **Stop the Containers:**

    ```sh
    docker-compose down
    ```

  * **Stop and Remove Data:**
    *To completely reset your database, stop and remove the persistent volume.*

    ```sh
    docker-compose down -v
    ```
