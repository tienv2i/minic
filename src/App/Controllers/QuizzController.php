<?php
namespace Minic\App\Controllers;

use Minic\Core\View;
use Minic\Core\Bootstrap;

class QuizzController {
    public function __construct () {
        Bootstrap::getInstance()->simpleAuth();
    }
    function index (Bootstrap $app, $params) {


        $app->render('quiz/index.html', [
            "links_list" => [
                '/quiz/daicuong' => 'Đại cương',
                '/quiz/chitren' => 'Chi trên',
                '/quiz/hohap' => 'Hô hấp',
                '/quiz/timmach' => 'Tim mạch',
                '/quiz/bung' => 'Bụng',
                '/quiz/coxuongkhop' => 'Cơ xương khớp',
                '/quiz/sinhsan' => 'Sinh sản',
                '/quiz/tietnieu' => 'Tiết niệu',
                '/quiz/tieuhoa' => 'Tiêu hoá',
                '/quiz/thankinh_noitiet_giacquan' => 'Thần kinh - Nội tiết - Giác quan',
                '/quiz/thacsi_2024' => 'Thạc sĩ 2024 (chép lại)',
            ],
            "page_title" => "Ôn thi GP sau ĐH <<2025>>"
        ]);
    }
    function quiz ($app, $params) {
        $quizz_file="quiz/contents/".($params["quiz_name"] ?? "").".html";
        if (View::template_exists($quizz_file)) {
            $app->render("quiz/quiz.html", [
                "content_file" => $quizz_file,
                "page_title" => "Ôn thi GP sau ĐH <<2025>>"
            ]);
        } else {
            http_response_code(404);
            echo "Quizz doesn't exist.";
            exit();
        }

    }
}