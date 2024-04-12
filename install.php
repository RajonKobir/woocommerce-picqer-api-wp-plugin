<?php

// initializing options useful for cron
if ( !get_option('picqer_cron_list') ) {
    add_option( 'picqer_cron_list', [] );
}

if ( !get_option('picqer_products_sku_list') ) {
    add_option( 'picqer_products_sku_list', [] );
}

if ( !get_option('picqer_sku_next_to_update') ) {
    add_option( 'picqer_sku_next_to_update', '' );
}