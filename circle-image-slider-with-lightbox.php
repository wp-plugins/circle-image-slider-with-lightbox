<?php
    /* 
    Plugin Name: Circle Image Slider With Lightbox
    Plugin URI:http://www.i13websolution.com
    Author URI:http://www.i13websolution.com
    Description:Circle Image Silder With Lightbox is beautiful responsive circle thumbnail image slider with responsive lightbox.Add any number of images from admin panel.
    Author:I Thirteen Web Solution
    Version:1.0

    */
    error_reporting(0);
    add_filter('widget_text', 'do_shortcode');
    add_action('admin_menu', 'circle_slider_plus_lightbox_add_admin_menu');
    register_activation_hook(__FILE__,'install_circle_slider_plus_lightbox');
    add_action('wp_enqueue_scripts', 'circle_slider_plus_lightbox_load_styles_and_js');
    add_shortcode( 'print_circle_slider_plus_lightbox', 'print_circle_slider_plus_lightbox_func' );
    add_action('admin_notices', 'circle_slider_plus_lightbox_admin_notices');
    
    function circle_slider_plus_lightbox_admin_notices() {
        
        if (is_plugin_active('circle-image-slider-with-lightbox/circle-image-slider-with-lightbox.php')) {
            
            $uploads = wp_upload_dir();
            $baseDir=$uploads['basedir'];
            $baseDir=str_replace("\\","/",$baseDir);
            $pathToImagesFolder=$baseDir.'/circle-image-slider-with-lightbox';
            
            if(file_exists($pathToImagesFolder) and is_dir($pathToImagesFolder)){
                
                if( !is_writable($pathToImagesFolder)){
                        echo "<div class='updated'><p>Circle Image Slider With Lightbox is active but does not have write permission on</p><p><b>".$pathToImagesFolder."</b> directory.Please allow write permission.</p></div> ";
                }       
            }
            else{
               
                  wp_mkdir_p($pathToImagesFolder);  
                  if(!file_exists($pathToImagesFolder) and !is_dir($pathToImagesFolder)){
                    echo "<div class='updated'><p>Circle Image Slider With Lightbox is active but plugin does not have permission to create directory</p><p><b>".$pathToImagesFolder."</b> .Please create circle-image-slider-with-lightbox directory inside upload directory and allow write permission.</p></div> "; 
                    
                  }
            }
        }
    }


    function circle_slider_plus_lightbox_load_styles_and_js(){
        
        if (!is_admin()) {                                                       


            wp_enqueue_style( 'images-circle-thumbnail-slider-plus-lighbox-style', plugins_url('/css/images-circle-thumbnail-slider-plus-lighbox-style.css', __FILE__) );
            wp_enqueue_style( 'circle-l-box-css', plugins_url('/css/circle-l-box-css.css', __FILE__) );
            wp_enqueue_script('jquery'); 
            wp_enqueue_script('images-circle-thumbnail-slider-plus-lightbox-jc',plugins_url('/js/images-circle-thumbnail-slider-plus-lightbox-jc.js', __FILE__));
            wp_enqueue_script('circle-l-box-js',plugins_url('/js/circle-l-box-js.js', __FILE__));

        }  
    }

    function install_circle_slider_plus_lightbox(){

        global $wpdb;
        $table_name = $wpdb->prefix . "circle_image_carousel";

        $sql = "CREATE TABLE " . $table_name . " (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `title` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
        `image_name` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
        `image_description` text COLLATE utf8_unicode_ci DEFAULT NULL,
        `image_order` int(11) NOT NULL DEFAULT '0',
        `open_link_in` tinyint(1) NOT NULL DEFAULT '1',
        `enable_light_box_img_desc` tinyint(1) NOT NULL DEFAULT '1',
        `createdon` datetime NOT NULL,
        `custom_link` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
        `post_id` int(10) unsigned DEFAULT NULL,
        `slider_id` int(10) unsigned NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`)
        );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);


        $circle_thumbnail_slider_plus_lightbox_settings=array('pauseonmouseover' => '1','auto' =>'','speed' => '1000','pause'=>1000,'circular' => '1','imageheight' => '120','imagewidth' => '120','visible'=> '5','min_visible'=> '1','scroll' => '1','resizeImages'=>'1','scollerBackground'=>'#FFFFFF','imageMargin'=>'15','show_caption'=>'0','lightbox'=>'1');

        if( !get_option( 'circle_thumbnail_slider_plus_lightbox_settings' ) ) {

            update_option('circle_thumbnail_slider_plus_lightbox_settings',$circle_thumbnail_slider_plus_lightbox_settings);
        }

         $uploads = wp_upload_dir();
         $baseDir=$uploads['basedir'];
         $baseDir=str_replace("\\","/",$baseDir);
         $pathToImagesFolder=$baseDir.'/circle-image-slider-with-lightbox';
         wp_mkdir_p($pathToImagesFolder);  
          

    } 




    function circle_slider_plus_lightbox_add_admin_menu(){

        $hook_suffix_r_l=add_menu_page( __( 'Circle Slider plus Lightbox'), __( 'Circle Slider plus Lightbox' ), 'administrator', 'circle_thumbnail_slider_with_lightbox', 'circle_thumbnail_slider_with_lightbox_admin_options_func' );
        $hook_suffix_r_l=add_submenu_page( 'circle_thumbnail_slider_with_lightbox', __( 'Manage Sliders'), __( 'Slider Settings' ),'administrator', 'circle_thumbnail_slider_with_lightbox', 'circle_thumbnail_slider_with_lightbox_admin_options_func' );
        $hook_suffix_r_l_1=add_submenu_page( 'circle_thumbnail_slider_with_lightbox', __( 'Manage Images'), __( 'Manage Images'),'administrator', 'circle_thumbnail_slider_with_lightbox_image_management', 'circle_thumbnail_slider_with_lightbox_image_management_func' );
        $hook_suffix_r_l_2=add_submenu_page( 'circle_thumbnail_slider_with_lightbox', __( 'Preview Slider'), __( 'Preview Slider'),'administrator', 'circle_thumbnail_slider_with_lightbox_preview', 'circle_thumbnail_slider_with_lightbox_admin_preview_func' );

        add_action( 'load-' . $hook_suffix_r_l , 'circle_slider_plus_lightbox_plugin_admin_init' );
        add_action( 'load-' . $hook_suffix_r_l_1 , 'circle_slider_plus_lightbox_plugin_admin_init' );
        add_action( 'load-' . $hook_suffix_r_l_2 , 'circle_slider_plus_lightbox_plugin_admin_init' );

    }

    function circle_slider_plus_lightbox_plugin_admin_init(){


            $url = plugin_dir_url(__FILE__);  
            wp_enqueue_script( 'jquery.validate', $url.'js/jquery.validate.js' );  
            wp_enqueue_style( 'images-circle-thumbnail-slider-plus-lighbox-style', plugins_url('/css/images-circle-thumbnail-slider-plus-lighbox-style.css', __FILE__) );
            wp_enqueue_style( 'circle-l-box-css', plugins_url('/css/circle-l-box-css.css', __FILE__) );
            wp_enqueue_script('jquery'); 
            wp_enqueue_script('images-circle-thumbnail-slider-plus-lightbox-jc',plugins_url('/js/images-circle-thumbnail-slider-plus-lightbox-jc.js', __FILE__));
            wp_enqueue_script('circle-l-box-js',plugins_url('/js/circle-l-box-js.js', __FILE__));

            circle_slider_plus_lightbox_admin_scripts_init();

    }

    function circle_thumbnail_slider_with_lightbox_admin_options_func(){

        if(isset($_POST['btnsave'])){

            $auto=trim($_POST['isauto']);

            if($auto=='auto')
                $auto=true;
            else
                $auto=false; 

            $speed=(int)trim($_POST['speed']);
            $pause=(int)trim($_POST['pause']);

            if(isset($_POST['circular']))
                $circular=true;  
            else
                $circular=false;  

            //$scrollerwidth=$_POST['scrollerwidth'];

            $visible=trim($_POST['visible']);

            $min_visible=trim($_POST['min_visible']);


            if(isset($_POST['pauseonmouseover']))
                $pauseonmouseover=true;  
            else 
                $pauseonmouseover=false;


            $scroll=trim($_POST['scroll']);

            if($scroll=="")
                $scroll=1;

            $imageMargin=(int)trim($_POST['imageMargin']);
            $imageheight=(int)trim($_POST['imageheight']);
            $imagewidth=(int)trim($_POST['imagewidth']);
            $show_caption=$_POST['show_caption'];  

            $scollerBackground=trim($_POST['scollerBackground']);
            
            if(isset($_POST['lightbox']))
                $lightbox=1;  
            else 
                $lightbox=0;


            $options=array();
            $options['pauseonmouseover']=$pauseonmouseover;  
            $options['auto']=$auto;  
            $options['speed']=$speed;  
            $options['pause']=$pause;  
            $options['circular']=$circular;  
            //$options['scrollerwidth']=$scrollerwidth;  
            $options['imageMargin']=$imageMargin;  
            $options['imageheight']=$imageheight;  
            $options['imagewidth']=$imagewidth;  
            $options['visible']=$visible;  
            $options['min_visible']=$min_visible;  
            $options['scroll']=$scroll;  
            $options['resizeImages']=1;  
            $options['scollerBackground']=$scollerBackground;  
            $options['show_caption']=$show_caption;  
            $options['lightbox']=$lightbox;  
            
            


            $settings=update_option('circle_thumbnail_slider_plus_lightbox_settings',$options); 
            $circle_image_carousel=array();
            $circle_image_carousel['type']='succ';
            $circle_image_carousel['message']='Settings saved successfully.';
            update_option('circle_image_carousel', $circle_image_carousel);



        }  
        $settings=get_option('circle_thumbnail_slider_plus_lightbox_settings');


    ?>      
    <div id="poststuff" > 
        <div id="post-body" class="metabox-holder columns-2" >  
            <div id="post-body-content">
                <div class="wrap">
                    <table><tr><td><a href="https://twitter.com/FreeAdsPost" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @FreeAdsPost</a>
                                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></td>
                            <td>
                                <a target="_blank" title="Donate" href="http://www.i13websolution.com/donate-wordpress_image_thumbnail.php">
                                    <img id="help us for free plugin" height="30" width="90" src="http://www.i13websolution.com/images/paypaldonate.jpg" border="0" alt="help us for free plugin" title="help us for free plugin">
                                </a>
                            </td>
                        </tr>
                    </table>

                    <?php
                        $messages=get_option('circle_image_carousel'); 
                        $type='';
                        $message='';
                        if(isset($messages['type']) and $messages['type']!=""){

                            $type=$messages['type'];
                            $message=$messages['message'];

                        }  


                        if($type=='err'){ echo "<div class='errMsg'>"; echo $message; echo "</div>";}
                        else if($type=='succ'){ echo "<div class='succMsg'>"; echo $message; echo "</div>";}


                        update_option('circle_image_carousel', array());     
                    ?>      

                    <span><h3 style="color: blue;"><a target="_blank" href="http://www.i13websolution.com/wordpress-responsive-slider-with-responsive-lightbox.html">UPGRADE TO PRO VERSION</a></h3></span>
                    
                    <h2>Slider Settings</h2>
                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="post-body-content">
                                <form method="post" action="" id="scrollersettiings" name="scrollersettiings" >


                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Auto Scroll ?</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input style="width:20px;" type='radio' <?php if($settings['auto']==true){echo "checked='checked'";}?>  name='isauto' value='auto' >Auto &nbsp;<input style="width:20px;" type='radio' name='isauto' <?php if($settings['auto']==false){echo "checked='checked'";} ?> value='manuall' >Scroll By Left & Right Arrow
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label >Speed</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="speed" size="30" name="speed" value="<?php echo $settings['speed']; ?>" style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>

                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label >Pause</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="pause" size="30" name="pause" value="<?php echo $settings['pause']; ?>" style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div style="clear:both">The amount of time (in ms) between each auto transition</div>

                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label >Circular Slider ?</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" id="circular" size="30" name="circular" value="" <?php if($settings['circular']==true){echo "checked='checked'";} ?> style="width:20px;">&nbsp;Circular Slider ? 
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>

                                        </div>
                                    </div>
                                     <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Display Lightbox On Image Click ?</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" id="lightbox" size="30" name="lightbox" value="" <?php if($settings['lightbox']==true){echo "checked='checked'";} ?> style="width:20px;">&nbsp;Display Lightbox ? 
                                                        <div style="clear:both;margin-top:3px">On click of image show lightbox</div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Slider Background color</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="scollerBackground" size="30" name="scollerBackground" value="<?php echo $settings['scollerBackground']; ?>" style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>

                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Max Visible</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="visible" size="30" name="visible" value="<?php echo $settings['visible']; ?>" style="width:100px;">
                                                        <div style="clear:both">This will decide your slider width automatically</div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            specifies the number of items visible at all times within the slider.
                                            <div style="clear:both"></div>

                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Min Visible</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="min_visible" size="30" name="min_visible" value="<?php echo $settings['min_visible']; ?>" style="width:100px;">
                                                        <div style="clear:both">This will decide your slider width in responsive layout</div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            The responsive layout decide by slider itself using min visible.
                                            <div style="clear:both"></div>

                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Scroll</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="scroll" size="30" name="scroll" value="<?php echo $settings['scroll']; ?>" style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            You can specify the number of items to scroll when you click the next or prev buttons.
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Pause On Mouse Over ?</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" id="pauseonmouseover" size="30" name="pauseonmouseover" value="" <?php if($settings['pauseonmouseover']==true){echo "checked='checked'";} ?> style="width:20px;">&nbsp;Pause On Mouse Over ? 
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                  
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Image Height</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="imageheight" size="30" name="imageheight" value="<?php echo $settings['imageheight']; ?>" style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>

                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Image Width</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="imagewidth" size="30" name="imagewidth" value="<?php echo $settings['imagewidth']; ?>" style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>

                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Image Margin</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="imageMargin" size="30" name="imageMargin" value="<?php echo $settings['imageMargin']; ?>" style="width:100px;">
                                                        <div style="clear:both;padding-top:5px">Gap between two images </div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>

                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                     <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Show Title Bellow Circle Image ?</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input style="width:20px;" type='radio' <?php if($settings['show_caption']==true){echo "checked='checked'";}?>  name='show_caption' value='1' >yes &nbsp;<input style="width:20px;" type='radio' name='show_caption' <?php if($settings['show_caption']==false){echo "checked='checked'";} ?> value='0' >No
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>

                                    <input type="submit"  name="btnsave" id="btnsave" value="Save Changes" class="button-primary">&nbsp;&nbsp;<input type="button" name="cancle" id="cancle" value="Cancel" class="button-primary" onclick="location.href='admin.php?page=circle_thumbnail_slider_with_lightbox_image_management'">

                                </form> 
                                <script type="text/javascript">

                                    var $n = jQuery.noConflict();  
                                    $n(document).ready(function() {

                                            $n("#scrollersettiings").validate({
                                                    rules: {
                                                        isauto: {
                                                            required:true
                                                        },speed: {
                                                            required:true, 
                                                            number:true, 
                                                            maxlength:15
                                                        },pause: {
                                                            required:true, 
                                                            number:true, 
                                                            maxlength:15
                                                        },
                                                        visible:{
                                                            required:true,  
                                                            number:true,
                                                            maxlength:15

                                                        },
                                                        min_visible:{
                                                            required:true,  
                                                            number:true,
                                                            maxlength:15

                                                        },
                                                        scroll:{
                                                            required:true,
                                                            number:true,
                                                            maxlength:15  
                                                        },
                                                        scollerBackground:{
                                                            required:true,
                                                            maxlength:7  
                                                        },
                                                        /*scrollerwidth:{
                                                        required:true,
                                                        number:true,
                                                        maxlength:15    
                                                        },*/imageheight:{
                                                            required:true,
                                                            number:true,
                                                            maxlength:15    
                                                        },
                                                        imagewidth:{
                                                            required:true,
                                                            number:true,
                                                            maxlength:15    
                                                        },imageMargin:{
                                                            required:true,
                                                            number:true,
                                                            maxlength:15    
                                                        }

                                                    },
                                                    errorClass: "image_error",
                                                    errorPlacement: function(error, element) {
                                                        error.appendTo( element.next().next());
                                                    } 


                                            })
                                            
                                            
                                            $n('#scollerBackground').wpColorPicker();
                                            
                                    });

                                </script> 

                            </div>
                        </div>
                    </div>  
                </div>      
            </div>
            <div id="postbox-container-1" class="postbox-container" > 

                <div class="postbox"> 
                    <h3 class="hndle"><span></span>Access All Themes In One Price</h3> 
                    <div class="inside">
                        <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank"><img border="0" src="http://www.elegantthemes.com/affiliates/banners/300x250.gif" width="250" height="250"></a></center>

                        <div style="margin:10px 5px">

                        </div>
                    </div></div>
                <div class="postbox"> 
                    <h3 class="hndle"><span></span>Recommended WordPress Hostings</h3> 
                    <div class="inside">
                        <center><a href="http://secure.hostgator.com/~affiliat/cgi-bin/affiliates/clickthru.cgi?id=nik00726-hs-wp"><img src="http://tracking.hostgator.com/img/WordPress_Hosting/300x250-animated.gif" width="250" height="250" border="0"></a></center>
                        <div style="margin:10px 5px">
                        </div>
                    </div></div>

            </div>
            <div class="clear"></div>
        </div>  
    </div> 
    <?php
    } 
    function circle_thumbnail_slider_with_lightbox_image_management_func(){

        $action='gridview';
        global $wpdb;


        if(isset($_GET['action']) and $_GET['action']!=''){


            $action=trim($_GET['action']);
        }

    ?>

    <?php 
        if(strtolower($action)==strtolower('gridview')){ 


            $wpcurrentdir=dirname(__FILE__);
            $wpcurrentdir=str_replace("\\","/",$wpcurrentdir);
            
            $uploads = wp_upload_dir();
            $baseurl=$uploads['baseurl'];
            $baseurl.='/circle-image-slider-with-lightbox/';



        ?> 
        <style type="text/css">
            .pagination {
                clear:both;
                padding:20px 0;
                position:relative;
                font-size:11px;
                line-height:13px;
            }

            .pagination span, .pagination a {
                display:block;
                float:left;
                margin: 2px 2px 2px 0;
                padding:6px 9px 5px 9px;
                text-decoration:none;
                width:auto;
                color:#fff;
                background: #555;
            }

            .pagination a:hover{
                color:#fff;
                background: #3279BB;
            }

            .pagination .current{
                padding:6px 9px 5px 9px;
                background: #3279BB;
                color:#fff;
            }
        </style>
        <!--[if !IE]><!-->
        <style type="text/css">

            @media only screen and (max-width: 800px) {

                /* Force table to not be like tables anymore */
                #no-more-tables table, 
                #no-more-tables thead, 
                #no-more-tables tbody, 
                #no-more-tables th, 
                #no-more-tables td, 
                #no-more-tables tr { 
                    display: block; 

                }

                /* Hide table headers (but not display: none;, for accessibility) */
                #no-more-tables thead tr { 
                    position: absolute;
                    top: -9999px;
                    left: -9999px;
                }

                #no-more-tables tr { border: 1px solid #ccc; }

                #no-more-tables td { 
                    /* Behave  like a "row" */
                    border: none;
                    border-bottom: 1px solid #eee; 
                    position: relative;
                    padding-left: 50%; 
                    white-space: normal;
                    text-align:left;      
                }

                #no-more-tables td:before { 
                    /* Now like a table header */
                    position: absolute;
                    /* Top/left values mimic padding */
                    top: 6px;
                    left: 6px;
                    width: 45%; 
                    padding-right: 10px; 
                    white-space: nowrap;
                    text-align:left;
                    font-weight: bold;
                }

                /*
                Label the data
                */
                #no-more-tables td:before { content: attr(data-title); }
            }
        </style>
        <!--<![endif]-->
        <div id="poststuff"  class="wrap">
            <div id="post-body" class="metabox-holder columns-2">
                <table><tr><td><a href="https://twitter.com/FreeAdsPost" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @FreeAdsPost</a>
                            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></td>
                        <td>
                            <a target="_blank" title="Donate" href="http://www.i13websolution.com/donate-wordpress_image_thumbnail.php">
                                <img id="help us for free plugin" height="30" width="90" src="http://www.i13websolution.com/images/paypaldonate.jpg" border="0" alt="help us for free plugin" title="help us for free plugin">
                            </a>
                        </td>
                    </tr>
                </table>

                <?php 

                    $messages=get_option('circle_image_carousel'); 
                    $type='';
                    $message='';
                    if(isset($messages['type']) and $messages['type']!=""){

                        $type=$messages['type'];
                        $message=$messages['message'];

                    }  


                    if($type=='err'){ echo "<div class='errMsg'>"; echo $message; echo "</div>";}
                    else if($type=='succ'){ echo "<div class='succMsg'>"; echo $message; echo "</div>";}


                    update_option('circle_image_carousel', array());     
                ?>

                <div id="post-body-content" >  

                
                    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
                    <span><h3 style="color: blue;"><a target="_blank" href="http://www.i13websolution.com/wordpress-responsive-slider-with-responsive-lightbox.html">UPGRADE TO PRO VERSION</a></h3></span>
                    <h2>Images <a class="button add-new-h2" href="admin.php?page=circle_thumbnail_slider_with_lightbox_image_management&action=addedit">Add New</a> </h2>


                    <form method="POST" action="admin.php?page=circle_thumbnail_slider_with_lightbox_image_management&action=deleteselected"  id="posts-filter">
                        <div class="alignleft actions">
                            <select name="action_upper">
                                <option selected="selected" value="-1">Bulk Actions</option>
                                <option value="delete">delete</option>
                            </select>
                            <input type="submit" value="Apply" class="button-secondary action" id="deleteselected" name="deleteselected">
                        </div>
                        <br class="clear">
                        <?php 

                            $settings=get_option('circle_thumbnail_slider_plus_lightbox_settings'); 
                            $visibleImages=$settings['visible'];
                            $query="SELECT * FROM ".$wpdb->prefix."circle_image_carousel order by createdon desc";
                            $rows=$wpdb->get_results($query,'ARRAY_A');
                            $rowCount=sizeof($rows);

                        ?>
                        <?php if($rowCount<$visibleImages){ ?>
                            <h4 style="color: green"> Current slider setting - Total visible images <?php echo $visibleImages; ?></h4>
                            <h4 style="color: green">Please add atleast <?php echo $visibleImages; ?> images</h4>
                            <?php } else{
                                echo "<br/>";
                        }?>
                        <div id="no-more-tables">
                            <table cellspacing="0" id="gridTbl" class="table-bordered table-striped table-condensed cf" >
                                <thead>
                                    <tr>
                                        <th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
                                        <th><span>Title</span></th>
                                        <th><span></span></th>
                                        <th><span>Published On</span></th>
                                        <th><span>Edit</span></th>
                                        <th><span>Delete</span></th>
                                    </tr>
                                </thead>

                                <tbody id="the-list">
                                    <?php

                                        if(count($rows) > 0){

                                            global $wp_rewrite;
                                            $rows_per_page = 5;

                                            $current = (isset($_GET['paged'])) ? ($_GET['paged']) : 1;
                                            $pagination_args = array(
                                                'base' => @add_query_arg('paged','%#%'),
                                                'format' => '',
                                                'total' => ceil(sizeof($rows)/$rows_per_page),
                                                'current' => $current,
                                                'show_all' => false,
                                                'type' => 'plain',
                                            );


                                            $start = ($current - 1) * $rows_per_page;
                                            $end = $start + $rows_per_page;
                                            $end = (sizeof($rows) < $end) ? sizeof($rows) : $end;

                                            for ($i=$start;$i < $end ;++$i ) {

                                                $row = $rows[$i];
                                                $id=$row['id'];
                                                $editlink="admin.php?page=circle_thumbnail_slider_with_lightbox_image_management&action=addedit&id=$id";
                                                $deletelink="admin.php?page=circle_thumbnail_slider_with_lightbox_image_management&action=delete&id=$id";
                                                $outputimgmain = $baseurl.$row['image_name'];
                                                
                                            ?>
                                            <tr valign="top" >
                                                <td class="alignCenter check-column"   data-title="Select Record" ><input type="checkbox" value="<?php echo $row['id'] ?>" name="thumbnails[]"></td>
                                                <td   data-title="Title" ><strong><?php echo stripslashes($row['title']) ?></strong></td>  
                                                 <td class="alignCenter">
                                                      <img src="<?php echo $outputimgmain;?>" style="width:50px" height="50px"/>
                                                </td>  
                                                <td class="alignCenter"   data-title="Published On" ><?php echo $row['createdon'] ?></td>
                                                <td class="alignCenter"   data-title="Edit Record" ><strong><a href='<?php echo $editlink; ?>' title="edit">Edit</a></strong></td>  
                                                <td class="alignCenter"   data-title="Delete Record" ><strong><a href='<?php echo $deletelink; ?>' onclick="return confirmDelete();"  title="delete">Delete</a> </strong></td>  
                                            </tr>
                                            <?php 
                                            } 
                                        }
                                        else{
                                        ?>

                                        <tr valign="top" class="" id="">
                                            <td colspan="5" data-title="No Record" align="center"><strong>No Images Found</strong></td>  
                                        </tr>

                                        <?php 
                                        } 
                                    ?>      
                                </tbody>
                            </table>
                        </div>
                        <?php
                            if(sizeof($rows)>0){
                                echo "<div class='pagination' style='padding-top:10px'>";
                                echo paginate_links($pagination_args);
                                echo "</div>";
                            }
                        ?>
                        <br/>
                        <div class="alignleft actions">
                            <select name="action">
                                <option selected="selected" value="-1">Bulk Actions</option>
                                <option value="delete">delete</option>
                            </select>
                            <input type="submit" value="Apply" class="button-secondary action" id="deleteselected" name="deleteselected">
                        </div>

                    </form>
                    <script type="text/JavaScript">

                        function  confirmDelete(){
                            var agree=confirm("Are you sure you want to delete this image ?");
                            if (agree)
                                return true ;
                            else
                                return false;
                        }
                    </script>

                    <br class="clear">
                    <h3>To print this slider into WordPress Post/Page use below Short code</h3>
                    <input type="text" value="[print_circle_slider_plus_lightbox]" style="width: 400px;height: 30px" onclick="this.focus();this.select()" />
                    <div class="clear"></div>
                    <h3>To print this slider into WordPress theme/template PHP files use below php code</h3>
                    <input type="text" value="echo do_shortcode('[print_circle_slider_plus_lightbox]');" style="width: 400px;height: 30px" onclick="this.focus();this.select()" />
                    <div class="clear"></div>
                </div>
                <div id="postbox-container-1" class="postbox-container"> 
                    <div class="postbox"> 
                        <h3 class="hndle"><span></span>Recommended WordPress Themes</h3> 
                        <div class="inside">
                            <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank"><img border="0" src="http://www.elegantthemes.com/affiliates/banners/300x250.gif" width="250" height="250"></a></center>
                            <div style="margin:10px 5px">

                            </div>
                        </div></div>
                    
                </div>
            </div>
        </div>

        <?php 
        }   
        else if(strtolower($action)==strtolower('addedit')){
            $url = plugin_dir_url(__FILE__);

        ?>
        <?php        
            if(isset($_POST['btnsave'])){

               $uploads = wp_upload_dir();
               $baseDir=$uploads['basedir'];
               $baseDir=str_replace("\\","/",$baseDir);
               $pathToImagesFolder=$baseDir.'/circle-image-slider-with-lightbox';
             
                //edit save
                if(isset($_POST['imageid'])){

                    //add new
                    $location='admin.php?page=circle_thumbnail_slider_with_lightbox_image_management';
                    $title=trim(addslashes($_POST['imagetitle']));
                    $imageurl=trim($_POST['imageurl']);
                    $imageid=trim($_POST['imageid']);
                    $imagename="";
                     $imagename="";
                    if($_FILES["image_name"]['name']!="" and $_FILES["image_name"]['name']!=null){

                        if ($_FILES["image_name"]["error"] > 0)
                        {
                            $circle_image_carousel=array();
                            $circle_image_carousel['type']='err';
                            $circle_image_carousel['message']='Error while file uploading.';
                            update_option('circle_image_carousel', $circle_image_carousel);


                            echo "<script type='text/javascript'> location.href='$location';</script>";
                            exit;

                        }
                        else{

                            $wpcurrentdir=dirname(__FILE__);
                            $wpcurrentdir=str_replace("\\","/",$wpcurrentdir);
                            $path_parts = pathinfo($_FILES["image_name"]["name"]);
                            $extension = $path_parts['extension'];       
                            $imagename=md5(time()).".$extension";
                            $imageUploadTo=$pathToImagesFolder.'/'.$imagename;
                            move_uploaded_file($_FILES["image_name"]["tmp_name"],$imageUploadTo); 

                        }

                    }
                    else if(trim($_POST['HdnMediaSelection'])!=''){

                        $postThumbnailID=(int)$_POST['HdnMediaSelection'];
                        $photoMeta = wp_get_attachment_metadata( $postThumbnailID );
                        if(is_array($photoMeta) and isset($photoMeta['file'])) {

                            $fileName=$photoMeta['file'];
                            $phyPath=ABSPATH;
                            $phyPath=str_replace("\\","/",$phyPath);

                            $pathArray=pathinfo($fileName);

                            $imagename=$pathArray['basename'];

                            $fileUrl=$phyPath.'wp-content/uploads/'.$fileName;
                            $fileUrl=str_replace("\\","/",$fileUrl);

                            $wpcurrentdir=dirname(__FILE__);
                            $wpcurrentdir=str_replace("\\","/",$wpcurrentdir);
                            $imageUploadTo=$pathToImagesFolder.'/'.$imagename;

                            @copy($fileUrl, $imageUploadTo);

                        }

                    }    


                    try{
                        if($imagename!=""){
                            $query = "update ".$wpdb->prefix."circle_image_carousel set title='$title',image_name='$imagename',
                            custom_link='$imageurl' where id=$imageid";
                        }
                        else{
                            $query = "update ".$wpdb->prefix."circle_image_carousel set title='$title',
                            custom_link='$imageurl' where id=$imageid";
                        } 
                        $wpdb->query($query); 

                        $circle_image_carousel=array();
                        $circle_image_carousel['type']='succ';
                        $circle_image_carousel['message']='image updated successfully.';
                        update_option('circle_image_carousel', $circle_image_carousel);


                    }
                    catch(Exception $e){

                        $circle_image_carousel=array();
                        $circle_image_carousel['type']='err';
                        $circle_image_carousel['message']='Error while updating image.';
                        update_option('circle_image_carousel', $circle_image_carousel);
                    }  


                    echo "<script type='text/javascript'> location.href='$location';</script>";
                    exit;
                }
                else{

                    //add new

                    $location='admin.php?page=circle_thumbnail_slider_with_lightbox_image_management';
                    $title=trim(addslashes($_POST['imagetitle']));
                    $imageurl=trim($_POST['imageurl']);
                    $createdOn=date('Y-m-d h:i:s');
                    if(function_exists('date_i18n')){

                        $createdOn=date_i18n('Y-m-d'.' '.get_option('time_format') ,false,false);
                        if(get_option('time_format')=='H:i')
                            $createdOn=date('Y-m-d H:i:s',strtotime($createdOn));
                        else   
                            $createdOn=date('Y-m-d h:i:s',strtotime($createdOn));

                    }

                    if ($_FILES["image_name"]['name']!='' and $_FILES["image_name"]["error"] > 0)
                    {
                        $circle_image_carousel=array();
                        $circle_image_carousel['type']='err';
                        $circle_image_carousel['message']='Error while file uploading.';
                        update_option('circle_image_carousel', $circle_image_carousel);


                        echo "<script type='text/javascript'> location.href='$location';</script>";
                        exit;

                    }
                    else{
                        $location='admin.php?page=circle_thumbnail_slider_with_lightbox_image_management';

                        try{

                            if(isset($_FILES["image_name"]['name']) and $_FILES["image_name"]['name']!="" and $_FILES["image_name"]['name']!=null){

                                $wpcurrentdir=dirname(__FILE__);
                                $wpcurrentdir=str_replace("\\","/",$wpcurrentdir);
                                $path_parts = pathinfo($_FILES["image_name"]["name"]);
                                $extension = $path_parts['extension'];       
                                $imagename=md5(time()).".$extension";
                                $imageUploadTo=$pathToImagesFolder.'/'.$imagename;

                                move_uploaded_file($_FILES["image_name"]["tmp_name"],$imageUploadTo ); 

                            }
                            else if(trim($_POST['HdnMediaSelection'])!=''){

                                $postThumbnailID=(int)$_POST['HdnMediaSelection'];
                                $photoMeta = wp_get_attachment_metadata( $postThumbnailID );

                                if(is_array($photoMeta) and isset($photoMeta['file'])) {

                                    $fileName=$photoMeta['file'];
                                    $phyPath=ABSPATH;
                                    $phyPath=str_replace("\\","/",$phyPath);

                                    $pathArray=pathinfo($fileName);

                                    $imagename=$pathArray['basename'];

                                    $fileUrl=$phyPath.'wp-content/uploads/'.$fileName;
                                    $fileUrl=str_replace("\\","/",$fileUrl);

                                    $wpcurrentdir=dirname(__FILE__);
                                    $wpcurrentdir=str_replace("\\","/",$wpcurrentdir);
                                    $imageUploadTo=$pathToImagesFolder.'/'.$imagename;

                                    @copy($fileUrl, $imageUploadTo);

                                }

                            }
                            $query = "INSERT INTO ".$wpdb->prefix."circle_image_carousel (title, image_name,createdon,custom_link) 
                            VALUES ('$title','$imagename','$createdOn','$imageurl')";


                            $wpdb->query($query); 

                            $circle_image_carousel=array();
                            $circle_image_carousel['type']='succ';
                            $circle_image_carousel['message']='New image added successfully.';
                            update_option('circle_image_carousel', $circle_image_carousel);


                        }
                        catch(Exception $e){

                            $circle_image_carousel=array();
                            $circle_image_carousel['type']='err';
                            $circle_image_carousel['message']='Error while adding image.';
                            update_option('circle_image_carousel', $circle_image_carousel);
                        }  

                    }     
                    echo "<script type='text/javascript'> location.href='$location';</script>";          
					exit;
                } 

            }
            else{ 

            ?>
            <div id="poststuff">  
            <span><h3 style="color: blue;"><a target="_blank" href="http://www.i13websolution.com/wordpress-responsive-slider-with-responsive-lightbox.html">UPGRADE TO PRO VERSION</a></h3></span>
            <div id="post-body" class="metabox-holder columns-2" >
                <div id="post-body-content">
                    <?php if(isset($_GET['id']) and $_GET['id']>0)
                        { 


                            $id= $_GET['id'];
                            $query="SELECT * FROM ".$wpdb->prefix."circle_image_carousel WHERE id=$id";
                            $myrow  = $wpdb->get_row($query);

                            if(is_object($myrow)){

                                $title=stripslashes($myrow->title);
                                $image_link=$myrow->custom_link;
                                $image_name=stripslashes($myrow->image_name);

                            }   

                        ?>

                        <h2>Update Image </h2>

                        <?php }else{ 

                            $title='';
                            $image_link='';
                            $image_name='';

                        ?>

                        <h2>Add Image </h2>
                        <?php } ?>

                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="post-body-content">
                                <form method="post" action="" id="addimage" name="addimage" enctype="multipart/form-data" >

                                    <div class="stuffbox" id="namediv" style="width:100%">
                                        <h3><label for="link_name">Upload Image</label></h3>
                                        <div class="inside" id="fileuploaddiv">
                                            <?php if($image_name!=""){ ?>
                                                <div><b>Current Image : </b><a id="currImg" href="<?php echo $baseurl.$image_name; ?>" target="_new"><?php echo $image_name; ?></a></div>
                                                <?php } ?>      
                                            <input type="file" name="image_name" onchange="reloadfileupload();"  id="image_name" size="30" />
                                            <div style="clear:both"></div>
                                            <div></div>
                                            <div class="uploader">
                                                <br/>
                                                <b style="margin-left: 50px;">OR</b><div style="clear: both;margin-top: 15px;"></div>
                                                <?php if(circle_slider_plus_responsive_lightbox_get_wp_version()>=3.5){ ?>
                                                    <a href="javascript:;" class="niks_media" id="myMediaUploader"><b>Use WordPress Media Uploader</b></a>
                                                    <?php }?>  
                                                <input id="HdnMediaSelection" name="HdnMediaSelection" type="hidden" value="" />
                                                <br/>
                                            </div>  
                                            <script>
                                                var $n = jQuery.noConflict();  
                                                $n(document).ready(function() {
                                                        //uploading files variable
                                                        var custom_file_frame;
                                                        $n("#myMediaUploader").click(function(event) {
                                                                event.preventDefault();
                                                                //If the frame already exists, reopen it
                                                                if (typeof(custom_file_frame)!=="undefined") {
                                                                    custom_file_frame.close();
                                                                }

                                                                //Create WP media frame.
                                                                custom_file_frame = wp.media.frames.customHeader = wp.media({
                                                                        //Title of media manager frame
                                                                        title: "WP Media Uploader",
                                                                        library: {
                                                                            type: 'image'
                                                                        },
                                                                        button: {
                                                                            //Button text
                                                                            text: "Set Image"
                                                                        },
                                                                        //Do not allow multiple files, if you want multiple, set true
                                                                        multiple: false
                                                                });

                                                                //callback for selected image
                                                                custom_file_frame.on('select', function() {

                                                                        var attachment = custom_file_frame.state().get('selection').first().toJSON();


                                                                        var validExtensions=new Array();
                                                                        validExtensions[0]='jpg';
                                                                        validExtensions[1]='jpeg';
                                                                        validExtensions[2]='png';
                                                                        validExtensions[3]='gif';
                                                                        validExtensions[4]='bmp';
                                                                        validExtensions[5]='tif';

                                                                        var inarr=parseInt($n.inArray( attachment.subtype, validExtensions));

                                                                        if(inarr>0 && attachment.type.toLowerCase()=='image' ){

                                                                            var titleTouse="";
                                                                            var imageDescriptionTouse="";

                                                                            if($n.trim(attachment.title)!=''){

                                                                                titleTouse=$n.trim(attachment.title); 
                                                                            }  
                                                                            else if($n.trim(attachment.caption)!=''){

                                                                                titleTouse=$n.trim(attachment.caption);  
                                                                            }

                                                                            if($n.trim(attachment.description)!=''){

                                                                                imageDescriptionTouse=$n.trim(attachment.description); 
                                                                            }  
                                                                            else if($n.trim(attachment.caption)!=''){

                                                                                imageDescriptionTouse=$n.trim(attachment.caption);  
                                                                            }

                                                                            $n("#imagetitle").val(titleTouse);  
                                                                            $n("#image_description").val(imageDescriptionTouse);  

                                                                            if(attachment.id!=''){
                                                                                $n("#HdnMediaSelection").val(attachment.id);  
                                                                            }   

                                                                        }  
                                                                        else{

                                                                            alert('Invalid image selection.');
                                                                        }  
                                                                        //do something with attachment variable, for example attachment.filename
                                                                        //Object:
                                                                        //attachment.alt - image alt
                                                                        //attachment.author - author id
                                                                        //attachment.caption
                                                                        //attachment.dateFormatted - date of image uploaded
                                                                        //attachment.description
                                                                        //attachment.editLink - edit link of media
                                                                        //attachment.filename
                                                                        //attachment.height
                                                                        //attachment.icon - don't know WTF?))
                                                                        //attachment.id - id of attachment
                                                                        //attachment.link - public link of attachment, for example ""http://site.com/?attachment_id=115""
                                                                        //attachment.menuOrder
                                                                        //attachment.mime - mime type, for example image/jpeg"
                                                                        //attachment.name - name of attachment file, for example "my-image"
                                                                        //attachment.status - usual is "inherit"
                                                                        //attachment.subtype - "jpeg" if is "jpg"
                                                                        //attachment.title
                                                                        //attachment.type - "image"
                                                                        //attachment.uploadedTo
                                                                        //attachment.url - http url of image, for example "http://site.com/wp-content/uploads/2012/12/my-image.jpg"
                                                                        //attachment.width
                                                                });

                                                                //Open modal
                                                                custom_file_frame.open();
                                                        });
                                                })
                                            </script>
                                        </div>
                                    </div>    
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label for="link_name">Image Title</label></h3>
                                        <div class="inside">
                                            <input type="text" id="imagetitle"  size="30" name="imagetitle" value="<?php echo $title;?>">
                                            <div style="clear:both"></div>
                                            <div></div>
                                            <div style="clear:both"></div>
                                            <p><?php _e('Used in image alt for seo'); ?></p>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label for="link_name">Image Url(<?php _e('On click redirect to this url.'); ?>)</label></h3>
                                        <div class="inside">
                                            <input type="text" id="imageurl" class="url"   size="30" name="imageurl" value="<?php echo $image_link; ?>">
                                            <div style="clear:both"></div>
                                            <div></div>
                                            <div style="clear:both"></div>
                                            <p><?php _e('On image click users will redirect to this url.'); ?></p>
                                        </div>
                                    </div>
                                   
                                    <?php if(isset($_GET['id']) and $_GET['id']>0){ ?> 
                                        <input type="hidden" name="imageid" id="imageid" value="<?php echo $_GET['id'];?>">
                                        <?php
                                        } 
                                    ?>
                                    <input type="submit" onclick="return validateFile();" name="btnsave" id="btnsave" value="Save Changes" class="button-primary">&nbsp;&nbsp;<input type="button" name="cancle" id="cancle" value="Cancel" class="button-primary" onclick="location.href='admin.php?page=circle_thumbnail_slider_with_lightbox_image_management'">

                                </form> 
                                <script type="text/javascript">

                                    var $n = jQuery.noConflict();  
                                    $n(document).ready(function() {

                                            $n("#addimage").validate({
                                                    rules: {
                                                        imagetitle: {
                                                            required:true, 
                                                            maxlength: 200
                                                        },imageurl: {
                                                            url:true,  
                                                            maxlength: 500
                                                        },
                                                        image_name:{
                                                            isimage:true  
                                                        }
                                                    },
                                                    errorClass: "image_error",
                                                    errorPlacement: function(error, element) {
                                                        error.appendTo( element.next().next().next());
                                                    } 


                                            })
                                    });

                                   function validateFile(){

                                        var $n = jQuery.noConflict();  
                                        if($n('#currImg').length>0 || $n.trim($n("#HdnMediaSelection").val())!=""){
                                            return true;
                                        }
                                        var fragment = $n("#image_name").val();
                                        var filename = $n("#image_name").val().replace(/.+[\\\/]/, "");  
                                        var imageid=$n("#image_name").val();

                                        if(imageid==""){

                                            if(filename!="")
                                                return true;
                                            else
                                                {
                                                $n("#err_daynamic").remove();
                                                $n("#image_name").after('<label class="image_error" id="err_daynamic">Please select file or use media manager to select file.</label>');
                                                return false;  
                                            } 
                                        }
                                        else{
                                            return true;
                                        }      
                                    }
                                    function reloadfileupload(){

                                        var $n = jQuery.noConflict();  
                                        var fragment = $n("#image_name").val();
                                        var filename = $n("#image_name").val().replace(/.+[\\\/]/, "");
                                        var validExtensions=new Array();
                                        validExtensions[0]='jpg';
                                        validExtensions[1]='jpeg';
                                        validExtensions[2]='png';
                                        validExtensions[3]='gif';
                                        validExtensions[4]='bmp';
                                        validExtensions[5]='tif';

                                        var extension = filename.substr( (filename.lastIndexOf('.') +1) ).toLowerCase();

                                        var inarr=parseInt($n.inArray( extension, validExtensions));

                                        if(inarr<0){

                                            $n("#err_daynamic").remove();
                                            $n('#fileuploaddiv').html($n('#fileuploaddiv').html());   
                                            $n("#image_name").after('<label class="image_error" id="err_daynamic">Invalid file extension</label>');

                                        }
                                        else{
                                            $n("#err_daynamic").remove();

                                        } 


                                    }  
                                </script> 

                            </div>
                        </div>
                    </div>  
                </div>      
                <div id="postbox-container-1" class="postbox-container"> 
                    <div class="postbox"> 
                        <h3 class="hndle"><span></span>Access All Themes In One Price</h3> 
                        <div class="inside">
                            <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank"><img border="0" src="http://www.elegantthemes.com/affiliates/banners/300x250.gif" width="250" height="250"></a></center>

                            <div style="margin:10px 5px">

                            </div>
                        </div></div>
                    <div class="postbox"> 
                        <h3 class="hndle"><span></span>Best WordPress Hosting </h3> 
                        <div class="inside">
                            <center><a href="http://secure.hostgator.com/~affiliat/cgi-bin/affiliates/clickthru.cgi?id=nik00726-hs-wp"><img src="http://tracking.hostgator.com/img/WordPress_Hosting/300x250-animated.gif" width="250" height="250" border="0"></a></center>

                            <div style="margin:10px 5px">

                            </div>
                        </div></div>
                      
                </div>

            </div>
            <?php 
            } 
        }  

        else if(strtolower($action)==strtolower('delete')){

            $location='admin.php?page=circle_thumbnail_slider_with_lightbox_image_management';
            $deleteId=(int)$_GET['id'];

            $uploads = wp_upload_dir();
            $baseDir=$uploads['basedir'];
            $baseDir=str_replace("\\","/",$baseDir);
            $pathToImagesFolder=$baseDir.'/circle-image-slider-with-lightbox';
            
            try{


                $query="SELECT * FROM ".$wpdb->prefix."circle_image_carousel WHERE id=$deleteId";
                $myrow  = $wpdb->get_row($query);

                if(is_object($myrow)){

                    $image_name=stripslashes($myrow->image_name);
                    $wpcurrentdir=dirname(__FILE__);
                    $wpcurrentdir=str_replace("\\","/",$wpcurrentdir);
                    $imagename=$_FILES["image_name"]["name"];
                    $imagetoDel=$pathToImagesFolder.'/'.$image_name;
                    @unlink($imagetoDel);

                    $query = "delete from  ".$wpdb->prefix."circle_image_carousel where id=$deleteId";
                    $wpdb->query($query); 

                    $circle_image_carousel=array();
                    $circle_image_carousel['type']='succ';
                    $circle_image_carousel['message']='Image deleted successfully.';
                    update_option('circle_image_carousel', $circle_image_carousel);
                }    


            }
            catch(Exception $e){

                $circle_image_carousel=array();
                $circle_image_carousel['type']='err';
                $circle_image_carousel['message']='Error while deleting image.';
                update_option('circle_image_carousel', $circle_image_carousel);
            }  

            echo "<script type='text/javascript'> location.href='$location';</script>";
            exit;

        }  
        else if(strtolower($action)==strtolower('deleteselected')){

            $uploads = wp_upload_dir();
            $baseDir=$uploads['basedir'];
            $baseDir=str_replace("\\","/",$baseDir);
            $pathToImagesFolder=$baseDir.'/circle-image-slider-with-lightbox';
            
            $location='admin.php?page=circle_thumbnail_slider_with_lightbox_image_management'; 
            if(isset($_POST) and isset($_POST['deleteselected']) and  ( $_POST['action']=='delete' or $_POST['action_upper']=='delete')){

                if(sizeof($_POST['thumbnails']) >0){

                    $deleteto=$_POST['thumbnails'];
                    $implode=implode(',',$deleteto);   

                    try{

                        foreach($deleteto as $img){ 

                            $query="SELECT * FROM ".$wpdb->prefix."circle_image_carousel WHERE id=$img";
                            $myrow  = $wpdb->get_row($query);

                            if(is_object($myrow)){

                                $image_name=stripslashes($myrow->image_name);
                                $wpcurrentdir=dirname(__FILE__);
                                $wpcurrentdir=str_replace("\\","/",$wpcurrentdir);
                                $imagename=$_FILES["image_name"]["name"];
                                $imagetoDel=$pathToImagesFolder.'/'.$image_name;
                                @unlink($imagetoDel);
                                $query = "delete from  ".$wpdb->prefix."circle_image_carousel where id=$img";
                                $wpdb->query($query); 

                                $circle_image_carousel=array();
                                $circle_image_carousel['type']='succ';
                                $circle_image_carousel['message']='selected images deleted successfully.';
                                update_option('circle_image_carousel', $circle_image_carousel);
                            }

                        }

                    }
                    catch(Exception $e){

                        $circle_image_carousel=array();
                        $circle_image_carousel['type']='err';
                        $circle_image_carousel['message']='Error while deleting image.';
                        update_option('circle_image_carousel', $circle_image_carousel);
                    }  

                    echo "<script type='text/javascript'> location.href='$location';</script>";
					exit;

                }
                else{

                    echo "<script type='text/javascript'> location.href='$location';</script>"; 
                    exit;  
                }

            }
            else{

                echo "<script type='text/javascript'> location.href='$location';</script>"; 
                exit;     
            }

        }       
    } 
    function circle_thumbnail_slider_with_lightbox_admin_preview_func(){             
        $settings=get_option('circle_thumbnail_slider_plus_lightbox_settings');

    ?>      
    <style type='text/css' >
        .bx-wrapper_ .bx-viewport_ {
            background: none repeat scroll 0 0 <?php echo $settings['scollerBackground']; ?> !important;
            border: 0px none !important;
            box-shadow: 0 0 0 0 !important;
        }
    </style>
    <div style="">  
        <div style="float:left;">
            <div class="wrap">
                <h2>Slider Preview</h2>
                <br>

                <?php
                     $wpcurrentdir=dirname(__FILE__);
                     $wpcurrentdir=str_replace("\\","/",$wpcurrentdir);
                     $uploads = wp_upload_dir();
                     $baseurl=$uploads['baseurl'];
                     $baseurl.='/circle-image-slider-with-lightbox/';
                     $baseDir=$uploads['basedir'];
                     $baseDir=str_replace("\\","/",$baseDir);
                     $pathToImagesFolder=$baseDir.'/circle-image-slider-with-lightbox';
                     $prevButton_= uniqid('prev_');
                     $nextButton_=  uniqid('next_');

                ?>
                <div id="poststuff">
                    <span><h3 style="color: blue;"><a target="_blank" href="http://www.i13websolution.com/wordpress-responsive-slider-with-responsive-lightbox.html">UPGRADE TO PRO VERSION</a></h3></span>
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content">
                            <div style="clear: both;"></div>
                            <?php $url = plugin_dir_url(__FILE__);  ?>
                            <div style="postion:relative;margin: 0px auto;" id="divResponsiveSliderPlusLightboxMain_admin">
                                
                                  <?php if($settings['show_caption'] and !$settings['auto']):?>  
                                    <div class="prevButton_" id="<?php echo $prevButton_;?>" style="position: relative;"></div>
                                    <div class="nextButton_" id="<?php echo $nextButton_;?>" style="position: relative;"></div>
                                  <?php endif; ?>
                                <script>
                                 function clickedItem(uniqObjList){ 
                                        var uniqObj=$n("a[rel='<?php echo $randOmeAlbName;?>']");
                                         $n(".r_lbox").fancybox({
                                         'overlayColor':'#000000',
                                          'padding': 10,
                                          'autoScale': true,
                                          'autoDimensions':true,
                                          'transitionIn': 'none',
                                          'uniqObj':uniqObjList,
                                          'transitionOut': 'none',
                                          'titlePosition': 'over',
                                          <?php if ($settings['circular']): ?>
                                          'cyclic':true,
                                         <?php else: ?>
                                          'cyclic':false,
                                         <?php endif; ?>
                                          'hideOnContentClick':false,
                                          'width' : 600,
                                          'height' : 350,
                                          'titleFormat': function(title, currentArray, currentIndex, currentOpts) {

                                              var currtElem = $n('.responsiveSlider a[href="'+currentOpts.href+'"]');

                                              var isoverlay = $n(currtElem).attr('data-overlay')

                                             if(isoverlay=="1" && $n.trim(title)!=""){

                                              return '<span id="fancybox-title-over">' + title  + '</span>';
                                             }
                                             else{
                                                 return '';
                                             }

                                             },

                                        });

                                         return false;
                                    }
                                 </script>
                                 <div class="responsiveSlider" style="margin-top: 2px !important;">
                                    <?php
                                        global $wpdb;
                                        $imageheight=$settings['imageheight'];
                                        $imagewidth=$settings['imagewidth'];
                                        $query="SELECT * FROM ".$wpdb->prefix."circle_image_carousel order by createdon desc";
                                        $rows=$wpdb->get_results($query,'ARRAY_A');
                                        $randOmeAlbName=uniqid('slider_');
                                        if(count($rows) > 0){
                                            foreach($rows as $row){

                                                $imagename=$row['image_name'];
                                                $imageUploadTo=$baseurl.$imagename;
                                                $imageUploadTo=str_replace("\\","/",$imageUploadTo);
                                                $pathinfo=pathinfo($imageUploadTo);
                                                $filenamewithoutextension=$pathinfo['filename'];
                                                $outputimg="";

                                                $outputimgmain = $baseurl.$row['image_name']; 
                                                if($settings['resizeImages']==0){

                                                    $outputimg = $baseurl.$row['image_name']; 

                                                }
                                                else{
                                                    
                                                    $imagetoCheck=$pathToImagesFolder.'/'.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];
                                                    $imagetoCheckSmall=$pathToImagesFolder.'/'.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.strtolower($pathinfo['extension']);
                                           

                                                    if(file_exists($imagetoCheck)){
                                                        
                                                        $outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];
                                                    }
                                                    else if(file_exists($imagetoCheckSmall)){
                                                        
                                                        $outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.strtolower($pathinfo['extension']);
                                                    }
                                                    else{

                                                        if(function_exists('wp_get_image_editor')){

                                                            
                                                            $image = wp_get_image_editor($pathToImagesFolder.'/'.$row['image_name']); 

                                                            if ( ! is_wp_error( $image ) ) {
                                                                $image->resize( $imagewidth, $imageheight, true );
                                                                $image->save( $imagetoCheck );
                                                                //$outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];
                                                                
                                                                 if(file_exists($imagetoCheck)){
                                                                    $outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];
                                                                }
                                                                else if(file_exists($imagetoCheckSmall)){
                                                                    $outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.strtolower($pathinfo['extension']);
                                                                }
                                                                
                                                            }
                                                            else{
                                                                $outputimg = $baseurl.$row['image_name'];
                                                            }     

                                                        }
                                                        else if(function_exists('image_resize')){

                                                            $return=image_resize($pathToImagesFolder."/".$row['image_name'],$imagewidth,$imageheight) ;
                                                            if ( ! is_wp_error( $return ) ) {

                                                                $isrenamed=rename($return,$imagetoCheck);
                                                                if($isrenamed){
                                                                    //$outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];  
                                                                    
                                                                   if(file_exists($imagetoCheck)){
                                                                        $outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];
                                                                    }
                                                                    else if(file_exists($imagetoCheckSmall)){
                                                                        $outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.strtolower($pathinfo['extension']);
                                                                    }
                                                                    
                                                                }
                                                                else{
                                                                    $outputimg = $baseurl.$row['image_name']; 
                                                                } 
                                                            }
                                                            else{
                                                                $outputimg = $baseurl.$row['image_name'];
                                                            }  
                                                        }
                                                        else{

                                                            $outputimg = $baseurl.$row['image_name'];
                                                        }  

                                                        //$url = plugin_dir_url(__FILE__)."imagestoscroll/".$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];

                                                    } 
                                                } 

                                                $title="";
                                                $Caption="";
                                                $rowTitle=stripslashes($row['title']);
                                                $rowTitle=str_replace("'","’",$rowTitle); 
                                                $rowTitle=str_replace('"','”',$rowTitle); 
                                                if(trim($row['title'])!='' and trim($row['custom_link'])!=''){

                                                    $Caption="<div class='mycaption_title'><a class='Captionlink' href='{$row['custom_link']}'>{$rowTitle}</a></div>";
                                                    $title="<a class='Imglink'  href='{$row['custom_link']}'>{$rowTitle}</a>";

                                                }
                                                else if(trim($row['title'])!='' and trim($row['custom_link'])==''){
                                                    
                                                   $Caption="<div class='mycaption_title'><a  class='Captionlink' >{$rowTitle}</a></div>";
                                                   $title="<a class='Imglink' href='#'>{$rowTitle}</a>"; 

                                                }
                                                else{

                                                    if($row['title']!='')
                                                        $title="<a class='Imglink' >{$rowTitle}</a>"; 
                                                }

                                            ?>         

                                            <div class="limargin">
                                                <a style="cursor: default"  rel="<?php echo $randOmeAlbName;?>" data-overlay="1" data-title="<?php echo $title;?>" class="r_lbox" <?php if($settings['lightbox']):?> href="<?php echo $outputimgmain;?>" <?php elseif($row['custom_link']!='' ):?> href="<?php echo $row['custom_link'];?>" <?php else: ?> <?php endif;?>>
                                                    <div class="circle-img" >
                                                        <img <?php if($settings['lightbox']):?> onclick="return clickedItem(uniqObj);" <?php endif;?> src="<?php echo $outputimg; ?>" alt="<?php echo $rowTitle; ?>" title="<?php if(trim($rowTitle)!=''){ echo $rowTitle;}  ?>"  />
                                                    </div>
                                                </a> 
                                                <?php if($settings['show_caption']):?>  
                                                    <div class='myCaption'>
                                                        <?php echo $Caption; ?>
                                                    </div>
                                                <?php endif;?>
                                            </div>

                                            <?php }?>   
                                        <?php }?>   
                                </div>
                            </div>
                            <script>
                                var $n = jQuery.noConflict();
                                var uniqObj=$n("a[rel='<?php echo $randOmeAlbName;?>']");
                                $n(document).ready(function(){
                                        var sliderMainHtmladmin=$n('#divResponsiveSliderPlusLightboxMain_admin').html();      
                                        var slider= $n('.responsiveSlider').bxSlider_({
                                          <?php if( $settings['visible']==1):?>
					    mode:'fade',
					   <?php endif;?>
						slideWidth: <?php echo $settings['imagewidth'];?>,
                                                minSlides: <?php echo $settings['min_visible'];?>,
                                                maxSlides: <?php echo $settings['visible'];?>,
                                                moveSlides: <?php echo $settings['scroll'];?>,
                                                slideMargin: <?php echo $settings['imageMargin'];?>,  
                                                speed:<?php echo $settings['speed']; ?>,
                                                pause:<?php echo $settings['pause']; ?>,
                                                <?php if($settings['show_caption'] and !$settings['auto']):?>  
                                                      prevSelector:'#<?php echo $prevButton_;?>',
                                                      prevText:'Prev',
                                                      nextSelector:'#<?php echo $nextButton_;?>',
                                                      nextText:'Next',
                                                  <?php endif; ?>   
                                                <?php if($settings['pauseonmouseover'] and $settings['auto']){ ?>
                                                    autoHover: true,
                                                    <?php }else{ if($settings['auto']){?>   
                                                        autoHover:false,
                                                        <?php }} ?>
                                                <?php if($settings['auto']):?>
                                                    controls:false,
                                                    <?php else: ?>
                                                    controls:true,
                                                    <?php endif;?>
                                                pager:false,
                                                useCSS:false,
                                                <?php if($settings['auto']):?>
                                                    autoStart:true,
                                                    autoDelay:200,
                                                    auto:true,       
                                                    <?php endif;?>
                                                infiniteLoop: <?php echo ($settings['circular'])? 'true':'false' ?>,
                                                 onSliderLoad: function(){
                                   
                                                         var maxHeight = Math.max.apply(null, $n(".responsiveSlider .limargin .circle-img ").map(function ()
                                                             {
                                                                 return $n(this).height();
                                                             }).get());

                                                             
                                                            var heightscroll=parseInt(maxHeight/2)-4;
                                                            $n("#<?php echo $prevButton_;?>").css('top',heightscroll+'px');     
                                                            $n("#<?php echo $nextButton_;?>").css('top',heightscroll+'px');     
                                                            $n("#divResponsiveSliderPlusLightboxMain_admin").css('max-width',$n("#divResponsiveSliderPlusLightboxMain_admin .bx-wrapper_").width()+'px');    

                                  
                                     
                                                      }        


                                        });
                                        <?php if($settings['auto']){?>

                                            var is_firefox=navigator.userAgent.toLowerCase().indexOf('firefox') > -1;  
                                            var is_android=navigator.userAgent.toLowerCase().indexOf('android') > -1;
                                            var is_iphone=navigator.userAgent.toLowerCase().indexOf('iphone') > -1;
                                            var width = $n(window).width();
                                            if(is_firefox && (is_android || is_iphone)){

                                            }else{
                                                var timer;
                                                $n(window).bind('resize', function(){
                                                        if($n(window).width() != width){ 

                                                            width = $n(window).width();   
                                                            timer && clearTimeout(timer);
                                                            timer = setTimeout(onResize, 600);

                                                        }
                                                });

                                            }   

                                              function onResize(){
                                                slider.reloadSlider();
                                                $n(".responsiveSliderWithResponsiveLightbox").show();
                                            }
                                         
                                            <?php }?>  
                                                
                                                
                                                
                                                 var widthx = $n(window).width();
                                                 function onResize_arrow(){

                                                     if($n(window).width() != widthx){
                                                        widthx = $n(window).width();
                                                       $n("#divResponsiveSliderPlusLightboxMain_admin").css('max-width','100%');
                                                       slider.reloadSlider();
                                                       $n(".responsiveSlider").show();
                                                     }


                                                     var maxHeight = Math.max.apply(null, $n(".responsiveSlider .limargin .circle-img ").map(function ()
                                                        {
                                                            return $n(this).height();
                                                        }).get());


                                                       var heightscroll=parseInt(maxHeight/2)-4;
                                                       $n("#<?php echo $prevButton_;?>").css('top',heightscroll+'px');     
                                                       $n("#<?php echo $nextButton_;?>").css('top',heightscroll+'px');     
                                                       $n("#divResponsiveSliderPlusLightboxMain_admin").css('max-width',$n("#divResponsiveSliderPlusLightboxMain_admin .bx-wrapper_").width()+'px');    

                                                   }

                                                var timerx;
                                                $n(window).bind('resize', function(){


                                                           timerx && clearTimeout(timerx);
                                                           timerx = setTimeout(onResize_arrow, 200);


                                                   });


                                              var maxHeight = Math.max.apply(null, $n(".responsiveSlider .limargin .circle-img ").map(function ()
                                                {
                                                    return $n(this).height();
                                                }).get());


                                               var heightscroll=parseInt(maxHeight/2)-4;
                                               $n("#<?php echo $prevButton_;?>").css('top',heightscroll+'px');     
                                               $n("#<?php echo $nextButton_;?>").css('top',heightscroll+'px');     
                                               $n("#divResponsiveSliderPlusLightboxMain_admin").css('max-width',$n("#divResponsiveSliderPlusLightboxMain_admin .bx-wrapper_").width()+'px');    


                                });
                                
                                 $n(document).ready(function(){

                                      var maxHeight = Math.max.apply(null, $n(".responsiveSlider .limargin .circle-img ").map(function ()
                                        {
                                            return $n(this).height();
                                        }).get());


                                       var heightscroll=parseInt(maxHeight/2)-4;
                                       $n("#<?php echo $prevButton_;?>").css('top',heightscroll+'px');     
                                       $n("#<?php echo $nextButton_;?>").css('top',heightscroll+'px');     
                                       $n("#divResponsiveSliderPlusLightboxMain_admin").css('max-width',$n("#divResponsiveSliderPlusLightboxMain_admin .bx-wrapper_").width()+'px');    

                                   }); 
                                  $n( window ).load(function() {

                                       var maxHeight = Math.max.apply(null, $n(".responsiveSlider .limargin .circle-img ").map(function ()
                                        {
                                            return $n(this).height();
                                        }).get());


                                       var heightscroll=parseInt(maxHeight/2)-4;
                                       $n("#<?php echo $prevButton_;?>").css('top',heightscroll+'px');     
                                       $n("#<?php echo $nextButton_;?>").css('top',heightscroll+'px');     
                                       $n("#divResponsiveSliderPlusLightboxMain_admin").css('max-width',$n("#divResponsiveSliderPlusLightboxMain_admin .bx-wrapper_").width()+'px');    


                                   }); 
                            </script>

                        </div>
                    </div>      
                </div>  
            </div>      
        </div>
        <div class="clear"></div>
    </div>
    <h3>To print this slider into WordPress Post/Page use below Short code</h3>
    <input type="text" value="[print_circle_slider_plus_lightbox]" style="width: 400px;height: 30px" onclick="this.focus();this.select()" />
    <div class="clear"></div>
    <h3>To print this slider into WordPress theme/template PHP files use below php code</h3>
    <input type="text" value="echo do_shortcode('[print_circle_slider_plus_lightbox]');" style="width: 400px;height: 30px" onclick="this.focus();this.select()" />
    <div class="clear"></div>
    <div class="clear"></div>
    <?php       
    }

    function print_circle_slider_plus_lightbox_func($atts){

        $settings=get_option('circle_thumbnail_slider_plus_lightbox_settings');    
        $wpcurrentdir=dirname(__FILE__);
        $wpcurrentdir=str_replace("\\","/",$wpcurrentdir);
        $uploads = wp_upload_dir();
        $baseurl=$uploads['baseurl'];
        $baseurl.='/circle-image-slider-with-lightbox/';
        $baseDir=$uploads['basedir'];
        $baseDir=str_replace("\\","/",$baseDir);
        $pathToImagesFolder=$baseDir.'/circle-image-slider-with-lightbox';
        $prevButton_= uniqid('prev_');
        $nextButton_=  uniqid('next_');
        ob_start();
    ?>  
    <style type='text/css' >
        .bx-wrapper_ .bx-viewport_ {
            background: none repeat scroll 0 0 <?php echo $settings['scollerBackground']; ?> !important;
            border: 0px none !important;
            box-shadow: 0 0 0 0 !important;
        }
    </style>
    <div style="clear: both;"></div>
                            <?php $url = plugin_dir_url(__FILE__);  ?>
                            <div style="postion:relative;margin: 0px auto;" id="divResponsiveSliderPlusLightboxMain_admin">
                                
                                  <?php if($settings['show_caption'] and !$settings['auto']):?>  
                                    <div class="prevButton_" id="<?php echo $prevButton_;?>" style="position: relative;"></div>
                                    <div class="nextButton_" id="<?php echo $nextButton_;?>" style="position: relative;"></div>
                                  <?php endif; ?>
                                <script>
                                 function clickedItem(uniqObjList){ 
                                        var uniqObj=$n("a[rel='<?php echo $randOmeAlbName;?>']");
                                         $n(".r_lbox").fancybox({
                                         'overlayColor':'#000000',
                                          'padding': 10,
                                          'autoScale': true,
                                          'autoDimensions':true,
                                          'transitionIn': 'none',
                                          'uniqObj':uniqObjList,
                                          'transitionOut': 'none',
                                          'titlePosition': 'over',
                                          <?php if ($settings['circular']): ?>
                                          'cyclic':true,
                                         <?php else: ?>
                                          'cyclic':false,
                                         <?php endif; ?>
                                          'hideOnContentClick':false,
                                          'width' : 600,
                                          'height' : 350,
                                          'titleFormat': function(title, currentArray, currentIndex, currentOpts) {

                                              var currtElem = $n('.responsiveSlider a[href="'+currentOpts.href+'"]');

                                              var isoverlay = $n(currtElem).attr('data-overlay')

                                             if(isoverlay=="1" && $n.trim(title)!=""){

                                              return '<span id="fancybox-title-over">' + title  + '</span>';
                                             }
                                             else{
                                                 return '';
                                             }

                                             },

                                        });

                                         return false;
                                    }
                                 </script>
                                 <div class="responsiveSlider" style="margin-top: 2px !important;">
                                    <?php
                                        global $wpdb;
                                        $imageheight=$settings['imageheight'];
                                        $imagewidth=$settings['imagewidth'];
                                        $query="SELECT * FROM ".$wpdb->prefix."circle_image_carousel order by createdon desc";
                                        $rows=$wpdb->get_results($query,'ARRAY_A');
                                        $randOmeAlbName=uniqid('slider_');
                                        if(count($rows) > 0){
                                            foreach($rows as $row){

                                                $imagename=$row['image_name'];
                                                $imageUploadTo=$baseurl.$imagename;
                                                $imageUploadTo=str_replace("\\","/",$imageUploadTo);
                                                $pathinfo=pathinfo($imageUploadTo);
                                                $filenamewithoutextension=$pathinfo['filename'];
                                                $outputimg="";

                                                $outputimgmain = $baseurl.$row['image_name']; 
                                                if($settings['resizeImages']==0){

                                                    $outputimg = $baseurl.$row['image_name']; 

                                                }
                                                else{
                                                    
                                                    $imagetoCheck=$pathToImagesFolder.'/'.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];
                                                    $imagetoCheckSmall=$pathToImagesFolder.'/'.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.strtolower($pathinfo['extension']);
                                           

                                                    if(file_exists($imagetoCheck)){
                                                        
                                                        $outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];
                                                    }
                                                    else if(file_exists($imagetoCheckSmall)){
                                                        
                                                        $outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.strtolower($pathinfo['extension']);
                                                    }
                                                    else{

                                                        if(function_exists('wp_get_image_editor')){

                                                            
                                                            $image = wp_get_image_editor($pathToImagesFolder.'/'.$row['image_name']); 

                                                            if ( ! is_wp_error( $image ) ) {
                                                                $image->resize( $imagewidth, $imageheight, true );
                                                                $image->save( $imagetoCheck );
                                                                //$outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];
                                                                
                                                                 if(file_exists($imagetoCheck)){
                                                                    $outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];
                                                                }
                                                                else if(file_exists($imagetoCheckSmall)){
                                                                    $outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.strtolower($pathinfo['extension']);
                                                                }
                                                                
                                                            }
                                                            else{
                                                                $outputimg = $baseurl.$row['image_name'];
                                                            }     

                                                        }
                                                        else if(function_exists('image_resize')){

                                                            $return=image_resize($pathToImagesFolder."/".$row['image_name'],$imagewidth,$imageheight) ;
                                                            if ( ! is_wp_error( $return ) ) {

                                                                $isrenamed=rename($return,$imagetoCheck);
                                                                if($isrenamed){
                                                                    //$outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];  
                                                                    
                                                                   if(file_exists($imagetoCheck)){
                                                                        $outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];
                                                                    }
                                                                    else if(file_exists($imagetoCheckSmall)){
                                                                        $outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.strtolower($pathinfo['extension']);
                                                                    }
                                                                    
                                                                }
                                                                else{
                                                                    $outputimg = $baseurl.$row['image_name']; 
                                                                } 
                                                            }
                                                            else{
                                                                $outputimg = $baseurl.$row['image_name'];
                                                            }  
                                                        }
                                                        else{

                                                            $outputimg = $baseurl.$row['image_name'];
                                                        }  

                                                        //$url = plugin_dir_url(__FILE__)."imagestoscroll/".$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];

                                                    } 
                                                } 

                                                $title="";
                                                $Caption="";
                                                $rowTitle=stripslashes($row['title']);
                                                $rowTitle=str_replace("'","’",$rowTitle); 
                                                $rowTitle=str_replace('"','”',$rowTitle); 
                                                if(trim($row['title'])!='' and trim($row['custom_link'])!=''){

                                                    $Caption="<div class='mycaption_title'><a class='Captionlink' href='{$row['custom_link']}'>{$rowTitle}</a></div>";
                                                    $title="<a class='Imglink'  href='{$row['custom_link']}'>{$rowTitle}</a>";

                                                }
                                                else if(trim($row['title'])!='' and trim($row['custom_link'])==''){
                                                    
                                                   $Caption="<div class='mycaption_title'><a  class='Captionlink' >{$rowTitle}</a></div>";
                                                   $title="<a class='Imglink' href='#'>{$rowTitle}</a>"; 

                                                }
                                                else{

                                                    if($row['title']!='')
                                                        $title="<a class='Imglink' >{$rowTitle}</a>"; 
                                                }

                                            ?>         

                                            <div class="limargin">
                                                <a style="cursor: default"  rel="<?php echo $randOmeAlbName;?>" data-overlay="1" data-title="<?php echo $title;?>" class="r_lbox" <?php if($settings['lightbox']):?> href="<?php echo $outputimgmain;?>" <?php elseif($row['custom_link']!='' ):?> href="<?php echo $row['custom_link'];?>" <?php else: ?> <?php endif;?>>
                                                    <div class="circle-img" >
                                                        <img <?php if($settings['lightbox']):?> onclick="return clickedItem(uniqObj);" <?php endif;?> src="<?php echo $outputimg; ?>" alt="<?php echo $rowTitle; ?>" title="<?php if(trim($rowTitle)!=''){ echo $rowTitle;}  ?>"  />
                                                    </div>
                                                </a> 
                                                <?php if($settings['show_caption']):?>  
                                                    <div class='myCaption'>
                                                        <?php echo $Caption; ?>
                                                    </div>
                                                <?php endif;?>
                                            </div>

                                            <?php }?>   
                                        <?php }?>   
                                </div>
                            </div>
                            <script>
                                var $n = jQuery.noConflict();
                                var uniqObj=$n("a[rel='<?php echo $randOmeAlbName;?>']");
                                $n(document).ready(function(){
                                        var sliderMainHtmladmin=$n('#divResponsiveSliderPlusLightboxMain_admin').html();      
                                        var slider= $n('.responsiveSlider').bxSlider_({
                                          <?php if( $settings['visible']==1):?>
					    mode:'fade',
					   <?php endif;?>
						slideWidth: <?php echo $settings['imagewidth'];?>,
                                                minSlides: <?php echo $settings['min_visible'];?>,
                                                maxSlides: <?php echo $settings['visible'];?>,
                                                moveSlides: <?php echo $settings['scroll'];?>,
                                                slideMargin: <?php echo $settings['imageMargin'];?>,  
                                                speed:<?php echo $settings['speed']; ?>,
                                                pause:<?php echo $settings['pause']; ?>,
                                                <?php if($settings['show_caption'] and !$settings['auto']):?>  
                                                      prevSelector:'#<?php echo $prevButton_;?>',
                                                      prevText:'Prev',
                                                      nextSelector:'#<?php echo $nextButton_;?>',
                                                      nextText:'Next',
                                                  <?php endif; ?>   
                                                <?php if($settings['pauseonmouseover'] and $settings['auto']){ ?>
                                                    autoHover: true,
                                                    <?php }else{ if($settings['auto']){?>   
                                                        autoHover:false,
                                                        <?php }} ?>
                                                <?php if($settings['auto']):?>
                                                    controls:false,
                                                    <?php else: ?>
                                                    controls:true,
                                                    <?php endif;?>
                                                pager:false,
                                                useCSS:false,
                                                <?php if($settings['auto']):?>
                                                    autoStart:true,
                                                    autoDelay:200,
                                                    auto:true,       
                                                    <?php endif;?>
                                                infiniteLoop: <?php echo ($settings['circular'])? 'true':'false' ?>,
                                                 onSliderLoad: function(){
                                   
                                                         var maxHeight = Math.max.apply(null, $n(".responsiveSlider .limargin .circle-img ").map(function ()
                                                             {
                                                                 return $n(this).height();
                                                             }).get());

                                                             
                                                            var heightscroll=parseInt(maxHeight/2)-4;
                                                            $n("#<?php echo $prevButton_;?>").css('top',heightscroll+'px');     
                                                            $n("#<?php echo $nextButton_;?>").css('top',heightscroll+'px');     
                                                            $n("#divResponsiveSliderPlusLightboxMain_admin").css('max-width',$n("#divResponsiveSliderPlusLightboxMain_admin .bx-wrapper_").width()+'px');    

                                  
                                     
                                                      }        


                                        });
                                        <?php if($settings['auto']){?>

                                            var is_firefox=navigator.userAgent.toLowerCase().indexOf('firefox') > -1;  
                                            var is_android=navigator.userAgent.toLowerCase().indexOf('android') > -1;
                                            var is_iphone=navigator.userAgent.toLowerCase().indexOf('iphone') > -1;
                                            var width = $n(window).width();
                                            if(is_firefox && (is_android || is_iphone)){

                                            }else{
                                                var timer;
                                                $n(window).bind('resize', function(){
                                                        if($n(window).width() != width){ 

                                                            width = $n(window).width();   
                                                            timer && clearTimeout(timer);
                                                            timer = setTimeout(onResize, 600);

                                                        }
                                                });

                                            }   

                                              function onResize(){
                                                slider.reloadSlider();
                                                $n(".responsiveSliderWithResponsiveLightbox").show();
                                            }
                                         
                                            <?php }?>  
                                                
                                                
                                                
                                                 var widthx = $n(window).width();
                                                 function onResize_arrow(){

                                                     if($n(window).width() != widthx){
                                                        widthx = $n(window).width();
                                                       $n("#divResponsiveSliderPlusLightboxMain_admin").css('max-width','100%');
                                                       slider.reloadSlider();
                                                       $n(".responsiveSlider").show();
                                                     }


                                                     var maxHeight = Math.max.apply(null, $n(".responsiveSlider .limargin .circle-img ").map(function ()
                                                        {
                                                            return $n(this).height();
                                                        }).get());


                                                       var heightscroll=parseInt(maxHeight/2)-4;
                                                       $n("#<?php echo $prevButton_;?>").css('top',heightscroll+'px');     
                                                       $n("#<?php echo $nextButton_;?>").css('top',heightscroll+'px');     
                                                       $n("#divResponsiveSliderPlusLightboxMain_admin").css('max-width',$n("#divResponsiveSliderPlusLightboxMain_admin .bx-wrapper_").width()+'px');    

                                                   }

                                                var timerx;
                                                $n(window).bind('resize', function(){


                                                           timerx && clearTimeout(timerx);
                                                           timerx = setTimeout(onResize_arrow, 200);


                                                   });


                                              var maxHeight = Math.max.apply(null, $n(".responsiveSlider .limargin .circle-img ").map(function ()
                                                {
                                                    return $n(this).height();
                                                }).get());


                                               var heightscroll=parseInt(maxHeight/2)-4;
                                               $n("#<?php echo $prevButton_;?>").css('top',heightscroll+'px');     
                                               $n("#<?php echo $nextButton_;?>").css('top',heightscroll+'px');     
                                               $n("#divResponsiveSliderPlusLightboxMain_admin").css('max-width',$n("#divResponsiveSliderPlusLightboxMain_admin .bx-wrapper_").width()+'px');    


                                });
                                
                                 $n(document).ready(function(){

                                      var maxHeight = Math.max.apply(null, $n(".responsiveSlider .limargin .circle-img ").map(function ()
                                        {
                                            return $n(this).height();
                                        }).get());


                                       var heightscroll=parseInt(maxHeight/2)-4;
                                       $n("#<?php echo $prevButton_;?>").css('top',heightscroll+'px');     
                                       $n("#<?php echo $nextButton_;?>").css('top',heightscroll+'px');     
                                       $n("#divResponsiveSliderPlusLightboxMain_admin").css('max-width',$n("#divResponsiveSliderPlusLightboxMain_admin .bx-wrapper_").width()+'px');    

                                   }); 
                                  $n( window ).load(function() {

                                       var maxHeight = Math.max.apply(null, $n(".responsiveSlider .limargin .circle-img ").map(function ()
                                        {
                                            return $n(this).height();
                                        }).get());


                                       var heightscroll=parseInt(maxHeight/2)-4;
                                       $n("#<?php echo $prevButton_;?>").css('top',heightscroll+'px');     
                                       $n("#<?php echo $nextButton_;?>").css('top',heightscroll+'px');     
                                       $n("#divResponsiveSliderPlusLightboxMain_admin").css('max-width',$n("#divResponsiveSliderPlusLightboxMain_admin .bx-wrapper_").width()+'px');    


                                   }); 
                            </script>   
    <?php
        $output = ob_get_clean();
        return $output;
    }

    function circle_slider_plus_responsive_lightbox_get_wp_version() {
        global $wp_version;
        return $wp_version;
    }

    //also we will add an option function that will check for plugin admin page or not
    function circle_slider_plus_lightbox_is_plugin_page() {
        $server_uri = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

        foreach (array('circle_thumbnail_slider_with_lightbox_image_management','circle_thumbnail_slider_with_lightbox') as $allowURI) {
            if(stristr($server_uri, $allowURI)) return true;
        }
        return false;
    }

    //add media WP scripts
    function circle_slider_plus_lightbox_admin_scripts_init() {
        if(circle_slider_plus_lightbox_is_plugin_page()) {
            //double check for WordPress version and function exists
            if(function_exists('wp_enqueue_media') && version_compare(circle_slider_plus_responsive_lightbox_get_wp_version(), '3.5', '>=')) {
                //call for new media manager
                wp_enqueue_media();
            }
            wp_enqueue_style('media');
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );

            
        }
    }
?>