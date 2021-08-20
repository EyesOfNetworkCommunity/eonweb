## Cacti
### Modification sur EONWEB de fichiers liés à cacti

module/capacity_per_device/index.php
    \-> Correction de la requête SQL pour prendre en compte l'équipement sélectionné

module/report_performance/display.php
    \-> Correction de la requête SQL pour prendre en compte tous les paramètres du rapport

module/admin_device/index.php (en cours de modification)
    \-> Modification des paramètres des fichiers appelés dans cacti pour la nouvelle version de cacti


### Mise à jour de Cacti
appliance/cacti/cacti_upgrade_sql.sql
    \-> Met à jour les tables de cacti

appliance/cacti/upgrade_cacti_bdd.php
    \-> Met à jour les données de cacti

appliance/cacti/upgrade_cacti_plugins.php
    \-> Met à jour les plugins de cacti (syslog, weathermap), les autres plugins (aggregate, rrdclean, realtime, settings) ont été intégré à cacti


Les fichiers doient être exécutés dans l'ordre suivant:
- cacti_upgrade_sql.sql
- upgrade_cacti_bdd.php
- upgrade_cacti_plugins.php