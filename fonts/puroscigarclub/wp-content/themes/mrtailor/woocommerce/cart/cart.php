<?php
/**
 * Cart Page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wc_print_notices();

do_action( 'woocommerce_before_cart' ); ?>

    <div class="cart_container">
    
    <form class="cart-form" action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">
    
        <?php do_action( 'woocommerce_before_cart_table' ); ?>
            
        <div class="row">
            <div class="large-10 large-centered columns">
        
                <table class="shop_table cart" cellspacing="0">
                    
                    <thead>
                        <tr>
                            <th class="product-thumbnail">&nbsp;</th>
                            <th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
                            <th class="product-quantity">
                                <span class="show-for-small-only text-center product_quantity_mobile"><?php _e( 'Qty', 'woocommerce' ); ?></span>
                                <span class="show-for-medium-up"><?php _e( 'Quantity', 'woocommerce' ); ?></span>
                            </th>	
                            <th class="product-subtotal"><?php _e( 'Total', 'woocommerce' ); ?></th>
                            <th class="product-remove">&nbsp;</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        
                        <?php do_action( 'woocommerce_before_cart_contents' ); ?>
                
                        <?php
                        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                            $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                            $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                
                            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                                ?>
                                <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                
                                    <td class="product-thumbnail">
										<?php
                                            $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                
                                            if ( ! $_product->is_visible() )
                                                echo $thumbnail;
                                            else
                                                printf( '<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
                                        ?>
                                    </td>
                
                                    <td class="product-name">
                                        <?php
											if ( ! $_product->is_visible() ) {
                                                echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
											} else {
                                                //echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', $_product->get_permalink(), $_product->get_title() ), $cart_item, $cart_item_key );
												$product_name  = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
											?>
                                            	<a href="<?php echo get_permalink( $product_id ); ?>"><?php echo $product_name; ?></a>
                                            <?php
											}
											
											// Price
                                            echo '<div>' . apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ) . '</div>';
                                            
                                            // Meta data
                                            echo WC()->cart->get_item_data( $cart_item );
                
                                            // Backorder notification
                                            if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) )
                                                echo '<p class="backorder_notification">' . __( 'Available on backorder', 'woocommerce' ) . '</p>';
                                        ?>
                                    </td>
                
                                    <td class="product-quantity">
						<?php
							if ( $_product->is_sold_individually() ) {
								$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
							} else {
								$product_quantity = woocommerce_quantity_input( array(
									'input_name'  => "cart[{$cart_item_key}][qty]",
									'input_value' => $cart_item['quantity'],
									'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
									'min_value'   => '0'
								), $_product, false );
							}

							echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key );
						?>
					</td>
                
                                    <td class="product-subtotal">
                                        <?php
                                            echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
                                        ?>
                                    </td>
                                    
                                    <td class="product-remove">
                                        <?php
                                            echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" title="%s"><i class="getbowtied-icon-close_regular"></i></a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', 'woocommerce' ) ), $cart_item_key );
                                        ?>
                                    </td>
                                    
                                </tr>
                                <?php
                            }
                        }
                
                        do_action( 'woocommerce_cart_contents' );
                        ?>
                        
                        <?php do_action( 'woocommerce_after_cart_contents' ); ?>
                        
                    </tbody>
                </table>
                
                <?php do_action( 'woocommerce_after_cart_table' ); ?>
        
            </div><!-- .columns -->
        </div><!-- .row -->
        
        <div class="cart-buttons">                
    
            <div class="row">
                                            
                <div class="medium-6 large-5 large-push-1 columns">
                    <?php if ( WC()->cart->coupons_enabled() ) { ?>
                        <div class="coupon">
    
                            <input name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php _e( 'Coupon code', 'woocommerce' ); ?>" />
                            <input type="submit" class="button apply_coupon" name="apply_coupon" value="<?php _e( 'Apply Coupon', 'woocommerce' ); ?>" />
    
                            <?php do_action('woocommerce_cart_coupon'); ?>
    
                        </div>
                    <?php } ?>
                </div><!-- .columns -->
            
                <div class="medium-6 large-5 large-pull-1 columns">
                    <div class="update_and_checkout">                
                        <input type="submit" class="button update_cart" name="update_cart" value="<?php _e( 'Update Cart', 'woocommerce' ); ?>" />
                        <?php do_action('woocommerce_proceed_to_checkout'); ?>                
                    </div>
                </div><!-- .columns -->
    
            </div><!-- .row -->
    
            <?php wp_nonce_field( 'woocommerce-cart') ?>
        
        </div>
    
    </form>
    
    <?php do_action( 'woocommerce_cart_actions' ); ?>

    <?php do_action( 'woocommerce_after_cart' ); ?>
    
    <div class="cart-collaterals">
    
        <div class="row">
            <div class="large-10 large-centered columns">
        
                <div class="row">
                            
                    <div class="medium-6 columns medium-push-6">
                        <?php woocommerce_cart_totals(); ?>
                    </div><!-- .columns -->		
                                                
                    <div class="medium-6 columns medium-pull-6 show-for-medium-up">
                        <div class="row">
                            <?php do_action('woocommerce_cart_collaterals'); ?>
                        </div><!-- .row -->
                    </div><!-- .columns -->
                                
                </div><!-- .row -->
                
            </div><!-- .columns -->    
        </div><!-- .row -->
    
    </div><!-- .cart-collaterals -->

</div><!-- .cart_container-->