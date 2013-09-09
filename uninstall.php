<?php

//if uninstall not called from WordPress exit
if (!defined('WP_UNINSTALL_PLUGIN')) exit();

require_once dirname( __FILE__ ) . '/defines.php';

delete_option( CONTENT_SCROLLER_NAV_TYPE_OPTION );
delete_option( CONTENT_SCROLLER_NAV_TRUNCATE_LEN_OPTION );

?>