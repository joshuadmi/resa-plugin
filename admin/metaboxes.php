<?php

// Ajouter une metabox dans l'admin pour les organisateurs
function resa_ajouter_metabox_organisateur()
{
    add_meta_box(
        'organisateur_infos_box',
        'Informations du profil organisateur',
        'resa_afficher_metabox_organisateur',
        'organisateur',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'resa_ajouter_metabox_organisateur');


// Contenu de la metabox
function resa_afficher_metabox_organisateur($post)
{
    // Récupération des champs personnalisés
    $type = get_post_meta($post->ID, 'organisateur_type', true);
    $adresse = get_post_meta($post->ID, 'organisateur_adresse', true);
    $contact_nom = get_post_meta($post->ID, 'organisateur_contact_nom', true);
    $fonction = get_post_meta($post->ID, 'organisateur_contact_fonction', true);
    $email = get_post_meta($post->ID, 'organisateur_email', true);
    $tel = get_post_meta($post->ID, 'organisateur_tel', true);
    $lieu = get_post_meta($post->ID, 'organisateur_lieu_defaut', true);
    $infos = get_post_meta($post->ID, 'organisateur_infos', true);


    // Affichage des champs
    echo '<p><strong>Type d’entité :</strong> ' . esc_html($type) . '</p>';
    echo '<p><strong>Adresse postale :</strong> ' . esc_html($adresse) . '</p>';
    echo '<p><strong>Nom du contact :</strong> ' . esc_html($contact_nom) . '</p>';
    echo '<p><strong>Fonction :</strong> ' . esc_html($fonction) . '</p>';
    echo '<p><strong>Email :</strong> ' . esc_html($email) . '</p>';
    echo '<p><strong>Téléphone :</strong> ' . esc_html($tel) . '</p>';
    echo '<p><strong>Lieu de la prestation par défaut :</strong> ' . esc_html($lieu) . '</p>';
    echo '<p><strong>Autres informations :</strong><br>' . nl2br(esc_html($infos)) . '</p>';
}
