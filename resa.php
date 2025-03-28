<?php

// l'en tête du plugin

/**
 * Plugin Name: Resa Plugin
 * Description: Gestion de réservations
 * Version: 1.0
 * Author: Joshua De Moura Idi
 */

// absolute path: chemin absolu. Pour la sécurité, bloque l'accès si quelqu'un tente un accès direct par l'url
if (!defined('ABSPATH')) {
    exit('Non autorisé !');
}

// création ds custom post types - CPT

// CPT evenements
function resa_creer_evenements()
{
    $libelles = array(
        'name' => 'Evénements', // nom affiché dans le menu wordPress
        'singular_name' => 'Événement',
        'menu_name' => 'Événements', // nom affiché dans le menu admin
        'add_new_item'  => 'Nouvel événement', // Bouton dans interface wordPress
    );

    $parametres = array(
        'labels' => $libelles,
        'public' => true,
        'supports' => array('title', 'editor', 'thumbnail') // 'thumbnail' : image à la une
    );

    register_post_type('evenement', $parametres);


    //CPT organisateurs
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

    // méthode pour stocker les profils crées en front end
    register_post_type('organisateur', $parametres_organisateur);
}

// exécute la fonction resa_creer_evenements()
add_action('init', 'resa_creer_evenements');

// Pour les permaliens. 
function resa_activation()
{
    resa_creer_evenements(); // création des CPT 
    flush_rewrite_rules();  // mise à jour des permaliens
}
register_activation_hook(__FILE__, 'resa_activation');


// POur lier avec le fichier style.css
function resa_charger_styles()
{
    wp_enqueue_style(
        'resa-plugin-style',
        plugin_dir_url(__FILE__) . 'style.css',
        array(),
        '1.0'
    );
}


add_action('wp_enqueue_scripts', 'resa_charger_styles'); // hook en charge du style et javascript cote front, si existant


// --- Formulaire frontend : création d'un profil organisateur ---
function afficher_formulaire_organisateur()
{

    // Capture du HTML généré avec ob_start et get_clean
    // utilisation d'un jeton de sécurité avec wp_nonce_field
    ob_start(); ?>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <?php wp_nonce_field('create_organisateur_action', 'organisateur_nonce'); ?>
        <input type="hidden" name="action" value="create_organisateur">

        <p><label>Type d'entité :</label><br>
            <select name="organisateur_type" required>
                <option value="">-- Sélectionnez --</option>
                <option value="collectivite">Collectivité</option>
                <option value="entreprise">Entreprise</option>
            </select>
        </p>
        <p><label>Nom de l’entité :</label><br><input type="text" name="organisateur_nom" required></p>
        <p><label>Adresse postale :</label><br><textarea name="organisateur_adresse" required></textarea></p>
        <p><label>Nom du contact :</label><br><input type="text" name="organisateur_contact_nom" required></p>
        <p><label>Fonction du contact :</label><br><input type="text" name="organisateur_contact_fonction" required></p>
        <p><label>Email du contact :</label><br><input type="email" name="organisateur_email" required></p>
        <p><label>Téléphone du contact :</label><br><input type="tel" name="organisateur_tel" required></p>
        <p><label>Lieu de la prestation par défaut :</label><br><input type="text" name="organisateur_lieu_defaut"></p>
        <p><label>Autres informations :</label><br><textarea name="organisateur_infos"></textarea></p>
        <p><button type="submit" class="bouton">Envoyer la demande</button></p>
    </form>
<?php return ob_get_clean();
}

// création du shortcode pour affichage dans la page 
add_shortcode('organisateur_form', 'afficher_formulaire_organisateur');

// Formulaire de demande d'événement pré-rempli pour un organisateur
function afficher_formulaire_evenement()
{
    if (!is_user_logged_in()) { //vérification de connexion
        return '<p>Vous devez être connecté pour demander un événement.</p>';
    }

    $current_user = wp_get_current_user(); // récupération de l'utilisateur connecté

    // création de tableau avec les critères pour récupérer les données
    $args = array(
        'post_type' => 'organisateur', // récupère le contenu 
        'post_status' => 'publish', // uniquement les post qui ont été publiés
        'posts_per_page' => 1, // un résultat par profil
        'author' => $current_user->ID // récupère l'ID conneté actuellement
    );
    $organisateurs = get_posts($args); // stocke les résultats dans un tableau

    if (empty($organisateurs)) {
        return '<p>Aucun profil organisateur trouvé.</p>';
    }

    $organisateur = $organisateurs[0]; // prend le premier utilisateur trouvé dans le tableau (index 0)

    // récupération des données utilisateur
    $type = get_post_meta($organisateur->ID, 'organisateur_type', true);
    $nom = get_the_title($organisateur);
    $adresse = get_post_meta($organisateur->ID, 'organisateur_adresse', true);
    $contact_nom = get_post_meta($organisateur->ID, 'organisateur_contact_nom', true);
    $fonction = get_post_meta($organisateur->ID, 'organisateur_contact_fonction', true);
    $email = get_post_meta($organisateur->ID, 'organisateur_contact_email', true);
    $tel = get_post_meta($organisateur->ID, 'organisateur_contact_tel', true);
    $lieu = get_post_meta($organisateur->ID, 'organisateur_lieu_defaut', true);

    ob_start(); ?>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <input type="hidden" name="action" value="traiter_evenement">
        <h3>Demande de prestation</h3>
        <p><label>Nom de l'entité :</label><br>
            <input type="text" name="entite_nom" value="<?php echo esc_attr($nom); ?>">
        </p>
        <p><label>Adresse de l'entité :</label><br>
            <textarea name="entite_adresse"><?php echo esc_textarea($adresse); ?></textarea>
        </p>
        <p><label>Nom du contact :</label><br>
            <input type="text" name="contact_nom" value="<?php echo esc_attr($contact_nom); ?>">
        </p>
        <p><label>Fonction :</label><br>
            <input type="text" name="contact_fonction" value="<?php echo esc_attr($fonction); ?>">
        </p>
        <p><label>Email :</label><br>
            <input type="email" name="contact_email" value="<?php echo esc_attr($email); ?>">
        </p>
        <p><label>Téléphone :</label><br>
            <input type="tel" name="contact_tel" value="<?php echo esc_attr($tel); ?>">
        </p>
        <p><label>Type de prestation :</label><br>
            <select name="type_prestation">
                <option value="">-- Sélectionnez --</option>
                <option value="formation">Formation Premiers Secours</option>
                <option value="formation_kids">Formation Premiers Secours Enfants</option>
                <option value="atelier">Atelier Gestes qui Sauve,t</option>
                <option value="sensibilisation">Sensibilisation Cybersécurité</option>
            </select>
        </p>
        <p><label>Adresse de la prestation :</label><br>
            <input type="text" name="lieu_prestation" value="<?php echo esc_attr($lieu); ?>">
        </p>
        <p><label>Nombre de participants :</label><br>
            <input type="number" name="nb_participants" min="1">
        </p>
        <p><label>Informations complémentaires :</label><br>
            <textarea name="infos_complementaires"></textarea>
        </p>
        <p><button type="submit" class="bouton">Soumettre la demande</button></p>
    </form>
<?php
    return ob_get_clean();
}

function traiter_creation_organisateur()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sécurité
        if (!isset($_POST['organisateur_nonce']) || !wp_verify_nonce($_POST['organisateur_nonce'], 'create_organisateur_action')) {
            wp_die('Vérification échouée');
        }

        // Création du post organisateur
        $post_id = wp_insert_post(array(
            'post_type'    => 'organisateur',
            'post_title'   => sanitize_text_field($_POST['organisateur_nom']),
            'post_status'  => 'pending'
        ));

        // Sauvegarde des autres champs si nécessaire
        update_post_meta($post_id, 'organisateur_type', sanitize_text_field($_POST['organisateur_type']));
        update_post_meta($post_id, 'organisateur_adresse', sanitize_textarea_field($_POST['organisateur_adresse']));
        update_post_meta($post_id, 'organisateur_contact_nom', sanitize_text_field($_POST['organisateur_contact_nom']));
        update_post_meta($post_id, 'organisateur_contact_fonction', sanitize_text_field($_POST['organisateur_contact_fonction']));
        update_post_meta($post_id, 'organisateur_email', sanitize_email($_POST['organisateur_email']));
        update_post_meta($post_id, 'organisateur_tel', sanitize_text_field($_POST['organisateur_tel']));
        update_post_meta($post_id, 'organisateur_lieu_defaut', sanitize_text_field($_POST['organisateur_lieu_defaut']));
        update_post_meta($post_id, 'organisateur_infos', sanitize_textarea_field($_POST['organisateur_infos']));

        // Redirection après soumission
        wp_redirect(home_url('/home'));
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

        // Redirection après succès
        wp_redirect(home_url('/home'));
        exit;
    }
}
add_action('admin_post_nopriv_traiter_evenement', 'traiter_demande_evenement');
add_action('admin_post_traiter_evenement', 'traiter_demande_evenement');



//shortcode généré :
add_shortcode('demande_evenement', 'afficher_formulaire_evenement');

function resa_afficher_evenements($atts) // fonction avec attributs du shortcode
{
    $options = shortcode_atts(array( // paramétrer les attributs par défaut
        'nombre' => 5, // Nombre maxi d'affichage
        'ordre' => 'DESC' // ordre d'affichage (évolution à un tri par date)
    ), $atts);

    $requete = new WP_Query(array( // nouvelle classe pour récuperer les événements (possibilité d'évoluer le filtre de la requête)
        'post_type' => 'evenement',
        'posts_per_page' => $options['nombre'],
        'order' => $options['ordre']
    ));

    ob_start();
    $index = 0;

    if ($requete->have_posts()) :
        echo '<div class="liste-evenements">';

        // boucle qui parcourt les résultats
        while ($requete->have_posts()) : $requete->the_post();
            $index++;
            $class = ($index % 2 === 0) ? 'image-right' : 'image-left';

            echo '<article class="evenement ' . $class . '">';
            $image = get_the_post_thumbnail(get_the_ID(), 'medium');

            if ($image) { // permet l'affichage de l'image à la une
                echo '<div class="evenement-image">' . $image . '</div>';
            }

            echo '<div class="evenement-texte">';
            echo '<h2>' . get_the_title() . '</h2>';
            $extrait = get_the_excerpt();
            $date = get_post_meta(get_the_ID(), '_resa_date', true);
            $heure = get_post_meta(get_the_ID(), '_resa_heure', true);
            $lieu = get_post_meta(get_the_ID(), '_resa_lieu', true);
            $places = get_post_meta(get_the_ID(), '_resa_places', true);


            // Les 'if' pour éviter un bug d'affichage si l'élément n'existe pas
            if ($extrait) {
                echo '<p>' . esc_html($extrait) . '</p>';
            }
            if ($date) {
                echo '<p><strong>Date :</strong> ' . esc_html($date) . '</p>';
            }
            if ($heure) {
                echo '<p><strong>Heure :</strong> ' . esc_html($heure) . '</p>';
            }
            if ($lieu) {
                echo '<p><strong>Lieu :</strong> ' . esc_html($lieu) . '</p>';
            }
            if ($places) {
                echo '<p><strong>Places disponibles :</strong> ' . esc_html($places) . '</p>';
            }

            echo '<a href="' . get_permalink() . '" class="bouton">Détails</a>';
            echo '</div>';
            echo '</article>';
        endwhile;
        echo '</div>';
    else :
        echo '<p>Aucun événement à venir.</p>';
    endif;

    wp_reset_postdata(); // Met le post global à ce qu'il était avant 

    return ob_get_clean();
}

//Shortcode
add_shortcode('afficher_evenements', 'resa_afficher_evenements');

// Ajouter les colonnes personnalisées dans l’admin
function resa_ajouter_colonnes_evenement($colonnes)
{
    $colonnes['type_prestation'] = 'Type de prestation';
    $colonnes['nb_participants'] = 'Participants';
    $colonnes['type_organisateur'] = 'Type d\'organisateur';

    return $colonnes;
}
add_filter('manage_evenement_posts_columns', 'resa_ajouter_colonnes_evenement');

// Afficher les données dans les colonnes personnalisées
function resa_afficher_contenu_colonnes_evenement($colonne, $post_id)
{
    if ($colonne === 'type_prestation') {
        $type = get_post_meta($post_id, '_resa_type_prestation', true);
        echo esc_html($type ?: '—');
    }

    if ($colonne === 'nb_participants') {
        $places = get_post_meta($post_id, '_resa_places', true);
        echo esc_html($places ?: '—');
    }
    if ($colonne === 'type_organisateur') {
        $type = get_post_meta($post_id, '_resa_organisateur_type', true);
        echo esc_html($type ?: '—');
    }
}
add_action('manage_evenement_posts_custom_column', 'resa_afficher_contenu_colonnes_evenement', 10, 2);

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

    echo '<p><strong>Type d’entité :</strong> ' . esc_html($type) . '</p>';
    echo '<p><strong>Adresse postale :</strong> ' . esc_html($adresse) . '</p>';
    echo '<p><strong>Nom du contact :</strong> ' . esc_html($contact_nom) . '</p>';
    echo '<p><strong>Fonction :</strong> ' . esc_html($fonction) . '</p>';
    echo '<p><strong>Email :</strong> ' . esc_html($email) . '</p>';
    echo '<p><strong>Téléphone :</strong> ' . esc_html($tel) . '</p>';
    echo '<p><strong>Lieu de la prestation par défaut :</strong> ' . esc_html($lieu) . '</p>';
    echo '<p><strong>Autres informations :</strong><br>' . nl2br(esc_html($infos)) . '</p>';
}
