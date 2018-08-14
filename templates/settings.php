<div class="wrap">
    <h2>VaultRE Contact Form 7</h2>
    <form method="post" action="options.php"> 
        <?php @settings_fields('vaultre_contact_enquiries-group'); ?>

        <?php @do_settings_fields('vaultre_contact_enquiries-group', 'vaultre_contact_enquiries'); ?>

        <?php do_settings_sections('vaultre_contact_enquiries'); ?>

        <?php @submit_button(); ?>
    </form>
</div>
