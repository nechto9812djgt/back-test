# Developer Landing API

## Description

Developer Landing API is a REST API for processing contact form requests.

The application validates incoming data, limits the number of requests from one IP address, analyzes the sentiment of the message using Google Gemini AI, sends email notifications, stores request statistics, logs events, and provides interactive API documentation via Swagger.

---

## Tech Stack

### Backend

- PHP 8.2
- Laravel 12

### AI

- Google Gemini API (Gemini Flash)

### Documentation

- L5 Swagger (OpenAPI)

### Other

- Laravel HTTP Client
- Laravel Mail
- Laravel Storage

---

## Architecture

The project follows a service-oriented architecture.

Business logic is separated into dedicated services:

- **ContactService** — orchestrates the request processing workflow.
- **AIService** — integrates with the Google Gemini API and performs sentiment analysis.
- **MailService** — sends notification emails.
- **RateLimitService** — limits requests from the same IP address.
- **MetricsService** — collects and stores request statistics.
- **LogService** — writes request information to log files.

Controllers are responsible only for handling HTTP requests and delegating business logic to services. This approach keeps the code modular, maintainable, and easy to extend.

## Infrastructure

- CORS is configured using Laravel's built-in middleware.
- Laravel's global exception handler converts exceptions into appropriate HTTP responses.
- Environment-specific configuration is managed through the `.env` file.

---

## Installation

### 1. Clone the repository

```bash
git clone <repository_url>
cd back-test
```

### 2. Install dependencies

```bash
composer install
```

### 3. Create the environment file

```bash
cp .env.example .env
```

If you are using Windows:

```bash
copy .env.example .env
```

### 4. Generate the application key

```bash
php artisan key:generate
```

### 5. Configure the environment

Edit the `.env` file and set the required values.

Example:

```env
APP_NAME="Developer Landing API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

GEMINI_API_KEY=your_gemini_api_key

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
```

### 6. Create the SQLite database

```bash
touch database/database.sqlite
```

On Windows:

```bash
type nul > database\database.sqlite
```

### 7. Run database migrations

```bash
php artisan migrate
```

### 8. Generate Swagger documentation

```bash
php artisan l5-swagger:generate
```

### 9. Start the application

```bash
php artisan serve
```

The API will be available at:

```
http://127.0.0.1:8000
```

Swagger UI:

```
http://127.0.0.1:8000/api/documentation
```

---

# API Reference

## Base URL

```
http://127.0.0.1:8000/api
```

---

## POST /contact

Processes a contact form submission, validates input, performs AI sentiment analysis, updates metrics, writes logs, and sends notification emails.

### Request

**POST**

```
/api/contact
```

**Content-Type**

```
application/json
```

### Request Body

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "comment": "Your service is amazing!"
}
```

### cURL Example

```bash
curl -X POST http://127.0.0.1:8000/api/contact \
-H "Content-Type: application/json" \
-d '{
"name":"John",
"email":"john@example.com",
"phone":"+123456789",
"comment":"Your service is amazing!"
}'
```

### Successful Response

**200 OK**

```json
{
    "success": true,
    "message": "Request received",
    "sentiment": "positive"
}
```

Possible sentiment values:

- `positive`
- `neutral`
- `negative`
- `unknown`

---

### Validation Error

**422 Unprocessable Entity**

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "The email field must be a valid email address."
        ]
    }
}
```

---

### Rate Limit Exceeded

**429 Too Many Requests**

```json
{
    "success": false,
    "message": "Too many requests."
}
```

---

## GET /metrics

Returns application statistics.

### Request

**GET**

```
/api/metrics
```

### Response

```json
{
    "requests": 18,
    "positive": 10,
    "neutral": 5,
    "negative": 2,
    "unknown": 1
}
```

---

## GET /health

Simple health check endpoint.

### Request

**GET**

```
/api/health
```

### Response

```json
{
    "status": "ok"
}
```

---

## Swagger Documentation

Interactive API documentation is available at:

```
http://127.0.0.1:8000/api/documentation
```

---

# AI Integration

## Overview

The project uses the Google Gemini API to automatically analyze the sentiment of user messages submitted through the contact form.

Each incoming comment is sent to the Gemini model, which classifies the text as one of the following:

- positive
- neutral
- negative

If the AI response cannot be parsed or the request fails, the application returns the `unknown` sentiment.

---

## Workflow

1. The client sends a POST request to `/api/contact`.
2. The request is validated.
3. The comment is sent to the Gemini API.
4. Gemini returns the sentiment.
5. The application validates the AI response.
6. The sentiment is stored in the metrics.
7. The request is logged.
8. The API returns the detected sentiment to the client.

---

## Gemini Prompt

The application sends the following prompt to Gemini:

```text
Determine the sentiment of this message.
Respond with only one word:
positive, neutral, or negative.

Message: {user_comment}
```

Example:

```text
Determine the sentiment of this message.
Respond with only one word:
positive, neutral, or negative.

Message: Your service is amazing!
```

Expected response:

```text
positive
```

---

## Response Processing

The returned text is converted to lowercase and compared against the allowed values:

- positive
- neutral
- negative

Any unexpected response is converted to:

```text
unknown
```

This guarantees that the API always returns a valid sentiment value.

---

## Error Handling (Fallback)

If the Gemini API is unavailable or returns an invalid response, the application does not fail.

Instead it:

- catches the exception;
- writes the error to the Laravel log;
- returns the sentiment value `unknown`.

This allows the rest of the request to be processed normally.

---

# Data Storage

The project uses simple file-based storage for logs, metrics, and rate limiting. This approach keeps the application lightweight and eliminates the need for a database for these features.

## Logs

Every processed contact request is written to a log file.

Each log entry contains:

- timestamp;
- request data;
- processing status.

Logs are stored using Laravel's logging system.

This makes it easy to inspect requests and troubleshoot issues during development.

---

## Metrics

Application statistics are stored in a JSON file:

```
storage/app/private/metrics/stats.json
```

The following metrics are tracked:

- total number of requests;
- positive sentiments;
- neutral sentiments;
- negative sentiments;
- unknown sentiments.

Example:

```json
{
    "requests": 25,
    "positive": 12,
    "neutral": 8,
    "negative": 4,
    "unknown": 1
}
```

The `/api/metrics` endpoint returns the current statistics.

---

## Rate Limiting

A custom rate limiter is implemented without using Laravel's built-in RateLimiter.

Request timestamps are stored in:

```
storage/app/private/rate_limit/requests.json
```

The rate limiting policy is:

- maximum **5 requests**
- per **60 seconds**
- for each client IP address.

If the limit is exceeded, the API returns:

```http
429 Too Many Requests
```

Response:

```json
{
    "success": false,
    "message": "Too many requests."
}
```

Old timestamps are automatically removed whenever a new request is processed.

---

## Why File Storage?

JSON files were chosen because they:

- are simple to implement;
- require no additional database configuration;
- are sufficient for a small backend service and test assignment;
- make stored data easy to inspect during development.

# AI Assistance During Development

AI was used as a development assistant rather than a code generator.

## AI-generated Parts

AI assisted with:

- generating the initial structure of some service classes;
- suggesting Laravel-specific implementations;
- creating Swagger/OpenAPI documentation examples;
- improving README formatting and documentation;
- explaining framework features and debugging errors.

Example prompts used during development:

- Generate a Laravel service for sending emails.
- Explain how to integrate Google Gemini API in Laravel.
- Generate OpenAPI documentation for the REST API.
- Suggest improvements for project documentation.

---

## Manually Implemented

The following parts were implemented and refined manually:

- project architecture;
- service interaction;
- request validation;
- custom file-based rate limiting;
- metrics collection;
- AI integration with the Gemini API;
- mail sending workflow;
- logging logic;
- error handling;
- API testing and debugging.

---

## Manual Improvements

Several AI-generated suggestions required manual refinement, including:

- adapting generated code to the project architecture;
- fixing API integration issues;
- correcting response parsing from the Gemini API;
- resolving SMTP configuration and SSL certificate problems;
- fixing Swagger configuration;
- improving error handling and validation;
- simplifying and cleaning up generated code where necessary.

---

## Development Workflow

AI was primarily used for:

- accelerating development;
- exploring Laravel best practices;
- troubleshooting configuration issues;
- generating documentation.

All generated code was reviewed, tested, and adapted before being integrated into the project.
