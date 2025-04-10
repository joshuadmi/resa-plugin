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
function afficher_formulaire_evenement()
{
    if (!is_user_logged_in()) {
        return '<p>Vous devez être connecté pour demander un événement.</p>';
    }

    $current_user = wp_get_current_user();

    // Vérification si l'utilisateur est un organisateur
    global $wpdb;
    $table = $wpdb->prefix . 'resa_organisateurs';
    
    $organisateur = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d LIMIT 1",
            $current_user->ID
        )
    );
    
    if (!$organisateur) {
        return '<p>Aucun profil organisateur validé trouvé.</p>';
    }

        $type        = $organisateur->type;
$nom         = $organisateur->nom;
$adresse     = $organisateur->adresse;
$contact_nom = $organisateur->contact_nom;
$fonction    = $organisateur->contact_fonction;
$email       = $organisateur->email;
$tel         = $organisateur->tel;
$lieu        = $organisateur->lieu_defaut;

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
add_shortcode('formulaire_reservation_invite', 'afficher_formulaire_reservation_invite');


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
