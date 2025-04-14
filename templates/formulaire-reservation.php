<?php if (!defined('ABSPATH')) exit; ?>

<?php
$evenement_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$evenement_id) {
    echo '<p style="color:red;"><strong>Aucun événement sélectionné.</strong></p>';
    return;
}

// Récupération des infos de l'événement
$titre  = get_the_title($evenement_id);
$date   = get_post_meta($evenement_id, '_resa_date', true);
$lieu   = get_post_meta($evenement_id, '_resa_lieu', true);
$subventionne = get_post_meta($evenement_id, '_resa_subventionne', true);
?>

<h3>Réservation pour : <?php echo esc_html($titre); ?></h3>
<p><strong>Date :</strong> <?php echo esc_html($date); ?></p>
<p><strong>Lieu :</strong> <?php echo esc_html($lieu); ?></p>

<form id="resa-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" target="_self">
    <input type="hidden" name="action" value="traiter_reservation_invite">
    <input type="hidden" name="evenement_id" value="<?php echo esc_attr($evenement_id); ?>">
    <input type="hidden" id="is_subventionne" value="<?php echo esc_attr($subventionne); ?>">

    <p><label>Nom :</label><br>
        <input type="text" name="nom_invite" required>
    </p>

    <p><label>Email :</label><br>
        <input type="email" name="email_invite" required>
    </p>

    <p id="champ-nb">
        <label>Nombre de participants :</label><br>
        <input type="number" name="places_demandees" min="1" value="1">
    </p>

    <div id="form-buttons">
        <button type="submit" class="bouton" id="btn-inscription" style="display:none;">S’inscrire gratuitement</button>

        <a href="#" class="bouton" id="btn-helloasso" style="display:none;" target="_blank">Réserver avec HelloAsso</a>
    </div>

    <?php
    // récupère l'ID de l'événement sélectionné (via GET)
    $evenement_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $places_dispo = ($evenement_id) ? get_post_meta($evenement_id, '_resa_places', true) : '';
    ?>

    <input type="hidden" id="places_dispo" value="<?php echo esc_attr($places_dispo); ?>">


</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const inputPlaces = form.querySelector('input[name="places_demandees"]');
        const placesMax = parseInt(document.getElementById('places_dispo')?.value || 0);

        if (inputPlaces && placesMax > 0) {
            form.addEventListener('submit', function(e) {
                const placesDemandées = parseInt(inputPlaces.value);
                if (placesDemandées > placesMax) {
                    e.preventDefault();
                    alert(`Il ne reste que ${placesMax} place(s) disponible(s). Veuillez ajuster votre demande.`);
                }
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const subventionne = document.getElementById('is_subventionne').value;
        const champNb = document.getElementById('champ-nb');
        const boutonInscription = document.getElementById('btn-inscription');
        const boutonHelloAsso = document.getElementById('btn-helloasso');

        if (subventionne === '1') {
            // Subventionné : inscription simple et une seule place
            boutonInscription.style.display = 'inline-block';
            champNb.style.display = 'none';
        } else {
            // Non subventionné : paiement via HelloAsso avec choix du nombre
            boutonHelloAsso.style.display = 'inline-block';
            champNb.style.display = 'block';
            boutonHelloAsso.href = "https://www.helloasso.com/associations/adess-pays-basque/evenements/" + encodeURIComponent("reservation-" + Date.now());
        }
    });
</script>