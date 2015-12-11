<?php


namespace oog\mediaplayer;


class Player {
    const TYPE_AUDIO = 'audio';
    const TYPE_VIDEO = 'video';

    function __construct() {
        remove_shortcode('audio');
        add_shortcode('audio', [$this, 'addAudioShortcode']);
        add_action('plugins_loaded', [$this, 'init']);
    }

    public function init() {
        add_action('wp_print_styles', [$this, 'loadScripts']);
    }

    /**
     * Loads the javascripts & css on the WordPress frontend.
     */
    public function loadScripts() {
        wp_register_script('oog-media-player-js', WP_PLUGIN_URL . '/oogmediaplayer/js/oog-media-player.min.js', ['jquery']);
        wp_register_style('oog-media-player-css', WP_PLUGIN_URL . '/oogmediaplayer/css/oog-media-player.css');
        wp_enqueue_script('oog-media-player-js');
        wp_enqueue_style('oog-media-player-css');
    }

    public function addAudioShortcode($attributes) {
        $media = (object) [
            'url' => $attributes['mp3']
        ];
        include OOG_MEDIAPLAYER_PLUGIN_DIR . '/templates/audio-player.php';
    }
}