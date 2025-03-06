<?php
namespace Minic\App\Controllers;

use Minic\Core\View;

class QuizzController {
    function index ($app, $params) {
        $app->render('quizz/index.html', [
            "links_list" => [
                'quiz/daicuong' => 'Đại cương',
                'quiz/chitren' => 'Chi trên',
                'quiz/hohap' => 'Hô hấp',
                'quiz/timmach' => 'Tim mạch',
                'quiz/bung' => 'Bụng',
                'quiz/coxuongkhop' => 'Cơ xương khớp',
                'quiz/sinhsan' => 'Sinh sản',
                'quiz/tietnieu' => 'Tiết niệu',
                'quiz/tieuhoa' => 'Tiêu hoá',
                'quiz/thankinh_noitiet_giacquan' => 'Thần kinh - Nội tiết - Giác quan',
                'quiz/thacsi_2024' => 'Thạc sĩ 2024 (chép lại)',
            ]
        ]);
    }
    function quizz ($app, $params) {
        $quizz_file="quizz/contents/".($params["quizz_name"] ?? "").".html";
        if (View::template_exists($quizz_file)) {
            $app->render("quizz/quizz.html", [
                "content_file" => $quizz_file
            ]);
        } else {
            http_response_code(404);
            echo "Quizz doesn't exist.";
            exit();
        }

    }
}