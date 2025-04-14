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

// Chargement des fichiers du plugin //

require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/form-handlers.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'admin/metaboxes.php';
require_once plugin_dir_path(__FILE__) . 'includes/pdo-connection.php';
require_once plugin_dir_path(__FILE__) . 'includes/cpt.php';


// Lignes désactivées après la création des tables:
//require_once plugin_dir_path(__FILE__) . 'includes/new-table.php';
//require_once plugin_dir_path(__FILE__) . 'includes/new-table-evenements.php';


// Table des organisateurs
function resa_activer_plugin()
{
    if (function_exists('resa_creer_table_organisateurs')) {
        resa_creer_table_organisateurs();
    }
}
register_activation_hook(__FILE__, 'resa_activer_plugin');

// Table des événements
function resa_creer_evenements_activation()
{
    if (function_exists('resa_creer_table_evenements')) {
        resa_creer_table_evenements();
    }
}
register_activation_hook(__FILE__, 'resa_creer_evenements_activation');

// Table des réservations
function resa_creer_table_reservations_activation()
{
    require_once plugin_dir_path(__FILE__) . 'includes/new-table-reservations.php';
    if (function_exists('resa_creer_table_reservations')) {
        resa_creer_table_reservations();
    }
}
register_activation_hook(__FILE__, 'resa_creer_table_reservations_activation');



