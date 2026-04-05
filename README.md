# Issue Intake App

A Laravel application with Inertia.js and React for managing issue intake.

## Prerequisites

- Docker and Docker Compose
- Git

## Windows Users

If you're developing on Windows, it is **highly recommended** to use WSL (Windows Subsystem for Linux) to place and run this application. The app setup heavily uses Sail (Docker) for ease of setup and installation, which works best in a Linux environment.

To set up WSL:
1. Install WSL 2 by running `wsl --install` in PowerShell as Administrator
2. Clone this repository inside your WSL filesystem (e.g., `~/projects/issue-intake-app`)
3. Proceed with the installation steps below from your WSL terminal

## Installation

Follow these steps to set up the application on your local machine:

### 1. Clone the repository

```bash
git clone <repository-url>
cd issue-intake-app
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node.js dependencies

```bash
npm install
```

### 4. Start Docker containers

```bash
./vendor/bin/sail up -d
```

This will start all necessary containers (MySQL, phpMyAdmin, etc.) in detached mode.

### 5. Run database migrations

```bash
./vendor/bin/sail artisan migrate
```

### 6. Generate application key

```bash
./vendor/bin/sail artisan key:generate
```

### 7. Create a .env file (if not exists)

```bash
cp .env.example .env
```

Make sure to configure your `.env` file with appropriate settings.

### 8. Pull the Ollama model

```bash
docker-compose exec ollama ollama pull llama3.2
```

### 9. Verify Ollama is running and model is loaded

Check if Ollama is running and list models:

```bash
curl http://localhost:11434/api/tags
```

Expected output: JSON like `{"models": [{"name": "llama3.2", ...}]}`. If you see `llama3.2` in the list, the model is loaded.

### 10. Run the queue service

Start the queue worker to process background jobs for AI analysis:

```bash
./vendor/bin/sail artisan queue:work --tries=3 --timeout=90
```

## Running the Application

To start the development server with hot module replacement:

```bash
./vendor/bin/sail npm run dev
```

The application will be available at `http://localhost`

## Accessing phpMyAdmin

phpMyAdmin is available at:

```
http://localhost:8080
```

Use the database credentials from your `.env` file to log in.

## Additional Commands

### Stop Docker containers

```bash
./vendor/bin/sail down
```

### Run tests

```bash
./vendor/bin/sail artisan test
```

### Format code with Pint

```bash
./vendor/bin/sail pint
```

## Troubleshooting

- If you encounter permission issues, ensure Docker is running and your user has appropriate permissions
- For Sail command shortcuts, consider creating an alias: `alias sail='./vendor/bin/sail'`
