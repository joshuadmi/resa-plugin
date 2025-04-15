<form method="get" action="">
    <input type="text" name="recherche_evenement" placeholder="Rechercher un événement..." value="<?php echo isset($_GET['recherche_evenement']) ? esc_attr($_GET['recherche_evenement']) : ''; ?>">
    <button type="submit">Rechercher</button>
</form>



<?php


if (!isset($query) || !($query instanceof WP_Query)) {
    echo '<p>Erreur : la requête n’a pas été transmise.</p>';
    return;
}

if ($query->have_posts()) :
    echo '<div class="liste-evenements">';
    $index = 0;
    while ($query->have_posts()) : $query->the_post();
        $index++;
        $class = ($index % 2 === 0) ? 'image-right' : 'image-left';
        $image = get_the_post_thumbnail(get_the_ID(), 'medium');
        $extrait = get_the_excerpt();
        $date = get_post_meta(get_the_ID(), '_resa_date', true);
        $heure = get_post_meta(get_the_ID(), '_resa_heure', true);
        $lieu = get_post_meta(get_the_ID(), '_resa_lieu', true);
        $places = get_post_meta(get_the_ID(), '_resa_places', true);
?>
        <article class="evenement <?php echo esc_attr($class); ?>">
            <?php if ($image) : ?>
                <div class="evenement-image"><?php echo $image; ?></div>
            <?php endif; ?>



            <div class="evenement-texte">
                <h2><?php the_title(); ?></h2>
                <?php if ($extrait) : ?><p><?php echo esc_html($extrait); ?></p><?php endif; ?>
                <?php if ($date) : ?><p><strong>Date :</strong> <?php echo esc_html($date); ?></p><?php endif; ?>
                <?php if ($heure) : ?><p><strong>Heure :</strong> <?php echo esc_html($heure); ?></p><?php endif; ?>
                <?php if ($lieu) : ?><p><strong>Lieu :</strong> <?php echo esc_html($lieu); ?></p><?php endif; ?>
                <?php if ($places) : ?><p><strong>Places disponibles :</strong> <?php echo esc_html($places); ?></p><?php endif; ?>
                <a href="<?php the_permalink(); ?>" class="bouton">Détails</a>
                <?php
                global $wpdb;

                $organisateur_id = get_post_meta(get_the_ID(), '_resa_organisateur_id', true);
                $afficher_bouton = false;

                if ($organisateur_id) {
                    $table = $wpdb->prefix . 'resa_organisateurs';
                    $type = $wpdb->get_var($wpdb->prepare("SELECT type FROM $table WHERE id = %d", $organisateur_id));

                    if ($type === 'collectivite') {
                        $afficher_bouton = true;
                    }
                }

                if ($afficher_bouton) : ?>

                    <a href="<?php echo esc_url(home_url('/reserver-un-evenement?id=' . get_the_ID())); ?>" class="bouton">S'inscrire</a>





                <?php endif; ?>

            </div>
        </article>
<?php endwhile;
    echo '</div>';
else :
    echo '<p>Aucun événement à venir.</p>';
endif;

wp_reset_postdata();
?>