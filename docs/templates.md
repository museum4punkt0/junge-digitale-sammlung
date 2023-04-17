[« Intro](README.md)

---

# Templates

Die Templates (oder Typen) der unterschiedlichen Seiten bestehen aus mehreren Dateien, die eine bestimmte Rolle spielen:
- blueprint
- php template
- controller
- model

## Blueprints
In Blueprints werden vor allem Daten definiert, die in einem Template zur Verfügung stehen werden, bspw. Felder die der Benutzer oder ein Admin bearbeiten können (Objekttyp, Alter einer Person, Klassifikation eines Objekts, etc). Diese können dann mittels der passenden PHP Datei des Templates visuell dargestellt werden.

Es gibt grundsätzlich zwei Typen von Felder, die angelegt werden können: statische und Datenfelder. Statische Felder sind hilfreich, um das Admin-Backend zu strukturieren, z.B. Überschriften, Trennlinien, etc.

Mit Datenfelder kann man hingegen Daten im System speichern. Das heisst, diese Felder werden im Admin-Backend als Eingabefelder visualisiert, z.B. ein Textfeld, ein Dropdown, etc.

Datenfelder können auch über das Frontend gespeichert werden, so wie in unserem Fall. Mit Frontend sind hier konkret die Workshop-Räume gemeint. Da diese Bereiche eine bestimmte Gestaltung und User Experience benötigen, die etwas ansprechender als im Admin-Bereich sind, wurden sie speziell mittels PHP, Javascript und CSS programmiert.


## PHP Templates
Die PHP Templates repräsentieren die visuelle Ebene. Hier werden die Daten geladen und im HTML-Code platziert. Je nach Template können Sie PHP Logik beinhalten.

## Controller
Diese PHP Dateien beinhalten nur Logik und liefern Variablen zu dem jeweiligen PHP Template.

## Model
Jeder Seitentyp kann eine einige Klasse haben. Diese Klassen werden mit Models repräsentiert. In diesem System sind alle Models Unterklassen von JDSPage, eine Klasse die als Plugin exsitiert. Diese beinhaltet globale Logik und Funktionen, wie z.B. Loggen der Änderungen, Benutzer-Auth Funktionen, etc.


---

[« Intro](README.md)