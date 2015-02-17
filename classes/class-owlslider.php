<?php

    if ( ! defined( 'ABSPATH' ) ) exit;

    class OwlSlider {

        public $file;
        public $plugin_url;
        public $plugin_path;
        public $post_type;
        public $slider_ids;

        //each slider settings will be stored in this table
        public $settings = array();

        private $args = array();
        private $echo;

        // turn $debug to true and see the magic...
        private $debug = false;

        //default values
        public $defaults = array(
            'slider_type'=> 'post',       // slide || post
            'transition_style' => null, // fade || backSlide || goDown || fadeUp
            'slider_page'=> null,
            'limit' => 10,
            'items' => 5,
            'slide_speed' => 300,
            'navigation' => 'true',
            'single_item' => 'true',
            'pagination' => 'true',
            'autoplay' => 'false'
        );

        public function __construct ( $args = array(), $echo = false ) {
            
            $this->args = $args;
            $this->echo = $echo;

            $file = __FILE__;
            $this->plugin_url = plugins_url( '', $plugin = dirname( $file ) ).'/';
            $this->plugin_path = trailingslashit( dirname( dirname( $file ) ) );

            require_once( 'class-owlslider-posttype.php' );
            $this->post_types = new OwlSlider_PostType();

            if ( !is_admin() ) {
                add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
                add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
                add_action( 'wp_footer', array( $this, 'load_slider_javascript' ), 100 );
            }

            add_shortcode( 'owlslider', array($this, 'owlslider_shortcode') );

            //if function called directly
            if($this->args != null) {
                return $this->owlslider( $this->args, $this->echo );
            };
        }

        // Shortcode usage example :
        // [owlslider slider_type="post" transition_style="backSlide" slider_page="sliderTemoignages" limit="3" items="20" slide_speed="300"]
        // ---------------------------------
        


        // Add Shortcode
        public function owlslider_shortcode( $atts = array() ) {
            return $this->owlslider( $atts, false );
        }

        public function owlslider($args = array(), $echo = false) {

            //if no slider_page found, will return false and break;
            if( !$args['slider_page'] ) {
                if($echo) {
                    echo 'aucun slider trouvé';
                } else {
                    return false;
                }
                break;
            }

            $slides = $this->query_slides( $args );

            $id = 'owl-'.$args['slider_page'];
            $this->slider_ids[] = $id;
            
            //retrieve the default slide settings from taxonomy
            $term = get_term_by('slug', $args['slider_page'], 'owlslidepage');
            $term_meta = get_option( "owlslidepage_".$term->term_id);
            
            //fix render for defaults booleans
            foreach($term_meta as $key => $meta) {
                if(isset($meta) || $meta == null) {
                    $term_meta[$key] = $this->defaults[$key];
                    if($this->debug) {
                        echo $key.'<br>';
                        echo $term_meta[$key].'<br>';
                    }
                }
            }
            
            if($this->debug) {
                echo '<pre>defaults :<br>';
                var_dump($this->defaults);
                echo '<br>Metas :<br>';
                var_dump($term_meta);
                echo '<br>Args :<br>';
                var_dump($args);

                $temp = array_replace($this->defaults, $term_meta);
                $temp2 = array_replace($temp, $args);
                echo '<br>Merged: <br>';
                var_dump( $temp2 );
                echo '</pre>';
            }

            $this->settings[] = wp_parse_args( $args,wp_parse_args( $term_meta , $this->defaults) );

            $html = '<div id="'.$id.'" class="owl-carousel">';

            if($slides->have_posts()) {
                if($args['slider_type'] == 'post') {
                    while($slides->have_posts()) {
                        $slides->the_post();
                        $image = get_the_post_thumbnail( get_the_ID() );
                        $html .= '<div class="owl-slide">';
                        if($image) $html .= '<div class="owl-slide-image">'.$image.'</div>';
                        $html .= '<div '.$imageUrl.' class="owl-slide-content">'.apply_filters( 'the_content' , get_the_content() ).'</div>';
                        $html .= '</div>';
                    }
                }
                else {
                    while($slides->have_posts()) {
                        $slides->the_post();
                            $thumb_id = get_post_thumbnail_id( $post_id );
                            $image = get_the_post_thumbnail( get_the_ID() );
                        $url = get_post_meta( get_the_ID(), '_owlslider_url', true );

                        $html .= '<div><a href="' . esc_url( $url ) . '">' . $image . '</a></div>';
                    }
                }
            }

            $html .= '</div>';


            if ( true == $echo ) { echo $html; }

            return $html;
        }


        private function query_slides( $args = array() ) {

            $taxonomy = array();

            if(isset($args['slider_page']) && !empty($args['slider_page'])) {
                $taxonomy = array(
                    'taxonomy' => 'owlslidepage',
                    'field' => 'slug',
                    'terms' => $args['slider_page']
                );
            }

            $query = new WP_Query(array(
                'post_type' => 'owlslide',
                'tax_query' => array($taxonomy),
                'post_count' => $args['limit']
            ));

            return $query;
        }


        public function enqueue_styles() {
            wp_register_style( 'owlslider-carousel', esc_url( $this->plugin_url . 'assets/css/owl.carousel.css' ), '', '1.3.3', 'all' );
            wp_register_style( 'owlslider-theme', esc_url( $this->plugin_url . 'assets/css/owl.theme.css' ), array( 'owlslider-carousel' ), '1.3.3', 'all' );
            wp_register_style( 'owlslider-transition', esc_url( $this->plugin_url . 'assets/css/owl.transitions.css' ), array( 'owlslider-theme' ), '1.3.3', 'all' );

            wp_enqueue_style( 'owlslider-carousel' );
            wp_enqueue_style( 'owlslider-theme' );
            wp_enqueue_style( 'owlslider-transition' );
        }

        public function enqueue_scripts() {
            wp_register_script( 'owlslider-script', esc_url( $this->plugin_url . 'assets/js/owl.carousel1.min.js'), array( 'jquery' ), null, true);

            wp_enqueue_script( 'owlslider-script' );
        }

        public function load_slider_javascript()
        {
            if(count($this->slider_ids) > 0) {
                foreach ($this->slider_ids as $key => $id) {
                        //retrieve each carousel settings
                        $settings = $this->settings[$key];
                    ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function ($) {
                            $("#<?php echo $id; ?>").owlCarousel1({
                                //common settings
                                navigation: <?php echo $settings['navigation']; ?>,
                                pagination: <?php echo $settings['pagination']; ?>,
                                singleItem: <?php echo $settings['single_item']; ?>,
                                autoPlay: <?php echo $settings['autoplay']; ?>,
                                navigationText: ["<i class='glyphicon glyphicon-chevron-left'></i>", "<i class='glyphicon glyphicon-chevron-right'></i>"],
                                paginationSpeed: <?php echo $settings['slide_speed']; ?>,
                                slideSpeed: <?php echo $settings['slide_speed']; ?>,
                                <?php if($settings['transition_style'] != null) : ?>transitionStyle: "<?php echo $settings['transition_style']; ?>",<?php endif; ?>

                                items: <?php echo $settings['items']; ?>

                            });
                        });
                    </script>
                <?php }
            }
        }
    }