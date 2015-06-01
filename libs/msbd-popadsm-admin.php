<?php
class MsbdPopAdsAdmin {

    var $parent;

    function __construct($parent) {
        $this->parent = $parent;
        
        add_action('admin_menu', array(&$this, 'init_admin_menu'));
        
        //Loading Styles and Scripts for admin
        add_action( 'admin_enqueue_scripts', array(&$this, 'load_admin_scripts_styles'), 100);
        
    }





    function init_admin_menu() {
        global $wpdb;

        $var_manage_authority = $this->parent->popadsm_options_obj->get_option('msbd_popadsm_manage_authority');
        $var_view_authority = $this->parent->popadsm_options_obj->get_option('msbd_popadsm_view_authority');
        
       
        add_submenu_page( 
            'edit.php?post_type='.$this->parent->plugin_post_type, 
            __($this->parent->plugin_post_type_title.' Settings', 'msbd-popadsm'), //Page Title
            __('Settings', 'msbd-popadsm'), //Menu Title
            'manage_options', // TODO Capabilities
            'msbd-popadsm-settings', 
            array(&$this, 'msbd_popadsm_settings_page_render') 
        );
        
        add_submenu_page( 
            'edit.php?post_type='.$this->parent->plugin_post_type, 
            __($this->parent->plugin_post_type_title.' Instructions', 'msbd-popadsm'), //Page Title
            __('Read Me', 'msbd-popadsm'), //Menu Title
            'manage_options', // TODO Capabilities
            'msbd-popadsm-readme', 
            array(&$this, 'msbd_popadsm_readme_page_render') 
        );
    }
    
    


    function msbd_popadsm_settings_page_render($wrapped = false) {
        
        $options = $this->parent->popadsm_options_obj->get_option();

        if (!$wrapped) {
            $this->wrap_admin_page('settings');
            return;
        }

        //Check User Permission
        $var_manage_authority = $this->parent->popadsm_options['msbd_popadsm_manage_authority'];
        if (!current_user_can($var_manage_authority)) {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }
        
        ?>
        <form id="msbd-popadsm-settings-form" action="" method="post">
            <input type="hidden" name="action" value="msbd-popadsm-update-options">
            <input type="hidden" name="msbd_popadsm_view_authority" value="edit_published_posts">
            
            <div class="form-table">
                    
                <div class="form-row">
                    <div class="grid_3">
                        <label for="msbd_popadsm_hold_popup">Hold Popup (in seconds)</label>
                    </div>
                    
                    <div class="grid_9">
                        <input type="text" name="msbd_popadsm_hold_popup" id="msbd_popadsm_hold_popup" value="<?php echo $options['msbd_popadsm_hold_popup'] ?>"  /> <p class="note">[after the mentioned seconds of page load the popup will open!]</p>
                    </div>
                </div>
                    
                <div class="form-row">
                    <div class="grid_3">
                        <label for="msbd_popadsm_repeat_popup">Popup Repeat Time</label>
                    </div>
                    
                    <div class="grid_9">
                        <input type="text" name="msbd_popadsm_repeat_popup" id="msbd_popadsm_repeat_popup" value="<?php echo $options['msbd_popadsm_repeat_popup'] ?>" /> <p class="note">[the popup will repeat on the mentioned minutes of inactivity on our site!]</p>
                    </div>
                </div>
                    
                
                <div class="form-row">
                    <div class="grid_6">
                        <input name="resetButton" type="reset" value="Reset" />
                        <input type="submit" class="button" value="Save Settngs">
                    </div>
                </div>
            </div>
        </form>
        <?php
    }





    function load_admin_scripts_styles() {
        wp_enqueue_style( "msbd-popadsm-admin", MSBD_POPADSM_URL . 'css/msbd-popadsm-admin.css', false, false );           
        wp_enqueue_script( "msbd-popadsm-admin-script", MSBD_POPADSM_URL ."js/msbd-popadsm-admin.js", "jquery", false, true);
    }




    function wrap_admin_page($page = null) {
        
        $page_header = '';
        switch($page) {                
            case 'settings':
                $page_header = $this->parent->plugin_name.' Settings';
                break;

            case 'instructions':
                $page_header = $this->parent->plugin_name.' Instructions';
                break;
        }
        
        echo '<div class="wrap msbd-popadsm">';
        echo '<h2><img src="' . MSBD_POPADSM_URL . 'images/msbd_favicon_32.png" /> '.$page_header.' </h2>';
        
        echo '<div class="popadsm-body-content">';
        
        MsbdPopAdsmAdminHelper::render_container_open('content-container');        
        
        if ($page == 'settings') {
            MsbdPopAdsmAdminHelper::render_postbox_open('Settings');
            echo $this->msbd_popadsm_settings_page_render(TRUE);
            MsbdPopAdsmAdminHelper::render_postbox_close();
        }
        
        
        if ($page == 'instructions') {
            MsbdPopAdsmAdminHelper::render_postbox_open('Instructions');
            echo $this->msbd_popadsm_readme_page_render(TRUE);
            MsbdPopAdsmAdminHelper::render_postbox_close();
        }

        MsbdPopAdsmAdminHelper::render_container_close();
        
        MsbdPopAdsmAdminHelper::render_container_open('sidebar-container');        
        MsbdPopAdsmAdminHelper::render_sidebar();
        MsbdPopAdsmAdminHelper::render_container_close();
        
        echo '</div>'; /* .popadsm-body-content */
        echo '</div>'; /* .wrap msbd-popadsm */
    }



    function msbd_popadsm_readme_page_render($wrapped = false) {
        if (!$wrapped) {
            $this->wrap_admin_page('instructions');
            return;
        }
        
        ?>
            <div class="instructions">                
                <p><strong>Recommended: </strong>Once you have installed the plugin, Make sure you have updated/resave the permalinks of your site. Because without update the permalink, your site may not get the advertisement post that you are going to create for popup!</p>
                
                <p>** If you added multiple advertisement for any category or for home page the advertisement will publish randomly!</p>
                
                <p>** If you add a subcategory tto any advertisement will show also to the parent category! On the other hand if you add a parent category for any advertisement then it will not effect the child category.</p>
                
                <p><strong>Settings Page: </strong>: Use the settings page form to set time for your popup. </p>
                <ul>
                    <li><strong>Hold Popup:</strong>strong> write the muber of seconds; The popup will show after the mentioned seconds, once the page is loaded! By default the Hold time is one(1) second.</li>
                    <li><strong>Popup Repeat Time :</strong>strong>  write the muber of minutes; by default ten(5) minutes will use. After the inactivity of visitor on you site the popup will show again!</li>
                </ul>
                
                <p><strong>[** This is just an initial release of this plugin; Please Let us know if you found something to be fix or to be added for batter performance!]</strong></p>
                
            </div>
        <?php
    }

}
/* end of file msbd-popadsm-admin.php */
