<?php
// Fichier : includes/cpt.php

// CPT Événements
function resa_creer_evenements() {
    $libelles = array(
        'name'          => 'Événements',        // Nom au pluriel affiché dans le menu
        'singular_name' => 'Événement',         // Nom singulier
        'menu_name'     => 'Événements',        // Nom affiché dans le menu admin
        'add_new_item'  => 'Nouvel événement',  // Texte du bouton pour ajouter un événement
    );

    $parametres = array(
        'labels'        => $libelles,
        'public'        => true,                // Visible sur le front et dans l’admin
        'has_archive'   => true,                // Permet d’avoir une archive des événements
        'rewrite'       => array('slug' => 'evenement'),
        'supports'      => array('title', 'editor', 'thumbnail'),
        'menu_icon'     => 'dashicons-tickets-alt',
    );

    register_post_type('evenement', $parametres);

    // CPT Organisateurs
    $libelles_organisateur = array(
        'name'          => 'Organisateurs',
        'singular_name' => 'Organisateur',
        'add_new'       => 'Nouvel Organisateur',
        'menu_name'     => 'Organisateurs'
    );

    $parametres_organisateur = array(
        'labels'        => $libelles_organisateur,
        'public'        => false,                // Non visible sur le front-end
        'show_ui'       => true,                 // Visible dans l’admin
        'show_in_menu'  => true,
        'menu_icon'     => 'dashicons-groups',
        'supports'      => array('title', 'editor')
    );

    register_post_type('organisateur', $parametres_organisateur);

    // CPT Réservations
    $libelles_reservation = array(
        'name'          => 'Réservations',
        'singular_name' => 'Réservation'
    );
    $parametres_reservation = array(
        'labels'       => $libelles_reservation,
        'public'       => false,              // On veut garder ce CPT pour l’admin éventuellement
        'show_ui'      => true,
        'supports'     => array('title')
    );

    register_post_type('reservation', $parametres_reservation);
}

add_action('init', 'resa_creer_evenements');
