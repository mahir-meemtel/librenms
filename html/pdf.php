<?php
use App\Facades\ObzoraConfig;
use ObzoraNMS\Util\Debug;

$init_modules = ['web', 'auth'];
require realpath(__DIR__ . '/..') . '/includes/init.php';

if (! Auth::check()) {
    exit('Unauthorized');
}

Debug::set(strpos($_SERVER['PATH_INFO'], 'debug'));

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$project_name = ObzoraConfig::get('project_name');

$pdf->SetCreator($project_name);
$pdf->SetAuthor($project_name);
$pdf->setFooterData([0, 64, 0], [0, 64, 128]);
$pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
$pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setFontSubsetting(true);
$pdf->SetFont('dejavusans', '', 14, '', true);
$pdf->setTextShadow(['enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => [196, 196, 196], 'opacity' => 1, 'blend_mode' => 'Normal']);

if (! empty($_GET['report'])) {
    $report = \ObzoraNMS\Util\Clean::fileName($_GET['report']);
    $image = base_path('html/' . ObzoraConfig::get('title_image'));
    $pdf->SetHeaderData($image, 40, ucfirst($report), $project_name, [0, 0, 0], [0, 64, 128]);
    include_once "includes/html/reports/$report.pdf.inc.php";
} else {
    $report = 'report';
}

$pdf->Output("$report.pdf", 'I');
