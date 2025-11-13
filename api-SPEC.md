# DocParser-PHP API Specification

**Version:** 0.0.1

**Base URL (local):** `http://localhost:8080/api/v1`

**Base URL (production):** `https://docparser-php.onrender.com/api/v1`

DocParser-PHP is a lightweight **document parsing and validation microservice** written in **PHP 8.3**.
It provides a RESTful API for programmatic validation and parsing of HTML documents.

---

## **1. General Information**

### Content Types

| Type | Description |
|------|--------------|
| `application/json` | For JSON payloads (e.g. `/parse/json`) |
| `multipart/form-data` | For file uploads (e.g. `/parse/file`) |

---

## **2. Endpoints**

### **2.1 Healthcheck**

#### `GET /health`

Health monitoring endpoint.

| Property            | Description |
|-----------          |-------------|
| **Response Format** | JSON        |

**Example Request**

```bash
curl http://localhost:8080/api/v1/health
```

**Example Response**

```json
{
  "status": "ok",
  "version": "0.0.1"
}
```

**Status Codes**

| Code | Meaning         |
| ---- | --------------- |
| 200  | Service healthy |
| 500  | Internal error  |

---

### **2.2 Documentation**

#### `GET /api/v1/openapi.yaml`

Triggers the download of the raw YAML openapi file.

| Property            | Description |
|-----------          |-------------|
| **Response Format** | YAML        |

**Example Request**

```bash
curl http://localhost:8080/api/v1/openapi.yaml
```

---

### **2.3 Parse Uploaded File**

#### `POST /parse/file`

Parses and validates the content of an uploaded file.

| Property          | Description                                      |
| ----------------- | ------------------------------------------------ |
| **Auth Required** | Optional                                         |
| **Content-Type**  | `multipart/form-data`                            |
| **Fields**        | `document` (file), `type` (`html`)               |

**Example Request**

```bash
curl -X POST http://localhost:8080/api/v1/parse/file \
     -F "document=@/path/to/file.html" \
     -F "type=html"
```

**Example Response**

```json
{
  "status": "ok",
  "requestId": "req-f77ecced103d7f6d",
  "validation": {
    "Valid": "yes",
    "Errors": [],
    "Warnings": []
  },
  "parsed": {
    "Name": "root",
    "Children": [
      {
        "Name": "doctype",
        "Children": [
          {
            "Name": "html",
            "Attributes": {
              "lang": "en"
            },
            "Children": [
              {
                "Name": "head",
                "Children": [
                  {
                    "Name": "title",
                    "Content": "Example Document"
                  }
                ]
              },
              {
                "Name": "body",
                "Children": [
                  {
                    "Name": "p",
                    "Content": "This is a valid HTML5 document."
                  }
                ]
              }
            ]
          }
        ]
      }
    ]
  },
  "meta": {
    "durationMs": 18,
    "sizeBytes": 901,
    "version": "0.0.1"
  }
}
```

**Status Codes**

| Code | Meaning                         |
| ---- | ------------------------------- |
| 200  | Document parsed successfully    |
| 400  | Missing or invalid input fields |
| 409  | Upload error                    |
| 500  | Internal server error           |

---

### **2.3 Parse JSON Payload**

#### `POST /parse/json`

Parses and validates the content of a document sent as a JSON payload.

| Property          | Description                                               |
| ----------------- | --------------------------------------------------------- |
| **Content-Type**  | `application/json`                                        |
| **Fields**        | `type` (`html` or `markdown`), `content` (encoded string) |

**Example Request**

```bash
curl -X POST http://localhost:8080/api/v1/parse/json \
     -H "Content-Type: application/json" \
     -d '{"type":"html","content":"%3C!DOCTYPE%20html%3E%3Chtml%3E%3Chead%3E%3Ctitle%3EDoc%3C%2Ftitle%3E%3C%2Fhead%3E%3Cbody%3E%3C%2Fbody%3E%3C%2Fhtml%3E"}'
```

**Example Response**

```json
{
  "status": "ok",
  "requestId": "req-5a23fd1d927cf8f2",
  "validation": {
    "Valid": "yes",
    "Errors": [],
    "Warnings": []
  },
  "parsed": { ... },
  "meta": {
    "durationMs": 12,
    "sizeBytes": 612,
    "version": "0.0.1"
  }
}
```

**Status Codes**

| Code | Meaning                      |
| ---- | ---------------------------- |
| 200  | Document parsed successfully |
| 400  | Invalid or missing body      |
| 500  | Internal server error        |

---

## **3. Error Model**

All non-2xx responses follow this structure:

```json
{
  "status": "error",
  "code": "ERR_UNSUPPORTED_TYPE",
  "message": "Input type not supported",
  "details": ""
}
```

| Field     | Type   | Description                          |
| --------- | ------ | ------------------------------------ |
| `status`  | string | Always `"error"`                     |
| `code`    | string | Unique string representing the error |
| `message` | string | Short description of the error       |
| `details` | string | Optional error details               |

### Common error codes

*Error codes* include:

- `ERR_NOT_FOUND`: the requested resource was not found
- `ERR_NO_AUTH_HEADER`: authentication header not present (for the moment the authentication process is just a stub)
- `ERR_INVALID_TOKEN`: authentication token is invalid
- `ERR_MISSING_REQUIRED_FIELD`: required field is missing
- `ERR_UNSUPPORTED_TYPE`: requested to parse an unsupported type of language
- `ERR_NO_FILE_UPLOADED`: no file was uploaded
- `ERR_UPLOAD`: an error happened during upload
- `ERR_INTERNAL_SERVER_ERROR`: an internal error has happened

---

## **4. Versioning**

All endpoints are namespaced under `/api/v1`.
Future versions will follow [semantic versioning](https://semver.org/) and may introduce breaking changes under `/api/v2`.

---

## **5. Changelog**

| Version   | Changes                                                                              |
| --------- | ------------------------------------------------------------------------------------ |
| **0.0.1** | Initial public release. Added `/health`, `/parse/file`, and `/parse/json` endpoints. |

---

## **6. Future Endpoints (Planned)**

| Endpoint       | Description                                              | Status    |
| -------------- | -------------------------------------------------------- | --------- |
| `GET /metrics` | Prometheus-style metrics for uptime and request counters | Planned   |
| `GET /docs`    | Auto-generated API documentation page (OpenAPI spec)     | Planned   |

---


© 2025 Niccolò Vettorello — MIT License
