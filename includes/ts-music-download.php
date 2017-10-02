<?php

include_once("../../../../wp-load.php");

if(isset($_REQUEST['ts_pretty_filename']) && !empty($_REQUEST['ts_real_filename'])){

    $pretty_filename = sanitize_file_name($_GET['ts_pretty_filename']);
    $real_filename = sanitize_file_name($_GET['ts_real_filename']);

    $upload_dir = wp_upload_dir();
    $file = $upload_dir['path'] . "/" .$real_filename;

    header('Content-Type: application/zip');
    header('Content-Length: ' . filesize($file));
    header('Content-Disposition: attachment; filename="'. $pretty_filename .'.zip"');

    readfile($file);
    unlink($file);

    exit;
}
