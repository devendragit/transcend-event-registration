<?php
require_once('vendor/autoload.php');

$stripe = array(
  'secret_key' => 'sk_test_S5U7OZGpfjuJXOEXzTkWSEkO',
  'publishable_key' => 'pk_test_v3E80MoS8lJPC1cVpBqefUBI'
);

\Stripe\Stripe::setApiKey($stripe['secret_key']);