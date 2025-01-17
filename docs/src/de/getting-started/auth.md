---
title: Authentifizierung & Autorisierung
sidebarDepth: 3
---

# Authentifizierung & Autorisierung

Castopod behandelt Authentifizierung und Autorisierung mit `codeigniter/shield`
kombiniert mit eigenen Regeln. Rollen und Berechtigungen sind auf zwei Ebenen
definiert:

1. [instanzweit](#1-instance-wide-roles-and-permissions)
2. [pro Podcast](#2-per-podcast-roles-and-permissions)

## 1. Instanzweite Rollen und Berechtigungen

### Instanz Rollen

<!-- AUTH-INSTANCE-ROLES-LIST:START - Do not remove or modify this section -->

| role                | description                                   | permissions                                                                                |
| ------------------- | --------------------------------------------- | ------------------------------------------------------------------------------------------ |
| Super-Administrator | Hat die vollständige Kontrolle über Castopod. | admin.\*, podcasts.\*, users.manage, persons.manage, pages.manage, fediverse.manage-blocks |
| Manager             | Verwaltet Castopods Inhalte.                  | podcasts.create, podcasts.import, persons.manage, pages.manage                             |
| Podcaster           | Allgemeine Benutzer von Castopod.             | admin.access                                                                               |

<!-- AUTH-INSTANCE-ROLES-LIST:END -->

### Instanz Berechtigungen

<!-- AUTH-INSTANCE-PERMISSIONS-LIST:START - Do not remove or modify this section -->

| permission              | description                                                                  |
| ----------------------- | ---------------------------------------------------------------------------- |
| admin.access            | Kann auf den Admin-Bereich von Castopod zugreifen.                           |
| admin.settings          | Kann auf die Einstellungen von Castopod zugreifen.                           |
| users.manage            | Kann Castopod-Benutzer verwalten.                                            |
| persons.manage          | Kann Mitwirkende verwalten.                                                  |
| pages.manage            | Kann Seiten verwalten.                                                       |
| podcasts.view           | Kann alle Podcasts einsehen.                                                 |
| podcasts.create         | Kann neue Podcasts erstellen.                                                |
| podcasts.import         | Kann Podcasts importieren.                                                   |
| fediverse.manage-blocks | Kann föderierte Nutzer/Domains davon abhalten, mit Castopod zu interagieren. |

<!-- AUTH-INSTANCE-PERMISSIONS-LIST:END -->

## 2. Pro Podcast Rollen und Berechtigungen

### Pro Podcast Rollen

<!-- AUTH-PODCAST-ROLES-LIST:START - Do not remove or modify this section -->

| role          | description                                                                 | permissions                                                                                                                                                                                                                                                                                 |
| ------------- | --------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Administrator | Hat die vollständige Kontrolle über Podcast #{id}.                          | \*                                                                                                                                                                                                                                                                                          |
| Editor        | Verwaltet Inhalte und Veröffentlichungen von Podcast #{id}.                 | view, edit, manage-import, manage-persons, manage-platforms, manage-publications, manage-notifications, interact-as, episodes.view, episodes.create, episodes.edit, episodes.delete, episodes.manage-persons, episodes.manage-clips, episodes.manage-publications, episodes.manage-comments |
| Autor         | Verwaltet Inhalte von Podcast #{id}, kann diese aber nicht veröffentlichen. | view, manage-persons, episodes.view, episodes.create, episodes.edit, episodes.manage-persons, episodes.manage-clips                                                                                                                                                                         |
| Gast          | Allgemeiner Mitwirkender des Podcasts #{id}.                                | view, episodes.view                                                                                                                                                                                                                                                                         |

<!-- AUTH-PODCAST-ROLES-LIST:END -->

### Pro Podcast Berechtigung

<!-- AUTH-PODCAST-PERMISSIONS-LIST:START - Do not remove or modify this section -->

| permission                   | description                                                                                            |
| ---------------------------- | ------------------------------------------------------------------------------------------------------ |
| view                         | Kann das Dashboard und Analysen des Podcasts #{id} einsehen.                                           |
| edit                         | Kann Podcast #{id} bearbeiten.                                                                         |
| delete                       | Kann Podcast #{id} löschen.                                                                            |
| manage-import                | Kann den importierten Podcast #{id} synchronisieren.                                                   |
| manage-persons               | Kann Abonnements des Podcasts #{id} verwalten.                                                         |
| manage-subscriptions         | Kann Abonnements des Podcasts #{id} verwalten.                                                         |
| manage-contributors          | Kann Mitwirkende des Podcasts #{id} verwalten.                                                         |
| manage-platforms             | Kann Plattform-Links des Podcasts #{id} verwalten.                                                     |
| manage-publications          | Kann Podcast #{id} veröffentlichen.                                                                    |
| manage-notifications         | Kann Benachrichtigungen des Podcasts #{id} einsehen und als gelesen markieren.                         |
| interact-as                  | Kann als Podcast #{id} interagieren, um Beiträge zu favorisieren, zu teilen oder diese zu beantworten. |
| episodes.view                | Kann das Dashboard und Analysen des Podcasts #{id} einsehen.                                           |
| episodes.create              | Kann Folgen für Podcast #{id} erstellen.                                                               |
| episodes.edit                | Kann Podcast #{id} bearbeiten.                                                                         |
| episodes.delete              | Kann Podcast #{id} löschen.                                                                            |
| episodes.manage-persons      | Kann Abonnements des Podcasts #{id} verwalten.                                                         |
| episodes.manage-clips        | Kann Videoclips und Soundbites des Podcasts #{id} verwalten.                                           |
| episodes.manage-publications | Kann Podcast #{id} veröffentlichen.                                                                    |
| episodes.manage-comments     | Kann Kommentare von Folgen des Podcasts #{id} erstellen und löschen.                                   |

<!-- AUTH-PODCAST-PERMISSIONS-LIST:END -->
