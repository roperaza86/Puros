<?php

    if ( ! class_exists( 'MrTailor_Theme_Options' ) ) {

        class MrTailor_Theme_Options {

            public $args = array();
            public $sections = array();
            public $theme;
            public $ReduxFramework;

            public function __construct() {

                if ( ! class_exists( 'ReduxFramework' ) ) {
                    return;
                }

                // This is needed. Bah WordPress bugs.  ;)
                if ( true == Redux_Helpers::isTheme( __FILE__ ) ) {
                    $this->initSettings();
                } else {
                    add_action( 'plugins_loaded', array( $this, 'initSettings' ), 10 );
                }

            }

            public function initSettings() {

                // Just for demo purposes. Not needed per say.
                $this->theme = wp_get_theme();

                // Set the default arguments
                $this->setArguments();

                // Set a few help tabs so you can see how it's done
                //$this->setHelpTabs();

                // Create the sections and fields
                $this->setSections();

                if ( ! isset( $this->args['opt_name'] ) ) { // No errors please
                    return;
                }

                // If Redux is running as a plugin, this will remove the demo notice and links
                //add_action( 'redux/loaded', array( $this, 'remove_demo' ) );

                // Function to test the compiler hook and demo CSS output.
                // Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
                //add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 3);

                // Change the arguments after they've been declared, but before the panel is created
                //add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );

                // Change the default value of a field after it's been set, but before it's been useds
                //add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );

                // Dynamically add a section. Can be also used to modify sections/fields
                //add_filter('redux/options/' . $this->args['opt_name'] . '/sections', array($this, 'dynamic_section'));

                $this->ReduxFramework = new ReduxFramework( $this->sections, $this->args );
            }

            /**
             * This is a test function that will let you see when the compiler hook occurs.
             * It only runs if a field    set with compiler=>true is changed.
             * */
            function compiler_action( $options, $css, $changed_values ) {
                echo '<h1>The compiler hook has run!</h1>';
                echo "<pre>";
                print_r( $changed_values ); // Values that have changed since the last save
                echo "</pre>";
                //print_r($options); //Option values
                //print_r($css); // Compiler selector CSS values  compiler => array( CSS SELECTORS )

                /*
              // Demo of how to use the dynamic CSS and write your own static CSS file
              $filename = dirname(__FILE__) . '/style' . '.css';
              global $wp_filesystem;
              if( empty( $wp_filesystem ) ) {
                require_once( ABSPATH .'/wp-admin/includes/file.php' );
              WP_Filesystem();
              }

              if( $wp_filesystem ) {
                $wp_filesystem->put_contents(
                    $filename,
                    $css,
                    FS_CHMOD_FILE // predefined mode settings for WP files
                );
              }
             */
            }

            /**
             * Custom function for filtering the sections array. Good for child themes to override or add to the sections.
             * Simply include this function in the child themes functions.php file.
             * NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
             * so you must use get_template_directory_uri() if you want to use any of the built in icons
             * */
            function dynamic_section( $sections ) {
                //$sections = array();
                $sections[] = array(
                    'title'  => __( 'Section via hook', 'redux-framework-demo' ),
                    'desc'   => __( '<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'redux-framework-demo' ),
                    'icon'   => 'el-icon-paper-clip',
                    // Leave this as a blank section, no options just some intro text set above.
                    'fields' => array()
                );

                return $sections;
            }

            /**
             * Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.
             * */
            function change_arguments( $args ) {
                //$args['dev_mode'] = true;

                return $args;
            }

            /**
             * Filter hook for filtering the default value of any given field. Very useful in development mode.
             * */
            function change_defaults( $defaults ) {
                $defaults['str_replace'] = 'Testing filter hook!';

                return $defaults;
            }

            // Remove the demo link and the notice of integrated demo from the redux-framework plugin
            function remove_demo() {

                // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
                if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
                    remove_filter( 'plugin_row_meta', array(
                        ReduxFrameworkPlugin::instance(),
                        'plugin_metalinks'
                    ), null, 2 );

                    // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
                    remove_action( 'admin_notices', array( ReduxFrameworkPlugin::instance(), 'admin_notices' ) );
                }
            }

            public function setSections() {

                /**
                 * Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
                 * */
                // Background Patterns Reader
                $sample_patterns_path = ReduxFramework::$_dir . '../sample/patterns/';
                $sample_patterns_url  = ReduxFramework::$_url . '../sample/patterns/';
                $sample_patterns      = array();

                if ( is_dir( $sample_patterns_path ) ) :

                    if ( $sample_patterns_dir = opendir( $sample_patterns_path ) ) :
                        $sample_patterns = array();

                        while ( ( $sample_patterns_file = readdir( $sample_patterns_dir ) ) !== false ) {

                            if ( stristr( $sample_patterns_file, '.png' ) !== false || stristr( $sample_patterns_file, '.jpg' ) !== false ) {
                                $name              = explode( '.', $sample_patterns_file );
                                $name              = str_replace( '.' . end( $name ), '', $sample_patterns_file );
                                $sample_patterns[] = array(
                                    'alt' => $name,
                                    'img' => $sample_patterns_url . $sample_patterns_file
                                );
                            }
                        }
                    endif;
                endif;

                ob_start();

                $ct          = wp_get_theme();
                $this->theme = $ct;
                $item_name   = $this->theme->get( 'Name' );
                $tags        = $this->theme->Tags;
                $screenshot  = $this->theme->get_screenshot();
                $class       = $screenshot ? 'has-screenshot' : '';

                $customize_title = sprintf( __( 'Customize &#8220;%s&#8221;', 'redux-framework-demo' ), $this->theme->display( 'Name' ) );

                ?>
                <div id="current-theme" class="<?php echo esc_attr( $class ); ?>">
                    <?php if ( $screenshot ) : ?>
                        <?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
                            <a href="<?php echo wp_customize_url(); ?>" class="load-customize hide-if-no-customize"
                               title="<?php echo esc_attr( $customize_title ); ?>">
                                <img src="<?php echo esc_url( $screenshot ); ?>"
                                     alt="<?php esc_attr_e( 'Current theme preview', 'redux-framework-demo' ); ?>"/>
                            </a>
                        <?php endif; ?>
                        <img class="hide-if-customize" src="<?php echo esc_url( $screenshot ); ?>"
                             alt="<?php esc_attr_e( 'Current theme preview', 'redux-framework-demo' ); ?>"/>
                    <?php endif; ?>

                    <h4><?php echo $this->theme->display( 'Name' ); ?></h4>

                    <div>
                        <ul class="theme-info">
                            <li><?php printf( __( 'By %s', 'redux-framework-demo' ), $this->theme->display( 'Author' ) ); ?></li>
                            <li><?php printf( __( 'Version %s', 'redux-framework-demo' ), $this->theme->display( 'Version' ) ); ?></li>
                            <li><?php echo '<strong>' . __( 'Tags', 'redux-framework-demo' ) . ':</strong> '; ?><?php printf( $this->theme->display( 'Tags' ) ); ?></li>
                        </ul>
                        <p class="theme-description"><?php echo $this->theme->display( 'Description' ); ?></p>
                        <?php
                            if ( $this->theme->parent() ) {
                                printf( ' <p class="howto">' . __( 'This <a href="%1$s">child theme</a> requires its parent theme, %2$s.', 'redux-framework-demo' ) . '</p>', __( 'http://codex.wordpress.org/Child_Themes', 'redux-framework-demo' ), $this->theme->parent()->display( 'Name' ) );
                            }
                        ?>

                    </div>
                </div>

                <?php
                $item_info = ob_get_contents();

                ob_end_clean();

                $sampleHTML = '';
                if ( file_exists( dirname( __FILE__ ) . '/info-html.html' ) ) {
                    Redux_Functions::initWpFilesystem();

                    global $wp_filesystem;

                    $sampleHTML = $wp_filesystem->get_contents( dirname( __FILE__ ) . '/info-html.html' );
                }

                // ACTUAL DECLARATION OF SECTIONS
                
				$this->sections[] = array(
                    'title' => __('General', 'mr_tailor_settings'),
					'icon' 	=> 'fa fa-dot-circle-o',
					
					'fields' => array (
						
						array (
							'title' => __('Favicon', 'mr_tailor_settings'),
							'subtitle' => __('<em>Upload your custom Favicon image. <br>.ico or .png file required.</em>', 'mr_tailor_settings'),
							'id' => 'favicon',
							'type' => 'media',
							'default' => array (
								'url' => get_template_directory_uri() . '/favicon.png',
							),
						),
						
					),
                );
				
				$this->sections[] = array(
					'title' => __('Header', 'mr_tailor_settings'),
					'icon' 	=> 'fa fa-arrow-circle-o-up',
					
					'fields' => array (
						
						array (
							'id' => 'top_bar_info',
							'icon' => true,
							'type' => 'info',
							'raw' => __('<h3 style="margin: 0;">Top Bar</h3>', 'mr_tailor_settings'),
						),
						
						array (
							'title' => __('Top Bar', 'mr_tailor_settings'),
							'subtitle' => __('<em>Enable / Disable the Top Bar.</em>', 'mr_tailor_settings'),
							'id' => 'top_bar_switch',
							'on' => __('Enabled', 'mr_tailor_settings'),
							'off' => __('Disabled', 'mr_tailor_settings'),
							'type' => 'switch',
							'default' => 1,
						),
						
						array (
							'title' => __('Top Bar Background Color', 'mr_tailor_settings'),
							'subtitle' => __('<em>The Top Bar background color.</em>', 'mr_tailor_settings'),
							'id' => 'top_bar_background_color',
							'type' => 'color',
							'default' => '#3e5372',
							'required' => array('top_bar_switch','=','1')
						),
						
						array (
							'title' => __('Top Bar Text Color', 'mr_tailor_settings'),
							'subtitle' => __('<em>Specify the Top Bar Typography.</em>', 'mr_tailor_settings'),
							'id' => 'top_bar_typography',
							'type' => 'color',
							'default' => '#fff',
							'transparent' => false,
							'required' => array('top_bar_switch','=','1')
						),
						
						array (
							'title' => __('Top Bar Text', 'mr_tailor_settings'),
							'subtitle' => __('<em>Type in your Top Bar info here.</em>', 'mr_tailor_settings'),
							'id' => 'top_bar_text',
							'type' => 'text',
							'default' => 'Free Shipping on All Orders Over $75!',
							'required' => array('top_bar_switch','=','1')
						),
						
						array (
							'id' => 'main_header_info',
							'icon' => true,
							'type' => 'info',
							'raw' => '<h3 style="margin: 0;">Main Header</h3>',
						),
						
						array (
							'title' => __('Header Layout', 'mr_tailor_settings'),
							'subtitle' => __('<em>Select the layout style for the Header.', 'mr_tailor_settings'),
							'id' => 'header_layout',
							'type' => 'image_select',
							'options' => array (
								0 => get_template_directory_uri() . '/images/theme_options/icons/header_1.png',
								1 => get_template_directory_uri() . '/images/theme_options/icons/header_2.png',
							),
							'default' => 0,
							
						),
						
						array (
							'title' => __('Sticky Header', 'mr_tailor_settings'),
							'subtitle' => __('<em>Enable / Disable the Sticky Header.</em>', 'mr_tailor_settings'),
							'id' => 'sticky_header',
							'on' => __('Enabled', 'mr_tailor_settings'),
							'off' => __('Disabled', 'mr_tailor_settings'),
							'type' => 'switch',
							'default' => 1,
						),
						
						array (
							'title' => __('Your Logo', 'mr_tailor_settings'),
							'subtitle' => __('<em>Upload your logo image.</em>', 'mr_tailor_settings'),
							'id' => 'site_logo',
							'type' => 'media',
						),
						
						array (
							'title' => __('Your Retina Logo', 'mr_tailor_settings'),
							'subtitle' => __('<em>Upload a higher-resolution image <br />to be used for retina display devices.</em>', 'mr_tailor_settings'),
							'id' => 'site_logo_retina',
							'type' => 'media',
						),
						
						array(
							'title' => __('Logo Height', 'mr_tailor_settings'),
							'subtitle' => __('<em>Drag the slider to set the logo height.</em>', 'mr_tailor_settings'),
							'id' => 'logo_height',
							'type' => 'slider',
							"default" => 60,
							"min" => 0,
							"step" => 1,
							"max" => 300,
							'display_value' => 'text'
						),
						
						array(
							'title' => __('Header Paddings (Top/Bottom)', 'mr_tailor_settings'),
							'subtitle' => __('<em>Drag the slider to set the paddings of the header.</em>', 'mr_tailor_settings'),
							'id' => 'header_paddings',
							'type' => 'slider',
							"default" => 30,
							"min" => 0,
							"step" => 1,
							"max" => 200,
							'display_value' => 'text'
						),
						
						array (
							'title' => __('Main Header Background Color', 'mr_tailor_settings'),
							'subtitle' => __('<em>The Main Header background color.</em>', 'mr_tailor_settings'),
							'id' => 'main_header_background_color',
							'type' => 'color',
							'transparent' => false,
							'default' => '#ffffff',
						),
						
						array (
							'title' => __('Main Header Transparency', 'mr_tailor_settings'),
							'subtitle' => __('<em>Enable / Disable the Main Header Transparency.</em>', 'mr_tailor_settings'),
							'id' => 'main_header_background_transparency',
							'on' => __('Enabled', 'mr_tailor_settings'),
							'off' => __('Disabled', 'mr_tailor_settings'),
							'type' => 'switch',
							'default' => 0,
						),
						
						array (
							'title' => __('Main Header Typography', 'mr_tailor_settings'),
							'subtitle' => __('<em>Specify the Main Header Typography.</em>', 'mr_tailor_settings'),
							'id' => 'main_header_typography',
							'type' => 'typography',
							'google' => false,
							'line-height' => false,
							'preview' => false,
							'subsets' => false,
							'text-align' => false,
							'font-style' => false,
							'font-weight' => false,
							'font-family' => false,
							'default' => array (
								'font-size' => '13px',
								'units' => 'px',
								'color' => '#000000',
							),
						),
						
						array (
							'title' => __('Main Header Wishlist', 'mr_tailor_settings'),
							'subtitle' => __('<em>Enable / Disable the Wishlist in the Header.</em>', 'mr_tailor_settings'),
							'id' => 'main_header_wishlist',
							'on' => __('Enabled', 'mr_tailor_settings'),
							'off' => __('Disabled', 'mr_tailor_settings'),
							'type' => 'switch',
							'default' => 1,
						),
						
						array (
							'title' => __('Main Header Shopping Bag', 'mr_tailor_settings'),
							'subtitle' => __('<em>Enable / Disable the Shopping Bag in the Header.</em>', 'mr_tailor_settings'),
							'id' => 'main_header_shopping_bag',
							'on' => __('Enabled', 'mr_tailor_settings'),
							'off' => __('Disabled', 'mr_tailor_settings'),
							'type' => 'switch',
							'default' => 1,
						),
						
						array (
							'title' => __('Main Header Search bar', 'mr_tailor_settings'),
							'subtitle' => __('<em>Enable / Disable the Search Bar in the Header.</em>', 'mr_tailor_settings'),
							'id' => 'main_header_search_bar',
							'on' => __('Enabled', 'mr_tailor_settings'),
							'off' => __('Disabled', 'mr_tailor_settings'),
							'type' => 'switch',
							'default' => 1,
						),
						
					),
                );
				
				$this->sections[] = array(
					'title' => __('Footer', 'mr_tailor_settings'),
					'icon' 	=> 'fa fa-arrow-circle-o-down',
					
					'fields' => array (
						
						array (
							'title' => __('Footer Background Color', 'mr_tailor_settings'),
							'subtitle' => __('<em>The Top Bar background color.</em>', 'mr_tailor_settings'),
							'id' => 'footer_background_color',
							'type' => 'color',
							'default' => '#262628',
						),
						
						array (
							'title' => __('Footer Text', 'mr_tailor_settings'),
							'subtitle' => __('<em>Specify the Footer Text Color.</em>', 'mr_tailor_settings'),
							'id' => 'footer_texts_color',
							'type' => 'color',
							'transparent' => false,
							'default' => '#c9c9c9',
						),
						
						array (
							'title' => __('Footer Links', 'mr_tailor_settings'),
							'subtitle' => __('<em>Specify the Footer Links Color.</em>', 'mr_tailor_settings'),
							'id' => 'footer_links_color',
							'type' => 'color',
							'transparent' => false,
							'default' => '#fff',
						),
						
						array (
							'title' => __('Footer Credit Card Icons', 'mr_tailor_settings'),
							'subtitle' => __('<em>Upload your custom icons sprite.</em>', 'mr_tailor_settings'),
							'id' => 'credit_card_icons',
							'type' => 'media',
							'default' => array (
								'url' => get_template_directory_uri() . '/images/theme_options/icons/payment_cards.png',
							),
						),
						
						array (
							'title' => __('Footer Copyright Text', 'mr_tailor_settings'),
							'subtitle' => __('<em>Enter your copyright information here.</em>', 'mr_tailor_settings'),
							'id' => 'footer_copyright_text',
							'type' => 'text',
							'default' => '&copy; <a href=\'http://www.getbowtied.com/\'>Get Bowtied</a> - Elite ThemeForest Author.',
						),
						
					),
                );
				
				$this->sections[] = array(
					'title' => __('Shop', 'mr_tailor_settings'),
					'icon' 	=> 'fa fa-shopping-cart',
					
					'fields' => array (
						
						array (
							'title' => __('Shop layout', 'mr_tailor_settings'),
							'subtitle' => __('<em>Select the layout style for the shop.', 'mr_tailor_settings'),
							'id' => 'shop_layout',
							'type' => 'image_select',
							'options' => array (
								0 => get_template_directory_uri() . '/images/theme_options/icons/shop-sidebar-off.png',
								1 => get_template_directory_uri() . '/images/theme_options/icons/shop-sidebar-on.png',
							),
							'default' => 0,
							
						),
						
						array (
							'title' => __('Catalog Mode', 'mr_tailor_settings'),
							'subtitle' => __('<em>Enable / Disable the Catalog Mode.</em>', 'mr_tailor_settings'),
							'desc' => __('<em>When enabled, the feature Turns Off the shopping functionality of WooCommerce.</em>', 'mr_tailor_settings'),
							'id' => 'catalog_mode',
							'on' => __('Enabled', 'mr_tailor_settings'),
							'off' => __('Disabled', 'mr_tailor_settings'),
							'type' => 'switch',
						),
						
						array (
							'title' => __('Breadcrumbs', 'mr_tailor_settings'),
							'subtitle' => __('<em>Enable / Disable the Breadcrumbs.</em>', 'mr_tailor_settings'),
							'id' => 'breadcrumbs',
							'on' => __('Enabled', 'mr_tailor_settings'),
							'off' => __('Disabled', 'mr_tailor_settings'),
							'type' => 'switch',
							'default' => 1,
						),
						
						array (
							'title' => __('Number of Products per Column', 'mr_tailor_settings'),
							'subtitle' => __('<em>Drag the slider to set the number of products per column <br />to be listed on the shop page and catalog pages.</em>', 'mr_tailor_settings'),
							'id' => 'products_per_column',
							'min' => '2',
							'step' => '1',
							'max' => '6',
							'type' => 'slider',
							'default' => '4',
						),
						
						array (
							'title' => __('Number of Products per Page', 'mr_tailor_settings'),
							'subtitle' => __('<em>Drag the slider to set the number of products per page <br />to be listed on the shop page and catalog pages.</em>', 'mr_tailor_settings'),
							'id' => 'products_per_page',
							'min' => '1',
							'step' => '1',
							'max' => '48',
							'type' => 'slider',
							'edit' => '1',
							'default' => '12',
						),
						
						array (
							'title' => __('Product Animation', 'mr_tailor_settings'),
							'subtitle' => __('<em>A list of all the product animations.</em>', 'mr_tailor_settings'),
							'id' => 'products_animation',
							'type' => 'select',
							'options' => array (
								'e0' => 'No Animation',
								'e1' => 'Fade',
								'e2' => 'Move Up',
								'e3' => 'Scale Up',
								'e4' => 'Fall Perspective',
								'e5' => 'Fly',
								'e6' => 'Flip',
								'e7' => 'Helix',
								'e8' => 'PopUp',
							),
							'default' => 'e2',
						),
						
						array (
							'title' => __('Product Hover Animation', 'mr_tailor_settings'),
							'subtitle' => __('<em>Enable / Disable the Animation on product hover.</em>', 'mr_tailor_settings'),
							'id' => 'product_hover_animation',
							'on' => __('Enabled', 'mr_tailor_settings'),
							'off' => __('Disabled', 'mr_tailor_settings'),
							'type' => 'switch',
							'default' => 1,
						),
						
						array (
							'title' => __('Sale Text', 'mr_tailor_settings'),
							'subtitle' => __('<em>Type in your custom Sale Text.</em>', 'mr_tailor_settings'),
							'id' => 'sale_text',
							'type' => 'text',
							'default' => 'Sale!'
						),
						
						array (
							'title' => __('Out of Stock Text', 'mr_tailor_settings'),
							'subtitle' => __('<em>Type in your custom Out of Stock Text.</em>', 'mr_tailor_settings'),
							'id' => 'out_of_stock_text',
							'type' => 'text',
							'default' => 'Out of stock'
						),
						
						array (
							'title' => __('My Account image', 'mr_tailor_settings'),
							'subtitle' => __('<em>Upload your custom My Account image.</em>', 'mr_tailor_settings'),
							'id' => 'my_account_image',
							'type' => 'media',
							'default' => '',
						),
						
					),
                );
				
				$this->sections[] = array(
					'title' => __('Product', 'mr_tailor_settings'),
					'icon' 	=> 'fa fa-archive',
					
					'fields' => array (
						
						array (
							'title' => __('Products layout', 'mr_tailor_settings'),
							'subtitle' => __('<em>Select the layout style for the products.', 'mr_tailor_settings'),
							'id' => 'products_layout',
							'type' => 'image_select',
							'options' => array (
								0 => get_template_directory_uri() . '/images/theme_options/icons/product-sidebar-off.png',
								1 => get_template_directory_uri() . '/images/theme_options/icons/product-sidebar-on.png',
							),
							'default' => 0,
							
						),
						
						array (
							'title' => __('Product Gallery Zoom', 'mr_tailor_settings'),
							'subtitle' => __('<em>Enable / Disable Product Gallery Zoom.<em>', 'mr_tailor_settings'),
							'id' => 'product_gallery_zoom',
							'on' => __('Enabled', 'mr_tailor_settings'),
							'off' => __('Disabled', 'mr_tailor_settings'),
							'type' => 'switch',
							'default' => 1,
						),
						
						array (
							'title' => __('Recently viewed', 'mr_tailor_settings'),
							'subtitle' => __('<em>Enable / Disable the Recently viewed products.</em>', 'mr_tailor_settings'),
							'id' => 'recently_viewed_products',
							'on' => __('Enabled', 'mr_tailor_settings'),
							'off' => __('Disabled', 'mr_tailor_settings'),
							'type' => 'switch',
							'default' => 1,
						),
						
						array (
							'title' => __('Number of Related Products per View', 'mr_tailor_settings'),
							'subtitle' => __('<em>Drag the slider to set the number of Related Products per View.</em>', 'mr_tailor_settings'),
							'id' => 'related_products_per_view',
							'min' => '2',
							'step' => '1',
							'max' => '6',
							'type' => 'slider',
							'default' => '4',
						),
						
						array (
							'title' => __('Sharing Options', 'mr_tailor_settings'),
							'subtitle' => __('<em>Enable / Disable Sharing Options.<em>', 'mr_tailor_settings'),
							'id' => 'sharing_options',
							'on' => __('Enabled', 'mr_tailor_settings'),
							'off' => __('Disabled', 'mr_tailor_settings'),
							'type' => 'switch',
							'default' => 1,
						),
						
					),
                );
				
				$this->sections[] = array(
					'title' => __('Blog', 'mr_tailor_settings'),
					'icon' 	=> 'fa fa-list-alt',
					
					'fields' => array (
						
						array (
							'title' => __('Blog Layout', 'mr_tailor_settings'),
							'subtitle' => __('<em>Select the layout style for the Blog Listing.</em>', 'mr_tailor_settings'),
							'id' => 'sidebar_blog_listing',
							'type' => 'image_select',
							'options' => array (
								0 => get_template_directory_uri() . '/images/theme_options/icons/blog_no_sidebar.png',
								1 => get_template_directory_uri() . '/images/theme_options/icons/blog_sidebar.png',
								2 => get_template_directory_uri() . '/images/theme_options/icons/blog-masonry.png',
							),
							'default' => 0,					
						),
						
						array (
							'title' => __('Sharing Options', 'mr_tailor_settings'),
							'subtitle' => __('<em>Enable / Disable Sharing Options.<em>', 'mr_tailor_settings'),
							'id' => 'sharing_options_blog',
							'on' => __('Enabled', 'mr_tailor_settings'),
							'off' => __('Disabled', 'mr_tailor_settings'),
							'type' => 'switch',
							'default' => 1,
						),
						
					),
                );
				
				$this->sections[] = array(
					'title' => __('Styling', 'mr_tailor_settings'),
					'icon' 	=> 'fa fa-pencil-square-o',
					
					'fields' => array (
						
						array (
							'title' => __('Body Texts Color', 'shopkeeper'),
							'subtitle' => __('<em>Body Texts Color of the site.</em>', 'shopkeeper'),
							'id' => 'body_color',
							'type' => 'color',
							'transparent' => false,
							'default' => '#222222',
						),
						
						array (
							'title' => __('Headings Color', 'shopkeeper'),
							'subtitle' => __('<em>Headings Color of the site.</em>', 'shopkeeper'),
							'id' => 'headings_color',
							'type' => 'color',
							'transparent' => false,
							'default' => '#000000',
						),
						
						array (
							'title' => __('Main Theme Color', 'mr_tailor_settings'),
							'subtitle' => __('<em>The main color of the site.</em>', 'mr_tailor_settings'),
							'id' => 'main_color',
							'type' => 'color',
							'transparent' => false,
							'default' => '#3e5372',
						),
						
						array (
							'title' => __('Background Color', 'mr_tailor_settings'),
							'subtitle' => __('<em>The main background color of the site.</em>', 'mr_tailor_settings'),
							'id' => 'main_bg_color',
							'type' => 'color',
							'transparent' => false,
							'default' => '#fff',
						),
						
						array (
							'title' => __('Background Image', 'mr_tailor_settings'),
							'subtitle' => __('<em>Upload a background image or specify an image URL.</em>', 'mr_tailor_settings'),
							'id' => 'main_bg_image',
							'type' => 'media',
							'url' => true,
						),
						
					),
                );
				
				$this->sections[] = array(
					'title' => __('Typography', 'mr_tailor_settings'),
					'icon' => 'fa fa-font',
					
					'fields' => array (
						
						array (
							'id' => 'main_font_info',
							'icon' => true,
							'type' => 'info',
							'raw' => __('<h3 style="margin: 0;">Main Font</h3>', 'mr_tailor_settings'),
						),
						
						array(
							'title'    => __('Font Source', 'mr_tailor_settings'),
							'subtitle' => __('<em>Choose the Main Font Source</em>', 'mr_tailor_settings'),
							'id'       => 'main_font_source',
							'type'     => 'radio',
							//Must provide key => value pairs for radio options
							'options'  => array(
								'1' => 'Standard + Google Webfonts', 
								'2' => 'Adobe Typekit'
							),
							'default' => '1'
						),
						
						array(
							'id'=>'main_font_typekit_kit_id',
							'type' => 'text',
							'title' => __('Typekit Kit ID', 'mr_tailor_settings'), 
							'subtitle' => __('<em>Main Font Typekit Kit ID</em>', 'mr_tailor_settings'),
							'desc' => __('<em>Paste the provided Typekit Kit ID for the Main Font.</em>', 'mr_tailor_settings'),
							'validate' => 'js',
							'default' => '',
							'required' => array('main_font_source','=','2')
						),
						
						array (
							'title' => __('Typekit Font Face', 'mr_tailor_settings'),
							'subtitle' => __('<em>Enter your Typekit Font Name for the theme\'s Main Typography</em>', 'mr_tailor_settings'),
							'desc' => __('e.g.: futura-pt', 'mr_tailor_settings'),
							'id' => 'main_typekit_font_face',
							'type' => 'text',
							'default' => '',
							'required' => array('main_font_source','=','2')
						),
						
						array (
							'title' => __('Font Face', 'mr_tailor_settings'),
							'subtitle' => __('<em>Pick the Main Font for your site.</em>', 'mr_tailor_settings'),
							'id' => 'main_font',
							'type' => 'typography',
							'line-height' => false,
							'text-align' => false,
							'font-style' => false,
							'font-weight' => false,
							'font-size' => false,
							'color' => false,
							'default' => array (
								'font-family' => 'Raleway',
								'subsets' => '',
							),
							'required' => array('main_font_source','=','1')
						),
						
						array (
							'id' => 'secondary_font_info',
							'icon' => true,
							'type' => 'info',
							'raw' => __('<h3 style="margin: 0;">Secondary Font</h3>', 'mr_tailor_settings'),
						),
						
						array(
							'title'    => __('Font Source', 'mr_tailor_settings'),
							'subtitle' => __('<em>Choose the Main Font Source</em>', 'mr_tailor_settings'),
							'id'       => 'secondary_font_source',
							'type'     => 'radio',
							//Must provide key => value pairs for radio options
							'options'  => array(
								'1' => 'Standard + Google Webfonts', 
								'2' => 'Adobe Typekit'
							),
							'default' => '1'
						),
						
						array(
							'id'=>'secondary_font_typekit_kit_id',
							'type' => 'text',
							'title' => __('Typekit Kit ID', 'mr_tailor_settings'), 
							'subtitle' => __('<em>Secondary Font Typekit Kit ID</em>', 'mr_tailor_settings'),
							'desc' => __('<em>Paste the provided Typekit Kit ID for the Secondary Font.</em>', 'mr_tailor_settings'),
							'validate' => 'js',
							'default' => '',
							'required' => array('secondary_font_source','=','2')
						),
						
						array (
							'title' => __('Typekit Font Face', 'mr_tailor_settings'),
							'subtitle' => __('<em>Enter your Typekit Font Name for the theme\'s Secondary Typography</em>', 'mr_tailor_settings'),
							'desc' => __('e.g.: proxima-nova', 'mr_tailor_settings'),
							'id' => 'secondary_typekit_font_face',
							'type' => 'text',
							'default' => '',
							'required' => array('secondary_font_source','=','2')
						),
						
						array (
							'title' => __('Font Face', 'mr_tailor_settings'),
							'subtitle' => __('<em>Pick the Secondary Font for your site.</em>', 'mr_tailor_settings'),
							'id' => 'secondary_font',
							'type' => 'typography',
							'line-height' => false,
							'text-align' => false,
							'font-style' => false,
							'font-weight' => false,
							'font-size' => false,
							'color' => false,
							'default' => array (
								'font-family' => 'Raleway',
								'subsets' => '',
							),
							'required' => array('secondary_font_source','=','1')
							
						),
						
					),
                );
				
				$this->sections[] = array(
					'title' => __('Social Media', 'mr_tailor_settings'),
					'icon' => 'fa fa-share-square-o',
					
					'fields' => array (
						
						array (
							'title' => __('Facebook', 'mr_tailor_settings'),
							'subtitle' => __('<em>Type your Facebook profile URL here.</em>', 'mr_tailor_settings'),
							'id' => 'facebook_link',
							'type' => 'text',
							'default' => 'https://www.facebook.com/GetBowtied',
						),
						
						array (
							'title' => __('Twitter', 'mr_tailor_settings'),
							'subtitle' => __('<em>Type your Twitter profile URL here.</em>', 'mr_tailor_settings'),
							'id' => 'twitter_link',
							'type' => 'text',
							'default' => 'http://twitter.com/GetBowtied',
						),
						
						array (
							'title' => __('Pinterest', 'mr_tailor_settings'),
							'subtitle' => __('<em>Type your Pinterest profile URL here.</em>', 'mr_tailor_settings'),
							'id' => 'pinterest_link',
							'type' => 'text',
							'default' => 'http://www.pinterest.com/',
						),
						
						array (
							'title' => __('LinkedIn', 'mr_tailor_settings'),
							'subtitle' => __('<em>Type your LinkedIn profile URL here.</em>', 'mr_tailor_settings'),
							'id' => 'linkedin_link',
							'type' => 'text',
						),
						
						array (
							'title' => __('Google+', 'mr_tailor_settings'),
							'subtitle' => __('<em>Type your Google+ profile URL here.</em>', 'mr_tailor_settings'),
							'id' => 'googleplus_link',
							'type' => 'text',
						),
						
						array (
							'title' => __('RSS', 'mr_tailor_settings'),
							'subtitle' => __('<em>Type your RSS Feed URL here.</em>', 'mr_tailor_settings'),
							'id' => 'rss_link',
							'type' => 'text',
						),
						
						array (
							'title' => __('Tumblr', 'mr_tailor_settings'),
							'subtitle' => __('<em>Type your Tumblr URL here.</em>', 'mr_tailor_settings'),
							'id' => 'tumblr_link',
							'type' => 'text',
						),
						
						array (
							'title' => __('Instagram', 'mr_tailor_settings'),
							'subtitle' => __('<em>Type your Instagram profile URL here.</em>', 'mr_tailor_settings'),
							'id' => 'instagram_link',
							'type' => 'text',
							'default' => 'http://instagram.com/getbowtied',
						),
						
						array (
							'title' => __('Youtube', 'mr_tailor_settings'),
							'subtitle' => __('<em>Type your Youtube profile URL here.</em>', 'mr_tailor_settings'),
							'id' => 'youtube_link',
							'type' => 'text',
							'default' => 'https://www.youtube.com/channel/UC88KP4HSF-TnVhPCJLe9P-g',
						),
						
						array (
							'title' => __('Vimeo', 'mr_tailor_settings'),
							'subtitle' => __('<em>Type your Vimeo profile URL here.</em>', 'mr_tailor_settings'),
							'id' => 'vimeo_link',
							'type' => 'text',
						),
						
					),
                );
				
				$this->sections[] = array(
					'title' => __('Custom Code', 'mr_tailor_settings'),
					'icon' => 'fa fa-code',
					
					'fields' => array (
						
						array (
							'title' => __('Custom CSS', 'mr_tailor_settings'),
							'subtitle' => __('<em>Paste your custom CSS code here.</em>', 'mr_tailor_settings'),
							'id' => 'custom_css',
							'type' => 'ace_editor',
							'mode' => 'css',
						),
						
						array (
							'title' => __('Header JavaScript Code', 'mr_tailor_settings'),
							'subtitle' => __('<em>Paste your custom JS code here. The code will be added to the header of your site.</em>', 'mr_tailor_settings'),
							'id' => 'header_js',
							'type' => 'ace_editor',
							'mode' => 'javascript',
						),
						
						array (
							'title' => __('Footer JavaScript Code', 'mr_tailor_settings'),
							'subtitle' => __('<em>Here is the place to paste your Google Analytics code or any other JS code you might want to add to be loaded in the footer of your website.</em>', 'mr_tailor_settings'),
							'id' => 'footer_js',
							'type' => 'ace_editor',
							'mode' => 'javascript',
						),
						
					),
                );
            }

            public function setHelpTabs() {

                // Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
                $this->args['help_tabs'][] = array(
                    'id'      => 'redux-help-tab-1',
                    'title'   => __( 'Theme Information 1', 'redux-framework-demo' ),
                    'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'redux-framework-demo' )
                );

                $this->args['help_tabs'][] = array(
                    'id'      => 'redux-help-tab-2',
                    'title'   => __( 'Theme Information 2', 'redux-framework-demo' ),
                    'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'redux-framework-demo' )
                );

                // Set the help sidebar
                $this->args['help_sidebar'] = __( '<p>This is the sidebar content, HTML is allowed.</p>', 'redux-framework-demo' );
            }

            /**
             * All the possible arguments for Redux.
             * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
             * */
            public function setArguments() {

                $theme = wp_get_theme(); // For use with some settings. Not necessary.

                $this->args = array(
                    // TYPICAL -> Change these values as you need/desire
                    'opt_name'             => 'mr_tailor_theme_options',
                    // This is where your data is stored in the database and also becomes your global variable name.
                    'display_name'         => $theme->get( 'Name' ),
                    // Name that appears at the top of your panel
                    'display_version'      => $theme->get( 'Version' ),
                    // Version that appears at the top of your panel
                    'menu_type'            => 'menu',
                    //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                    'allow_sub_menu'       => true,
                    // Show the sections below the admin menu item or not
                    'menu_title'           => __( 'Theme Options', 'redux-framework-demo' ),
                    'page_title'           => __( 'Theme Options', 'redux-framework-demo' ),
                    // You will need to generate a Google API key to use this feature.
                    // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                    'google_api_key'       => 'AIzaSyDGJehqeZnxz4hABrNgi9KrBTG7ev6rIgY',
                    // Set it you want google fonts to update weekly. A google_api_key value is required.
                    'google_update_weekly' => false,
                    // Must be defined to add google fonts to the typography module
                    'async_typography'     => true,
                    // Use a asynchronous font on the front end or font string
                    //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
                    'admin_bar'            => true,
                    // Show the panel pages on the admin bar
                    'admin_bar_icon'     => 'dashicons-portfolio',
                    // Choose an icon for the admin bar menu
                    'admin_bar_priority' => 50,
                    // Choose an priority for the admin bar menu
                    'global_variable'      => '',
                    // Set a different name for your global variable other than the opt_name
                    'dev_mode'             => false,
                    // Show the time the page took to load, etc
                    'update_notice'        => true,
                    // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
                    'customizer'           => true,
                    // Enable basic customizer support
                    //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
                    //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

                    // OPTIONAL -> Give you extra features
                    'page_priority'        => 3,
                    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                    'page_parent'          => 'themes.php',
                    // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                    'page_permissions'     => 'manage_options',
                    // Permissions needed to access the options panel.
                    'menu_icon'            => '',
                    // Specify a custom URL to an icon
                    'last_tab'             => '',
                    // Force your panel to always open to a specific tab (by id)
                    'page_icon'            => 'icon-themes',
                    // Icon displayed in the admin panel next to your menu_title
                    'page_slug'            => 'theme_options',
                    // Page slug used to denote the panel
                    'save_defaults'        => true,
                    // On load save the defaults to DB before user clicks save or not
                    'default_show'         => false,
                    // If true, shows the default value next to each field that is not the default value.
                    'default_mark'         => '',
                    // What to print by the field's title if the value shown is default. Suggested: *
                    'show_import_export'   => true,
                    // Shows the Import/Export panel when not used as a field.

                    // CAREFUL -> These options are for advanced use only
                    'transient_time'       => 60 * MINUTE_IN_SECONDS,
                    'output'               => true,
                    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                    'output_tag'           => true,
                    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                    // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

                    // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                    'database'             => '',
                    // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                    'system_info'          => false,
                    // REMOVE

                    // HINTS
                    'hints'                => array(
                        'icon'          => 'icon-question-sign',
                        'icon_position' => 'right',
                        'icon_color'    => 'lightgray',
                        'icon_size'     => 'normal',
                        'tip_style'     => array(
                            'color'   => 'light',
                            'shadow'  => true,
                            'rounded' => false,
                            'style'   => '',
                        ),
                        'tip_position'  => array(
                            'my' => 'top left',
                            'at' => 'bottom right',
                        ),
                        'tip_effect'    => array(
                            'show' => array(
                                'effect'   => 'slide',
                                'duration' => '500',
                                'event'    => 'mouseover',
                            ),
                            'hide' => array(
                                'effect'   => 'slide',
                                'duration' => '500',
                                'event'    => 'click mouseleave',
                            ),
                        ),
                    )
                );

                // ADMIN BAR LINKS -> Setup custom links in the admin bar menu as external items.
                $this->args['admin_bar_links'][] = array(
                    'id'    => 'mrtailor-docs',
                    'href'   => 'http://support.getbowtied.com/hc/en-us/categories/200103142-Mr-Tailor',
                    'title' => __( 'Documentation', 'mr_tailor_settings' ),
                );

                $this->args['admin_bar_links'][] = array(
                    'id'    => 'mrtailor-support',
                    'href'   => 'http://support.getbowtied.com/hc/en-us/requests/new',
                    'title' => __( 'Support', 'mr_tailor_settings' ),
                );

                // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
                $this->args['share_icons'][] = array(
                    'url'   => 'https://www.facebook.com/GetBowtied',
                    'title' => 'Like us on Facebook',
                    'icon'  => 'el-icon-facebook'
                );
                $this->args['share_icons'][] = array(
                    'url'   => 'https://twitter.com/GetBowtied',
                    'title' => 'Follow us on Twitter',
                    'icon'  => 'el-icon-twitter'
                );
                $this->args['share_icons'][] = array(
                    'url'   => 'https://plus.google.com/+Getbowtied/posts',
                    'title' => 'Find us on Google+',
                    'icon'  => 'el-icon-googleplus'
                );

                // Panel Intro text -> before the form
                if ( ! isset( $this->args['global_variable'] ) || $this->args['global_variable'] !== false ) {
                    if ( ! empty( $this->args['global_variable'] ) ) {
                        $v = $this->args['global_variable'];
                    } else {
                        $v = str_replace( '-', '_', $this->args['opt_name'] );
                    }
                    //$this->args['intro_text'] = sprintf( __( '<p>Did you know that Redux sets a global variable for you? To access any of your saved options from within your code you can use your global variable: <strong>$%1$s</strong></p>', 'redux-framework-demo' ), $v );
                } else {
                    //$this->args['intro_text'] = __( '<p>This text is displayed above the options panel. It isn\'t required, but more info is always better! The intro_text field accepts all HTML.</p>', 'redux-framework-demo' );
                }

                // Add content after the form.
                //$this->args['footer_text'] = __( '<p>This text is displayed below the options panel. It isn\'t required, but more info is always better! The footer_text field accepts all HTML.</p>', 'redux-framework-demo' );
            }

            public function validate_callback_function( $field, $value, $existing_value ) {
                $error = true;
                $value = 'just testing';

                /*
              do your validation

              if(something) {
                $value = $value;
              } elseif(something else) {
                $error = true;
                $value = $existing_value;
                
              }
             */

                $return['value'] = $value;
                $field['msg']    = 'your custom error message';
                if ( $error == true ) {
                    $return['error'] = $field;
                }

                return $return;
            }

            public function class_field_callback( $field, $value ) {
                print_r( $field );
                echo '<br/>CLASS CALLBACK';
                print_r( $value );
            }

        }

        global $reduxConfig;
        $reduxConfig = new MrTailor_Theme_Options();
    } else {
        echo "The class named MrTailor_Theme_Options has already been called. <strong>Developers, you need to prefix this class with your company name or you'll run into problems!</strong>";
    }

    /**
     * Custom function for the callback referenced above
     */
    if ( ! function_exists( 'redux_my_custom_field' ) ):
        function redux_my_custom_field( $field, $value ) {
            print_r( $field );
            echo '<br/>';
            print_r( $value );
        }
    endif;

    /**
     * Custom function for the callback validation referenced above
     * */
    if ( ! function_exists( 'redux_validate_callback_function' ) ):
        function redux_validate_callback_function( $field, $value, $existing_value ) {
            $error = true;
            $value = 'just testing';

            /*
          do your validation

          if(something) {
            $value = $value;
          } elseif(something else) {
            $error = true;
            $value = $existing_value;
            
          }
         */

            $return['value'] = $value;
            $field['msg']    = 'your custom error message';
            if ( $error == true ) {
                $return['error'] = $field;
            }

            return $return;
        }
    endif;
