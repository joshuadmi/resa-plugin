
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
                <option value="atelier">Atelier Gestes qui Sauvent</option>
                <option value="sensibilisation">Sensibilisation Cybersécurité</option>
            </select>
        </p>

        <p><label>Adresse de la prestation :</label><br>
            <input type="text" name="lieu_prestation" value="<?php echo esc_attr($lieu); ?>">
        </p>

        <p><label>Date de la prestation :</label><br>
            <input type="date" name="date_prestation">
        </p>

        <p><label>Nombre de participants :</label><br>
            <input type="number" name="nb_participants" min="10">
        </p>

        <p>
            <label>Coût estimé :</label>
            <small>(Calculé automatiquement en fonction du type de prestation et du nombre de participants)</small><br>
            <input type="text" id="cout_estime" name="cout_estime" readonly>
        </p>

        <p><label>Informations complémentaires :</label><br>
            <textarea name="infos_complementaires"></textarea>
        </p>

        <p><button type="submit" class="bouton">Soumettre la demande</button></p>

        <?php if ($type === 'entreprise') : ?>
            <p><em>En tant qu’entreprise, cette prestation vous sera facturée. Un devis vous sera envoyé.</em></p>
        <?php elseif ($type === 'collectivite') : ?>
            <p><em>En tant que collectivité, la prestation pourra être gratuite selon les subventions. Une vérification sera faite.</em></p>
        <?php endif; ?>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const typeSelect = document.querySelector('select[name="type_prestation"]');
                const participantsInput = document.querySelector('input[name="nb_participants"]');
                const coutField = document.getElementById('cout_estime');

                function calculerCout() {
                    const type = typeSelect.value;
                    const participants = parseInt(participantsInput.value) || 0;
                    let tarifUnitaire = 0;

                    switch (type) {
                        case 'formation':
                            tarifUnitaire = 30;
                            break;
                        case 'formation_kids':
                            tarifUnitaire = 25;
                            break;
                        case 'atelier':
                            tarifUnitaire = 20;
                            break;
                        case 'sensibilisation':
                            tarifUnitaire = 15;
                            break;
                    }

                    const total = participants * tarifUnitaire;
                    coutField.value = (type && participants) ? total + ' €' : '';
                }

                typeSelect.addEventListener('change', calculerCout);
                participantsInput.addEventListener('input', calculerCout);
            });
        </script>
    </form>

