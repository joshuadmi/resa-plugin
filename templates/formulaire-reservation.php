<?php

if (!defined('ABSPATH')) exit; ?>


    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <input type="hidden" name="action" value="traiter_reservation_invite">

        <h3>Réserver une place à un événement</h3>

        <p><label>Choisissez un événement :</label><br>
            <select name="evenement_id" required>
                <?php
                $evenements = get_posts(array(
                    'post_type' => 'evenement',
                    'post_status' => 'publish',
                    'numberposts' => -1,
                    'meta_key' => '_resa_organisateur_type',
                    'meta_value' => 'collectivite'
                ));

                $selected_event = isset($_GET['id']) ? intval($_GET['id']) : null;


                foreach ($evenements as $event) {
                    $selected = ($selected_event === $event->ID) ? 'selected' : '';
                    echo '<option value="' . esc_attr($event->ID) . '" ' . $selected . '>' . esc_html($event->post_title) . '</option>';
                }
                
                
                ?>
            </select>
        </p>

        <p><label>Nom :</label><br>
            <input type="text" name="nom_invite" required>
        </p>

        <p><label>Email :</label><br>
            <input type="email" name="email_invite" required>
        </p>

        <p><label>Nombre de participants :</label><br>
            <input type="number" name="places_demandees" min="1" required>
        </p>

        <!-- Tu pourras ajouter un module de paiement ici si besoin -->
        <p><button type="submit" class="bouton">Réserver</button></p>
    </form>
