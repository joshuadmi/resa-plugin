<?php


// Fonctions utilitaires et hooks globaux

function resa_charger_styles()
{
    wp_enqueue_style(
        'resa-plugin-style',
        plugin_dir_url(__DIR__) . 'public/css/style.css',
        array(),
        '1.0'
    );
}

add_action('wp_enqueue_scripts', 'resa_charger_styles');


// Redirection personnalisée après connexion
function resa_redirection_apres_login($redirect_to, $request, $user)
{
    if (isset($user->roles) && is_array($user->roles)) {
        return home_url(); // Redirige vers la page d’accueil
    }
    return $redirect_to;
}
add_filter('login_redirect', 'resa_redirection_apres_login', 10, 3);

// Fonction utilitaire pour calcul du coût estimé (peut être utilisée en PHP)
function resa_calculer_cout_estime($type, $participants)
{
    $tarifs = array(
        'formation' => 30,
        'formation_kids' => 25,
        'atelier' => 20,
        'sensibilisation' => 15
    );

    $tarif = isset($tarifs[$type]) ? $tarifs[$type] : 0;
    return $participants * $tarif;
}

// Activation du plugin
function resa_activation()
{
    resa_creer_evenements(); // Crée les CPT
    flush_rewrite_rules();   // Met à jour les permaliens
}
register_activation_hook(__FILE__, 'resa_activation');


// Fonction utilitaire pour inclure un template
function resa_inclure_template($fichier, $variables = array())
{
    extract($variables); // Transforme les clés du tableau en variables
    include $fichier;
}
