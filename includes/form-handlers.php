<?php

function traiter_creation_organisateur()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['organisateur_nonce']) || !wp_verify_nonce($_POST['organisateur_nonce'], 'create_organisateur_action')) {
            wp_die('Vérification échouée');
        }

        $username = sanitize_user($_POST['organisateur_username']);
        $password = sanitize_text_field($_POST['organisateur_password']);
        $email = sanitize_email($_POST['organisateur_email']);

        // Création de l’utilisateur
        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            wp_die('Erreur lors de la création de l’utilisateur : ' . $user_id->get_error_message());
        }

        // Création du post organisateur
        $post_id = wp_insert_post(array(
            'post_type'    => 'organisateur',
            'post_title'   => sanitize_text_field($_POST['organisateur_nom']),
            'post_status'  => 'publish',
            'post_author'  => $user_id
        ));

        // Champs personnalisés
        update_post_meta($post_id, 'organisateur_type', sanitize_text_field($_POST['organisateur_type']));
        update_post_meta($post_id, 'organisateur_adresse', sanitize_textarea_field($_POST['organisateur_adresse']));
        update_post_meta($post_id, 'organisateur_contact_nom', sanitize_text_field($_POST['organisateur_contact_nom']));
        update_post_meta($post_id, 'organisateur_contact_fonction', sanitize_text_field($_POST['organisateur_contact_fonction']));
        update_post_meta($post_id, 'organisateur_email', $email);
        update_post_meta($post_id, 'organisateur_tel', sanitize_text_field($_POST['organisateur_tel']));
        update_post_meta($post_id, 'organisateur_lieu_defaut', sanitize_text_field($_POST['organisateur_lieu_defaut']));
        update_post_meta($post_id, 'organisateur_infos', sanitize_textarea_field($_POST['organisateur_infos']));
        update_post_meta($post_id, 'user_id', $user_id); // lien avec l'utilisateur

        // Redirection
        wp_redirect(home_url('/confirmation-inscription'));
        exit;
    }
}

// traitement des hooks corrspondants
add_action('admin_post_nopriv_create_organisateur', 'traiter_creation_organisateur');
add_action('admin_post_create_organisateur', 'traiter_creation_organisateur');

//fonction pour vérifier les données
function traiter_demande_evenement()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Vérification 
        if (!isset($_POST['entite_nom']) || empty($_POST['entite_nom'])) {
            wp_die('Nom de l’entité manquant.');
        }

        // Création du post de type "evenement"
        $post_id = wp_insert_post(array(
            'post_type'    => 'evenement',
            'post_title'   => sanitize_text_field($_POST['type_prestation']) . ' - ' . sanitize_text_field($_POST['entite_nom']),
            'post_content' => sanitize_textarea_field($_POST['infos_complementaires']),
            'post_status'  => 'pending',
        ));

        // On va retrouver l'organisateur et son type
        $current_user = wp_get_current_user();
        $args = array(
            'post_type' => 'organisateur',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'author' => $current_user->ID
        );
        $organisateurs = get_posts($args);

        if (!empty($organisateurs)) {
            $organisateur = $organisateurs[0];
            $type = get_post_meta($organisateur->ID, 'organisateur_type', true);
            update_post_meta($post_id, '_resa_organisateur_type', sanitize_text_field($type));
        }


        // Gestion d' erreur
        if (is_wp_error($post_id)) {
            wp_die('Erreur lors de la création de l’événement.');
        }



        // Ajout des métadonnées personnalisées
        update_post_meta($post_id, '_resa_lieu', sanitize_text_field($_POST['lieu_prestation']));
        update_post_meta($post_id, '_resa_places', intval($_POST['nb_participants']));
        update_post_meta($post_id, '_resa_organisateur_nom', sanitize_text_field($_POST['entite_nom']));
        update_post_meta($post_id, '_resa_organisateur_email', sanitize_email($_POST['contact_email']));
        update_post_meta($post_id, '_resa_organisateur_tel', sanitize_text_field($_POST['contact_tel']));
        update_post_meta($post_id, '_resa_type_prestation', sanitize_text_field($_POST['type_prestation']));
        update_post_meta($post_id, '_resa_date', sanitize_text_field($_POST['date_prestation']));
        update_post_meta($post_id, '_resa_cout_estime', sanitize_text_field($_POST['cout_estime']));



        // Redirection après succès
        wp_redirect(home_url('/home'));
        exit;
    }
}
add_action('admin_post_nopriv_traiter_evenement', 'traiter_demande_evenement');
add_action('admin_post_traiter_evenement', 'traiter_demande_evenement');

function traiter_reservation_invite()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $evenement_id = intval($_POST['evenement_id']);
        $nom = sanitize_text_field($_POST['nom_invite']);
        $email = sanitize_email($_POST['email_invite']);
        $places = intval($_POST['places_demandees']);

        // Tu peux stocker ça dans un CPT ou dans les métadonnées de l’événement
        $reservation_id = wp_insert_post(array(
            'post_type' => 'reservation',
            'post_title' => $nom . ' - Réservation',
            'post_status' => 'pending',
            'meta_input' => array(
                'evenement_id' => $evenement_id,
                'nom_invite' => $nom,
                'email_invite' => $email,
                'places_demandees' => $places
            )
        ));

        wp_redirect(home_url('/merci-reservation'));
        exit;
    }
}
add_action('admin_post_nopriv_traiter_reservation_invite', 'traiter_reservation_invite');
add_action('admin_post_traiter_reservation_invite', 'traiter_reservation_invite');
