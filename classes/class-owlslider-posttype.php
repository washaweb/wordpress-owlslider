<?php

    if ( ! defined( 'ABSPATH' ) ) exit;

    class OwlSlider_PostType {


        public function __construct () {

            add_action( 'init', array( $this, 'setup_slide_post_type' ), 100 );
            add_action( 'init', array( $this, 'setup_slide_pages_taxonomy' ), 100 );

            add_action( 'admin_menu', array( $this, 'meta_box_setup' ), 20 );
            add_action( 'save_post', array( $this, 'meta_box_save' ) );

            if ( is_admin() ) {
                global $pagenow;
                add_filter( 'manage_edit-owlslide_columns', array( $this, 'add_column_headings' ), 10, 1 );
                add_action( 'manage_posts_custom_column', array( $this, 'add_column_data' ), 10, 2 );

                add_action('owlslidepage_edit_form_fields', array($this, 'owlslidepage_edit_form_fields'), 10, 2);
                add_action('owlslidepage_form_fields', array($this, 'owlslidepage_edit_form_fields'), 10, 2);
                add_action('edited_owlslidepage', array($this, 'owlslidepage_save_form_fields'), 10, 2);
                add_action('created_owlslidepage', array($this, 'owlslidepage_save_form_fields'), 10, 2);
            }

        }


        public function setup_slide_post_type() {

            $labels = array(
                'name'               => __( 'Slides', 'owlslide' ),
                'singular_name'      => __( 'Slide',  'owlslide' ),
                'menu_name'          => __( 'Slides', 'owlslide' ),
                'name_admin_bar'     => __( 'Slide',  'owlslide' ),
                'add_new'            => __( 'Add New', 'owlslide' ),
                'add_new_item'       => __( 'Add New Slide',  'owlslide' ),
                'new_item'           => __( 'New Slide', ' owlslide' ),
                'edit_item'          => __( 'Edit Slide', ' owlslide' ),
                'view_item'          => __( 'View Slide', ' owlslide' ),
                'all_items'          => __( 'All Slides', ' owlslide' ),
                'search_items'       => __( 'Search Slides', ' owlslide' ),
                'parent_item_colon'  => __( 'Parent Slides:', ' owlslide' ),
                'not_found'          => __( 'No books found.', ' owlslide' ),
                'not_found_in_trash' => __( 'No books found in Trash.', ' owlslide' )
            );

            $args = array(
                'labels' => $labels,
                'public' => false,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => array( 'slug' => 'slider', 'with_front' => false, 'feeds' => false, 'pages' => false ),
                'capability_type' => 'post',
                'has_archive' => false,
                'hierarchical' => false,
                'menu_position' => 20, // Below "Pages"
                'menu_icon' => 'dashicons-images-alt2',
                'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' )
            );

            register_post_type( 'owlslide', $args );
        }


        public function setup_slide_pages_taxonomy() {

            $labels = array(
                'name' => _x( 'Slide Groups', 'taxonomy general name', ' owlslide' ),
                'singular_name' => _x( 'Slide Group', 'taxonomy singular name', ' owlslide' ),
                'search_items' =>  __( 'Search Slide Groups', ' owlslide' ),
                'all_items' => __( 'All Slide Groups', ' owlslide' ),
                'parent_item' => __( 'Parent Slide Group', ' owlslide' ),
                'parent_item_colon' => __( 'Parent Slide Group:', ' owlslide' ),
                'edit_item' => __( 'Edit Slide Group', ' owlslide' ),
                'update_item' => __( 'Update Slide Group', ' owlslide' ),
                'add_new_item' => __( 'Add New Slide Group', ' owlslide' ),
                'new_item_name' => __( 'New Slide Group Name', ' owlslide' ),
                'menu_name' => __( 'Slide Groups', ' owlslide' ),
                'popular_items' => null 
            );

            $args = array(
                'hierarchical' => true,
                'labels' => $labels,
                'show_ui' => true,
                'query_var' => true,
                'rewrite' => array( 'slug' => 'owlslidepage' )
            );

            register_taxonomy( 'owlslidepage', array( 'owlslide' ), $args );

        }


        public function add_column_headings($defaults) {
            $new_columns['cb'] = '<input type="checkbox" />';
            $new_columns['title'] = _x( 'Slide Title', 'column name', 'owlslider' );
            $new_columns['owlslide-thumbnail'] = _x( 'Featured Image', 'column name', 'owlslider' );
            $new_columns['owlslidepage'] = _x( 'Slide Groups', 'column name', 'owlslider' );

            if ( isset( $defaults['date'] ) ) {
                $new_columns['date'] = $defaults['date'];
            }
            return $new_columns;
        }


        public function add_column_data ( $column_name, $id ) {
            global $wpdb, $post;

            switch ( $column_name ) {
                case 'id':
                    echo $id;
                    break;

                case 'owlslidepage':
                    $value = __( 'No Slide Groups Specified', 'wooslider' );
                    $terms = get_the_terms( $id, 'owlslidepage' );

                    if ( $terms && ! is_wp_error( $terms ) ) {
                        $term_links = array();

                        foreach ( $terms as $term ) {
                            $term_links[] = sprintf( '<a href="%s">%s</a>',
                                esc_url( add_query_arg( array( 'post_type' => 'owlslide', 'tag_ID' => $term->term_id, 'taxonomy' => 'owlslidepage', 'action' => 'edit' ), 'edit-tags.php' ) ),
                                esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'owlslidepage', 'display' ) )
                            );
                        }

                        $value = join( ', ', $term_links );
                    }
                    echo $value;
                    break;

                case 'owlslide-thumbnail':
                    echo '<a href="' . esc_url( admin_url( add_query_arg( array( 'post' => intval( $id ), 'action' => 'edit' ), 'post.php' ) ) ) . '">' . "\n";
                    if ( has_post_thumbnail( $id ) ) {
                        the_post_thumbnail( array( 75, 75 ) );
                    }
                    echo '</a>' . "\n";
                    break;

                default:
                    break;
            }
        }


        public function meta_box_setup() {
            add_meta_box('owlslide_url', 'Url', array( $this, 'meta_box_content' ), 'owlslide', 'side', 'high');
        }


        public function meta_box_content($post, $args) {
            global $post_id;
            $field = get_post_meta( $post_id , 'url', true);

            $html = '<input type="hidden" name="owlslider_url" id="owlslider_url" value="' . $field . '" />';

            echo $html;
        }


        public function meta_box_save($post_id) {
            global $post, $messages;

            if ( ( get_post_type() != 'owlslide' )) {
                return $post_id;
            }

            if ( 'page' == $_POST['post_type'] ) {
                if ( ! current_user_can( 'edit_page', $post_id ) ) {
                    return $post_id;
                }
            } else {
                if ( ! current_user_can( 'edit_post', $post_id ) ) {
                    return $post_id;
                }
            }

            $fields = array('owlslider_url');

            foreach($fields as $field) {
                $value = $_POST[$field];

                if($field == 'owlslider_url') {
                    $value = esc_url($value);
                }

                if ( get_post_meta( $post_id, '_' . $field ) == '' ) {
                    add_post_meta( $post_id, '_' . $field, $value, true );
                } elseif( $value != get_post_meta( $post_id, '_' . $field, true ) ) {
                    update_post_meta( $post_id, '_' . $field, $value );
                } elseif ( $value == '' ) {
                    delete_post_meta( $post_id, '_' . $field, get_post_meta( $post_id, '_' . $field, true ) );
                }

            }
        }


        public function owlslidepage_edit_form_fields($term) {
            $t_id = $term->term_id;
            $term_meta = get_option( "owlslidepage_$t_id");
            ?>
            <tr class="form-field">
                <th scope="row" valign="top"><label for="speed"><?php _e('Speed'); ?></label></th>
                <td>
                    <input type="number" name="term_meta[slide_speed]" id="term_meta[slide_speed]" value="<?php echo $term_meta['slide_speed'] ? $term_meta['slide_speed'] : ''; ?>"><br />
                    <span class="description"><?php _e('Transition Speed of the slider (in miliseconds).'); ?></span>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top"><label for="items"><?php _e('Items'); ?></label></th>
                <td>
                    <input type="number" name="term_meta[items]" id="term_meta[items]" value="<?php echo $term_meta['items'] ? $term_meta['items'] : ''; ?>"><br />
                    <span class="description"><?php _e('Max displayed items'); ?></span>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top"><label for="effect"><?php _e('Effect'); ?></label></th>
                <td>
                    <input type="text" name="term_meta[transition_style]" id="term_meta[transition_style]" value="<?php echo $term_meta['transition_style'] ? $term_meta['transition_style'] : ''; ?>"><br />
                    <span class="description"><?php _e('Transition effect of the slide : fade || backSlide || goDown || fadeUp (default: slide)'); ?></span>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top"><label for="navigation"><?php _e('Navigation'); ?></label></th>
                <td>
                    <input type="text" name="term_meta[navigation]" id="term_meta[navigation]" value="<?php echo $term_meta['navigation'] ? $term_meta['navigation'] : ''; ?>"><br />
                    <span class="description"><?php _e('Display navigation : true || false, (default: true)'); ?></span>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top"><label for="pagination"><?php _e('Pagination'); ?></label></th>
                <td>
                    <input type="text" name="term_meta[pagination]" id="term_meta[pagination]" value="<?php echo $term_meta['pagination'] ? $term_meta['pagination'] : ''; ?>"><br />
                    <span class="description"><?php _e('Display the slider pagination : true || false, (default: true)'); ?></span>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top"><label for="single_item"><?php _e('Single Item'); ?></label></th>
                <td>
                    <input type="text" name="term_meta[single_item]" id="term_meta[single_item]" value="<?php echo $term_meta['single_item'] ? $term_meta['single_item'] : ''; ?>"><br />
                    <span class="description"><?php _e('Display the slider as one single item or a carousel : true || false, (default: true)'); ?></span>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top"><label for="autoplay"><?php _e('Autoplay'); ?></label></th>
                <td>
                    <input type="text" name="term_meta[autoplay]" id="term_meta[autoplay]" value="<?php echo $term_meta['autoplay'] ? $term_meta['autoplay'] : ''; ?>"><br />
                    <span class="description"><?php _e('Change to any integrer for example autoPlay : 5000 to play every 5 seconds. If you set autoPlay: true default speed will be 5 seconds. : true || false || any number, (default: false)'); ?></span>
                </td>
            </tr>
            <?php
        }


        public function owlslidepage_save_form_fields($term_id) {
            if ( isset( $_POST['term_meta'] ) ) {
                $t_id = $term_id;
                $term_meta = get_option( "owlslidepage_$t_id");
                $cat_keys = array_keys($_POST['term_meta']);
                foreach ($cat_keys as $key){
                    if (isset($_POST['term_meta'][$key])){
                        $term_meta[$key] = $_POST['term_meta'][$key];
                    }
                }
                update_option( "owlslidepage_$t_id", $term_meta );
            }
        }
    }