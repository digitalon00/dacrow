<?php
$db_name = 'mysql:host=localhost;dbname=dacrow_db';
$db_user = 'root';
$db_password='';

$conn = new PDO($db_name,$db_user,$db_password);

function uniq_id(){
    $chars ='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charsLength = strlen($chars);
    $randomString ='';
    for($i=0 ; $i<5 ; $i++){
        $randomString.=$chars[mt_rand(0,$charsLength-1)];
    }
    return $randomString;
}
?>