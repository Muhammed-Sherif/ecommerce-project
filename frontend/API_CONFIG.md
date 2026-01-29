# API Configuration Guide

## API Endpoints

The frontend is now configured to connect to your PHP API endpoints:

### Authentication Endpoints
- **Login**: `POST /api/login`
- **Register**: `POST /api/register`
- **Get User**: `GET /api/user` (protected)
- **Logout**: `POST /api/logout` (protected)
- **Refresh Token**: `POST /api/refresh` (protected)

## Configuration

### Setting the API Base URL

The API base URL is configured in `frontend/src/api.js`. By default, it uses:
- `http://localhost:8000`

### To change the API URL:

1. **Using Environment Variable (Recommended)**:
   Create a `.env` file in the `frontend/` directory:
   ```
   VITE_API_BASE_URL=http://localhost:8000
   ```
   Replace `http://localhost:8000` with your actual API server URL.

2. **Or modify directly in `frontend/src/api.js`**:
   ```javascript
   const api = axios.create({
     baseURL: 'http://your-api-url-here',
     headers: { 'Content-Type': 'application/json' }
   })
   ```

## Running the API Server

Make sure your PHP API server is running. You can start it with:

```bash
# Using PHP built-in server
php -S localhost:8000 -t . api.php

# Or if using Apache/Nginx, make sure it's configured to route to api.php
```

## CORS Configuration

CORS headers have been added to `api.php` to allow requests from the frontend. If you need to restrict access to specific origins, update the CORS headers in `api.php`:

```php
header('Access-Control-Allow-Origin: http://localhost:5173'); // Your frontend URL
```

## Testing the Connection

1. Start your PHP API server
2. Start your frontend development server: `npm run dev`
3. Try logging in or registering - the frontend will connect to `/api/login` and `/api/register`

## API Response Format

The API should return responses in this format:

**Success Response:**
```json
{
  "success": true,
  "access_token": "token_here",
  "refresh_token": "refresh_token_here",
  "user": { ... }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error message here"
}
```
