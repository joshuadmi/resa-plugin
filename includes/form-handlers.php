<?php

function traiter_creation_organisateur()
{


    if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Vérification de la méthode POST
        // Vérification du nonce pour la sécurité
        if (!isset($_POST['organisateur_nonce']) || !wp_verify_nonce($_POST['organisateur_nonce'], 'create_organisateur_action')) {
            wp_die('Vérification échouée');
        }

        // Création de l’utilisateur WordPress en nettoyant les données
        $username = sanitize_user($_POST['organisateur_username']);
        $password = sanitize_text_field($_POST['organisateur_password']);
        $email    = sanitize_email($_POST['organisateur_email']);
        $user_id  = wp_create_user($username, $password, $email);

        //Si la création échoue, la fonction s'arrête avec un message d'erreur.
        if (is_wp_error($user_id)) {
            wp_die('Erreur lors de la création de l’utilisateur : ' . $user_id->get_error_message());
        }

        // Connexion PDO
        $pdo = get_pdo_connection();
        global $wpdb;
        // Utilisation du préfixe WordPress pour former le nom complet de la table
        $table = $wpdb->prefix . 'resa_organisateurs'; // assure que le nom de la table est correct

        // Insertion dans la table personnalisée avec PDO dans un bloc try-catch
        try {
            $sql = "INSERT INTO $table 
                    (type, nom, adresse, contact_nom, contact_fonction, email, tel, lieu_defaut, infos, user_id)
                    VALUES 
                    (:type, :nom, :adresse, :contact_nom, :contact_fonction, :email, :tel, :lieu_defaut, :infos, :user_id)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'type'            => sanitize_text_field($_POST['organisateur_type']),
                'nom'             => sanitize_text_field($_POST['organisateur_nom']),
                'adresse'         => sanitize_textarea_field($_POST['organisateur_adresse']),
                'contact_nom'     => sanitize_text_field($_POST['organisateur_contact_nom']),
                'contact_fonction' => sanitize_text_field($_POST['organisateur_contact_fonction']),
                'email'           => $email,
                'tel'             => sanitize_text_field($_POST['organisateur_tel']),
                'lieu_defaut'     => sanitize_text_field($_POST['organisateur_lieu_defaut']),
                'infos'           => sanitize_textarea_field($_POST['organisateur_infos']),
                'user_id'         => $user_id,
            ]);
        } catch (PDOException $e) { //
            wp_die('Erreur lors de l\'insertion dans la base de données : ' . $e->getMessage());
        }

        // Redirection après une insertion réussie
        wp_redirect(home_url('/confirmation-inscription'));
        exit;
    }
}


// traitement des hooks corrspondants
add_action('admin_post_nopriv_create_organisateur', 'traiter_creation_organisateur');
add_action('admin_post_create_organisateur', 'traiter_creation_organisateur');


function traiter_evenement_pdo()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        wp_die("Méthode non autorisée.");
    }

    // Connexion PDO
    require_once plugin_dir_path(__FILE__) . 'pdo-connection.php';
    $pdo = get_pdo_connection();

    // Utilisation du préfixe WordPress pour composer le nom des tables
    global $wpdb;
    $table_evenements     = $wpdb->prefix . 'resa_evenements';
    $table_organisateurs  = $wpdb->prefix . 'resa_organisateurs';

    // Récupération et nettoyage des champs du formulaire
    $type_prestation   = sanitize_text_field($_POST['type_prestation'] ?? '');
    $lieu              = sanitize_text_field($_POST['lieu_prestation'] ?? '');
    $date              = sanitize_text_field($_POST['date_prestation'] ?? '');
    $nb_participants   = intval($_POST['nb_participants'] ?? 0);
    $cout_estime       = sanitize_text_field($_POST['cout_estime'] ?? '');
    $infos             = sanitize_textarea_field($_POST['infos_complementaires'] ?? '');
    // Le nom de l'organisateur peut être utilisé pour affichage, mais pour l'insertion, on va récupérer l'ID dans la table des organisateurs.

    // Récupération de l’ID de l’organisateur lié à l’utilisateur connecté
    $current_user_id = get_current_user_id();

    try {
        // On récupère l'organisateur depuis la table personnalisée
        $stmt = $pdo->prepare("SELECT id FROM $table_organisateurs WHERE user_id = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $current_user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            wp_die("Aucun profil organisateur trouvé pour cet utilisateur.");
        }

        $organisateur_id = $result['id'];

        // Préparation de la requête d'insertion dans la table des événements
        $sql = "INSERT INTO $table_evenements 
                (organisateur_id, type_prestation, lieu, date_prestation, nb_participants, cout_estime, infos_complementaires) 
                VALUES 
                (:organisateur_id, :type_prestation, :lieu, :date_prestation, :nb_participants, :cout_estime, :infos_complementaires)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'organisateur_id'       => $organisateur_id,
            'type_prestation'       => $type_prestation,
            'lieu'                  => $lieu,
            'date_prestation'       => $date,
            'nb_participants'       => $nb_participants,
            'cout_estime'           => $cout_estime,
            'infos_complementaires' => $infos,
        ]);

        // Redirection après succès
        wp_redirect(home_url('/merci-prestation'));
        exit;
    } catch (PDOException $e) {
        wp_die("Erreur PDO : " . $e->getMessage());
    }
}


add_action('admin_post_traiter_evenement', 'traiter_evenement_pdo');
add_action('admin_post_nopriv_traiter_evenement', 'traiter_evenement_pdo');



function traiter_reservation_invite_pdo()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Inclure la connexion PDO (assure-toi que ce fichier existe et est correct)
        require_once plugin_dir_path(__FILE__) . 'pdo-connection.php';
        $pdo = get_pdo_connection();

        global $wpdb;
        // On utilise $wpdb->prefix pour être sûr d'avoir le bon préfixe, même en mode PDO
        $table = $wpdb->prefix . 'resa_reservations';

        // Récupération et nettoyage des données du formulaire
        $evenement_id   = intval($_POST['evenement_id'] ?? 0);
        $nom_invite     = sanitize_text_field($_POST['nom_invite'] ?? '');
        $email_invite   = sanitize_email($_POST['email_invite'] ?? '');
        $places_demandees = intval($_POST['places_demandees'] ?? 0);
        $created_at     = current_time('mysql');



        try {
            $sql = "INSERT INTO $table 
                    (evenement_id, nom_invite, email_invite, places_demandees, created_at)
                    VALUES 
                    (:evenement_id, :nom_invite, :email_invite, :places_demandees, :created_at)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'evenement_id'    => $evenement_id,
                'nom_invite'      => $nom_invite,
                'email_invite'    => $email_invite,
                'places_demandees' => $places_demandees,
                'created_at'      => $created_at,
            ]);
            // Mise à jour du nombre de places disponibles
$current_places = get_post_meta($evenement_id, '_resa_places', true);

if ($current_places !== '') {
    $new_places = max(0, intval($current_places) - intval($places_demandees));
    update_post_meta($evenement_id, '_resa_places', $new_places);
}


            wp_redirect(home_url('/merci-reservation'));
            exit;
        } catch (PDOException $e) {
            wp_die("Erreur PDO : " . $e->getMessage());
        }
    }

    if ($subventionne === '1') {
        wp_redirect(home_url('/merci-reservation'));
    } else {
        wp_redirect('https://www.helloasso.com/mon-lien-de-paiement');
    }
    exit;
    
}
add_action('admin_post_traiter_reservation_invite', 'traiter_reservation_invite_pdo');
add_action('admin_post_nopriv_traiter_reservation_invite', 'traiter_reservation_invite_pdo');
