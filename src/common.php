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

    function rateLimit($ipLimit = 100, $globalLimit = 1000, $logDir="") {
        $logFile = $logDir ? $logDir."/rate_limit.json" : __DIR__ . '/logs/rate_limit.json';
    
        // Tạo thư mục logs nếu chưa có
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }
    
        // Lấy IP của client
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $currentHour = date('Y-m-d H'); // Định danh giờ hiện tại
    
        // Đọc dữ liệu từ file
        $rateData = [];
        if (file_exists($logFile)) {
            $rateData = json_decode(file_get_contents($logFile), true) ?? [];
        }
    
        // Nếu bước sang giờ mới, reset dữ liệu
        if (!isset($rateData[$currentHour])) {
            $rateData = [$currentHour => ['total' => 0]];
        }
    
        // Cập nhật số lần truy cập của IP và tổng số request
        $rateData[$currentHour][$ip] = ($rateData[$currentHour][$ip] ?? 0) + 1;
        $rateData[$currentHour]['total'] = ($rateData[$currentHour]['total'] ?? 0) + 1;
    
        // Kiểm tra giới hạn
        if ($rateData[$currentHour][$ip] > $ipLimit) {
            header("HTTP/1.1 429 Too Many Requests");
            die("Bạn đã vượt quá 100 request trong 1 giờ!");
        }
        if ($rateData[$currentHour]['total'] > $globalLimit) {
            header("HTTP/1.1 429 Too Many Requests");
            die("Hệ thống đã đạt 1000 request trong 1 giờ. Vui lòng thử lại sau.");
        }
    
        // Ghi lại file log
        file_put_contents($logFile, json_encode($rateData, JSON_PRETTY_PRINT));
    
        return true; // Cho phép truy cập nếu chưa vượt quá giới hạn
    }
    