<?php
/**
 * Plugin Name: custom-auction
 * Plugin URI: https://github.com/sumon665
 * Description: custom-auction
 * Version: 1.0
 * Author: Md. Sumon Mia
 * Author URI: https://github.com/sumon665
 */


/* showcase dispaly */
add_action('woocommerce_before_add_to_cart_form','auction_single_pro');
function auction_single_pro() {
    global $product;
    $options = get_option( 'auction-setting', array() );
    $active = $options['enable_auction'];

    date_default_timezone_set('America/Indiana/Indianapolis');
    $start_date = strtotime($options['start_auction_date_time']);
    $end_date = strtotime($options['end_auction_date_time']);
    $current_time = strtotime(date('Y-m-d H:i:s'))+60;


    if( has_term( 'flash-campaigns', 'product_cat', $product->get_id() ) ) {    
        
        if ($active && $start_date > $current_time) {
            /* Countdown Mode */
            echo '<div class="count_content">
            <h3>Product releases in:</h3>';
            ?>
            <div id="count-time"><?php echo $options['start_auction_date_time']; ?></div>
            <div class="auction-countdown">
                <div class="auction-countdown-item ">
                    <span class="d" id="au-d">0</span>
                    <p class="color-light">Days</p>
                </div>
                <div class="auction-countdown-item">
                    <span class="h" id="au-h">0</span>
                    <p class="color-light">Hrs</p>
                </div>
                <div class="auction-countdown-item">
                    <span class="m" id="au-m">0</span>
                    <p class="color-light">Mins</p>
                </div>
                <div class="auction-countdown-item">
                    <span class="s" id="au-s">0</span>
                    <p class="color-light">Secs</p>
                </div>
            </div>

            <?php
            if ( is_user_logged_in() ) { 
                if (wc_memberships_is_user_active_member(get_current_user_id(), 'vip-membership')) {
                    echo "<button class='aubtn' disabled>RELEASING SOON</button>";
                } else {
                    echo "<a class='aubtn' href='https://dev.thekallective.com/memberships/'>Become a member</a>";
                }
            } else {
                echo "<a class='aubtn' href='https://dev.thekallective.com/my-account/'>Login</a>";
            }
            echo '</div>';            

        } elseif ($active && $end_date > $current_time && $start_date <= $current_time) {
            /* Auction Open Mode */    
            echo '<div class="active_content">
            <h3>Product release ends in:</h3>';
            ?>
            <div id="count-time"><?php echo $options['end_auction_date_time']; ?></div>
            <div class="auction-countdown">
                <div class="auction-countdown-item ">
                    <span class="d" id="au-d">0</span>
                    <p class="color-light">Days</p>
                </div>
                <div class="auction-countdown-item">
                    <span class="h" id="au-h">0</span>
                    <p class="color-light">Hrs</p>
                </div>
                <div class="auction-countdown-item">
                    <span class="m" id="au-m">0</span>
                    <p class="color-light">Mins</p>
                </div>
                <div class="auction-countdown-item">
                    <span class="s" id="au-s">0</span>
                    <p class="color-light">Secs</p>
                </div>
            </div>

            <?php
            if ( is_user_logged_in() ) { 
                if (wc_memberships_is_user_active_member(get_current_user_id(), 'vip-membership')) {
                    /* Submit offer */
                    $user_id = get_current_user_id();
                    $users = get_post_meta( $product->get_id(), 'bid_par_user', true);
                    $u_array = explode(",",$users); 
                    $current = get_post_meta( $product->get_id(), 'start_bid', true);
                    $reg_price = $product->get_price();
                    
                    if (in_array($user_id, $u_array) && $current >= $reg_price) {
                        echo '<a class="aubtn" href="?add-to-cart='.$product->get_id().'">Buy Now</a>';
                    } else {
                ?>
                <form action="<?php echo esc_url( home_url( '/' ) ); ?>" id="bid_form" method="post">
                    <div class="offer_field">
                            <label>$</label>
                            <input type="number" name="bid_amount"  id="bid_amount" placeholder="&nbsp;&nbsp;&nbsp;Enter Your Offer" min="10" required>
                            <input type="hidden" name="pid" id="pid" value="<?php echo $product->get_id(); ?>">
                            <input type="hidden" name="uid" id="uid" value="<?php echo get_current_user_id(); ?>">
                     </div>
                     <p class="error">Please enter minimum current offer $<span id="minbid"></span></p>
                     <button type="submit" id="offerbtn" class="aubtn">Submit</button>
                </form>
                
                <?php
                }
                } else {
                    echo "<a class='aubtn' href='https://dev.thekallective.com/memberships/'>Members Only - Join Now</a>";
                }
            } else {
                echo "<a class='aubtn' href='https://dev.thekallective.com/my-account/'>Login to Participate</a>";
            }
            echo '</div>'; 


        } else {
            /* Showcase Mode */    
            echo '<div class="show_content">
            <h3>Releasing soon</h3>
            <p>Members choose the price they pay for the items in our shop. When the release ends, we’ll donate 100% of the purchase price to the “Designated Charity.”</p>';

            if ( is_user_logged_in() ) { 
                if (wc_memberships_is_user_active_member(get_current_user_id(), 'vip-membership')) {
                    echo "<a class='aubtn' href='#'>HOW IT WORKS</a>";
                } else {
                    echo "<a class='aubtn' href='https://dev.thekallective.com/memberships/'>Become a member</a>";
                }
            } else {
                echo "<a class='aubtn' href='https://dev.thekallective.com/my-account/'>Login</a>";
            }
            echo '</div>';
        }

    }
}


/* Hide price and add to cart */
add_action( 'woocommerce_single_product_summary', 'hide_single_product_prices', 1 );
function hide_single_product_prices(){
    global $product;
    if( has_term( 'flash-campaigns', 'product_cat', $product->get_id() ) ) {
     remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    }
}

/* Change stock level */
add_filter( 'woocommerce_get_availability', 'wcs_custom_get_availability', 1, 2);
function wcs_custom_get_availability( $availability, $_product ) {
    $stock_quantity = $_product->get_stock_quantity();
    if( has_term( 'flash-campaigns', 'product_cat', $_product->get_id() ) ) {
        $options = get_option( 'auction-setting', array() );
        $active = $options['enable_auction'];

        date_default_timezone_set('America/Indiana/Indianapolis');
        $start_date = strtotime($options['start_auction_date_time']);
        $end_date = strtotime($options['end_auction_date_time']);
        $current_time = strtotime(date('Y-m-d H:i:s'))+60;

        if ($active && $end_date > $current_time && $start_date <= $current_time) {
            $amount = get_post_meta( $_product->get_id(), 'start_bid', true);
        } else {
            $amount = 0;
        }

        $availability['availability'] = '<p id="current_offer"><span class="curoff_content"><span class="text">Current Offer:</span>&nbsp;<span class="bid">$'.$amount.'</span></span>&nbsp;';


        $availability['availability'] .= '<span class="stock_qty">'.$stock_quantity .' in stock</span></p>';
    }
    return $availability;
}


/* Add css/js file */
function auction_enqueue() {
    wp_enqueue_style( 'au-stye', plugins_url() . '/custom-auction/css/main.css', array(),  time() );
    wp_enqueue_script('wcs-ajax-script', plugins_url() . '/custom-auction/js/main.js', array('jquery'), time(), true);
    wp_localize_script( 'wcs-ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ));    
}
add_action( 'wp_enqueue_scripts', 'auction_enqueue' );


/* Auction Setting page */
require_once('RationalOptionPages.php');

date_default_timezone_set('America/Indiana/Indianapolis');
$today = date("Y/m/d 12:00");
$nextday = date('Y/m/d 00:00', strtotime($today . ' +1 day'));

$pages = array(
    'auction-setting'  => array(
        'page_title'    => __( 'Auction Setting', 'auction' ),
        'sections'      => array(
            'section-one'   => array(
                'title'         => __( 'Auction setting', 'auction' ),
                'fields'        => array(
                    'active-auction'      => array(
                        'title'         => __( 'Enable auction', 'auction' ),
                        'type'          => 'checkbox',
                        'text'          => "active <a href='https://dev.thekallective.com/?send=1' target='_blank'>Send Email</a>",
                    ),  
                    'start-auction'     => array(
                        'title'         => __( 'Start auction date-time', 'auction' ),
                        'value'         => $today,

                    ),                         
                    'end-auction'      => array(
                        'title'         => __( 'End auction date-time', 'auction' ),
                        'value'         => $nextday,

                    ),                                                                     
                ),
            ),
        
        ),      
    ),
);
$option_page = new RationalOptionPages( $pages );



/* Add a custom field */
add_action( 'woocommerce_product_options_general_product_data', 'auction_custom_products_fields' );
function auction_custom_products_fields() {
 woocommerce_wp_text_input ( array(
     'id' => 'start_bid',
     'class' => '',
     'label' => 'Start price'
     )
 );
 woocommerce_wp_text_input ( array(
     'id' => 'incr_bid',
     'class' => '',
     'label' => 'Increment amount'
     )
 ); 
 woocommerce_wp_textarea_input ( array(
     'id' => 'bid_par_user',
     'class' => '',
     'label' => 'Participant users'
     )
 ); 

}


// Save custom field
add_action( 'save_post', 'bbloomer_save_badge_checkbox_to_post_meta' );
function bbloomer_save_badge_checkbox_to_post_meta( $product_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    if ( isset( $_POST['start_bid'] ) ) {
            update_post_meta( $product_id, 'start_bid', $_POST['start_bid'] );
    } else {
     delete_post_meta( $product_id, 'start_bid' );
    }

    if ( isset( $_POST['incr_bid'] ) ) {
            update_post_meta( $product_id, 'incr_bid', $_POST['incr_bid'] );
    } else {
     delete_post_meta( $product_id, 'incr_bid' );
    }

    if ( isset( $_POST['bid_par_user'] ) ) {
            update_post_meta( $product_id, 'bid_par_user', $_POST['bid_par_user'] );
    } else {
     delete_post_meta( $product_id, 'bid_par_user' );
    }        
}


/* Ajax request */
function submit_auction_request() {

    if ( isset($_POST) ) {
        $pid = $_POST['pid'];
        $uid = $_POST['uid'];
        $bid = $_POST['bid'];
        $current = get_post_meta( $pid, 'start_bid', true);
        $increment = get_post_meta( $pid, 'incr_bid', true);
        $users = get_post_meta( $pid, 'bid_par_user', true);
        $u_array = explode(",",$users);
        $_product = wc_get_product( $pid );
        $regprice = $_product->get_regular_price();

        if ($current >= $regprice ) {
            $result['redirect'] = 1;
        }

        if ($bid >= $current) {
            if ( $bid >= $regprice) {
                $offer = $regprice;
                $result['redirect'] = 1;
            } else {
                $offer = $bid + $increment;
            }

            update_post_meta( $pid, 'start_bid', $offer );
            $result['offer'] = $offer;
            $result['success'] = 1;
            if ($users) {
                if (!in_array($uid, $u_array)) {
                    $user_list = $users.",".$uid;
                    update_post_meta( $pid, 'bid_par_user', $user_list );
                }
            } else {
                update_post_meta( $pid, 'bid_par_user', $uid );
            }
        } else {
            $result['error'] = 1;
            $result['offer'] = $current;
        }

        echo json_encode($result);
        die();
    }
}

add_action( 'wp_ajax_submit_auction_request', 'submit_auction_request' );
add_action( 'wp_ajax_nopriv_submit_auction_request', 'submit_auction_request' );


/* validation add to cart*/
add_filter( 'woocommerce_add_to_cart_validation', 'remove_cart_item_before_add_to_cart', 20, 3 );
function remove_cart_item_before_add_to_cart( $passed, $product_id, $quantity ) {
    if( has_term( 'flash-campaigns', 'product_cat', $product_id ) ) { 
        $user_id = get_current_user_id();
        $users = get_post_meta( $product_id, 'bid_par_user', true);
        $u_array = explode(",",$users); 
        $current = get_post_meta( $product_id, 'start_bid', true);
        $_product = wc_get_product( $product_id );
        $regprice = $_product->get_regular_price();        
        
        if (in_array($user_id, $u_array) && $current >= $regprice) {
            $passed = true;
        } else {
            wc_add_notice( __( 'Unable to purchase the product', 'woocommerce' ), 'error' );
            $passed = false;            
        }        
    } else {
        $passed = true;
    }

    return $passed;
}


/* Send email to members */

// function wpse27856_set_content_type(){
//     return "text/html";
// }
// add_filter( 'wp_mail_content_type','wpse27856_set_content_type' );


add_action('init', 'send_email_member_func');
function send_email_member_func() {
    if ( is_admin() || ( defined( 'DOING_AJAX' ) ) ) {
        return;  
    }

    if (isset($_GET['send'])) {
        $users = get_users();
        $email_list = array();

        foreach($users as $user){
            if (wc_memberships_is_user_active_member($user->id, 'vip-membership')) {
                $email_list[]=$user->user_email;
            }
        }

        $options = get_option( 'auction-setting', array() );
        date_default_timezone_set('America/Indiana/Indianapolis');
        $start_date = $options['start_auction_date_time'];

        $to = "sumonahmed27@gmail.com";
        $subject = "Notification of auction opening";
        $body = "<p>Dear Members</p><p>We like to inform you that our auction will be opened at ".$start_date."<p>Thank you</p>";
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $sent = wp_mail( $email_list, $subject, $body, $headers );

        if($sent) {
            wp_redirect(home_url());
            exit();
        }

    }
}


/* Shortcode membership */

// Requires at least WooCommerce version 4
add_shortcode( 'member_goal', 'display_num_items_sold' );

function display_num_items_sold( $atts ) {
    // Shortcode attribute (or argument)
    extract( shortcode_atts( array(
        'goal'   => '',   
        'pid'    => '',   
        'from'   => '', // Date from (is required)
        'to'     => $now, // Date to (optional: default is "now" value)
    ), $atts, 'num_items_sold' ) );

    // Formating dates
    $date_from = date('Y-m-d', strtotime($from) );
    $date_to   = date('Y-m-d', strtotime($to) );

    global $wpdb;

    $sql = "
    SELECT COUNT(*) AS sale_count
    FROM {$wpdb->prefix}woocommerce_order_items AS order_items
    INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_meta ON order_items.order_item_id = order_meta.order_item_id
    INNER JOIN {$wpdb->posts} AS posts ON order_meta.meta_value = posts.ID
    WHERE order_items.order_item_type = 'line_item'
    AND order_meta.meta_key = '_product_id'
    AND order_meta.meta_value = %d
    AND order_items.order_id IN (
        SELECT posts.ID AS post_id
        FROM {$wpdb->posts} AS posts
        WHERE posts.post_type = 'shop_order'
            AND posts.post_status IN ('wc-completed','wc-processing')
            AND DATE(posts.post_date) BETWEEN %s AND %s
    )
    GROUP BY order_meta.meta_value";


    if (explode(",",$pid)) {
        foreach (explode(",",$pid) as $proid) {
            $count = $wpdb->get_var($wpdb->prepare($sql, $proid, $date_from, $date_to));
            $_product = wc_get_product( $proid );
            $sold += ((($_product->get_regular_price() * $count)/100)*20);         
        }
    } else {
            $count = $wpdb->get_var($wpdb->prepare($sql, $pid, $date_from, $date_to));
            $_product = wc_get_product( $pid );
            $sold += ((($_product->get_regular_price() * $count)/100)*20);          
    }

    return '<div id="mem_content">
    <span class="tr_content">
        <span class="text">Impact Tracker</span><span class="amount"> $'.$sold.'</span>
        <i class="icon im-info"></i>
    </span> 
    <div class="goal">
    <span class="goal_text">Impact Goal: $'.$goal.'</span>
    <div>
    </div>';
}


/* Create custom post Cafeteria */
add_action('init', 'tracker_post_type');

function tracker_post_type() {
    $args = array(
        'label' => __('Tracker'),
        'singular_label' => __('Tracker'),
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => true,
        'supports' => array('title',),
        'has_archive' => true
    );

    register_post_type('tracker', $args);
}

add_filter("manage_edit-tracker_columns", "tracker_edit_columns");

function tracker_edit_columns($columns) {
    $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => "Tracker",
        "goal" => "Impact Goal",
        "impact" => "Impact Tracker",
        "date" => "Date",
    );

    return $columns;
}

add_action("manage_posts_custom_column", "tracker_custom_columns");

function tracker_custom_columns($column) {
    global $post;
    switch ($column) {
        case "goal":
            echo '$'.get_post_meta( $post->ID, 'tra_goal', true );
            break;
        case "impact":
            $pid = get_post_meta( $post->ID, 'tra_pid', true );
            $date_from = get_post_meta( $post->ID, 'tra_from_date', true );
            $date_to = get_post_meta( $post->ID, 'tra_to_date', true );
            $result = cal_tracker_func( $pid, $date_from, $date_to );
            echo '$'.$result;
            break;
    }
}


/* Calculate the goal achive*/
function cal_tracker_func( $pid, $from, $to ) {

    global $wpdb;

    $date_from = date('Y-m-d', strtotime($from) );
    $date_to   = date('Y-m-d', strtotime($to) );

    $sql = "
    SELECT COUNT(*) AS sale_count
    FROM {$wpdb->prefix}woocommerce_order_items AS order_items
    INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_meta ON order_items.order_item_id = order_meta.order_item_id
    INNER JOIN {$wpdb->posts} AS posts ON order_meta.meta_value = posts.ID
    WHERE order_items.order_item_type = 'line_item'
    AND order_meta.meta_key = '_product_id'
    AND order_meta.meta_value = %d
    AND order_items.order_id IN (
        SELECT posts.ID AS post_id
        FROM {$wpdb->posts} AS posts
        WHERE posts.post_type = 'shop_order'
            AND posts.post_status IN ('wc-completed','wc-processing')
            AND DATE(posts.post_date) BETWEEN %s AND %s
    )
    GROUP BY order_meta.meta_value";


    if (explode(",",$pid)) {
        foreach (explode(",",$pid) as $proid) {
            $count = $wpdb->get_var($wpdb->prepare($sql, $proid, $date_from, $date_to));
            $_product = wc_get_product( $proid );
            $sold += ((($_product->get_regular_price() * $count)/100)*20);         
        }
    } else {
            $count = $wpdb->get_var($wpdb->prepare($sql, $pid, $date_from, $date_to));
            $_product = wc_get_product( $pid );
            $sold += ((($_product->get_regular_price() * $count)/100)*20);          
    }

    return $sold;
}


/* Add custom field */
require 'metabox.php';
