<p class="form-field form-field-wide topdrop-dropshipping">&nbsp;</p>
<h3><?php esc_html_e('Dropshipping', 'topdrop'); ?></h3>
<div class="form-dropship-information">
    <p class="form-field form-field-wide topdrop-name" id="topdrop_name_field" data-priority="">
        <label for="topdrop_name">Name:
            <?php if ($user_id > 0 || (!empty($screen->action && $screen->action === 'add'))) : ?>
                &nbsp;
                <a id="topdrop-load-dropship-data">
                    <?php esc_html_e('Load dropship information →', 'topdrop'); ?>
                </a>&nbsp;<span class="topbli-loader"></span>
            <?php endif; ?>
        </label>
        <span class="woocommerce-input-wrapper">
            <input type="text" class="input-text " name="topdrop_name" id="topdrop_name" placeholder="" value="<?php esc_attr_e($dropship_name, 'topdrop'); ?>"></span>
    </p>
    <p class="form-field form-field-wide topdrop-phone" id="topdrop_phone_field" data-priority="">
        <label for="topdrop_phone" class="">Phone:</label>
        <span class="woocommerce-input-wrapper">
            <input type="tel" class="input-text" name="topdrop_phone" id="topdrop_phone" placeholder="" value="<?php esc_attr_e($dropship_phone, 'topdrop'); ?>">
        </span>
    </p>
</div>