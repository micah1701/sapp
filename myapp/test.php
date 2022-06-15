<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

require_once '../../devtools/vendor/autoload.php';

use ddn\sapp\PDFDoc;

$original_pdf = '../examples/LAB.pdf';
$firstSigned_pdf = 'files/pdfs/LAB1.pdf';
$secondSigned_pdf = 'files/pdfs/LAB2.pdf';

$sourcePath = $firstSigned_pdf;
$destinationPath = $secondSigned_pdf;

$p12 = "C:\\Program Files\\OpenSSL-Win64\\bin\\example.p12";
$initials = "files/initials/initials_mjm.jpg";

$imageCoordinates = [533,350]; // first time: [533,250];

$argv = ['',$sourcePath,$initials,$p12];
if (!file_exists($argv[1])) {
	exit("failed to open file");    
}

$file_content = file_get_contents($argv[1]);
$obj = PDFDoc::from_string($file_content);
if(!$obj){
    exit("couldn't parse doc from string");
}

$image = $argv[2];
$imagesize = @getimagesize($image);
if ($imagesize === false) {
    exit("failed to open the image");
}

$pagesize = $obj->get_page_size(0);
$pagesize = explode(" ", $pagesize[0]->val());

$p_x = intval("". $pagesize[0]);
$p_y = intval("". $pagesize[1]);
$p_w = intval("". $pagesize[2]) - $p_x;
$p_h = intval("". $pagesize[3]) - $p_y;
$i_w = 10;
$i_h = 10;

$p_x = $imageCoordinates[0];
$p_y = $imageCoordinates[1];

// Set the image appearance and the certificate file
$obj->set_signature_appearance(0, [ $p_x, $p_y, $p_x + 50, $p_y + 60 ], $image);

if ($obj === false){
	exit("failed to parsee file and/or set signature appearance");    
}

$obj->set_signature_certificate($argv[3], null);
$docsigned = $obj->to_pdf_file_s(true);
if ($docsigned === false){
	exit("could not sign the document");
}

file_put_contents($destinationPath,$docsigned);

// header('Content-Type: application/pdf');
// header('Content-Disposition: attachment; filename="signed.pdf"');
// echo $docsigned;