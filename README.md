# HTML-Parser mit Validator / HTML Parser with Validator

## Deutsch

Dieses Projekt ist ein praktisches Beispiel für einen Parser mit Validator für HTML-Dokumente.
Es dient dazu, die Konzepte aus dem Buch *"PHP 8 und MySQL: das umfassende Handbuch"* von Wenz und Hauser zu verstehen.

*Hinweis*: Dieser Parser ist **nicht vollständig oder perfekt**. Er verarbeitet nur einige Tags wie `<html>`, `<head>`, `<title>`, `<p>`, usw., und auch diese nicht vollständig.

---

### Projektziele

- Erlernen der Nutzung folgender Technologien und Methoden:
  - Docker
  - PHP und objektorientierte Programmierung (OOP)
  - Datenbanken (DB)
  - usw.
- Schrittweise Nachvollziehung der Entwicklung eines Praxisprojekts anhand eines Fachbuches.
- Praktische Übung: HTML-Parser und Validator in PHP erstellen.

---

### Projektstruktur

- Das Projekt beginnt im Branch `main`, der eine **basale, leere Struktur** enthält.
- Jeder weitere Branch entspricht einer Serie von **Buchkapiteln**.
- Jeder Branch wird zu einem **Pull Request (PR)**, der in `main` gemerged wird, inklusive der Änderungen, die den entsprechenden Kapiteln entsprechen.

---

### Unterstützte Tags

Der Parser verarbeitet aktuell nur einige grundlegende HTML-Tags:

- `<html>`
- `<head>`
- `<title>`
- `<p>`

> Die Liste ist nicht vollständig – der Parser dient hauptsächlich als **Lernbeispiel**.

---

### Nutzung des Projekts

1. Repository klonen:
   ```bash
   git clone git@github.com:niccolovettorello1997/docparser-php.git
   ```

2. Zum gewünschten Branch wechseln, um spezifische Kapitel zu sehen:
   ```bash
   git checkout <branch_name>
   ```

3. Docker-Umgebung starten:
   ```bash
   docker compose up -d
   ```

4. Projekt im Browser ausführen, um den Parser zu testen.
   ```bash
   http://localhost:8080
   ```

---

### Beiträge

Dieses Projekt ist hauptsächlich **zu Lernzwecken**. Externe Beiträge sind willkommen, sollten aber den pädagogischen Zweck bewahren und das Projekt nicht zu einem vollständigen Parser machen.

---

### Referenzen

- *PHP 8 und MySQL: das umfassende Handbuch* von Wenz und Hauser

---

## English

This project is a practical example of a parser with a validator for HTML documents.  
It aims to help understand the concepts explained in the book *"PHP 8 und MySQL: das umfassende Handbuch"* by Wenz and Hauser.

*Note*: This parser is **not complete or perfect**. It only handles some tags like `<html>`, `<head>`, `<title>`, `<p>`, etc., and not fully.

---

### Project Goals

- Learn how to use the following technologies and methods:
  - Docker
  - PHP and Object-Oriented Programming (OOP)
  - Databases (DB)
  - etc.
- Follow a practical project step by step based on a reference book.
- Hands-on exercise: create an HTML parser and validator in PHP.

---

### Project Structure

- The project starts in the `main` branch, which contains a **basic, empty structure**.
- Each additional branch corresponds to a series of **book chapters**.
- Each branch is then turned into a **Pull Request (PR)** merged into `main` with the changes reflecting the corresponding chapters.

---

### Supported Tags

Currently, the parser only handles a few basic HTML tags:

- `<html>`
- `<head>`
- `<title>`
- `<p>`

> The list is not exhaustive – the parser is primarily an **educational example**.

---

### How to Use

1. Clone the repository:
   ```bash
   git clone git@github.com:niccolovettorello1997/docparser-php.git
   ```

2. Checkout the desired branch to explore specific chapters:
   ```bash
   git checkout <branch_name>
   ```

3. Start the Docker environment:
   ```bash
   docker compose up -d
   ```

4. Open the project in your browser to test the parser:
   ```bash
   http://localhost:8080
   ```

---

### Contributions

This project is primarily **educational**. External contributions are welcome, but they should maintain the educational purpose and not turn the project into a full parser.

---

### References

- *PHP 8 und MySQL: das umfassende Handbuch* by Wenz and Hauser