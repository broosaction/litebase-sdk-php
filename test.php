<?php
/**
 * Created by Bruce Mubangwa on 07 /Nov, 2020 @ 7:54
 */


use Litebase\LitebaseClient;
use Litebase\Service\Test\LteTestService;

include_once 'vendor/autoload.php';

error_reporting(E_ALL);

$litebaseclient = new LitebaseClient(array(
    'client_id' => '7acajonek0te3ajoyugi5uh07ovomi3e.r',
    'client_secret' => '81879a89-e70c-48d5-95a7-6d1f985c2158.r',
    'username' => 'f3mg7pxrst',
    'application_name' => 'lte-test',
));


$qrcode = new \Litebase\Service\Charts\Qr_Code_Service($litebaseclient);
$qrcode->setText('hello people')->setSize(300);
 if($qrcode->getStatus()){
    echo '<img src="'.$qrcode->getImageUrl().'"/>';
 }else{
  // error message from Server
 echo $qrcode->getMessage();
}

 $currency = new \Litebase\Service\Finance\Currency_Converter_Service($litebaseclient);
$currency->setAmount("3.5")->setFromCurrency('usd')->setToUserCurrency();
if($currency->getStatus()){

    echo '<br><br>converting $3.5 to users currency<br>';

    echo 'server returns currency: '.$currency->getCurrency().' and amount: '.$currency->getAmount();
}else{
    // error message from Server
    print_r($currency->getMessage());
}


 $database = \Litebase\Service\Datastore\Database::createDatabase($litebaseclient);

//$database->getReference('girls')->getChild('pretty')->set("Kate");
//$database->getReference('users')->getChild('king')->set("truth");

 $snap = $database->getReference('girls')->getSnapshot();
 var_dump($snap->getChild('pretty')->getValue());


//$database->getReference('users')->getChild('king')->set("truth");
