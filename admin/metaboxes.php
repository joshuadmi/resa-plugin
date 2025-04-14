<?php

//  METABOX POUR LE PROFIL ORGANISATEUR (lecture seule) 
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

    // Affichage formaté dans l’admin
    echo '<p><strong>Type d’entité :</strong> ' . esc_html($type) . '</p>';
    echo '<p><strong>Adresse :</strong> ' . esc_html($adresse) . '</p>';
    echo '<p><strong>Contact :</strong> ' . esc_html($contact_nom) . '</p>';
    echo '<p><strong>Fonction :</strong> ' . esc_html($fonction) . '</p>';
    echo '<p><strong>Email :</strong> ' . esc_html($email) . '</p>';
    echo '<p><strong>Téléphone :</strong> ' . esc_html($tel) . '</p>';
    echo '<p><strong>Lieu par défaut :</strong> ' . esc_html($lieu) . '</p>';
    echo '<p><strong>Infos :</strong><br>' . nl2br(esc_html($infos)) . '</p>';
}


// === METABOX POUR LES ÉVÉNEMENTS ===

function resa_ajouter_metabox_evenement()
{
    add_meta_box(
        'infos_evenement_box',
        'Informations de l\'événement',
        'resa_afficher_metabox_evenement',
        'evenement',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'resa_ajouter_metabox_evenement');

function resa_afficher_metabox_evenement($post)
{
    // Récupération des champs existants
    $lieu = get_post_meta($post->ID, '_resa_lieu', true);
    $date = get_post_meta($post->ID, '_resa_date', true);
    $nb_participants = get_post_meta($post->ID, '_resa_places', true);
    $cout = get_post_meta($post->ID, '_resa_cout_estime', true);
    $subventionne = get_post_meta($post->ID, '_resa_subventionne', true);
    $organisateur_id = get_post_meta($post->ID, '_resa_organisateur_id', true);

    global $wpdb;
    $organisateurs = $wpdb->get_results("SELECT id, nom FROM {$wpdb->prefix}resa_organisateurs");

?>

    <p><label><strong>Lieu :</strong><br>
            <input type="text" name="resa_lieu" value="<?php echo esc_attr($lieu); ?>" style="width:100%;">
        </label></p>

        <?php
$type_prestation = get_post_meta($post->ID, '_resa_type_prestation', true);
?>

<p><label><strong>Type de prestation :</strong><br>
    <select name="resa_type_prestation">
        <option value="">-- Sélectionnez --</option>
        <option value="formation" <?php selected($type_prestation, 'formation'); ?>>Formation Premiers Secours</option>
        <option value="formation_kids" <?php selected($type_prestation, 'formation_kids'); ?>>Formation Enfants</option>
        <option value="atelier" <?php selected($type_prestation, 'atelier'); ?>>Atelier Gestes</option>
        <option value="sensibilisation" <?php selected($type_prestation, 'sensibilisation'); ?>>Sensibilisation</option>
    </select>
</label></p>


    <p><label><strong>Date :</strong><br>
            <input type="date" name="resa_date" value="<?php echo esc_attr($date); ?>">
        </label></p>


    <p><label><strong>Nombre de participants :</strong><br>
            <input type="number" name="resa_places" value="<?php echo esc_attr($nb_participants); ?>">
        </label></p>

    <p><label><strong>Coût estimé :</strong><br>
            <input type="text" name="resa_cout_estime" value="<?php echo esc_attr($cout); ?>">
        </label></p>

    <p>
        <label><input type="checkbox" name="resa_subventionne" value="1" <?php checked($subventionne, '1'); ?>>
            Événement subventionné
        </label>
    </p>

    <?php if (!empty($organisateurs)) : ?>
        <p><label><strong>Organisateur associé :</strong><br>
                <select name="resa_organisateur_id">
                    <option value="">-- Aucun --</option>
                    <?php foreach ($organisateurs as $orga) : ?>
                        <option value="<?php echo esc_attr($orga->id); ?>" <?php selected($orga->id, $organisateur_id); ?>>
                            <?php echo esc_html($orga->nom); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label></p>
<?php endif;
}


// === SAUVEGARDE DES CHAMPS DE LA METABOX ===

function resa_sauvegarder_infos_evenement($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (get_post_type($post_id) !== 'evenement') return;

    // Sauvegarde simple des champs
    if (isset($_POST['resa_lieu'])) update_post_meta($post_id, '_resa_lieu', sanitize_text_field($_POST['resa_lieu']));
    if (isset($_POST['resa_date'])) update_post_meta($post_id, '_resa_date', sanitize_text_field($_POST['resa_date']));
    if (isset($_POST['resa_places'])) update_post_meta($post_id, '_resa_places', intval($_POST['resa_places']));
    if (isset($_POST['resa_cout_estime'])) update_post_meta($post_id, '_resa_cout_estime', sanitize_text_field($_POST['resa_cout_estime']));
    if (isset($_POST['resa_organisateur_id'])) update_post_meta($post_id, '_resa_organisateur_id', intval($_POST['resa_organisateur_id']));
    if (isset($_POST['resa_type_prestation'])) {
        update_post_meta($post_id, '_resa_type_prestation', sanitize_text_field($_POST['resa_type_prestation']));
    }
    
    // Checkbox : 1 si cochée, 0 sinon
    $is_checked = isset($_POST['resa_subventionne']) ? '1' : '0';
    update_post_meta($post_id, '_resa_subventionne', $is_checked);
}
add_action('save_post', 'resa_sauvegarder_infos_evenement');
