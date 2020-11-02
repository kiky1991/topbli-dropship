<?php

/**
 * Template: Admin user profile
 *
 * @since   1.0.0
 * @version 1.0.0
 */

do_action('before_dropship_information_table');
?>
<h2><?php esc_html_e('Dropship Information', 'brandplus'); ?></h2>
<table class="form-table">
    <tr>
        <th><label for="topdrop-dropship-name"><?php esc_html_e('Name', 'brandplus'); ?></label></th>
        <td>
            <input type="text" id="topdrop-dropship-name" name="topdrop_dropship_name" value="<?php echo esc_attr($dropship_name); ?>" class="regular-text" />
        </td>
    </tr>
    <tr>
        <th><label for="topdrop-dropship-phone"><?php esc_html_e('Phone', 'brandplus'); ?></label></th>
        <td>
            <input type="text" id="topdrop-dropship-phone" name="topdrop_dropship_phone" value="<?php echo esc_attr($dropship_phone); ?>" class="regular-text" />
        </td>
    </tr>
</table>

<h2><?php esc_html_e('Dropship Settings', 'brandplus'); ?></h2>
<table class="form-table">
    <tr>
        <th><label for="topdrop-dropship-auto"><?php esc_html_e('Auto Dropship', 'brandplus'); ?></label></th>
        <td>
            <label for="topdrop-dropship-auto">
                <input name="topdrop_dropship_auto" type="checkbox" id="topdrop-dropship-auto" value="yes" <?php echo ($dropship_auto === 'yes') ? 'checked' : ''; ?>>
                Dropship Auto
            </label>
            <p class="description">Enable or disable auto dropship</p>
        </td>
    </tr>
</table>

<?php do_action('after_dropship_information_table'); ?>