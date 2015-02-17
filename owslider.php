<?php

    /**
     * Plugin Name: Owl slider
     * Description: OWL Responsive slider.
     * Version: 0.1
     * Author: JCD
     *
     * @package WordPress
     * @author JCD
     * @since 0.0.1
     */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    require_once( 'classes/class-owlslider.php' );
    if ( ! is_admin() ) require_once( 'templates/owlslider-template.php' );

    add_action('plugins_loaded', 'owl_setup' );
    
    function owl_setup() {
        global $owlslider;
        $owlslider = new OwlSlider( null, false );
    }