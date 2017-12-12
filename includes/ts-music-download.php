<?php
include_once("../../../../wp-load.php");

if( isset($_REQUEST['ts_pretty_filename']) && !empty($_REQUEST['ts_real_filename']) ){

    $pretty_filename = sanitize_file_name($_GET['ts_pretty_filename']);
    $real_filename = sanitize_file_name($_GET['ts_real_filename']);

    $file = TS_MUSIC_ZIP_FOLDER . "/" .$real_filename;

    header('Content-Type: application/zip');
    header('Content-Length: ' . filesize($file));
    header('Content-Disposition: attachment; filename="'. $pretty_filename .'.zip"');

    readfile($file);
    if($_REQUEST['ts_unlink']) {
        unlink($file);
    }

    exit;
}