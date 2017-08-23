<?php
require_once('vendor/autoload.php');

$stripe = array(
  'secret_key' => 'sk_test_S5U7OZGpfjuJXOEXzTkWSEkO',
  'publishable_key' => 'pk_test_v3E80MoS8lJPC1cVpBqefUBI'
);

/*$stripe = array(
  'secret_key' => 'sk_live_5runwUoteVTiGzfV6lwi7RwH',
  'publishable_key' => 'pk_live_fvcu8e2Cq2FJBYjPEJSYapYg'
);*/

\Stripe\Stripe::setApiKey($stripe['secret_key']);