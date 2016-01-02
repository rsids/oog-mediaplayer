<?php

namespace oog\mediaplayer;

require_once(ABSPATH . 'wp-admin/includes/media.php');

class Player {
    const TYPE_AUDIO = 'audio';
    const TYPE_VIDEO = 'video';

    function __construct() {
        add_shortcode('youtube', [$this, 'onYoutubeShortcode']);
        add_filter('wp_audio_shortcode_override', [$this, 'onAudioShortcode'], PHP_INT_MAX, 2);
        add_action('plugins_loaded', [$this, 'init']);
        add_action('wp_ajax_oog_media_player_get_audio_title', [$this, 'getAudioTitle']);
        add_action('wp_ajax_nopriv_oog_media_player_get_audio_title', [$this, 'getAudioTitle']);

        register_activation_hook(OOG_MEDIAPLAYER_PLUGIN_FILE, [$this, 'onActivate']);
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

    public function getAudioTitle() {
        $file = filter_input(INPUT_POST, 'file');
        if(strpos($file, '..') !== false) {
            die('');
        }
        $path = ABSPATH . substr($file, strlen(get_site_url()));
        if(is_file($path)) {
            $metadata = wp_read_audio_metadata($path);
            if($metadata && isset($metadata['title'])) {
                die($metadata['title']);
            } else {
                $fileParts = explode('/', $file);
                die(array_pop($fileParts));
            }

        }
        exit;
    }

    public function onActivate() {
        $upgrade = new Upgrade();
        $upgrade->upgradeFromJWPlayer();
    }

    /**
     * @param $content
     * @param $attributes
     * @return string
     * Called when an [audio] shortcode is encountered
     */
    public function onAudioShortcode($content, $attributes) {
        $media = (object) [
            'url' => $attributes['mp3']
        ];

        if(isset($attributes['title'])) {
            $media->title = $attributes['title'];
        }

        ob_start();
        include OOG_MEDIAPLAYER_PLUGIN_DIR . '/templates/audio-player.php';
        $content = ob_get_clean();
        return $content;
    }

    /**
     * @param {Array} $attrs Contains the youtube code
     * Called when a [youtube] shortcode is encountered
     */
    public function onYoutubeShortcode($attrs) {
        echo sprintf('<div class="oog-player-youtube"><iframe src="https://www.youtube.com/embed/%s" frameborder="0" width="560" height="315"></iframe></div>', $attrs['code']);
    }
}