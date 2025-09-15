# DocParser-PHP

A simple **HTML parser and validator** written in PHP 8, designed as a learning project to demonstrate **object-oriented PHP, unit testing, and modular architecture**. This repository showcases coding practices, structured parsing, validation, and output rendering in HTML and JSON formats.

---

## **Table of Contents**

- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Validation and Parsing Logic](#validation-and-parsing-logic)
- [Contributing](#contributing)
- [License](#license)

---

## **Features**

- Validate HTML structure with strict rules:
  - Unique and properly nested `<html>`, `<head>`, `<body>`, `<title>` and headings
  - Checks for empty elements and invalid characters
  - Warning system for optional attributes
- Parse HTML into a structured DOM-like tree
- Output results in:
  - Human-readable HTML
  - Structured JSON for further processing
- Modular architecture to support additional document types
- Fully tested with PHPUnit
- Dockerized for easy setup
- Configurable validators and parsers via YAML

---

## **Technology Stack**

- **PHP 8.3** (OOP, strict typing)
- **Composer** for dependency management
- **Docker & Docker Compose** for environment setup
- **PHPUnit** for unit testing
- HTML5 standards compliance

---

## **Installation**

1. Clone this repository:

```bash
git clone https://github.com/niccolovettorello1997/docparser-php.git
cd docparser-php
```

2. Start the Docker environment:

```bash
docker compose up -d
```

3. Enter the web container:

```bash
docker exec -it docparser-php-web-1 bash
```

4. Install PHP dependencies:

```bash
composer install
```

5. Access the app in your browser:

```
http://localhost:8080
```

---

## **Usage**

1. Insert HTML content directly into the textarea **or** upload an HTML file.
2. Select the data type (currently only `HTML` is supported).
3. Click **Parse**.
4. Results are displayed:

   * Validation errors and warnings
   * Parsed HTML view
   * Optional JSON view

---

## **Project Structure**

```
src/          # Core PHP source code (validators, parsers, factories)
tests/        # PHPUnit tests for validators, parsers, and views
views/        # HTML views for displaying validation results
public/       # Entry point for the web interface
docker/       # Docker and Compose configuration
config/       # Settings for parser and validator
fixtures/     # Project fixtures
```

---

## **Validation and Parsing Logic**

1. **Validation**

   * Fail-fast: stops at the first fatal error
   * Checks include:

     * Unique `doctype`, `html`, `head`, `body`, `title`
     * Balanced headings (`<h1>`-`<h6>`)
     * No nested `<p>` or invalid content
     * Warnings for optional attributes (e.g., `lang` on `<html>`)

2. **Parsing**

   * Converts HTML into a DOM-like tree structure
   * Each element type handled by dedicated parser classes
   * Recursive parsing from `doctype` root
   * Outputs can be rendered in HTML or JSON

---

## **Testing**

* Unit tests are written with **PHPUnit**
* Coverage includes validators, parsers, and views
* To run tests:

```bash
docker exec -it docparser-php-web-1 bash
vendor/bin/phpunit
```

---

## **Contributing**

Contributions are welcome! You can:

* Add support for more HTML tags
* Improve validation rules
* Add support for other document types (Markdown, XML)
* Enhance UI/JSON output

Please open a pull request or issue for discussion.

---

## **License**

MIT License

---