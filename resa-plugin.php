<?php

/**
 * Plugin Name: Resa Plugin
 * Description: Gestion de réservations pour l'ADESS.
 * Version: 1.0
 * Author: Joshua De Moura Idi
 */

// Sécurité : blocage de l'accès direct
if (!defined('ABSPATH')) {
    exit('Non autorisé !');
}

// === Chargement des fichiers du plugin === //

require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/form-handlers.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'admin/metaboxes.php';
require_once plugin_dir_path(__FILE__) . 'includes/new-table.php';
require_once plugin_dir_path(__FILE__) . 'includes/pdo-connection.php';
require_once plugin_dir_path(__FILE__) . 'includes/new-table-evenements.php';
require_once plugin_dir_path(__FILE__) . 'includes/new-table-evenements.php';
require_once plugin_dir_path(__FILE__) . 'includes/cpt.php';



function resa_activer_plugin()
{
    resa_creer_table_organisateurs(); // ← C’est ici qu’on crée la table
}
register_activation_hook(__FILE__, 'resa_activer_plugin');

register_activation_hook(__FILE__, 'resa_creer_table_evenements');


