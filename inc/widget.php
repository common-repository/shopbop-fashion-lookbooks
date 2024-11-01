<?php

define('VERSION_2', '2');
define('VERSION_3', '3');

class Digitallylux_ShopBop_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'sp_digitallylux_widget',
            __( 'Shopbop Lookbook Viewer' ),
            array( 'description' => __( 'Display the Shopbop Viewer' ) )
        );
    }

    function form( $instance ) {
        if ( $instance ) {
            $title = esc_attr( $instance['title'] );
        } else {
            $title = __( 'Shop now!' );
        }
?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

<?php
    }

    function update( $new_instance, $old_instance ) {
        $instance['title'] = strip_tags( $new_instance['title'] );
        return $instance;
    }

    function widget( $args, $instance ) {
        echo $args['before_widget'];
        
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'];
            echo esc_html( $instance['title'] );
            echo $args['after_title'];
        }

        sp_digitallylux_slider_widget();

        echo $args['after_widget'];
    }
}


function sbp_remove_url_prefix($link) {
    if (preg_match('/^https/', $link)) {
        $url_prefix = 'https://';
    } else {
        $url_prefix = 'http://';
    }
    $link = str_replace(array('http://','https://'), '//', $link);

    return $link;
}

function sp_digitallylux_slider_widget() {
    $api_host = "sealeaf";

    if ( !$api_host ) {
        return;
    }

    if (is_single()) {
        $post_id = get_the_ID();
    } else {
        $post_id = null;
    }

    try {
        $json = sp_http_fetch_json(sp_build_url(null, 'post_id', $post_id, $api_host, VERSION_3), $api_host);
    } catch(Exception $e) {
        return '';
    }

    $html = '';
    
    $options = get_option('sp_digitallylux_options');
    if (!empty($json['slide'])) {
        $s = $json['slide'];
        $img = sbp_remove_url_prefix($s['image']);

        $image_tag = '<a style="width:100%;height:250px;display:block;background:url(' . $img . ') no-repeat center center; background-size: cover;" href="' . $s['image_link'] . '" rel="nofollow" target="_blank"></a>';
        $title_tag = '<a class="slider-title" href="'. $s['link'] .'" target="_blank">'. $s['title'] .'</a>';
        $comment_tag = '<span class="slider-comment">'. $s['comment'] .'</span>';
        $links_tag = '';

        if (count($s['links']) > 0) {
            foreach ($s['links'] as $link) {
                if ($link['anchor'] == "" ) {
                    $links_tag .= '<div class="sbp-link-wrapper"><a href="'. $link['url'] .'" target="_blank" class="slider-comment" title="'. $link['title'] .'">'. $link['title'] .'</a></div>';
                } else {
                    $links_tag .= '<div class="sbp-link-wrapper">' . $link['anchor_prefix'] . ' <a href="'. $link['url'] .'" target="_blank" class="slider-comment" title="'. $link['anchor'] .'">'. $link['anchor'] . "</a> " . $link['anchor_suffix'] . '</a></div>';
                }
            }
        }

        $brand_name = "SHOPBOP";
        $html .= '<div class="sbp-container" id="sbp-socialroot-widget">' .                        
                        $image_tag .
                        '<div class="sbp-title-wrap"><a href="://www.shopbop.com" rel="nofollow" target="_blank"><img class="brand-logo" src="' . DLS_PLUGIN_URL . 'css/shopbop-logo.png" alt="ShopBop" /></a></div>' .
                        '<div class="sbp-overlay-wrapper">' .
                            $links_tag .
                        '</div>' .
                 '</div>';
       
        
    }

    echo $html;
    // echo '<img class="sb_pixel_img" src="https://shopbop.sp1.convertro.com/view/vt/v1/shopbop/1/cvo.gif?cvosrc=sponsored%20bloggers.' . $options["name"] . '.sb-km" />';
}

function sp_digitallylux_register_widget() {
    register_widget( 'Digitallylux_ShopBop_Widget' );
}

add_action( 'widgets_init', 'sp_digitallylux_register_widget' );

function sp_build_url($site_id, $key, $value, $api_host, $version = '1') {    
    if (sp_in_development()) {
        $slider_url = 'https://localhost:4567';
    } else {
        $slider_url = "https://{$api_host}.digitallylux.com";
    }

    if (!isset($version)) {
        $version = '1';
    }

    if (!isset($site_id)) {
        $site_url = site_url();
        $url = $slider_url."/links{$version}.json?site_url={$site_url}";
    } else {
        $url = $slider_url."/links{$version}.json?site_id=".$site_id;
    }

    if (!is_front_page()) {
        if (isset($value)) {
            $url .= '&'.$key.'='.urlencode($value);
            if ($key == 'post_id') {
                $post_date = get_post_time();
                $url .= '&post_date='.urlencode($post_date);
            }
        }
    }

    if (sp_in_development()) {
        print "-- REQUEST {$url}";
    }

    return $url;
}

function sp_http_fetch_json($url, $api_host) {
    $ch = curl_init();
 
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_USERPWD, "admin:netfish307!");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);

    if ($response == false) {
        $m = curl_error($ch);
        throw new Exception($m);
    }
    curl_close($ch);
    return json_decode($response, true);
}

?>
