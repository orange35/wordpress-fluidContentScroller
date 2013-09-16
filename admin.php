<?php

function content_scroller_plugin_action_links( $links, $file ) {
    if ( $file == plugin_basename( dirname(__FILE__).'/fluid-content-scroller.php' ) ) {
        $links[] = '<a href="' . admin_url( 'admin.php?page=fluid-content-scroller-config' ) . '">'.__( 'Settings' ).'</a>';
    }
    return $links;
}
add_filter( 'plugin_action_links', 'content_scroller_plugin_action_links', 10, 2 );

function content_scroller_admin_menu() {
    if ( class_exists( 'Jetpack' ) ) {
        add_action( 'jetpack_admin_menu', 'content_scroller_load_menu' );
    } else {
        content_scroller_load_menu();
    }
}
add_action( 'admin_menu', 'content_scroller_admin_menu' );

function content_scroller_load_menu() {
    if ( class_exists( 'Jetpack' ) ) {
        add_submenu_page( 'jetpack', __( 'Fluid Content Scroller' ), __( 'Fluid Content Scroller' ), 'manage_options', 'fluid-content-scroller-config', 'fluid_content_scroller_conf' );
    } else {
        add_submenu_page('plugins.php', __('Fluid Content Scroller'), __('Fluid Content Scroller'), 'manage_options', 'fluid-content-scroller-config', 'fluid_content_scroller_conf');
    }
}

function content_scroller_conf() {
    $saved_ok = false;
    $ms = array(); // messages list

    if ( isset( $_POST['submit'] ) ) {

        if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
            die(__('Cheatin&#8217; uh?'));
        }

        $nav_type = $_POST[CONTENT_SCROLLER_NAV_TYPE_OPTION];
        $truncate_len = (int) $_POST[CONTENT_SCROLLER_NAV_TRUNCATE_LEN_OPTION];

        if ( ! array_key_exists( $nav_type, content_scroller_get_nav_types() ) ) {
            $ms[] = 'wrong_nav_type';
        }

        if (!$truncate_len || 0 > $truncate_len) {
            $ms[] = 'wrong_nav_truncate_len';
        }

        if ( empty( $ms ) ) {
            update_option( CONTENT_SCROLLER_NAV_TYPE_OPTION, $nav_type );
            update_option( CONTENT_SCROLLER_NAV_TRUNCATE_LEN_OPTION, $truncate_len);
            $saved_ok = true;
        }
    }

    $messages = array(
        'wrong_nav_type'         => array( 'class' => 'error', 'text' => __('"Navigation Type" is wrong.' ) ),
        'wrong_nav_truncate_len' => array( 'class' => 'error', 'text' => __('"Title Max Length" is wrong.' ) ),
    );

?>

    <div class="wrap">
        <?php screen_icon(); ?>
        <h2><?php _e('Content Scroller'); ?></h2>
        <hr />
        <p>You are currently using Fluid Content Scroller Plugin version <?php esc_html_e( CONTENT_SCROLLER_VERSION ); ?></p>
        <div class="have-key">
            <?php if ( !empty($_POST['submit'] ) && $saved_ok ) : ?>
                <div id="message" class="updated fade"><p><strong><?php _e('Settings saved.') ?></strong></p></div>
            <?php endif; ?>
            <?php foreach( $ms as $m ) : ?>
                <div class="<?php echo $messages[$m]['class']; ?>"><p><strong><?php echo $messages[$m]['text']; ?></strong></p></div>
            <?php endforeach; ?>
            <form action="" method="post" id="content-scroller-conf">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th><?php _e('Navigation Type');?></th>
                        <td>
                            <?php foreach ( content_scroller_get_nav_types() as $type => $title ) : ?>
                                <input id="<?php echo CONTENT_SCROLLER_NAV_TYPE_OPTION; ?>_<?php echo $type; ?>" name="<?php echo CONTENT_SCROLLER_NAV_TYPE_OPTION; ?>" value="<?php echo $type; ?>" type="radio" <?php if ( $type == content_scroller_get_current_nav_type() ) echo 'checked="checked"'; ?> />
                                <label for="<?php echo CONTENT_SCROLLER_NAV_TYPE_OPTION; ?>_<?php echo $type; ?>"><?php esc_html_e( $title ); ?></label>
                                <br />
                            <?php endforeach; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="<?php echo CONTENT_SCROLLER_NAV_TRUNCATE_LEN_OPTION; ?>"><?php _e('Title Max Length');?></label></th>
                        <td>
                            <input id="<?php echo CONTENT_SCROLLER_NAV_TRUNCATE_LEN_OPTION; ?>" name="<?php echo CONTENT_SCROLLER_NAV_TRUNCATE_LEN_OPTION; ?>" value="<?php echo content_scroller_get_current_nav_truncate_len(); ?>" type="text" maxlength="3" size="4" />
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
    </div>

<?php
}

?>