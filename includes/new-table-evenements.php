<?php
// Sécurité : empêcher l'accès direct au fichier 
if (!defined('ABSPATH')) exit;

/**
 * Création de la table personnalisée pour les événements
 */

 // Fonction pour créer la table des événements
function resa_creer_table_evenements() {
    global $wpdb; // Accès à l'objet de base de données WordPress

    $table_name = $wpdb->prefix . 'resa_evenements'; 
    $charset_collate = $wpdb->get_charset_collate(); 

    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        organisateur_id INT NOT NULL,
        type_prestation VARCHAR(100) NOT NULL,
        lieu VARCHAR(255),
        date_prestation DATE,
        nb_participants INT,
        cout_estime VARCHAR(50),
        infos_complementaires TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
