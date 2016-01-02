<?php


namespace oog\mediaplayer;


class Upgrade
{

    public function upgradeFromJWPlayer() {
        /** @var \wpdb $wpdb */
        global $wpdb;
        $prefix = $wpdb->prefix;
        $query = "SELECT ID, post_content FROM {$prefix}posts WHERE post_content LIKE '%[jwplayer%'";

        $results = $wpdb->get_results($query);

        foreach($results as $result) {
            $result->post_content = preg_replace_callback('/\[jwplayer.*?mediaid=.+?([0-9]*).*\]/', function($matches) use ($result) {
                $mediaId = $matches[1];
                $post = get_post($mediaId);
                if($post) {
                    switch($post->post_mime_type) {
                        case 'audio/mpeg':
                        case 'audio/wav':
                            error_log("Adding audio tag [audio mp3='{$post->guid}']");
                            return "[audio mp3='{$post->guid}']";
                        default:
                            if(strpos($post->guid, 'youtube.com') !== false) {
                                error_log("Adding video tag [embed]{$post->guid}[/embed]");
                                return "[embed]{$post->guid}[/embed]";
                            }

                            error_log(sprintf("Post %s uses an unknown format (%s)", $result->ID, $post->guid));
                            return $matches[0];
                    }
                } else {
                    error_log(sprintf("Post %s has an invalid mediaid (%s)", $result->ID, $matches[1]));
                    return $matches[0];
                }
                error_log(sprintf("Post %s has no mediaid (%s), (%s)", $result->ID, $matches[0], print_r($matches,true)));
                return $matches[0];
            }, $result->post_content);

            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$prefix}posts
                    SET post_content = %s
                    WHERE ID = %d",
                    $result->post_content,
                    $result->ID
                )
            );
        }

    }
}