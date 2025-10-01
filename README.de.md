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

Ein einfacher **HTML-Parser und -Validator**, geschrieben in PHP 8, entworfen als Lernprojekt zur Demonstration von **objektorientiertem PHP, Unit-Testing und modularer Architektur**. Dieses Projekt stellt eine praktische Übung dar, die nach dem Studium des Buches *PHP 8 und MySQL: das umfassende Handbuch* von Wenz und Hauser geschrieben wurde. Es zeigt bewährte Programmierpraktiken, strukturiertes Parsen, Validierung und Ausgabe-Rendering in HTML- und JSON-Formaten.

---

## **Inhaltsverzeichnis**

- [Funktionen](#funktionen)
- [Architekturübersicht](#architekturübersicht)
- [Technologie-Stack](#technologie-stack)
- [Gezeigte Fähigkeiten](#gezeigte-fähigkeiten)
- [Installation](#installation)
- [Verwendung](#verwendung)
- [Eingabe/Ausgabe Beispiel](#ea-beispiel)
- [Projektstruktur](#projektstruktur)
- [Validierungs- und Parsing-Logik](#validierungs--und-parsing-logik)
- [Beitragen](#beitragen)
- [Lizenz](#lizenz)

---

## **Funktionen**

- Validierung der HTML-Struktur mit strengen Regeln:
  - Einzigartige und korrekt verschachtelte `<html>`, `<head>`, `<body>`, `<title>` und Überschriften
  - Prüfung auf leere Elemente und ungültige Zeichen
  - Warnsystem für optionale Attribute
- Parsen von HTML in eine strukturierte, DOM-ähnliche Baumstruktur
- Die HTML-Unterstützung ist nicht vollständig, da sie nicht der Hauptfokus dieses Projekts war
- Ausgabe der Ergebnisse in:
  - Menschlich lesbares HTML
  - Strukturiertes JSON für weitere Verarbeitung
- Modulare Architektur zur Unterstützung zusätzlicher Dokumenttypen (Stub-Code für Markdown als Beispiel)
- Vollständig mit PHPUnit getestet
- Dockerisiert für einfache Einrichtung
- Konfigurierbare Validatoren und Parser über YAML

---

## **Architekturübersicht**

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

## **Technologie-Stack**

- **PHP 8.3** (OOP, striktes Typing)  
- **Composer** für Abhängigkeitsmanagement  
- **Docker & Docker Compose** für die Umgebung  
- **PHPUnit** für Unit-Tests  
- HTML5-Standards

---

## **Gezeigte Fähigkeiten**
- Modulare, MVC-ähnliche Architektur in PHP 8.3
- OOP und Entwurfsmuster (Factory, Validator)
- Unit-Tests und Regressionstests mit PHPUnit
- Composer für Abhängigkeitsverwaltung
- CI/CD mit GitHub Actions
- Dockerisierte Umgebung für Portabilität
- Erweiterbares Design für neue Parser/Validatoren

---

## **Installation**

1. Repository klonen:

```bash
git clone https://github.com/niccolovettorello1997/docparser-php.git
cd docparser-php
```

2. Docker-Umgebung starten:

```bash
docker compose up -d
```

3. In den Web-Container wechseln:

```bash
docker exec -it docparser-php-web-1 bash
```

4. PHP-Abhängigkeiten installieren:

```bash
composer install
```

5. Zugriff auf die App im Browser:

```
http://localhost:8080
```

---

## **Verwendung**

1. HTML-Inhalt direkt in das Textfeld eingeben **oder** eine HTML-Datei hochladen.
2. Datentyp auswählen (derzeit nur `HTML` und Stub-Code für `Markdown` unterstützt).
3. Auf **Parse** klicken.
4. Ergebnisse werden angezeigt:

   * Validierungsfehler und Warnungen
   * Geparste HTML-Ansicht
   * Optionale herunterladbare JSON-Datei

---

## E/A Beispiel

**HTML Eingabe**
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

**JSON Ausgabe für HTML**
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

**Markdown Eingabe (stub)**
```md
# Example title
```

**JSON Ausgabe für Markdown**
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

## **Projektstruktur**

```
src/          # Core PHP Quellcode (Validatoren, Parser, Fabriken)
tests/        # PHPUnit Tests für Validatoren, Parser und Views
views/        # HTML-Views für Validierungsergebnisse
public/       # Einstiegspunkt für die Web-Oberfläche
docker/       # Docker-Konfiguration
config/       # Einstellungen für Parser und Validator
fixtures/     # Projekt-Fixtures
assets/       # Projekt-Assets
```

---

## **Validierungs- und Parsing-Logik**

1. **Validierung**

   * Überprüfungen beinhalten:

     * Einzigartiger `doctype`, `html`, `head`, `body`, `title`
     * Ausgeglichene Überschriften (`<h1>`-`<h6>`)
     * Keine verschachtelten `<p>`-Tags oder ungültiger Inhalt
     * Warnungen für optionale Attribute (z. B. `lang` auf `<html>`)

2. **Parsing**

   * Konvertiert HTML in eine DOM-ähnliche Baumstruktur
   * Jeder Elementtyp wird von dedizierten Parser-Klassen behandelt
   * Rekursives Parsen vom `doctype`-Root
   * Ausgaben können in HTML oder JSON gerendert werden

---

## **Tests & Qualitätssicherung**

Dieses Projekt verfügt über eine umfangreiche PHPUnit-Testabdeckung, um Zuverlässigkeit und Wartbarkeit sicherzustellen:

* Unit-Tests für Validatoren (fehlende Tags, Duplikate, ungültiger oder leerer Inhalt).

* Integrationstests (Validator + Parser + Views).

* YAML-Konfigurationstests (dynamische Validator- und Parserkonfiguration).

* Edge-Case-HTML-Tests (nicht standardisierte Strukturen, Leerzeichen, Kommentare).

* Performance-Tests mit großen Eingaben (10.000+ Tags).

Fehler und Warnungen werden klar getrennt: Fehler blockieren das Parsing, während Warnungen es fortsetzen lassen.

Tests ausführen:

```bash
docker exec -it docparser-php-web-1 bash
vendor/bin/phpunit
```

---

## **Beitragen**

Beiträge sind willkommen! Sie können:

* Unterstützung für weitere HTML-Tags hinzufügen
* Validierungsregeln verbessern
* Unterstützung für andere Dokumenttypen (Markdown, XML) hinzufügen
* UI/JSON-Ausgabe erweitern

Bitte öffnen Sie einen Pull-Request oder Issue zur Diskussion.

---

## **Lizenz**

MIT-License

---