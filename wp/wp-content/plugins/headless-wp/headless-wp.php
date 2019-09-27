<?php

/*
Plugin Name: Headless WP
Description: Modifies Wordpress to better suit headless use cases.
Author: Braid
Version: 1.0
*/

class BraidHeadlessWp
{
    private static $fieldId = 'bhwp_front_domain';
    private static $frontDomain;
    private static $previewUser;

    public static function init()
    {
        self::$frontDomain = get_option(self::$fieldId);
        add_action('init', [self::class, 'handlePreviewUser']);
        add_action('load-options-permalink.php', [self::class, 'addSettings']);
        add_filter('get_sample_permalink_html', [self::class, 'setSamplePermalinkHtml'], 10, 5);
    }

    public static function handlePreviewUser()
    {
        self::$previewUser = [
            'user_login' => getenv('BRAID_PREVIEW_USER_LOGIN'),
            'user_pass' => getenv('BRAID_PREVIEW_USER_PASSWORD'),
            'role' => 'editor'
        ];

        wp_insert_user(self::$previewUser);
    }

    public static function setSamplePermalinkHtml($html, $post_id, $new_title, $new_slug, $post)
    {
        $permalink = get_permalink($post_id);
        $front_link = str_replace(get_site_url(), self::$frontDomain, $permalink);
        if (get_post_status($post_id) === 'private') {
            $token = self::getToken();
            if (!empty($token)) {
                $front_link .= '?preview_token=' . $token;
            }
        }

        $html = str_replace($permalink, $front_link, $html);
        return str_replace(get_site_url(), self::$frontDomain, $html);
    }

    public static function setPostLink($link, $post)
    {
        $link = str_replace(get_site_url(), self::$frontDomain, $link);
        if (get_post_status($post) === 'private') {
            $token = self::getToken();
            if (!empty($token)) {
                $link .= '?preview_token=' . $token;
            }
        }
        return $link;
    }

    public static function addSettings()
    {
        if (isset($_POST[self::$fieldId])) {
            update_option(self::$fieldId, sanitize_url($_POST[self::$fieldId]));
        }

        add_settings_field(
            self::$fieldId,
            'Front End Domain Name',
            [self::class, 'renderSettings'],
            'permalink',
            'optional'
        );
    }

    public static function getToken()
    {
        if (!class_exists('Jwt_Auth_Public')) {
            return;
        }

        $url = get_site_url() . '/wp-json/jwt-auth/v1/token';
        $data = wp_remote_post($url, [
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'body' => json_encode([
                'username' => self::$previewUser['user_login'],
                'password' => self::$previewUser['user_pass'],
            ]),
            'method' => 'POST',
            'data_format' => 'body',
        ]);

        if (empty($data['body'])) {
            return;
        }
        
        $data = json_decode($data['body'], true);

        if (empty($data['token'])) {
            return;
        }

        return $data['token'];
    }

    public static function renderSettings()
    {
        ?>
            <input
                type="text"
                value="<?= esc_attr(self::$frontDomain) ?>"
                name="bhwp_front_domain"
                id="bhwp_front_domain"
                class="regular-text"
            >
        <?php
    }
}

BraidHeadlessWp::init();
