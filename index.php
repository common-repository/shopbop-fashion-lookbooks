<?php
/*
    Plugin Name: Shopbop Lookbook Viewer
    Plugin URI: https://www.stylst.com/
    Description: Shopbop Sidebar Advertising
    Version: 1.5.4
    Author: SocialRoot
    Author URI: https://www.stylst.com
    License: GPLv2 or later
*/

    define ('DLS_PLUGIN_PATH', dirname( __FILE__ ));
    define ('DLS_PLUGIN_URL', plugin_dir_url( __FILE__ ));
    define ('DLS_PLUGIN_BASENAME', plugin_basename(__FILE__));
    define ('DLS_VERSION', "1.5.4");
    
    require_once DLS_PLUGIN_PATH . '/inc/widget.php';
    
    function sp_in_development()
    {
        return strtolower(substr($_SERVER['HTTP_HOST'], 0, 9)) == 'localhost';
    }
    
    function sp_digitallylux_admin_init()
    {
        register_setting( 'sp_digitallylux_options', 'sp_digitallylux_options', 'sp_digitallylux_options_validate' );
    }

    function sp_digitallylux_admin_menu()
    {
        add_options_page(__('ShopBop Configuration'), __('ShopBop'), 'manage_options', 'sp_digitallylux_options', 'sp_digitallylux_options_page');
    }
    
    function sp_digitallylux_options_page()
    {
        require DLS_PLUGIN_PATH . '/tpl/options.php';
    }
    
    function sp_digitallylux_options_validate($input)
    {
        // Our first value is either 0 or 1
        $input['site_id'] = intval($input['site_id']);
        return $input;
    }
    
    // add_action('admin_init', 'sp_digitallylux_admin_init', 12);
    // add_action('admin_menu', 'sp_digitallylux_admin_menu', 12);
    
    function sp_digitallylux_init()
    {
        $placement = sp_digitallylux_options('placement');
        switch ($placement) {
            case 'top':
                add_action('loop_start', 'sp_digitallylux_widget');
                break;
            case 'bottom':
                add_action('loop_end', 'sp_digitallylux_widget');
                break;
            default:
                // nothing
        }

        $name = sp_digitallylux_options('name');
        if ( !$name ) {
            $cj_filename = str_replace('.', '', $_SERVER['HTTP_HOST']);
        } else {
            $cj_filename = strtolower($name);
        }

        if (sp_in_development())
        {
            $api_url = 'https://localhost:4567';
        }
        else
        {
            $api_url = 'https://home.digitallylux.com';
        }
    }

    function sp_digitallylux_action_links($links, $file)
    {
        if ( $file == DLS_PLUGIN_BASENAME ) {
            $link = '<a href="options-general.php?page=sp_digitallylux_options">Settings</a>';
            array_unshift($links, $link);
        }
        return $links;
    }

    function sp_digitallylux_options($key = null)
    {
        $options = get_option('sp_digitallylux_options');
        return $key ? $options[$key] : $options;
    }

    function sp_digitallylux_style_and_scripts()
    {
        wp_register_style('shopbop-css', DLS_PLUGIN_URL . 'css/shopbop.css?sv=' . DLS_VERSION, array());
        wp_enqueue_style('shopbop-css');
    }

    add_action( 'init', 'sp_digitallylux_init' );
    add_action( 'admin_enqueue_scripts', 'sp_digitallylux_style_and_scripts' );
    add_action( 'wp_enqueue_scripts', 'sp_digitallylux_style_and_scripts' );
    // add_filter( 'plugin_action_links', 'sp_digitallylux_action_links', 10, 2 );

    add_action('wp_head', 'sp_digitallylux_add_ga_tracking_code', 10, 1);
    function sp_digitallylux_add_ga_tracking_code() {
        $ga_tracking_id = 'UA-141583095-1';
        $site_name = get_bloginfo( 'name' );
        $site_url = site_url();
        ?>
        <!-- Google Analytics -->
        <script>
        
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', '<?= $ga_tracking_id ?>', 'auto', 'shopbopTracker');

        var __site_name = '<?= $site_name ?>';
        var __site_url = '<?= $site_url ?>';

        ga('set', 'dimension1', __site_name);
        ga('set', 'dimension2', __site_url);

        ga('shopbopTracker.send', 'event', 'site_info', 'view');
        ga('shopbopTracker.send', 'pageview');
        </script>
        <!-- End Google Analytics -->

        <?php
    }


?>
