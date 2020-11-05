<form method="post" id="topdrop-form-dropship">
    <h3><?php esc_html_e('Dropship Information', 'topdrop'); ?></h3>
    <?php $form->get_fields('form_dropship_information'); ?>

    <div class="clearfix"></div>
    <h3><?php esc_html_e('Dropship Setting', 'topdrop'); ?></h3>
    <?php $form->get_fields('form_dropship_setting'); ?>

    <p class="submit">
        <?php wp_nonce_field('topdrop_save_dropship', 'topdrop_save_dropship_nonce'); ?>
        <input type="submit" name="topdrop_submit_dropship" class="woocommerce-button button" value="<?php esc_attr_e('Save', 'topdrop'); ?>">
        <input type="hidden" name="action" value="topdrop_save_dropship" />
    </p>
</form>

<script type="text/javascript">
    jQuery(function($) {
        $(document).on('ready', function() {
            $('#topdrop_auto_field label > .optional').remove();
        });
    });
</script>