<?php
class GravatarHelper {
    private $links = array();

    function link($email) {
        if (in_array($email, $this->links)) return $this->links[$email];

        $md5_email = md5(strtolower($email));
        $this->links[$email] = "http://www.gravatar.com/avatar.php?gravatar_id={$md5_email}";
        return $this->links[$email];
    }

    function image($email, $options = array()) {
        $options = array_merge(array(
            'alt' => null,
            'height' => 48,
            'width' => 48,
        ), $options);

        $link = $this->link($email);
        if ($options['alt']) $options['alt'] = ' alt="' . $options['alt'] . '"';
        return "<img class=\"center\" src=\"{$link}\"{$options['alt']}' {$options['height']} {$options['width']} />";
    }
}