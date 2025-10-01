<p align="center">
    <img src="./assets/img/docparser_php.png">
</p>

<a href="https://codecov.io/gh/niccolovettorello1997/docparser-php" > 
 <img src="https://codecov.io/gh/niccolovettorello1997/docparser-php/graph/badge.svg?token=LNQFFW4GD3" style="height:auto; margin-right:10px; vertical-align:top;"/>
 <img src="https://github.com/niccolovettorello1997/docparser-php/actions/workflows/tests.yml/badge.svg" style="height:auto; vertical-align:top; margin-right:10px;">
 <img src="https://img.shields.io/badge/PHP-8.3-777BB4?logo=php" style="height:auto; vertical-align:top; margin-right:10px;">
 <img src="https://img.shields.io/github/license/niccolovettorello1997/docparser-php" style="height:auto; vertical-align:top; margin-right:10px;">
</a>

---

# DocParser-PHP

A simple **HTML parser and validator** written in PHP 8, designed as a learning project to demonstrate **object-oriented PHP, unit testing, and modular architecture**. This project represents a practical exercise written after studying the book *PHP 8 und MySQL: das umfassende Handbuch* by Wenz and Hauser. It showcases coding practices, structured parsing, validation, and output rendering in HTML and JSON formats.

---

## **Table of Contents**

- [Features](#features)
- [Architecture Overview](#architecture-overview)
- [Technology Stack](#technology-stack)
- [Skills Demonstrated](#skills-demonstrated)
- [Installation](#installation)
- [Usage](#usage)
- [Example Input/Output](#example-inputoutput)
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
- HTML support is not complete since it was not the main focus of this project
- Output results in:
  - Human-readable HTML
  - Structured JSON for further processing
- Modular architecture to support additional document types (stub code for Markdown as example)
- Fully tested with PHPUnit
- Dockerized for easy setup
- Configurable validators and parsers via YAML

---

## **Architecture Overview**

```mermaid
flowchart TD
    %% Entry point / UI
    A["index.php UI"] --> B["ParserController"]

    %% Controller orchestration
    B --> C["ParserFactory"]

    %% Parsers and Validators
    C --> D["HTMLValidator & HTMLParser"]
    C --> E["MarkdownValidator & MarkdownParser"]

    %% Model: validation results
    D --> F["ElementValidationResult (Model)"]
    E --> F

    %% Views: render output
    F --> G["HtmlParserView (HTML Output)"]
    F --> H["JsonParserView (JSON Output)"]

    %% Back to UI
    G --> A
    H --> A

    %% Optional: File download
    H --> I["Download Endpoint"]
```

---

## **Technology Stack**

- **PHP 8.3** (OOP, strict typing)
- **Composer** for dependency management
- **Docker & Docker Compose** for environment setup
- **PHPUnit** for unit testing
- HTML5 standards compliance

---

## **Skills Demonstrated**
- Modular MVC-like architecture in PHP 8.3
- OOP and design patterns (Factory, Validator)
- Unit testing and regression testing with PHPUnit
- Composer for dependency management
- CI/CD with GitHub Actions
- Dockerized environment for portability
- Extensible design for new parsers/validators

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
2. Select the data type (currently only `HTML` and a stub for `Markdown` are supported).
3. Click **Parse**.
4. Results are displayed:

   * Validation errors and warnings
   * Parsed HTML view
   * Optional JSON downloadable file

---

## **Example Input/Output**

**Input HTML**

```html
<!DOCTYPE html>
<html lang="de">
  <head><title>Test</title></head>
  <body>
    <h1>Hello</h1>
    <p>World</p>
  </body>
</html>
```

**Output JSON for HTML**

```json
{
    "Name": "root",
    "Children": [
        {
            "Name": "doctype",
            "Children": [
                {
                    "Name": "html",
                    "Attributes": {
                        "lang": "de"
                    },
                    "Children": [
                        {
                            "Name": "head",
                            "Children": [
                                {
                                    "Name": "title",
                                    "Content": "Test"
                                }
                            ]
                        },
                        {
                            "Name": "body",
                            "Children": [
                                {
                                    "Name": "paragraphs",
                                    "Children": [
                                        {
                                            "Name": "p",
                                            "Content": "World"
                                        }
                                    ]
                                },
                                {
                                    "Name": "headings",
                                    "Children": [
                                        {
                                            "Name": "h1",
                                            "Content": "Hello"
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                }
            ]
        }
    ]
}
```

**Input Markdown (stub)**
```md
# Example title
```

**Output JSON for Markdown**
```json
{
    "Name": "root",
    "Children": [
        {
            "Name": "markdown",
            "Content": "# Example title"
        }
    ]
}
```

---

## **Project Structure**

```
src/          # Core PHP source code (validators, parsers, factories)
tests/        # PHPUnit tests for validators, parsers, and views
views/        # HTML views for displaying validation results
public/       # Entry point for the web interface
docker/       # Docker configuration
config/       # Settings for parser and validator
fixtures/     # Project fixtures
assets/       # Project assets
```

---

## **Validation and Parsing Logic**

1. **Validation**

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

## **Tests & Quality Assurance**

This project includes extensive PHPUnit test coverage, ensuring reliability and maintainability:

* Unit tests for validators (missing tags, duplicates, invalid or empty content).

* Integration tests (validator + parser + views).

* YAML configuration tests (dynamic validator and parser configuration).

* Edge case HTML tests (non-standard structures, whitespace, comments).

* Performance tests with large inputs (10k+ tags).

Errors and warnings are clearly separated: errors block parsing, while warnings allow it to continue.

To run tests:

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