<p align="center">
    <img src="./assets/img/docparser_php.png">
</p>

<a href="https://codecov.io/gh/niccolovettorello1997/docparser-php" > 
 <img src="https://codecov.io/gh/niccolovettorello1997/docparser-php/graph/badge.svg?token=LNQFFW4GD3" style="height:auto; margin-right:10px; vertical-align:top;"/>
 <img src="https://github.com/niccolovettorello1997/docparser-php/actions/workflows/tests.yml/badge.svg" style="height:auto; vertical-align:top;">
</a>

# DocParser-PHP

Ein einfacher **HTML-Parser und Validator** in PHP 8, entwickelt als Lernprojekt, um **objektorientiertes PHP, Unit Testing und modulare Architektur** zu demonstrieren. Dieses Repository zeigt Programmierpraktiken, strukturiertes Parsen, Validierung und Ausgabe in HTML- und JSON-Formaten.

---

## **Inhaltsverzeichnis**

- [Funktionen](#funktionen)
- [Technologie-Stack](#technologie-stack)
- [Installation](#installation)
- [Verwendung](#verwendung)
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
- Ausgabe der Ergebnisse in:
  - Menschlich lesbares HTML
  - Strukturiertes JSON für weitere Verarbeitung
- Modulare Architektur zur Unterstützung zusätzlicher Dokumenttypen
- Vollständig mit PHPUnit getestet
- Dockerisiert für einfache Einrichtung
- Konfigurierbare Validatoren und Parser über YAML

---

## **Technologie-Stack**

- **PHP 8.3** (OOP, striktes Typing)  
- **Composer** für Abhängigkeitsmanagement  
- **Docker & Docker Compose** für die Umgebung  
- **PHPUnit** für Unit-Tests  
- HTML5-Standards

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
2. Datentyp auswählen (derzeit nur `HTML` unterstützt).
3. Auf **Parse** klicken.
4. Ergebnisse werden angezeigt:

   * Validierungsfehler und Warnungen
   * Geparste HTML-Ansicht
   * Optionale JSON-Ansicht

---

## **Projektstruktur**

```
src/          # Core PHP Quellcode (Validatoren, Parser, Fabriken)
tests/        # PHPUnit Tests für Validatoren, Parser und Views
views/        # HTML-Views für Validierungsergebnisse
public/       # Einstiegspunkt für die Web-Oberfläche
docker/       # Docker- und Compose-Konfiguration
config/       # Einstellungen für Parser und Validator
fixtures/     # Projekt-Fixtures
assets/       # Projekt-Assets
```

---

## **Validierungs- und Parsing-Logik**

1. **Validierung**

   * Fail-Fast: Stoppt beim ersten schwerwiegenden Fehler
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