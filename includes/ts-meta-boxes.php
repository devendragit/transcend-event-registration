<?php
function ts_entry_invoice_box_markup() {
    wp_nonce_field( 'ts-entry-meta-box-security', 'ts-entry-invoice-meta-box-nonce' );
    global $post;
    $entry_id = $post->ID;
    $check_entry = get_post_meta($entry_id, 'completed', true);
    $status = get_post_status($entry_id);
    $invoice_due = get_post_meta($entry_id, 'invoice_due', true);
    $invoice_id = get_post_meta($entry_id, 'invoice_id', true);
    $invoice_status = false;
    if($invoice_id) {
        $invoice_status = get_post_status($invoice_id);
    }
    if ( ( $status === 'paid' || $status === 'paidcheck' )  && $check_entry && 'paid' != $invoice_status ) {
        ?>
        <div class="ts-entry-invoice">
            <label for="ts-entry-invoice-amount"><?php _e('Invoice Amount'); ?></label>
            <input name="ts-entry-invoice-amount" type="number" value="" placeholder="$0.00" step=".01">
            <label for="ts-entry-invoice-note"><?php _e('Invoice Note'); ?></label>
            <textarea name="ts-entry-invoice-note" rows="3" cols="50"></textarea>
            <input type="hidden" name="ts_entry_hidden_post_status" value="<?php echo $status;?>">
            <button type="submit" class="button button-large" value="Submit"><?php _e('Create Invoice'); ?></button>
        </div>
        <?php
    } elseif( $invoice_due ) {
        _e('Invoice has been created. Please check the status here! ');
        echo '<a href="'.get_edit_post_link($invoice_id).'">Click here</a>';
    } elseif( $invoice_status !== false && $invoice_status === 'paid' ) {
        _e('Invoice has been paid! ');
    } else {
        _e('Please wait until registration & payment is completed!');
    }
}