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
    function logAccess(string $logDir = "") {
        $logDir = $logDir ?: __DIR__ . '/logs/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
    
        // Lấy ngày hiện tại để tạo file log
        $date = date('Y-m-d');
        $logFile = $logDir . "$date.txt";
    
        // Lấy thông tin truy cập
        $time = date('H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
        $url = $_SERVER['REQUEST_URI'] ?? 'UNKNOWN';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
    
        // Dữ liệu gửi lên (nếu có)
        $data = '';
        if (!empty($_GET)) {
            $data .= "GET Data: " . json_encode($_GET) . "; ";
        }
        if (!empty($_POST)) {
            $data .= "POST Data: " . json_encode($_POST) . "; ";
        }
    
        // Nội dung log
        $logEntry = "[$time] IP: $ip | $method $url | Agent: $userAgent | $data\n";
    
        // Ghi vào file log
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }