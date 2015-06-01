<?php
class MsbdPopAdsmAdminHelper {

    public static function render_header($title, $echo = TRUE) {
        global $file;
        
        $plugin_data = get_plugin_data( $file);
        $output = '';
        $output .= '<h1>' . $plugin_data['Name'] . '</h1>';
        
        if ($echo) {
            echo $output;
        } else {
            return $output;
        }
    }




    public static function render_sidebar() {
        MsbdPopAdsmAdminHelper::render_postbox_open('Micro Solutions Bangladesh');
        MsbdPopAdsmAdminHelper::render_msbd_logos();
        MsbdPopAdsmAdminHelper::render_postbox_close();
    }



    public static function render_msbd_logos() {
        //echo 'TODO at admin view helper ...';
        ?>
            <div class="msbd-logo">
                <a href="http://www.microsolutionsbd.com/" title="Micro Solutions Bangladesh" target="_blank">
                    <img src="<?php echo MSBD_ADSMP_URL."images/msbd_logo.png"; ?>" alt="msbd logo" />
                </a>
            </div>
            
            <div class="msbd-social-media-links-wrapper">
                <div class="msbd-social-media-link msbd-facebook-link">
                    <script>(function(d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id)) {return;}
                        js = d.createElement(s); js.id = id;
                        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
                        fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk'));</script>
                </div>
                
                <div class="msbd-social-media-link msbd-google-plus-link">
                    <div class="fb-like" data-href="https://www.facebook.com/microsolutionsbd" data-send="false" data-layout="button_count" data-width="100" data-show-faces="true"></div><br><br>
                    <g:plusone annotation="inline" width="216" href="https://www.google.com/+Microsolutionsbd"></g:plusone><br>
                    <!-- Place this tag where you want the +1 button to render -->
                    <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
                </div>
            </div>

        <?php
    }




    public static function render_postbox_open($title = '') {
        ?>
        <div class="postbox">
            <div class="handlediv" title="Click to toggle"><br/></div>
            <h3 class="hndle"><span><?php echo $title; ?></span></h3>
            <div class="inside">
        <?php
    }

    public static function render_postbox_close() {
        echo '</div>'; // end .inside
        echo '</div>'; // end .postbox
    }






    public static function render_container_open($extra_class = '', $echo = TRUE) {

        $output = '  <div class="' . $extra_class . '">';
        $output .= '    <div class="meta-box-sortables ui-sortable">';

        if ($echo) {
            echo $output;
        } else {
            return $output;
        }
    }

    public static function render_container_close($echo = TRUE) {
        $output = '';
        $output .= '</div>'; // end .ui-sortable
        $output .= '</div>'; // end .msbd-adsm-postbox-container

        if ($echo) {
            echo $output;
        } else {
            return $output;
        }
    }
    
    
    
}
/* end of file admin-view-helper-functions.php */
