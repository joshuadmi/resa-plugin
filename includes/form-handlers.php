<?php

function traiter_creation_organisateur() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['organisateur_nonce']) || !wp_verify_nonce($_POST['organisateur_nonce'], 'create_organisateur_action')) {
            wp_die('Vérification échouée');
        }

        // Création de l’utilisateur WP
        $username = sanitize_user($_POST['organisateur_username']);
        $password = sanitize_text_field($_POST['organisateur_password']);
        $email    = sanitize_email($_POST['organisateur_email']);
        $user_id  = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            wp_die('Erreur création utilisateur : ' . $user_id->get_error_message());
        }

        // Connexion PDO
        $pdo = get_pdo_connection();
        global $wpdb;
        $table = $wpdb->prefix . 'resa_organisateurs';

        // Insertion via PDO
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
            'contact_fonction'=> sanitize_text_field($_POST['organisateur_contact_fonction']),
            'email'           => $email,
            'tel'             => sanitize_text_field($_POST['organisateur_tel']),
            'lieu_defaut'     => sanitize_text_field($_POST['organisateur_lieu_defaut']),
            'infos'           => sanitize_textarea_field($_POST['organisateur_infos']),
            'user_id'         => $user_id,
        ]);

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
        if (empty($_POST['entite_nom'])) {
            wp_die('Le nom de l’entité est requis.');
        }

        // Connexion PDO
        $pdo = get_pdo_connection();
        global $wpdb;
        $table = $wpdb->prefix . 'resa_evenements';

        // Récupération de l'ID organisateur
        $current_user_id = get_current_user_id();
        $orga_table = $wpdb->prefix . 'resa_organisateurs';

        $stmtOrga = $pdo->prepare("SELECT id FROM $orga_table WHERE user_id = :user_id LIMIT 1");
        $stmtOrga->execute(['user_id' => $current_user_id]);
        $organisateur = $stmtOrga->fetch();

        if (!$organisateur) {
            wp_die("Aucun organisateur associé à cet utilisateur.");
        }

        // Insertion dans la table des événements
        $sql = "INSERT INTO $table 
                (organisateur_id, type_prestation, lieu, date_prestation, nb_participants, cout_estime, infos_complementaires)
                VALUES 
                (:organisateur_id, :type_prestation, :lieu, :date_prestation, :nb_participants, :cout_estime, :infos_complementaires)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'organisateur_id'       => $organisateur->id,
            'type_prestation'       => sanitize_text_field($_POST['type_prestation']),
            'lieu'                  => sanitize_text_field($_POST['lieu_prestation']),
            'date_prestation'       => sanitize_text_field($_POST['date_prestation']),
            'nb_participants'       => intval($_POST['nb_participants']),
            'cout_estime'           => sanitize_text_field($_POST['cout_estime']),
            'infos_complementaires' => sanitize_textarea_field($_POST['infos_complementaires']),
        ]);

        // Redirection
        wp_redirect(home_url('/merci-prestation'));
        exit;
    }
}

add_action('admin_post_nopriv_traiter_evenement', 'traiter_demande_evenement');
add_action('admin_post_traiter_evenement', 'traiter_demande_evenement');


function traiter_evenement_pdo()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Connexion PDO
        require_once plugin_dir_path(__FILE__) . 'pdo-connection.php';
        $pdo = get_pdo_connection();

        // Sécurisation et récupération des champs
        $type_prestation = $_POST['type_prestation'] ?? '';
        $lieu            = $_POST['lieu_prestation'] ?? '';
        $date            = $_POST['date_prestation'] ?? '';
        $participants    = (int) ($_POST['nb_participants'] ?? 0);
        $cout_estime     = $_POST['cout_estime'] ?? '';
        $infos           = $_POST['infos_complementaires'] ?? '';
        $nom_organisateur = $_POST['entite_nom'] ?? '';

        // Récupération de l’ID de l’organisateur en fonction de l'utilisateur connecté
        $current_user_id = get_current_user_id();
        $organisateur_id = null;

        try {
            // Vérification de l'existence d'un organisateur lié à l'utilisateur
            $stmt = $pdo->prepare("SELECT id FROM wp_resa_organisateurs WHERE user_id = ?");
            $stmt->execute([$current_user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                wp_die('Aucun profil organisateur trouvé pour cet utilisateur.');
            }

            $organisateur_id = $result['id'];

            // Insertion de l'événement
            $stmt = $pdo->prepare("
                INSERT INTO wp_resa_evenements 
                (organisateur_id, type_prestation, lieu, date_prestation, nb_participants, cout_estime, infos_complementaires) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $organisateur_id,
                $type_prestation,
                $lieu,
                $date,
                $participants,
                $cout_estime,
                $infos
            ]);

            // Redirection après succès
            wp_redirect(home_url('/merci-prestation'));
            exit;

        } catch (PDOException $e) {
            wp_die('Erreur PDO : ' . $e->getMessage());
        }
    }
}

add_action('admin_post_traiter_evenement', 'traiter_evenement_pdo');
add_action('admin_post_nopriv_traiter_evenement', 'traiter_evenement_pdo');



function traiter_reservation_invite(){
global $wpdb;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $evenement_id = intval($_POST['evenement_id']);
        $nom = sanitize_text_field($_POST['nom_invite']);
        $email = sanitize_email($_POST['email_invite']);
        $places = intval($_POST['places_demandees']);

        $table_name = $wpdb->prefix . 'resa_reservations'; // Assurez-vous que cette table existe

        // Vérification de l'existence de l'événement
        $wpdb->insert ($table_name, array(
            'evenement_id' => $evenement_id,
            'nom_invite' => $nom,
            'email_invite' => $email,
            'places_demandees' => $places,
            'created_at' => current_time('mysql')
        ));
        
        
        wp_redirect(home_url('/merci-reservation'));
        exit;
    }
}
add_action('admin_post_nopriv_traiter_reservation_invite', 'traiter_reservation_invite');
add_action('admin_post_traiter_reservation_invite', 'traiter_reservation_invite');
