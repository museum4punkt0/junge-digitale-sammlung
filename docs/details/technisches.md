[« zurück zu README](../../README.md)

---

# Technische Grundlage

## Inhaltsverzeichnis
1. [Kurzbeschreibung](#1-kurzbeschreibung)
2. [Hinweise für lokale Installation und bekannte Probleme](#2-hinweise-für-lokale-installation-und-bekannte-probleme)
3. [Source Code (src)](#3-source-code-src)
4. [Kirby Struktur und Seiten-Templates (Typen)](#4-kirby-struktur-und-seiten-templates-typen)
5. [Kirby Plugins](#5-kirby-plugins)
6. [Spezielle Ressourcen](#6-spezielle-ressourcen)
7. [System-Funktionen und CRON-Jobs](#7-system-funktionen-und-cron-jobs)

## 1. Kurzbeschreibung

Es wird empfohlen die Grundlagen von [Kirby CMS](https://getkirby.com/docs/guide) kennenzulernen, bevor man Anpassungen vornimmt. Diese Datei dient als knappe Einführung und Zusammenfassung des Aufbaus des Systems.

## 2. Hinweise für lokale Installation und bekannte Probleme

## Lokale Installation
Je nach Server-Konfiguration müssen Sie evtl. immer mal wieder PHP-Erweiterungen aktivieren, die Kirby benötigt. Bspw. INTL ist bei XAMPP und MAMP deaktiviert. Webserver haben in der Regel alle gängigen Erweiterungen aktiv. Diese können Sie wie üblich in den php.ini-Dateien Ihres Servers bearbeiten.

Die Admin- und Workshop-Bereiche sollten aus technischen Gründen nicht über die Live-Reload-URLs von Prepros oder CodeKit abgerufen werden, da Weiterleitungsfehler auftreten werden. Die Live-Reload-Funktion bleibt von daher nur für den Sammlung-Bereich relevant. Sollten Probleme auftreten, bspw. werden GTLF-3D-Modelle nicht geladen oder Ähnliches, bitte die normale localhost-URL verwenden und auf die Live-Reload-URLs verzichten.

Je nach Server treten manchmal Verbindungsprobleme zu den Drittanbieter-Embeds auf. Bspw. Twitter und Instagram können lokal nicht angesprochen werden, um die Metadaten zu liefern. Bestimmte Server blockieren die Play- (und somit auch Autoplay) Funktion von TikTok in macOS Safari und allen iOS Browsern. Dies ist bis jetzt auf virtuellen Servern in einem Cloudron Kontext aufgetreten. Übliche Webserver in einem Hosting-Paket können TikTok problemlos abspielen lassen. Bitte denken Sie dran, dass externe Drittanbieter evtl. ihre APIs verändern. Diese Plattform fokussiert sich hauptsächlich auf die physischen Modelle und hat die Embed Implementierung möglichst optimal aber nicht perfekt verfolgt.

## Twitter
Da Twitter an sich ohne Bilder arbeitet kann man dafür keine Previewbilder aufrufen. Für die Sammlung werden aus diesem Grund die eigentlichen Embeds geladen und transformiert (verkleinert). Die iFrames werden dann für Interaktion deaktiviert. Da CSS scale-Transformationen aber eine leere Fläche hinterlassen (Originalgröße des Elements bleibt bestehen) befinden sich diese Tweets in einem extra Container, der per JavaScript die benötigte berechnete Größe bekommt und den Subcontainer quasi zuschneidet. Es kommt manchmal dazu, dass die Berechnete Größe minimal abweicht, was an sich nicht sichtbar ist, bis der/die Besucher:in den Tweet fokusiert und der Glow erscheint.

### MAMP
Wenn Sie MAMP für Windows benutzen, bitte Folgendes beachten:

* Fügen Sie `C:\MAMP\bin\php\php8.1.0` zu Ihren System Umgebungsvariablen hinzu (bitte Pfad anpassen, falls Sie MAMP nicht im Standardpfad installiert haben).
* Bitte aktivieren Sie die INTL Extension. Die php.ini Datei finden Sie normalerweise unter `mamp/conf/php8.1.0`. Suchen Sie nach 'intl' und entfernen Sie das ; am Anfang der Zeile `extension=php_intl.dll` .
* Starten Sie am besten Ihren Rechner neu.

### XAMPP
Wenn Sie XAMPP für Windows benutzen, bitte Folgendes beachten:

* Die richtige XAMPP Version herunterladen! Das System unterstützt PHP 8.1.x.
* Ähnlich wie bei MAMP aktivieren Sie bitte die Extensions: `extension=gd`, `extension=intl`. Die php.ini können Sie direkt aus XAMPP öffnen.
* Starten Sie nur den Server neu.



## 3. Source Code (src)
Im Ordner `src` finden Sie SASS und Javascript-Dateien, die relevant für das Aussehen und Funktionalität der Plattform sind. Wenn Sie in CodeKit oder Prepros, wie in der README-Datei des Repositorys erklärt, das Projekt importiert haben, wird der Code aus dem `src`-Ordner kompiliert und in `www/assets` verschoben. Somit bleibt der Ordner `www`, der von Ihrem localhost aufgerufen wird, immer aktuell.


## 4. Kirby-Struktur und Seiten-Templates (Typen) 

### Die Kirby-Struktur
Man kann dieses Repository als ein Theme für Kirby CMS verstehen. Alle relevanten Dateien befinden sich im `www/site`-Ordner. 

Der Ordner `www/assets` wird, wie oben beschrieben, mit dem eigenen generierten Code befüllt.

Der Ordner `www/content` beinhaltet die eigentlichen Daten, die alle Benutzer bearbeiten werden. Kirby ist ein Flat-File CMS und besitzt keine Datenbank. Die Daten werden daher in Text-Dateien gespeichert (.txt).

Im Ordner `www/media` werden Daten vom System abgelegt. Wenn bspw. ein Bild für unterschiedliche Browser-Breiten (src-set) oder ein Thumbnail daraus generiert wird, landen diese Daten in diesem Ordner. In der Regel wird er selten von jemandem außer vom System verwendet.

Der letzte wichtige Ordner ist `www/kirby` und er beinhaltet den Kirby Core. Bitte mehr dazu in der README des Repositorys lesen. Der Kirby Core ist nicht Teil von diesem Repository.

### Seiten-Templates
Die Templates (oder Typen) der unterschiedlichen Seiten bestehen aus mehreren Dateien, die eine bestimmte Rolle spielen. 
- blueprint (yaml)
- template (php)
- controller (php)
- model (php)

Alle diese Dateien befinden sich im jeweiligen Ordner, z.B. `blueprints` oder `models` und haben den gleichen Namen, z. B. `blueprints/c_exhibit.yml` oder `models/c_exhibit.php`.

Ein Seiten-Template muss eine Blueprint (yaml) und kann und sollte in den meisten Fällen ein Template (php) besitzen. Controllers und Models sind optional.


#### **Blueprints**
In Blueprints werden vor allem Daten definiert, die in einem Template zur Verfügung stehen werden, bspw. Felder, die der Benutzer oder ein Admin bearbeiten können (Objekttyp, Alter einer Person, Klassifikation eines Objekts, etc). Diese können dann mittels der passenden PHP-Datei des Templates visuell dargestellt werden.

Es gibt grundsätzlich zwei Typen von Feldern, die angelegt werden können: statische Felder und Datenfelder. Statische Felder sind hilfreich, um das Admin-Backend zu strukturieren, z. B. Überschriften, Trennlinien, etc.

Mit Datenfeldern kann man hingegen Daten im System Daten speichern. Das heißt, diese Felder werden im Admin-Backend als Eingabefelder visualisiert, z. B. ein Textfeld, ein Dropdown, etc.

Datenfelder können auch über das Frontend gespeichert werden, so wie in unserem Fall. Mit Frontend sind hier konkret die Workshop-Räume gemeint. Da diese Bereiche eine bestimmte Gestaltung und User Experience benötigen, die etwas ansprechender als im Admin-Bereich sind, wurden sie speziell mittels PHP, Javascript und CSS programmiert.

*Mehr zu Kirby Blueprints:*

*https://getkirby.com/docs/guide/blueprints/introduction*


#### **Templates (php)**
Die PHP-Templates repräsentieren die visuelle Ebene. Hier werden die Daten ausgelesen und im HTML-Code visualisiert. Je nach Template können Sie PHP-Logik beinhalten. Es wird aber in der Regel empfohlen, Logik für ein Template in einem Controller oder Model zu kodieren.

*Mehr zu Kirby Templates:*

*https://getkirby.com/docs/guide/templates/basics*

#### **Controllers (php)**
Diese PHP-Dateien beinhalten nur Logik und liefern Variablen zu dem jeweiligen PHP-Template. Man kann beispielsweise per POST-Formular die gleiche URL der aktuellen Seite abrufen, um Daten zu verschicken. Die Controller-Datei, falls vorhanden, würde die Daten abarbeiten und Ergebnisse an die Template-Datei übergeben. So vermeidet man, dass die Template-Datei überfüllt mit Logik wird.

*Mehr zu Kirby Controllers:*

*https://getkirby.com/docs/guide/templates/controllers*

#### **Models (php)**
Jeder Seitentyp kann eine eigene spezielle Klasse besitzen, die die Grundfunktionen der Page-Klasse von Kirby vererbt. Diese Klassen werden mit Models repräsentiert. In diesem System sind alle Models Unterklassen von JDSPage, eine Klasse, die als Plugin existiert, und diese Klasse ist wiederum Unterklasse von Page. Die JDSPage beinhaltet wiederverwendbare Logik und Funktionen, wie z. B. Loggen der Änderungen, Teilnehmer-Auth-Funktionen, etc.

*Mehr zu Kirby Models:*

*https://getkirby.com/docs/guide/templates/page-models*

#### E-Mail Templates
Die vom System generierten E-Mails, die das Personal bekommt können auch umgestaltet werden. Die Templates dafür finden sie unter `www/site/templates/emails`. Es gibt jeweils ein Template für HTML und Plain-Text.

*Mehr zu Kirby E-Mail Templates:*

*https://getkirby.com/docs/guide/emails*


## 5. Kirby Plugins

Im Ordner `www/site/plugins` befinden sich alle Plugins, die dieses Kirby-"Theme" benötigt. Unter anderem finden Sie hier auch die speziell für die JDS programmierte Plugins von 2av. Aus diesem Grund haben diese Plugins keine eigenen Repositorys. Der Code ist kommentiert und sollte grundsätzlich nachvollziehbar sein, wenn Sie Kirby's Struktur und Funktionsweise kennen.

Es wird empfohlen die Plugins von Drittanbietern nur mit Vorsicht und wenn unbedingt nötig zu aktualisieren. Mehr Infos in der Kirby Dokumentation.

**2av-blocks-factory**

Kleine Sammlung von extra Blocks für Layouts. Für die JDS werden an sich nur Akkordeons verwendet.

**2av-custom-lock-class**

Spezielle Lock-Klasse, um Informationen zu den Gruppenkonten aber zu den Teilnehmern speichern zu können.

**2av-custom-methods**

Spezielle Methoden für Kirby Pages, Files, Fields, etc. Mehr dazu in der Kriby Dokumentation zum jeweiligen Kontext, z.B. für Fields:

https://getkirby.com/docs/reference/plugins/extensions/field-methods

**2av-global-functions**

Globale PHP Funktionen die ohne eingeschränkten Kontext Daten abarbeiten können.

**2av-jds-pagemodel**

Kern fast aller anderen Klassen des Themes. Enthält bestimmte Funktionen, bspw. die Logik für die Änderungshistorie.

**2av-temporary-users**

Erweitern eine Kirby Area damit man temporäre Konten mit einer angepassten Logik anlegen kann. Diese benötigt bswp. keine E-Mail Adressen sondern nur einen Benutzername und ein Passwort, das auch automatisch generiert wird.

**kirby3-janitor-2.16.0-von 2av angepasst**

Spezielle Version des Janitor-Plugins für Kirby die erweitert wurde, um Felder für die Menge an Teilnehmer und Leiter eingeben zu können für den Generator.

**Drittanbieter Plugins**

Die Auflistung der Plugins finden Sie im Admin-Bereich unter 'System' (Hauptnavigation oben links). Für mehr Informationen zu einem bestimmten Plugin bitte die jeweilige Dokumentation/Repository besuchen:

https://getkirby.com/plugins

* Cookie Consent Plugin: Die Texte können erst mal nur per Kirby-Config angepasst werden: https://github.com/michnhokn/kirby3-cookie-banner/wiki/02-Translate-the-modal


## 6. Spezielle Ressourcen

Die Plattform verwendet 2 wichtige Ressourcen von Drittanbietern.

### Google model-viewer
Diese 3D-Bibliothek ist verantwortlich für das Laden und Rendering der 3D-Modelle. Um das System geschlossen zu halten, liegen die relevanten Dateien direkt im Ordner `assets/js/vendor`. Es handelt sich um eine Javascript-Datei und einen GLTF Draco Decoder. Eine regelmäßige Aktualisierung dieser Bibliothek ist nicht vorgesehen.

### Virtual-Select
Die Plattform arbeitet sehr viel mit Dropdowns. Einige davon besitzen eine Autovervollständigung. Die Virtual-Select Bibliothek wurde an dieser Stelle eingesetzt, allerdings in einer abgewandelten Version, da die Handhabung der Daten der Plattform eine höhere Komplexität erforderte. Die Source-Code-Datei finden sie unter `src/js/vendor` und die dist-Version unter `assets/js/vendor`. Prepros/CodeKit aktualisieren die dist-Datei automatisch, wenn Sie die Quelle bearbeiten.


## 7. System-Funktionen und CRON-Jobs

Das System hat 3 Funktionen, die entweder per Klick oder CRON (automatisch von Ihrem Server) ausgelöst werden können. Um die Funktionen händisch auszulösen, gehen Sie bitte auf die Hauptseite des Admin-Bereichs, Tab "Website Einstellungen". Die Buttons befinden sich am Ende der Ansicht.

System-Funktionen | 
:-------------------------:|
![](../_media/admin/panel-system%20functions.png) | 

#### Workshops bereinigen

Das System geht alle Workshops durch, die noch nicht als bereinigt markiert sind. Bereinigen bedeutet in diesem Fall das Löschen von Ausstellungen und Objekten, die a) keine vollständigen Daten haben ***und*** b) nicht veröffentlicht wurden. Erst wenn beide Bedingungen zutreffen, wird die Seite gelöscht.

Ein Workshop wird auch bereinigt, wenn das verlinkte Gruppenkonto gelöscht wird.

#### Benutzer deaktivieren

Diese Funktion deaktiviert die temporären Gruppenkonten, deren Gültigkeit abgelaufen ist. Das heißt, wenn Sie sofort einen Benutzer deaktivieren wollen, bewirkt diese Funktion nichts, da sie nur Kontos mit einem Gültigkeitsdatum älter als "heute" deaktiviert.

Gruppenkonten, z. B. für eine komplette Klasse, sollten regelmäßig deaktiviert und im Anschluss gelöscht werden. So ist sichergestellt, dass die Teilnehmer nur für eine begrenzte Zeit Zugriff zum Workshop haben. Der Standardwert für die Gültigkeit eines Kontos ist 30 Tage. Dies kann im Blueprint für die Frontend-Benutzer angepasst werden. Dieser Wert wird beim Anlegen eines Kontos übernommen. Er kann im Admin-Backend aber jederzeit wieder angepasst werden.

***site/blueprints/users/frontenduser.yml***

    expiration:
        label: Verfallsdatum
        type: date
        ...
        default: today + 30 day

![](../_media/admin/users/tempusers-eigenschaften.png)  


#### Inaktive Benutzer löschen

Mit dieser Funktion werden inaktive Gruppenkonten endgültig gelöscht. Sie überprüft, ob die Lösch-Gültigkeit abgelaufen ist, um erst dann das Konto zu löschen. Die Lösch-Gültigkeit setzt sich zusammen aus "Ablaufdatum des Kontos" + "eingestellter Warteperiode (in Tagen) bis zum Löschen". Der Standardwert für die Warteperiode bis zum Löschen nach Ablaufdatum des Kontos beträgt 40 Tage. Dieser Wert kann jederzeit im Admin-Bereich angepasst werden (Tab "Website Einstellungen").

Wie weiter oben beschrieben: Vor dem Löschen des Kontos wird auch der damit verknüpfte Workshop erstmal bereinigt.

---

Wenn Sie diese Befehle per CRON-Job aufrufen wollen:

1. Erstellen Sie eine geplante Aufgabe auf Ihrem Server. Die Häufigkeit dürfen Sie entscheiden, es wird aber empfohlen, nur nachts und nicht zu häufig die Befehle auszuführen.

2. Der Befehl sollte wie folgt aussehen:

`wget https://meinedomain.de/plugin-janitor/namedesbefehls/xxxxxxMeinSchluesselxxxxxxx`

z. B.

`wget https://meinedomain.de/plugin-janitor/deactivateusers/669830Szuoa0x0193kjo`

Die Namen der Befehle können Sie aus der Kirby-config-Datei `system_functions.php` entnehmen. Sie werden hier auch andere Befehle finden, die bis jetzt ausschließlich per Knopfdruck ausgelöst werden.

***site/config/system_functions.php***

    ...
    'deactivateusers' => function (){...},
    'deleteusers'  => function (){...},
    'cleanroutine' => function (){...},
    ...

Den Schlüssel können Sie in der Kirby-config-Datei einstellen.

***site/config/config.php***

    'bnomei.janitor.secret' => 'ef1aebc3c119b6ddba70dd8b368f1d99',

*Hinweis: das Kirby-Plugin Janitor ist die Basis für diese Funktion, allerdings in einer abgewandelten Form. Dies bedeutet, dass Sie bitte nur die Version aus diesem Repository verwenden sollten. Wenn Sie das original Plugin verwenden, wird der Benutzer-Generator in den Workshops nicht mehr vorhanden sein. Für mehr Informationen bitte die Dokumentation des Plugins lesen. Achtung! In den aktuelleren Versionen hat sich die Syntax verändert!*

https://github.com/bnomei/kirby3-janitor



---

[« zurück zu README](../../README.md)