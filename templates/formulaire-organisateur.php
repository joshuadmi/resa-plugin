
    
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
    <!-- Utilisation de nonce  pour plus de sécurité , empêche des envois malveillants-->
        <?php wp_nonce_field('create_organisateur_action', 'organisateur_nonce'); ?>
        <input type="hidden" name="action" value="create_organisateur">

        <p><label>Type d'entité :</label><br>
            <select name="organisateur_type" required>
                
                <!-- choisir entre collectivité ou entreprise -->
                <option value="">-- Sélectionnez --</option>
                <option value="collectivite">Collectivité</option>
                <option value="entreprise">Entreprise</option>
            </select>
        </p>

        <p><label>Nom de l’entité :</label><br>
            <input type="text" name="organisateur_nom" required>
        </p>

        <p><label>Adresse postale :</label><br>
            <textarea name="organisateur_adresse" required></textarea>
        </p>

        <p><label>Nom du contact :</label><br>
            <input type="text" name="organisateur_contact_nom" required>
        </p>

        <p><label>Fonction du contact :</label><br>
            <input type="text" name="organisateur_contact_fonction" required>
        </p>

        <p><label>Email du contact :</label><br>
            <input type="email" name="organisateur_email" required>
        </p>

        <p><label>Téléphone du contact :</label><br>
            <input type="tel" name="organisateur_tel" required>
        </p>

        <p><label>Lieu de la prestation par défaut :</label><br>
            <input type="text" name="organisateur_lieu_defaut">
        </p>

        <p><label>Autres informations :</label><br>
            <textarea name="organisateur_infos"></textarea>
        </p>

        <hr>
        <h4>Informations de connexion</h4>

        <p><label>Nom d'utilisateur souhaité :</label><br>
            <input type="text" name="organisateur_username" required>
        </p>

        <p><label>Mot de passe :</label><br>
            <input type="password" name="organisateur_password" required>
        </p>

        <p>
            <button type="submit" class="bouton">Envoyer la demande</button>
        </p>
    </form>
