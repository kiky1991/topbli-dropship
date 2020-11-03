<p class="form-field form-field-wide wc-dropshipping" style="margin-top:50px">
    <h3><?php esc_html_e('Dropshipping', 'topdrop'); ?></h3>
</p>
<div class="form-dropship-information">
    <p class="form-row woocommerce-form-row form-row-wide validate-required" id="topdrop_name_field" data-priority="">
        <label for="topdrop_name" class="">Name:</label>
        <?php if ($user_id > 0 || (!empty($screen->action && $screen->action === 'add'))) : ?>
            &nbsp;
            <a id="topdrop-load-dropship-data">
                <?php esc_html_e('Load dropship information →', 'topdrop'); ?>
            </a>&nbsp;<span class="topbli-loader"></span>
        <?php endif; ?>
        <span class="woocommerce-input-wrapper">
            <input type="text" class="input-text " name="topdrop_name" id="topdrop_name" placeholder="" value="<?php esc_attr_e($dropship_name, 'topdrop'); ?>"></span>
    </p>
    <p class="form-row woocommerce-form-row form-row-wide" id="topdrop_phone_field" data-priority=""><label for="topdrop_phone" class="">Phone:</label><span class="woocommerce-input-wrapper"><input type="tel" class="input-text " name="topdrop_phone" id="topdrop_phone" placeholder="" value="<?php esc_attr_e($dropship_phone, 'topdrop'); ?>"></span></p>
</div>