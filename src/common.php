<?php
    function getCurrentUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $request_uri = $_SERVER['REQUEST_URI'];

        $full_url = $protocol . "://" . $host . $request_uri;

        return $full_url;
    }
    function dump($var) {
        echo "<pre>";
            var_dump($var);
        echo "</pre>";
    }