<?php
/*
Plugin Name: Popup Ads Management
Plugin URI: http://microsolutionsbd.com/
Description: Publish advertisement on Popup by specific category of Post.
Version: 0.0.4
Author: Micro Solutions Bangladesh
Author URI: http://microsolutionsbd.com/
Text Domain: msbd-popadsm
License: GPL2
*/

define('MSBD_POPADSM_URL', trailingslashit(plugins_url(basename(dirname(__FILE__)))));

class PopupAdsManagement {
    
    var $version = '0.0.4';
    var $plugin_name = 'Popup Ads Management';
    
    var $plugin_post_type = "popup-ads";
    var $plugin_post_type_title = "Popup Ads";
    var $plugin_post_type_title_single = "Popup Ad";


    /**
     * @var msbd_adsmp_options_obj
     */
    var $popadsm_options_obj;
    
    

    /**
     * The variable that stores all current options
     */
    var $popadsm_options;
    

    function __construct() {
        
        global $wpdb;
                
        $this->popadsm_options_obj = new MsbdPopAdOptions($this);
        
        $this->admin = new MsbdPopAdsAdmin($this);
        
        add_action('init', array(&$this, 'init'));
        
        add_action( 'add_meta_boxes', array(&$this, 'msbd_popadsm_metaboxes') );
        add_action( 'save_post', array(&$this, 'msbd_popadsm_save_meta_box_data') );
        
        add_action('wp_enqueue_scripts', array(&$this, 'load_scripts_styles'), 100);
        
        add_action('wp_footer', array( &$this, 'popadsm_show_ad'), 19);
    }




    function popadsm_show_ad() { 
        global $post, $wpdb;
        
        if( is_page() ) {
            return '';
        }
        
        if( is_home() ) {
            
            $args = array( 
                'post_type'             => $this->plugin_post_type, 
                'posts_per_page'    => 1,
                'orderby'               => 'rand', 
                //'order'                 => 'rand', // comment_count, rand, ASC, DESC, meta_value (Note that a 'meta_key=keyname' must also be present in the query.), meta_value_num

                'meta_query' => array(
                    array(
                        'key'     => 'msbd_popadsm_home_page',
                        'value'   => array( 'yes' ),
                        'compare' => 'IN',
                    ),
                ),
             );
            
            echo $this->create_popup_html($args);
            
            
        } else if( is_category() ) {
            
            $single_cat = msbd_get_category_id('term_id');

            $args = array( 
                'post_type'             => $this->plugin_post_type, 
                'posts_per_page'    => 1,
                'orderby'               => 'rand', 
                //'order'                 => 'rand', // comment_count, rand, ASC, DESC, meta_value (Note that a 'meta_key=keyname' must also be present in the query.), meta_value_num
                'cat'                   => $single_cat,
             );
             
            echo $this->create_popup_html($args);
            
            
        } else if( is_single() ) {
            
            $var_categories = wp_get_post_categories( $post->ID );
            //$csv_cat = implode(",", $cat_ids);
            
            $args = array( 
                'post_type'             => $this->plugin_post_type, 
                'posts_per_page'    => 1,
                'orderby'               => 'rand', 
                //'order'                 => 'rand', // comment_count, rand, ASC, DESC, meta_value (Note that a 'meta_key=keyname' must also be present in the query.), meta_value_num
                'category__in' => $var_categories,
             );
             
            echo $this->create_popup_html($args);
            
            
        } else {
            
            $args = array( 
                'post_type'             => $this->plugin_post_type, 
                'posts_per_page'    => 1,
                'orderby'               => 'ID', 
                'order'                 => 'rand', // comment_count, rand, ASC, DESC, meta_value (Note that a 'meta_key=keyname' must also be present in the query.), meta_value_num

                'meta_query' => array(
                    array(
                        'key'     => 'msbd_popadsm_other_page',
                        'value'   => array( 'yes' ),
                        'compare' => 'IN',
                    ),
                ),
             );
            
            echo $this->create_popup_html($args);
        }
          
    }
    /* end of function popadsm_show_ad() */



    function create_popup_html($args) {  
        $the_query = new WP_Query( $args );
        
        $holdingSeconds = $this->popadsm_options_obj->get_option('msbd_popadsm_hold_popup');
        $expMinutes = $this->popadsm_options_obj->get_option('msbd_popadsm_repeat_popup');
        
        ob_start();
    ?>
        <script type='text/javascript'>
            var holdingSeconds = parseInt(<?php echo $holdingSeconds; ?>); //seconds
            var expMinutes = parseInt(<?php echo $expMinutes; ?>); //minutes
        </script>
    <?php


            if ( $the_query->have_posts() ) {
    ?>    
        <div id='msbd-popup'>
            <div class="popup-wrapper">
                <a class="hide-me" href="#">No Thanks, Close it!</a>
    <?php
                while ( $the_query->have_posts() ) {
                    $the_query->the_post(); 
                    
                    $msbd_popadsm_width = get_post_meta( get_the_ID(), 'msbd_popadsm_width', true );
                    $msbd_popadsm_height = get_post_meta( get_the_ID(), 'msbd_popadsm_height', true );
                    
                    $var_inline_styles = "";
                    if( !empty($msbd_popadsm_width) ) {
                        $var_inline_styles = 'width: '.$msbd_popadsm_width.'px;';
                    }
                    
                    if( !empty($msbd_popadsm_height) ) {
                        $var_inline_styles .= ' height: '.$msbd_popadsm_height.'px;';
                    }
    ?>
                    <div class="popup-content content-<?php echo get_the_ID(); ?>" style="<?php echo $var_inline_styles; ?>">
                        <?php echo do_shortcode(get_the_content());  ?>
                    </div>        
    <?php  
                    break;
                }
    ?>
            </div>
        </div>
    <?php
            }

            $html = ob_get_clean();
            
            wp_reset_postdata();
            
            return $html;
    }
    /* end of function create_popup_html() */






    /***********************************************************
     *           ADDING FILTER OPTIONS TO CUSTOM POST TABLE
     ***********************************************************/
/*
public function modify_ticket_filters() {
    // Only apply the filter to our specific post type
    global $typenow;
    
    if( $typenow == $this->plugin_post_type ) {
        $admins= get_users( );
        //$admins= get_users( array( 'role' => 'administrator' ) );
        
        echo "<select name='filter_owner'>";
        
        echo "<option value=''>All tickets</option>";
        
        foreach( $admins as $admin) {
            //$selected = ($admins->ID == $_GET['filter_owner']) ? ' selected' : '';
            echo '<option value="'.$admin->user_nicename.'">' . $admin->user_nicename .  '</option>';
        }
        echo "</select>";
    }
}

public function modify_filter_owner( $query ) {
    global $typenow;
    global $pagenow;

    if( $pagenow == 'edit.php' && $typenow == 'ticket' && $_GET['filter_owner'] ) {
        $query->query_vars[ 'meta_key' ] = 'ticket_assigned_to';
        $query->query_vars[ 'meta_value' ] = (int)$_GET['filter_owner'];
    }
}
*/





    function init() {

        $this->popadsm_options_obj->update_options();
        $this->popadsm_options = $this->popadsm_options_obj->get_option();
        
        // Register custom post for Sponsors
        $this->register_sponsors_post();
    }
    /* end of function : init() */


    function load_scripts_styles() {        
        wp_enqueue_style( "msbd-popadsm", MSBD_POPADSM_URL . 'css/msbd-popadsm.css', false, false );
        wp_enqueue_script( "msbd-adsm-admin-script", MSBD_POPADSM_URL ."js/msbd-popadsm.js", "jquery", false, true);
    }





    /***********************************************************
     *                    META BOX SECTION
     ***********************************************************/
     
    function msbd_popadsm_metaboxes() {

        add_meta_box(
            'popadsm_sizes_box',
            __( 'Size Settings', 'msbd-popadsm' ),
            array(&$this, 'msbd_popadsm_meta_box_callback'),
            $this->plugin_post_type,
            'side',
            'high'
        );
        
        add_meta_box(
            'popadsm_page_select_box',
            __( 'Page Select Settings', 'msbd-popadsm' ),
            array(&$this, 'msbd_popadsm_page_select_box_callback'),
            $this->plugin_post_type,
            'normal',
            'default'
        );  
    }
    /* end of function : msbd_popadsm_metaboxes() */


    function msbd_popadsm_page_select_box_callback() {

        global $post;
        
        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'msbd_popadsm_meta_box', 'msbd_popadsm_meta_box_nonce' );
        
        $msbd_popadsm_home_page = (get_post_meta( $post->ID, 'msbd_popadsm_home_page', true ) == "yes") ? "yes" : "no";
        $msbd_popadsm_other_page = (get_post_meta( $post->ID, 'msbd_popadsm_other_page', true ) == "yes") ? "yes" : "no";
        
        echo '<p><label>';
        $selected = ($msbd_popadsm_home_page=="yes") ? ' checked="checked"' : "";
        echo '<input type="checkbox" name="msbd_popadsm_home_page" value="yes" class="widefat"'.$selected.' />';        
        echo  __( 'Use on Home page', 'msbd-popadsm' ) .'</label></p>';
        
        echo '<p><label>';
        $selected = ($msbd_popadsm_other_page=="yes") ? ' checked="checked"' : "";
        echo '<input type="checkbox" name="msbd_popadsm_other_page" value="yes" class="widefat"'.$selected.' />';        
        echo  __( 'Use on date archive, Tags, Authors pages', 'msbd-popadsm' ) .'</label></p>';
        
    }
    /* end of function : msbd_popadsm_page_select_box_callback() */
    
    
    
    function msbd_popadsm_meta_box_callback() {

        global $post;
        
        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'msbd_popadsm_meta_box', 'msbd_popadsm_meta_box_nonce' );
        
        $msbd_popadsm_width = get_post_meta( $post->ID, 'msbd_popadsm_width', true );
        $msbd_popadsm_height = get_post_meta( $post->ID, 'msbd_popadsm_height', true );
        
        echo '<p><label for="msbd_popadsm_width">' . __( 'Ad Width', 'msbd-popadsm' ) . '</label> ';
        echo '<input type="text" id="msbd_popadsm_width" name="msbd_popadsm_width" value="' . $msbd_popadsm_width . '" class="widefat" /></p>';
        
        echo '<p><label for="msbd_popadsm_height">' . __( 'Ad Height', 'msbd-popadsm' ) . '</label> ';
        echo '<input type="text" id="msbd_popadsm_height" name="msbd_popadsm_height" value="' . $msbd_popadsm_height . '" class="widefat" /></p>';
        
    }
    /* end of function : msbd_popadsm_meta_box_callback() */



    function msbd_popadsm_save_meta_box_data( $post_id ) {

        // Check if our nonce is set.
        if ( ! isset( $_POST['msbd_popadsm_meta_box_nonce'] ) ) {
            return;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['msbd_popadsm_meta_box_nonce'], 'msbd_popadsm_meta_box' ) ) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check the user's permissions.
        if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }

        } else {

            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }

        //OK, it's safe for us to save the data now. 
        
        // Make sure that it is set.
        if ( !isset($_POST['msbd_popadsm_width']) ) {
            return;
        }


        $msbd_popadsm_width = sanitize_text_field($_POST['msbd_popadsm_width']);
        update_post_meta( $post_id, 'msbd_popadsm_width', $msbd_popadsm_width );
        
        $msbd_popadsm_height = sanitize_text_field($_POST['msbd_popadsm_height']);
        update_post_meta( $post_id, 'msbd_popadsm_height', $msbd_popadsm_height );
        
        $msbd_popadsm_home_page = "no"; //sanitize_text_field($_POST['msbd_popadsm_home_page']);
        if ( isset($_POST['msbd_popadsm_home_page']) ) {
            $msbd_popadsm_home_page = sanitize_text_field($_POST['msbd_popadsm_home_page']);
        }
        update_post_meta( $post_id, 'msbd_popadsm_home_page', $msbd_popadsm_home_page );
        
        $msbd_popadsm_other_page = "no"; //sanitize_text_field($_POST['msbd_popadsm_other_page']);
        if ( isset($_POST['msbd_popadsm_other_page']) ) {
            $msbd_popadsm_other_page = sanitize_text_field($_POST['msbd_popadsm_other_page']);
        }
        update_post_meta( $post_id, 'msbd_popadsm_other_page', $msbd_popadsm_other_page );
        
    }
    /* end of function : msbd_popadsm_save_meta_box_data() */





    /***********************************************************
     *                    CUSTOM POST SECTION
     ***********************************************************/

    function register_sponsors_post() {
        $labels = array(
            'name'               => _x( $this->plugin_post_type_title, 'post type general name', 'msbd-popadsm' ),
            'singular_name'      => _x( $this->plugin_post_type_title_single, 'post type singular name', 'msbd-popadsm' ),
        );

        $args = array(
            'labels'             => $labels,
            'taxonomies' => array('category'),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => $this->plugin_post_type ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 25,
            'supports'           => array( 'title', 'editor' )
        );

        register_post_type( $this->plugin_post_type, $args );        
    }
    /* end of function : register_sponsors_post() */






    /***********************************************************
     *                    SANITIZATIONS
     ***********************************************************/

    /*
     * @ $field_type = text, email, number, html, no_html, custom_html, html_js default text
     */
    function msbd_sanitization($data, $field_type='text', $oArray=array()) {        
        
        $output = '';

        switch($field_type) {           
            
            case 'number':
                $output = sanitize_text_field($data);
                $output = intval($output);
                break;
            
            case 'email':
                $output = sanitize_email($data);
                $output = is_email($output);//returned false if not valid
                break;
                
            case 'textarea': 
                $output = esc_textarea($data);
                break;
            
            case 'html':                                         
                $output = wp_kses_post($data);
                break;
            
            case 'custom_html':                    
                $allowedTags = isset($oArray['allowedTags']) ? $oArray['allowedTags'] : "";                                        
                $output = wp_kses($data, $allowedTags);
                break;
            
            case 'no_html':                                        
                $output = strip_tags( $data );
                //$output = stripslashes( $output );
                break;
            
            
            case 'html_js':
                $output = $data;
                break;
            
            
            case 'text':
            default:
                $output = sanitize_text_field($data);
                break;
        }
        
        return $output;

    }    
    

} // End of Class PopupAdsManagement




function msbd_get_category_id($key='') {
    
    return get_cat_ID( single_cat_title("",false) );
}





if (!class_exists('MsbdPopAdsmAdminHelper')) {
    require_once('libs/views/admin-view-helper-functions.php');
}

if (!class_exists('MsbdPopAdOptions')) {
    require_once('libs/msbd-popad-options.php');
}

if (!class_exists('MsbdPopAdsAdmin')) {
    require_once('libs/msbd-popadsm-admin.php');
}

global $popAdsM;
$popAdsM = new PopupAdsManagement();

/* end of file main.php */
