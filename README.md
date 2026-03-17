# Prescription Tracking App 💊

> A comprehensive tracking system to efficiently manage, verify, and monitor prescriptions.

## Visual Preview
*(Replace this with a screenshot or GIF of the application running)*

## Key Features
- **Prescription Management:** Easily track and record prescription details.
- **Admin Dashboard:** Fully featured dashboard for verifying and managing users, drugs, and prescriptions.
- **Role-based Access:** Segregated public-facing frontend and admin-specific interfaces.
- **RESTful API:** Robust Node.js backend for secure administrative tasks.
- **Dockerized Setup:** Simple, consistent configuration using Docker and Docker Compose.

## Tech Stack
- **Frontend:** HTML5, Vanilla JavaScript, CSS
- **Backend (Public):** PHP 
- **Backend (Admin):** Node.js, Express.js
- **Database:** MySQL
- **Proxy/Web Server:** Nginx
- **Containerization:** Docker & Docker Compose

## Getting Started

### Prerequisites
Before you begin, ensure you have the following installed on your machine:
- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Installation

1. **Clone the repository**
   ```bash
   git clone <your-repository-url>
   cd Prescription-Tracking-App
   ```

2. **Environment Variables**
   Create an `.env` file in the `admin/server` directory based on the default configuration:
   ```env
   ADMIN_PORT=4000
   ADMIN_APP_ORIGIN=http://localhost:80
   MYSQL_HOST=db
   MYSQL_USER=root
   MYSQL_PASSWORD=
   MYSQL_DATABASE=wium_lie_demo
   MYSQL_POOL_LIMIT=10
   ```

3. **Run the Application**
   ```bash
   docker-compose up --build -d
   ```
   *The application will be available at `http://localhost`.*

## Scripts / Commands
| Command | Description |
|---|---|
| `docker-compose up -d` | Starts the containers in the background |
| `docker-compose down`  | Stops and removes the containers |
| `npm run dev` (in `admin/server`) | Runs the Node API in dev mode using nodemon |

## Project Structure
```text
Prescription-Tracking-App/
├── admin/
│   ├── frontend/        # Admin UI (Dashboard, Users, Drugs, Prescriptions)
│   └── server/          # Node.js Express API for Admin
├── database/            # MySQL initialization scripts
├── nginx/               # Nginx server configuration
├── public/              # Public-facing static files and views
├── src/                 # Public PHP backend logic 
├── Dockerfile.php       # Docker configuration for the PHP service
├── docker-compose.yml   # Multi-container orchestration
└── router.php           # Main PHP routing logic
```

## API Documentation
The Admin API runs on a Node.js Express server. Key endpoint areas include:
- `/api/users` - Manage users and authentication
- `/api/prescriptions` - Track and update prescription records
- `/api/drugs` - Manage the medicine inventory
- `/api/verification` - Pending and approved verifications

*For detailed request/response schemas, you can import the routes into an API client like Postman.*


