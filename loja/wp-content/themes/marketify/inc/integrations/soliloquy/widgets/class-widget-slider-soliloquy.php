<?php

class Marketify_Widget_Slider_Soliloquy extends Marketify_Widget {

    public function __construct() {
        $this->widget_cssclass    = 'marketify_widget_slider_hero';
        $this->widget_description = __( 'Display "Hero" Soliloquy slider.', 'marketify' );
        $this->widget_id          = 'marketify_widget_slider_hero';
        $this->widget_name        = __( 'Marketify - Page: Soliloquy Slider', 'marketify' );
        $this->settings           = array(
            'slider' => array(
                'type'    => 'select',
                'label'   => __( 'Slider:', 'marketify' ),
                'options' => $this->slider_options(),
                'std'     => 0
            )
        );
        parent::__construct();
    }

    function widget( $args, $instance ) {
        if ( $this->get_cached_widget( $args ) )
            return;

        extract( $args );

        $slider     = absint( $instance[ 'slider' ] );

        echo str_replace( 'container', '', $before_widget );

        if ( function_exists( 'soliloquy' ) ) {
            add_filter( 'soliloquy_output_before_caption', array( $this, 'soliloquy_output_before_caption' ), 10, 5 );
            add_filter( 'soliloquy_output_after_caption', array( $this, 'soliloquy_output_after_caption' ), 10, 5 );
            add_filter( 'soliloquy_output_caption', array( $this, 'soliloquy_output_caption' ), 10, 5 );

            soliloquy( $slider );

            remove_filter( 'soliloquy_output_before_caption', array( $this, 'soliloquy_output_before_caption' ), 10, 5 );
            remove_filter( 'soliloquy_output_after_caption', array( $this, 'soliloquy_output_after_caption' ), 10, 5 );
            remove_filter( 'soliloquy_output_caption', array( $this, 'soliloquy_output_caption' ), 10, 5 );
        }

        echo $after_widget;
    }

    function soliloquy_output_before_caption( $output, $id, $item, $data, $i ) {
        $existing = $output;

        $output = '<div class="soliloquy-caption-outer">';
        $output .= '<h2 class="soliloquy-caption-title">' . $item[ 'title' ] . '</h2>';

        $output = $existing . $output;

        return $output;
    }

    function soliloquy_output_after_caption( $output, $id, $item, $data, $i ) {
        $output = $output . '</div>';

        return $output;
    }

    function soliloquy_output_caption( $caption, $id, $item, $data, $i ) {
        $output = '<div class="caption-full">';
        $output .= wpautop( $caption );
        $output .= '</div>';

        $output .= '<div class="caption-alt">';
        $output .= wpautop( isset ( $item[ 'alt' ] ) ? $item[ 'alt' ] : null );

        if ( isset ( $item[ 'link' ] ) ) {
            $output .= wpautop( sprintf( '<a href="%s" class="button">%s</a>', $item[ 'link' ], $item[ 'title' ] ) );
        }

        $output .= '</div>';

        return $output;
    }

    function slider_options() {
        $sliders  = new WP_Query( array(
            'post_type'              => array( 'soliloquy' ),
            'no_found_rows'          => true,
            'nopaging'               => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false
        ) );

        if ( ! $sliders->have_posts() )
            return array();

        $_sliders = array_combine(
            wp_list_pluck( $sliders->posts, 'ID' ),
            wp_list_pluck( $sliders->posts, 'post_title' )
        );

        return $_sliders;
    }
}
