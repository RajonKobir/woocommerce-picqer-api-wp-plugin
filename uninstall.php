<?php

// uninstalling the cron
if ( wp_next_scheduled( 'picqer_cron_event' ) ) {
    wp_clear_scheduled_hook( 'picqer_cron_event' );
}