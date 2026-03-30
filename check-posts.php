<?php
define('WP_USE_THEMES', false);
require_once('wp-load.php');

$types = get_post_types(array('public' => true), 'objects');
echo "Public Post Types:\n";
foreach ($types as $slug => $obj) {
    echo "- $slug: {$obj->labels->name}\n";
}

$recent_posts = get_posts(array('post_type' => 'any', 'numberposts' => 10));
echo "\nRecent Posts:\n";
foreach ($recent_posts as $post) {
    echo "- ID: {$post->ID}, Type: {$post->post_type}, Title: {$post->post_title}, Status: {$post->post_status}\n";
}
