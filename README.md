# Prescription Tracking App 

> A comprehensive tracking system to efficiently manage, verify, and monitor prescriptions.

## Visual Preview
*(Replace this with a screenshot or GIF of the application running)*

## Key Features
- **Prescription Management:** Easily track and record prescription details.
- **Admin Dashboard:** Fully featured dashboard for verifying and managing users, drugs, and prescriptions.
- **Role-based Access:** Segregated public-facing frontend and admin-specific interfaces.
- **RESTful API:** Robust Node.js backend for secure administrative tasks.
- **Dockerized Setup:** Simple, consistent configuration using Docker and Docker Compose.

## 💻 Tech Stack

### Frontend
![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white)
![JavaScript](https://img.shields.io/badge/javascript-%23F7DF1E.svg?style=for-the-badge&logo=javascript&logoColor=black)
![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=for-the-badge&logo=css3&logoColor=white)

### Backend
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![NodeJS](https://img.shields.io/badge/node.js-6DA55F?style=for-the-badge&logo=node.js&logoColor=white)
![Express.js](https://img.shields.io/badge/express.js-%23404d59.svg?style=for-the-badge&logo=express&logoColor=%2361DAFB)

### Database & Web Server
![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white)
![Nginx](https://img.shields.io/badge/nginx-%23009639.svg?style=for-the-badge&logo=nginx&logoColor=white)

### Deployment & Containerization
![Docker](https://img.shields.io/badge/docker-%230db7ed.svg?style=for-the-badge&logo=docker&logoColor=white)
![Docker Compose](https://img.shields.io/badge/docker_compose-2496ED?style=for-the-badge&logo=docker&logoColor=white)

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


