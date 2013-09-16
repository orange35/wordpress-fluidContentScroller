<?php

define( 'CONTENT_SCROLLER_NAV_TYPE_NUMBER', 'number' );
define( 'CONTENT_SCROLLER_NAV_TYPE_DATE', 'date' );
define( 'CONTENT_SCROLLER_NAV_TYPE_TITLE', 'title' );

define( 'CONTENT_SCROLLER_NAV_TYPE_OPTION', 'content_scroller_nav_type' );
define( 'CONTENT_SCROLLER_NAV_TRUNCATE_LEN_OPTION', 'content_scroller_nav_truncate_len' );

define( 'CONTENT_SCROLLER_NAV_TYPE_DEFAULT', CONTENT_SCROLLER_NAV_TYPE_NUMBER );
define( 'CONTENT_SCROLLER_NAV_TRUNCATE_LEN_DEFAULT', 30 );


function content_scroller_get_current_nav_type() {
    $type = get_option( CONTENT_SCROLLER_NAV_TYPE_OPTION );
    if ( ! $type ||  ! array_key_exists( $type, content_scroller_get_nav_types() ) ) {
        $type = null;
    }
    return ( $type ) ? $type : CONTENT_SCROLLER_NAV_TYPE_DEFAULT;
}

function content_scroller_get_current_nav_truncate_len() {
    $len = (int) get_option( CONTENT_SCROLLER_NAV_TRUNCATE_LEN_OPTION );
    if ( ! $len ) {
        $len = CONTENT_SCROLLER_NAV_TRUNCATE_LEN_DEFAULT;
    }
    return $len;
}

function content_scroller_get_nav_types() {
    return array(
        CONTENT_SCROLLER_NAV_TYPE_NUMBER => 'Post Count',
        CONTENT_SCROLLER_NAV_TYPE_DATE   => 'Post Date',
        CONTENT_SCROLLER_NAV_TYPE_TITLE  => 'Post Title',
    );
}

?>