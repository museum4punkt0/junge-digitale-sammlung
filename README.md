# Junge Digitale Sammlung 

## Inhaltsverzeichnis
1. Kurzbeschreibung
2. Finanzierung
3. Empfohlenes Server Setup 
4. Voraussetzungen und Skills
5. Installation der Platform (deploy) 
6. Entwicklung
7. Benutzung/Usage
8. Beteiligung/Contributing
9. Credits
10. Lizenz


## 1. Kurzbeschreibung

Das Deutsche Auswandererhaus ist ein kulturhistorisches Museum zum Thema Migration in Bremerhaven. Im Rahmen des deutschlandweiten Förderprojekts »museum4punkt0« sollen neue Formen der digitalen Kommunikation, Partizipation, Bildung und Vermittlung in Museen entwickelt, umgesetzt und evaluiert werden. Das Projektteam des DAHs erarbeitete unter dem Titel »Junge Digitale Sammlung« JDS ein museumspädagogisches Workshop-Angebot für Schulklassen. Der Workshop setzt sich inhaltlich mit den Themenkomplexen Identität und Diversität auseinander sowie mit der Bedeutung und Handhabung von Objekten im Museum. Teilnehmende Schüler:innen können dabei ihre Alltagsobjekte, die sowohl physisch als auch digital sein können, in 3D scannen oder fotografieren. Diese und weitere Daten zum Objekt werden in eine Datenbank eingegeben und später auf einer Plattform veröffentlicht. Durch die Workshops wird die JDS zu einer umfangreichen Sammlung anwachsen.


---

## 2. Finanzierung
[Bitte Input von BB]


---

## 3. Empfohlenes Server Setup 

Das projekt wurde ursprünglich auf folgendem Server installiert.
- Ubuntu 20.04 (Cloudron installiert)
- Apache 2.4.x
- PHP 8.1 mit Standard-Modulen (OPCache deaktivieren)
- vCPU 3 AMD (virtueller Server)
- 4GB RAM
- 80GB Festplatte

*Die Größe der Festplatte hängt hauptsächlich davon ab, wieviele Video-Dateien erwartet werden. 3D Modelle und Bilder verbrauchen vergleichsweise wenig Platz.*


---

## 4. Voraussetzungen und Skills

Die Installation (deployment) der Platform ist relativ leicht durchzuführen und kann grundsätzlich ohne große technische Kenntnisse erfolgen.

Bevor Sie beginnen, stellen Sie sicher, dass Sie folgende Anforderungen erfüllt haben:

### Für die Installation
* Ein Server steht Ihnen zur Verfügung.
* Sie haben eine [Kirby](https://getkirby.com) Version heruntergeladen. Das ursprüngliche Projekt wurde mit [Kirby 3.8.3](https://github.com/getkirby/kirby/releases/tag/3.8.3) erstellt. Aus Kompatibilitätsgründen wird empfohlen diese Version zu verwenden. Alle relevante Plugins sind in diesem Repository inkludiert. Kirby ist ein Open-Source, lizenzbasiertes CMS System.
* Sie haben eine Kopie dieses Repositorys.

### Für die Entwicklung
*Hinweise: Für eine bessere Verwaltung der npm Packages wurde das Projekt ursprünglich mit den Software [Prepros](https://prepros.io) (Mac, Windows, Linux) und [CodeKit](https://codekitapp.com/) (Mac only) entwickelt. Beide bieten eine grafische und bequeme Benutzeroberfläche, um Source-Code zu kompilieren, Packages zu verwalten und Browser zu auto-refreshen. Beide config-Dateien sind in diesem Repository inkludiert (Sie benötigen nur eine von beiden Software). Für die Server-Funktion wurde es mit MAMP gearbeitet. Alternativ können Sie bspw. ein Webpack Workflow verfolgen, s. bitte [kirby-webpack](https://github.com/brocessing/kirby-webpack).*

Es gelten die gleichen Voraussetzungen wie für die Installation (deployment). Dazu noch:
- npm ist auf Ihrem Rechner installiert
- Prepros oder CodeKit ist auf Ihrem Rechner installiert (optional können Sie das Webpack Workflow konfigurieren)
- Apache steht auf Ihrem Rechner zur Verfügung, um localhost starten zu können (z.B MAMP, XAMPP, Apache Installation, etc.)

*Prepros oder CodeKit?: CodeKit ist eine Mac-only Software, die vergleichsweise performanter ist aber lizenziert/gekauft werden muss. Prepros bietet eine kostenlose Version an und ist universell.*


---

## 5. Installation der Platform (deploy)

- Kirby-Core Ordner entzippen (s. bitte 4. Voraussetzungen und Skills) und in `kirby` umbenennen (ohne Suffixe).

- Kirby-Core in den Ordner `www` verschieben.

- Den kompletten Ordner `www` auf den Zielserver kopieren/hochladen. Wichtig dabei ist, dass der komplette Ordner inkl. versteckten Dateien, bspw. .htaccess, kopiert/hochgeladen wird.

- Anschliessend im Browser die Domain oder IP Adresse besuchen. Die Sammlung sollte erscheinen.

Das Admin-Backend von Kirby erreichen Sie unter `/panel`, z.B.:

`https://meinedomain.de/panel`

oder

`http://localhost/meinprojekt/www/panel`

Der erste Admin ist bereits angelegt. **Benutzername und Passwort unbedingt nach dem ersten Einloggen ändern!**

Benutzername:

`admin-default@jds-dah.de`

Passwort:

`adminadmin`


---

## 6. Entwicklung der Platform

Für die Weiterentwicklung und/oder Anpassung der verschiedenen Teile des Systems wird Personal mit guten Kenntnissen in PHP, JS und CSS und optimalerweise Kirby CMS empfohlen. Auch mittlere Kenntnissen in der Serveradministration sind empfehlenswert, da eine möglichst reibungslose Kommunikation mit den externen Social-Media Anbieter (embeds) je nach Server bestimmte Anpassungen benötigt.

Zum starten:

- Laden Sie eine Kopie dieses Repositorys herunter.

- Die Platform wie unter "Installation" installieren.

- Starten Sie den lokalen Server.

- In Prepros oder CodeKit das Projekt importieren. Da beide config-Dateien vorhanden sind können Sie die Packages in dem ausgewählten Programm direkt installieren. 

- Alternativ können Sie die Packages mit dem Befehl `npm install` wie üblich installieren.

- Pfade zum localhost Ordner des Projekts in den Projekt-Einstellungen in Prepros oder CodeKit für die Auto-Refresh Funktion aktualisieren. Diese sollte zu Ihrem `www` Ordner zeigen.

- Das Kompilieren von Javascript und SCSS ist bereits in den config-Dateien eingerichtet (Output für kompilierte Dateien ist `www/assets`).

- Der Code im Ordner `src` kann jetzt bearbeitet werden. 

*Weitere technische Details finden Sie [hier](docs/intro.md).*

WICHTIG: die Admin- und Workshop-Bereiche sollten aus technischen Gründen nicht über die Preview-URLs von Prepros oder CodeKit abgerufen werden, da Weiterleitungsfehler auftretten werden. Die live Auto-Refresh Funktion bleibt von daher nur für den Sammlung-Bereich relevant.

*Für weitere Prepros Einstellungen bitte die [Prepros Dokumentation](https://prepros.io/help/) lesen.*

*Für weitere CodeKit Einstellungen bitte die [CodeKit Dokumentation](https://codekitapp.com/help/) lesen.*


---

## 7. Benutzung/Usage

*Die detaillierte Dokumentation finden Sie [hier](docs/intro.md).*


---

## 8. Beteiligung/Contributing
Wenn Sie etwas beitragen möchten, forken Sie bitte das Repository und verwenden Sie einen Feature-Fork.  Pull Requests sind herzlich willkommen.


---

## 9. Credits
[----TODO----]

### Team 2av
- Jens Döring (Projektkoordination)
- Santiago Duque (Projektleitung and Entwicklung)

### Team Deutsches Auswandererhaus
- Birgit Burghart (Projektkoordination)
- Jasper Stephan-Beneker (Museumswissenschaft)
- Astrid Bormann (Museumspädagogik)
- Marcel Leukel (Museumstechnik)

### Team Studio Andreas Heller Architects & Designers
- Dirk Kühne (Projektkoordination und Design)


---

## 10. Lizenz
GNU GENERAL PUBLIC LICENSE <br>
Copyright © 2022, 2av GmbH <br>
Please also see the LICENSE file provided within this repository
