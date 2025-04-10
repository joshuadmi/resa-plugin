<?php
// Sécurité : empêche l'accès direct
if (!defined('ABSPATH')) exit;

/**
 * Création de la table personnalisée pour les organisateurs
 */
function resa_creer_table_organisateurs() {
    global $wpdb;

    // Nom complet de la table (avec préfixe WordPress)
    $table_name = $wpdb->prefix . 'resa_organisateurs';

    // Charset et collation pour la compatibilité
    $charset_collate = $wpdb->get_charset_collate();

    // Requête SQL
    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        type VARCHAR(50) NOT NULL,
        nom VARCHAR(255) NOT NULL,
        adresse TEXT,
        contact_nom VARCHAR(255),
        contact_fonction VARCHAR(255),
        email VARCHAR(255),
        tel VARCHAR(50),
        lieu_defaut VARCHAR(255),
        infos TEXT,
        user_id BIGINT UNSIGNED,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
