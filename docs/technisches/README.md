[« zurück zu README](../../README.md)

---

# Technische Grundlage

## Inhaltsverzeichnis
1. Kurzbeschreibung
2. Source Code (src)
3. Kirby Templates (Seiten-Typen)
4. Kirby Plugins
5. Spezielle Ressourcen
6. Routinen und CRON-Jobs


## 1. Kurzbeschreibung

Es wird empfohlen die Grundlagen von [Kirby CMS](https://getkirby.com/docs/guide) kennenzulernen, bevor man Anpassungen vornimmt. Diese Datei dient als knappe Einführung und Zusammenfassung des Aufbaus des Systems.

## 2. Source Code (src)
Im Ordner `src` finden Sie SASS und Javascript Dateien die relevant für das Aussehen und Funktionalität der Platform sind. Wenn Sie in CodeKit oder Prepros, wie in der README Datei des Repositorys erklärt, das Projekt importiert haben wird der Code aus dem `src` Ordner kompiliert und in `www/assets` verschoben. Somit bleibt der Ordner `www`, der von Ihrem localhost aufgerufen wird, immer aktuell.


## 3. Kirby Struktur und Seiten-Templates (Typen) 

### Die Kirby Struktur
Man kann dieses repository als ein Theme für Kirby CMS verstehen. Alle relevante Dateien befinden sich im `www/site` Ordner. 

Der Ordner `www/assets` wird, wie oben beschrieben, mit dem eigenen generierten Code befüllt.

Der Ordner `www/content` beinhaltet die eigentlichen Daten, die alle Benutzer bearbeiten werden. Kirby ist ein Flat-File CMS und besitzt keine Datenbank. Die Daten werden von daher in Text-Dateien gespeichert (.txt).

Im Ordner `www/media` werden Daten vom System abgelegt. Wenn bspw. ein Bild für unterschiedlichen Browser-Breiten (src-set) oder ein Thumbnail daraus generiert wird landen diese Daten in diesem Ordner. In der Regel wird er selten von jemandem ausser vom System verwendet.

Der letzte wichtige Ordner ist `www/kirby` und er beinhaltet den Kirby Core. Bitte mehr dazu in der README des Repositorys lesen. Der Kirby Core ist nicht Teil von diesem Repository.

### Seiten-Templates
Die Templates (oder Typen) der unterschiedlichen Seiten bestehen aus mehreren Dateien, die eine bestimmte Rolle spielen. 
- blueprint (yaml)
- template (php)
- controller (php)
- model (php)

Alle diese Dateien befinden sich im jeweiligen Ordner, z.B. `blueprints` oder `models` und haben den gleichen name, z.B. `blueprints/c_exhibit.yml` oder `models/c_exhibit.php`.

Ein Seiten-Template muss eine Blueprint (yaml) und es kann und sollte in den meisten Fällen ein Template (php) besitzen. Controllers und Models sind optional.


#### **Blueprints**
In Blueprints werden vor allem Daten definiert, die in einem Template zur Verfügung stehen werden, bspw. Felder die der Benutzer oder ein Admin bearbeiten können (Objekttyp, Alter einer Person, Klassifikation eines Objekts, etc). Diese können dann mittels der passenden PHP Datei des Templates visuell dargestellt werden.

Es gibt grundsätzlich zwei Typen von Felder, die angelegt werden können: statische Felder und Datenfelder. Statische Felder sind hilfreich, um das Admin-Backend zu strukturieren, z.B. Überschriften, Trennlinien, etc.

Mit Datenfelder kann man hingegen Daten im System Daten speichern. Das heisst, diese Felder werden im Admin-Backend als Eingabefelder visualisiert, z.B. ein Textfeld, ein Dropdown, etc.

Datenfelder können auch über das Frontend gespeichert werden, so wie in unserem Fall. Mit Frontend sind hier konkret die Workshop-Räume gemeint. Da diese Bereiche eine bestimmte Gestaltung und User Experience benötigen, die etwas ansprechender als im Admin-Bereich sind, wurden sie speziell mittels PHP, Javascript und CSS programmiert.

*Mehr zu Kirby Blueprints:*

*https://getkirby.com/docs/guide/blueprints/introduction*


#### **Templates (php)**
Die PHP Templates repräsentieren die visuelle Ebene. Hier werden die Daten ausgelesen und im HTML-Code visualisiert. Je nach Template können Sie PHP Logik beinhalten. Es wird aber in der Regel empfohlen, Logik für ein Template in einem Controller oder Model zu kodieren.

*Mehr zu Kirby Templates:*

*https://getkirby.com/docs/guide/templates/basics*

#### **Controllers (php)**
Diese PHP Dateien beinhalten nur Logik und liefern Variablen zu dem jeweiligen PHP Template. Man kann beispielsweise per POST-Formular die gleiche URL der aktuellen Seite abrufen um Daten zu verschicken. Die Controller Datei, falls vorhanden, würde die Daten abarbeiten und Ergebnisse an die Template Datei übergeben. So vermeidet man, dass die template Datei überfüllt mit Logik wird.

*Mehr zu Kirby Controllers:*

*https://getkirby.com/docs/guide/templates/controllers*

#### **Models (php)**
Jeder Seitentyp kann eine einige spezielle Klasse besitzen, die die Grundfunktionen der Page-Klasse von Kirby vererben. Diese Klassen werden mit Models repräsentiert. In diesem System sind alle Models Unterklassen von JDSPage, eine Klasse die als Plugin exsitiert, und diese Klasse ist wiederrum Unterklasse von Page. Die JDSPage beinhaltet wiederverwendbare Logik und Funktionen, wie z.B. Loggen der Änderungen, Teilnehmer-Auth Funktionen, etc.

*Mehr zu Kirby Models:*

*https://getkirby.com/docs/guide/templates/page-models*


## 4. Kirby Plugins

Im Ordner `www/site/plugins` befinden sich alle Plugins von diesem Kirby "Theme".

## 5. Spezielle Ressourcen


## 6. Routinen und CRON-Jobs

Das System kommt mit 3 wichtigen Routinen die per Knopfdrück oder CRON-Job ausgeführt werden können.

## Benutzer deaktivieren
Gruppenbenutzer, z.B. für eine komplette Klasse, sollten regelmäßig deaktiviert und im Anschluss gelöscht werden. So ist es sicher gestellt, dass die Teilnehmer nur für eine begrenzte Zeit Zugriff zum Workshop haben.

Der Standardwert für die Gültigkeit eines Kontos ist 30 Tage. Dies kann in dem Blueprint für die Frontend-Benutzer angepasst werden. Dieser Wert wird beim Anlegen eines Kontos übernommen. Er kann im Admin-Backend aber jederzeit wieder angepasst werden.

***blueprints/users/frontenduser.yml***

    expiration:
        label: Verfallsdatum
        type: date
        ...
        default: today + 30 day

![](_media/admin/users/tempusers-eigenschaften.png)  



---

[« zurück zu README](../../README.md)