<?php
// Sécurité : empêche l'accès direct
if (!defined('ABSPATH')) exit;

/**
 * Création de la table personnalisée pour les réservations
 */
function resa_creer_table_reservations() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'resa_reservations';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        evenement_id INT NOT NULL,
        nom_invite VARCHAR(100) NOT NULL,
        email_invite VARCHAR(100) NOT NULL,
        places_demandees INT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
