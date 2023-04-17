[« Intro](README.md)

---

# Routinen und CRON-Jobs

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

[« Intro](README.md)