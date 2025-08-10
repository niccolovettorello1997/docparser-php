# docparser-php

Parser für HTML- und PDF-Dokumente mit *PHP 8.3*, *Docker* und *MySQL*

## HTML/PDF Parser Projekt mit PHP 8.3

Dieses Projekt ist ein schrittweise aufgebautes Lern- und Demonstrationsprojekt in **PHP 8.3**.  
Ziel ist es, HTML- und PDF-Dokumente zu analysieren, zu verarbeiten und die Ergebnisse in einer Datenbank zu speichern.  
Die Entwicklung orientiert sich an den Kapiteln des Buches *"PHP 8 und MySQL – Das umfassende Handbuch"* von Christian Wenz und Tobias Hauser.

## Ziele des Projekts
- Aufbau eines vollständigen Parser-Systems für HTML- und PDF-Dateien
- Speicherung der extrahierten Daten in einer Datenbank
- Benutzer-Authentifizierung und Admin-Oberfläche
- Saubere, erweiterbare Architektur mit OOP und Design-Patterns
- Bereitstellung einer lauffähigen Entwicklungsumgebung mit Docker

## Installation mit Docker

1. Repository klonen:
   ```bash
   git clone <REPO-URL>
   cd <REPO-NAME>
   ```
2. Container starten:
   ```bash
   docker compose up --build
   ```
3. Anwendung im Browser öffnen:
   ```
   http://localhost:8080
   ```

## Aktueller Stand

- Docker setup mit PHP 8.3
- Minimaler PHP-Entwicklungserver

## Geplante Entwicklungsschritte

- [ ] **HTML-Parser**: Grundlegende Extraktion von Text und Tags
- [ ] **HTML-Parser erweitert**: Attribute, Metadaten, strukturierte Ausgabe
- [ ] **PDF-Parser**: Extraktion von Textinhalten mit externer Bibliothek
- [ ] **Datenbank-Anbindung**: Speicherung der Parser-Ergebnisse in einer Datenbank
- [ ] **Benutzer-Authentifizierung**: Login, Registrierung, Rollenverwaltung
- [ ] **Admin-Oberfläche**: Verwaltung und Suche in den importierten Dokumenten
- [ ] **Erweiterungen**: SEO-Analyse, Metadaten-Extraktion, Exportfunktionen

## Lizenz

Dieses Projekt steht unter der MIT-Lizenz.