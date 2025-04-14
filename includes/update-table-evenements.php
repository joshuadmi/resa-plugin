<?php
// Empêche l'accès direct au fichier
if (!defined('ABSPATH')) exit;

// Fonction d'ajout de la colonne 'subventionne' à la table wp_resa_evenements
function resa_ajouter_colonne_subventionne()
{
    require_once plugin_dir_path(__FILE__) . 'pdo-connection.php';
    $pdo = get_pdo_connection();

    global $wpdb;
    $table_name = $wpdb->prefix . 'resa_evenements';

    // Vérifie si la colonne existe déjà
    $check_column = $pdo->prepare("SHOW COLUMNS FROM `$table_name` LIKE 'subventionne'");
    $check_column->execute();

    if ($check_column->rowCount() === 0) {
        // La colonne n'existe pas, on l'ajoute
        $sql = "ALTER TABLE `$table_name` ADD `subventionne` TINYINT(1) DEFAULT 0";
        $pdo->exec($sql);
    }
}
