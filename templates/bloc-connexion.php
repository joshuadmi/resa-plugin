<?php
$current_user = wp_get_current_user(); // variable qui stocke l'utilisateur connecté

// Si l'utilisateur est connecté
if (is_user_logged_in()) : ?> 
    <p>Connecté en tant que <strong><?php echo esc_html($current_user->display_name); ?></strong></p>
    <a class="bouton" href="<?php echo esc_url(wp_logout_url(home_url())); ?>">Se déconnecter</a>
    <a class="bouton" href="<?php echo esc_url(home_url('/creation-devenement')); ?>">Demander une prestation</a>
<?php else : ?>
    <div class="bloc-liens">
        <a class="bouton" href="<?php echo esc_url(wp_login_url()); ?>">Se connecter</a>
        <a class="bouton" href="<?php echo esc_url(home_url('/compte-entreprise-organisateur')); ?>">Devenir organisateur</a>
    </div>
<?php endif; ?>
