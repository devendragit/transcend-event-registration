<?php
require_once('vendor/autoload.php');

$stripe = array(
  'secret_key' => 'sk_test_8MRCQvXa68cq1aljeJM2I1Qs',
  'publishable_key' => 'pk_test_UO8A0OTV4k2fgJrL2MMirTEo'
);

/*$stripe = array(
  'secret_key' => 'sk_live_5runwUoteVTiGzfV6lwi7RwH',
  'publishable_key' => 'pk_live_fvcu8e2Cq2FJBYjPEJSYapYg'
);*/

\Stripe\Stripe::setApiKey($stripe['secret_key']);