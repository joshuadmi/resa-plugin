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
    global $wpdb;
    $table = $wpdb->prefix . 'resa_organisateurs';

    // Récupération du profil organisateur lié à l'utilisateur connecté
    $organisateur = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table WHERE user_id = %d LIMIT 1", $current_user->ID)
    );

    if (!$organisateur) {
        return '<p>Aucun profil organisateur validé trouvé.</p>';
    }

    // On récupère le type (entreprise ou collectivite) depuis la table personnalisée
    $type = $organisateur->type;

    // Si l'organisateur appartient à une collectivité, on n'affiche pas le formulaire
    if ($type === 'collectivite') {
        return '<p>Pour votre collectivité, les événements seront créés par l\'administration dans le dashboard. Veuillez contacter l\'administrateur pour plus d\'informations.</p>';
    }

    // Pour un organisateur de type "entreprise", on prépare les autres données
    $nom          = $organisateur->nom;
    $adresse      = $organisateur->adresse;
    $contact_nom  = $organisateur->contact_nom;
    $fonction     = $organisateur->contact_fonction;
    $email        = $organisateur->email;
    $tel          = $organisateur->tel;
    $lieu         = $organisateur->lieu_defaut;

    // Optionnel : préparer un tableau de données pour le template
    $form_data = array(
        'type'         => $type,
        'nom'          => $nom,
        'adresse'      => $adresse,
        'contact_nom'  => $contact_nom,
        'fonction'     => $fonction,
        'email'        => $email,
        'tel'          => $tel,
        'lieu'         => $lieu,
    );

    // Rendre ces variables accessibles dans le template
    // Tu peux les extraire ainsi pour simplifier l'accès :
    extract($form_data);

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


