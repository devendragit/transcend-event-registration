<?php
function ts_pay_invoice_html( $entry_id, $evid, $user_id ) {
    ?>
    <div class="ts-registrationform-wrapper ts-admin-wrapper">
        <div class="header clearfix">
            <?php ts_display_invoice_header_html( $entry_id, $evid, $user_id ); ?>
        </div>
        <div class="content clearfix">
            <?php ts_display_invoice_content_html( $entry_id, $evid, $user_id ); ?>
        </div>
    </div>
    <?php
}
