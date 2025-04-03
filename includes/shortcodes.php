<?php
// Fichier : includes/shortcodes.php

// Shortcode : Formulaire organisateur
function afficher_formulaire_organisateur()
{
    ob_start();

    include plugin_dir_path(__FILE__) . '../templates/formulaire-organisateur.php';

    return ob_get_clean();
}
add_shortcode('organisateur_form', 'afficher_formulaire_organisateur');


// Shortcode : Formulaire événement
function afficher_formulaire_evenement() {
    if (!is_user_logged_in()) {
        return '<p>Vous devez être connecté pour demander un événement.</p>';
    }

    $current_user = wp_get_current_user();

    // Vérification si l'utilisateur est un organisateur
    $args = array(
        'post_type'      => 'organisateur',
        'post_status'    => 'publish',
        'meta_key'       => 'user_id',
        'meta_value'     => $current_user->ID,
        'posts_per_page' => 1
    );

    $organisateurs = get_posts($args);

    if (empty($organisateurs)) {
        return '<p>Aucun profil organisateur validé trouvé.</p>';
    }

    $organisateur = $organisateurs[0];

    // Données à passer au template
    $form_data = array(
        'type'         => get_post_meta($organisateur->ID, 'organisateur_type', true),
        'nom'          => get_the_title($organisateur),
        'adresse'      => get_post_meta($organisateur->ID, 'organisateur_adresse', true),
        'contact_nom'  => get_post_meta($organisateur->ID, 'organisateur_contact_nom', true),
        'fonction'     => get_post_meta($organisateur->ID, 'organisateur_contact_fonction', true),
        'email'        => get_post_meta($organisateur->ID, 'organisateur_email', true),
        'tel'          => get_post_meta($organisateur->ID, 'organisateur_tel', true),
        'lieu'         => get_post_meta($organisateur->ID, 'organisateur_lieu_defaut', true),
    );

    ob_start();
    include plugin_dir_path(__FILE__) . '../templates/formulaire-evenement.php';
    return ob_get_clean();
}
add_shortcode('demande_evenement', 'afficher_formulaire_evenement');



// Shortcode : Formulaire de réservation invité

function afficher_formulaire_reservation_invite()
{
    ob_start();

    include plugin_dir_path(__FILE__) . '../templates/formulaire-reservation.php';

    return ob_get_clean();
}
add_shortcode('formulaire_reservation_invite', 'formulaire_reservation');


// Shortcode : Connexion/Déconnexion
function resa_bloc_connexion()
{
    ob_start();

    include plugin_dir_path(__FILE__) . '../templates/bloc-connexion.php';

    return ob_get_clean();
}
add_shortcode('resa_login_logout', 'resa_bloc_connexion');





function resa_afficher_evenements_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'nombre' => 5,
        'ordre' => 'DESC'
    ), $atts);

    $args = array(
        'post_type' => 'evenement',
        'posts_per_page' => intval($atts['nombre']),
        'order' => $atts['ordre']
    );

    $query = new WP_Query($args);

    ob_start();

    // Rend la variable $query accessible dans le template
    include plugin_dir_path(__FILE__) . '../templates/liste-evenements.php';
    return ob_get_clean();
}
add_shortcode('resa_afficher_evenements', 'resa_afficher_evenements_shortcode');
