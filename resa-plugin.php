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
require_once plugin_dir_path(__FILE__) . 'includes/cpt.php';
require_once plugin_dir_path(__FILE__) . 'includes/form-handlers.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'admin/metaboxes.php';
