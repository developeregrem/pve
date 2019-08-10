
# Pensionsverwaltung (PVE)

Das **P**ensions**ve**rwaltungtool ist ein PHP-Projekt, das auf dem PHP-Framework Symfony in Version 4 basiert.
Kleine Pensionen oder Unterkünfte verwalten ihre Zimmer oder Appartements in der Regel auf die alte Art und Weise mit einem Stift und einem Blatt Papier oder mit einem Tabellenverwaltungsprogramm. 

Das Ziel dieses Open-Source-Tools ist es, kleineren Unterkünften zu helfen, den handgeschriebenen Ansatz zur Raumverwaltung zu ersetzen und die Produktivität durch das Zusammenführen aller Informationen zu verbessern, was schließlich in einer Zeitersparnis bei der Verwaltung des Gästehauses oder Pension resultiert.

## Funktionen

 - Reservierungsübersicht (einfache Möglichkeit, Reservierungen hinzuzufügen und zu verwalten)
 - Verwaltung Ihrer Gästedaten (inkl. DSGVO-Exportfunktion)
 - umfangreiche Einstellungen zur Verwaltung der
	 - Zimmer, Unterkünfte, Preise, Reservierungsherkunft, Vorlagen, etc.
 - Rechnungen erstellen (PDF)
 - Gästekommunikation (Mails aus dem Tool heraus schreiben), Rechnungen, Reservierungsbestätigungen oder andere relevante Informationen an den Gast senden.
 - Statistiken
 - Meldebuch
 - Kassenbuch zur Verwaltung Ihrer Einnahmen und Ausgaben

## Anforderungen

Um das Tool nutzen zu können, benötigen man einen kleinen Webserver, der die Anforderungen von Symfony 4 [requirements](https://symfony.com/doc/current/reference/requirements.html) erfüllt:

 - PHP 7.1.3 oder höher
 - php-intl extension
 - einen Webserver z.B. nginx oder apache
 - einen Datenbankserver (empfohlen wird mysql oder mariadb)

## Quick Start

Erstellen einer Datenbank für das Tool:

    CREATE DATABASE pve CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

 Kopiere die Datei `.env.dist` und benenne die kopierte Datei in `.env` um.

Bearbeite die Datei `.env` und passe den Wert für `DATABASE_URL` an, um den eigenen Datenbankeinstellungen zu entsprechen.

Erzeuge einen zufällig und sicheren Wert für `APP_SECRET` (man kann einen Wert [hier](http://nux.net/secret) erzeugen lassen).

Wenn noch nicht vorhanden, lade den PHP dependency manager [composer](https://getcomposer.org/download/) herunter, um die Pensionsverwaltungstool Abhängigkeiten installieren zu können. Führe anschließend den folgenden Befehl im root-Ordner des Projekts aus:

    composer update

Führe den folgenden Befehl aus, um die Datenbank und die Anwendung zu initialisieren:

    php bin/console doctrine:migration:migrate
    php bin/console app:first-run

Anschließend kann mit einem Webbrowser zu dem Installationsordner gewechselt werden  z.B.
http://localhost/pve/public/index.php
um sich mit den zuvor angelegten Logindaten anzumelden.

## i18n

Das Tool ist grundlegend mehrsprachige aufgebaut. Aktuell liegt jedoch nur eine deutsche Übersetzung vor. Einige Features wie das Kassenbuch sind für den Einsatz in Deutschland optimiert.

## Author

Alexander Elchlepp

Das Projekt wird durch mich seit 2014 in der Freizeit entwickelt. Wenn Fragen aufkommen, kann ein Ticket angelegt oder mich direkt per mail kontaktiert werden (alex.pensionsverwaltung (at) gmail.com)