<?php

// CPT Événements
function resa_creer_evenements()
{
    // CPT evenement
    $libelles = array(
        'name' => 'Evénements',
        'singular_name' => 'Événement',
        'menu_name' => 'Événements',
        'add_new_item'  => 'Nouvel événement', // Ajout d'un nouvel élément
    );

    $parametres = array(
        'labels' => $libelles,
        'public' => true,
        'supports' => array('title', 'editor', 'thumbnail')
    );

    register_post_type('evenement', $parametres);

    // CPT organisateur
    $libelles_organisateur = array(
        'name' => 'Organisateurs',
        'singular_name' => 'Organisateur',
        'add_new' => 'Nouvel Organisateur',
        'menu_name' => 'Organisateurs'
    );

    $parametres_organisateur = array(
        'labels' => $libelles_organisateur,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-groups',
        'supports' => array('title', 'editor')
    );

    register_post_type('organisateur', $parametres_organisateur);

    // CPT réservation
    register_post_type('reservation', array(
        'label' => 'Réservations',
        'public' => false,
        'show_ui' => true,
        'supports' => array('title')
    ));
}
// Enregistrement du hook
add_action('init', 'resa_creer_evenements');
