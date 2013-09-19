<?php
/*
 * Plugin Name: Fluid Content Scroller
 * Plugin URI: http://orange35.com/plugins
 * Description: Plugin is represented with the tabbed navigation panel at the top and at the bottom of the page allowing to instantly scroll up and down to a particular post on the page making navigation through a long blog post lists smooth and easy.
 * Version: 1.0.0
 * Author: Orange35
 * Author URI: http://orange35.com/
 * License: Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License
 * */

if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define( 'CONTENT_SCROLLER_VERSION', '1.0.0' );
require_once dirname( __FILE__ ) . '/defines.php';

if ( is_admin() ) {
    require_once dirname( __FILE__ ) . '/admin.php';
}

function content_scroller_styles() {
    $base_url = plugin_dir_url(__FILE__);
    wp_enqueue_style( 'fluid-content-scroller', $base_url . 'css/jquery.fluidContentScroller.css', false );
    wp_enqueue_style( 'bootstrap-core', $base_url . 'css/bootstrap.css', false );
    wp_enqueue_style( 'bootstrap-responsive', $base_url . 'css/bootstrap-responsive.css', false );

    wp_enqueue_style( 'html5shiv-ie', $base_url . 'js/html5shiv.js', false );
    wp_style_add_data( 'html5shiv-ie', 'conditional', 'lt IE 9' );
}
add_action( 'wp_enqueue_scripts', 'content_scroller_styles' );

function content_scroller_scripts() {
    $base_url = plugin_dir_url(__FILE__);
    wp_enqueue_script( 'bootstrap-core', $base_url . 'js/bootstrap.js', array( 'jquery-core' ) );
    wp_enqueue_script( 'fluid-content-scroller-core', $base_url . 'js/jquery.fluidContentScroller.js', array( 'jquery-core' ) );
}
add_action( 'wp_enqueue_scripts', 'content_scroller_scripts' );

function content_scroller_head() {
    $nav_type = content_scroller_get_current_nav_type();
    $truncate_len = content_scroller_get_current_nav_truncate_len();

    if ( $nav_type == CONTENT_SCROLLER_NAV_TYPE_DATE ) {
        $titleFunction = '
            function (index, itemNode) {
                var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                var entryDate = $(".entry-date", itemNode).attr("datetime");
                if (entryDate) {
                    entryDate = new Date(entryDate);
                    var formatted = monthNames[entryDate.getMonth()] + " " + entryDate.getDate() + ", " + entryDate.getFullYear();
                    var hour = parseInt(entryDate.getHours());
                    var meridiem = "";
                    if (hour > 12) {
                        hour -= 12;
                        meridiem = "PM";
                    } else {
                        meridiem = "AM";
                    }
                    var minute = "" + entryDate.getMinutes();
                    formatted += " " + hour  + ":" + (minute.length == 1 ? "0" : "") + minute + " " + meridiem;
                    return formatted;
                }
                return null;
            };
        ';
    } else if ( $nav_type == CONTENT_SCROLLER_NAV_TYPE_TITLE ) {
        $titleFunction = '
            function (index, itemNode) {
                var entryTitle = $(".entry-title", itemNode).html();
                entryTitle = entryTitle.replace(/<\/?([a-z][a-z0-9]*)\b[^>]*>/gi, "");
                entryTitle = entryTitle.replace(/(^\s+)|(\s+$)/g, "");
                if (entryTitle) {
                    if (' . $truncate_len . ' >= entryTitle.length) {
                        return entryTitle;
                    } else {
                        var len = entryTitle.substring(' . $truncate_len . ').search(/\W/);
                        len = (len >= 0) ? (len + ' . $truncate_len . ') : len;
                        return entryTitle.substring(0, len) + "...";
                    }
                }
                return null;
            };
        ';
    } else {
        $titleFunction = '
            function (index, itemNode) {
                return null;
            };
        ';
    }

    $initFunction = '
        function (target, top, bottom) {
            if (!isMobile) {
                var tipOptions = {
                    title: tipCallback,
                    container: "body",
                    placement: null,
                    animation: false
                };

                tipOptions.placement = "bottom";
                top.find("li").tooltip(tipOptions);

                tipOptions.placement = "top";
                bottom.find("li").tooltip(tipOptions);
            }
        };
    ';

    $script = '
                var animating = false;
                var isMobile = new RegExp("(Android)|(iPod)|(iPad)|(iPhone)", "i").test(navigator.userAgent);

                var tipCallback = function () {
                    return (animating) ? null : $(".entry-title", $(this).data("csTarget")).text();
                };

                var scrollerOptions = {};

                if ($("#wpadminbar").length) {
                    if (scrollerOptions.nav == undefined) {
                        scrollerOptions.nav = {};
                    }
                    scrollerOptions.nav["topClass"] = "cs-top-wpadmin";
                }

                if (scrollerOptions.navItem == undefined) {
                    scrollerOptions.navItem = {};
                }
                scrollerOptions.navItem["title"] = ' . $titleFunction . '
                scrollerOptions.navItem["onBeforeClick"] = function (link) {
                    animating = true;
                    if (!isMobile) {
                        link.tooltip({animation: false});
                        link.tooltip("hide");
                        link.tooltip({});
                    }
                };

                scrollerOptions.navItem["onAfterClick"] = function (link) {
                    setTimeout(function () { animating = false; }, 500);
                };

                scrollerOptions.onInit = ' . $initFunction . '

                $("#content > .post").fluidContentScroller(scrollerOptions);
    ';

    $script = '<script type="text/javascript">jQuery(function ($) { ' . $script . ' });</script>';

    echo $script;
}
add_action( 'wp_head', 'content_scroller_head' );

?>