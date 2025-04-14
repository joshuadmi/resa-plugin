<?php
// Ce fichier définit les Custom Post Types qui servent à gérer les données spécifiques
// (ici, les événements, organisateurs et réservations). On utilise les CPT pour bénéficier
// de l'interface native WordPress dans l'admin et faciliter la gestion, même si on migre
// progressivement vers des tables personnalisées via PDO.

// CPT Événements
function resa_creer_evenements() {

    //textes affichés dans l'interface
    $libelles = array(
        'name'          => 'Événements',        // Nom au pluriel affiché dans le menu
        'singular_name' => 'Événement',         // Nom singulier
        'menu_name'     => 'Événements',        // Nom affiché dans le menu admin
        'add_new_item'  => 'Nouvel événement',  // Texte du bouton pour ajouter un événement
    );

    // paramètres pour ce CPT
    $parametres = array(
        'labels'        => $libelles,
        'public'        => true,                // Visible sur le front et dans l’admin
        'has_archive'   => true,                // Permet d’avoir une archive des événements
        'rewrite'       => array('slug' => 'evenement'),
        'supports'      => array('title', 'editor', 'thumbnail'),
        'menu_icon'     => 'dashicons-tickets-alt',
    );

    register_post_type('evenement', $parametres);

    // CPT Organisateurs. textes affichés pour les organisateurs dans l'admin
    $libelles_organisateur = array(
        'name'          => 'Organisateurs',
        'singular_name' => 'Organisateur',
        'add_new'       => 'Nouvel Organisateur',
        'menu_name'     => 'Organisateurs'
    );

        // Paramètres pour le CPT "organisateur"
    $parametres_organisateur = array(
        'labels'        => $libelles_organisateur,
        'public'        => false,                // Non visible sur le front-end
        'show_ui'       => true,                 // Visible dans l’admin
        'show_in_menu'  => true,
        'menu_icon'     => 'dashicons-groups', // Icône dans le menu admin
        'supports'      => array('title', 'editor')// le titre et l'éditeur pour stocker les infos de base
    );

    register_post_type('organisateur', $parametres_organisateur);

    // CPT Réservations. sert à gérer les réservations, qui seront aussi accessibles depuis l'admin.
    $libelles_reservation = array(
        'name'          => 'Réservations',
        'singular_name' => 'Réservation'
    );

    $parametres_reservation = array(
        'labels'       => $libelles_reservation,
        'public'       => false,              // On veut garder ce CPT pour l’admin
        'show_ui'      => true,
        'supports'     => array('title')
    );

    register_post_type('reservation', $parametres_reservation);
}

add_action('init', 'resa_creer_evenements');
