# Programmablaufplan für das Zoo-Verwaltungssystem

## 1. Start
- Initialisierung des Systems.
- Verbindung zur MySQL-Datenbank herstellen.
- Benutzeroberfläche laden (Homepage).

## 2. Benutzerauthentifizierung
- Auswahl des Benutzerbereichs:
  - Verwaltung
  - Pfleger
  - Besucher
- Eingabe der Zugangsdaten (falls erforderlich).
- Berechtigungen prüfen.

## 3. Navigation zum gewählten Bereich
### Verwaltung:
1. Eingabe-/Bearbeitungsmöglichkeiten anzeigen:
   - Tiere
   - Pfleger
   - Reviere
   - Futter/Lieferanten
2. Auswahl eines Bereichs:
   - Daten hinzufügen/bearbeiten/löschen.
   - Fehleingaben validieren.
   - Änderungen speichern und protokollieren.

### Pfleger:
1. Übersicht der zu betreuenden Tiere anzeigen:
   - Futterpläne (Art, Menge, Zeiten).
   - Standort der Tiere.
2. Futterplan aktualisieren (falls erforderlich):
   - Änderungen speichern.
   - Protokoll aktualisieren.

### Besucher:
1. Informationen zu Tieren anzeigen:
   - Name, Art, Fütterungszeiten und Futtermenge.
2. Keine sensiblen Daten anzeigen (z. B. Pfleger- oder Lieferanteninformationen).

## 4. Dateneingabe/-bearbeitung (Verwaltung und Pfleger)
- Formular anzeigen.
- Daten validieren (z. B. Pflichtfelder, Formate).
- Validierte Daten in der Datenbank speichern.
- Protokollierung durchführen.

## 5. Datenabfrage (alle Benutzergruppen)
- Benutzerabhängige Datenansicht:
  - Verwaltung: Gesamtdaten mit Bearbeitungsmöglichkeiten.
  - Pfleger: Tiere und Futterpläne ihres Reviers.
  - Besucher: Allgemeine Informationen zu Tieren.
- Daten aus der MySQL-Datenbank abrufen und anzeigen.

## 6. Fehlerbehandlung
- Fehleingaben abweisen und Fehlermeldung anzeigen.
- Systemstabilität gewährleisten (kein Absturz bei Fehlbedienung).

## 7. Systemverwaltung
- Zugriff auf alle Daten.
- Einsicht in Protokolle.
- Fehler und Manipulationen überprüfen.

## 8. Beenden des Systems
- Änderungen speichern.
- Verbindung zur Datenbank trennen.
- System herunterfahren.




naturzoo_rheine/
│
├─ index.html
├─ gehege.php
├─ enclosure_details.php
├─ login.html
├─ register.html
├─ dashboard.php
├─ logout.php
├─ login.php
├─ register.php
├─ db_config.php
│
├─ style.css
├─ script.js
│
├─ images/
│  ├─ generated_zoo_image.jpg
│  ├─ zoo_plan.jpg
│  ├─ enclosure_lions.jpg
│  ├─ enclosure_elephants.jpg
│  ├─ enclosure_birds.jpg
│  └─ ... (weitere Bilder)
│
└─ (Optional) uploads/
   └─ (verwendet, wenn du Dateien hochladen möchtest)
