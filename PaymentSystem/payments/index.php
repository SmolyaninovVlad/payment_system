<?php
require_once '../classes/db.class.php';
if ($_SERVER['REQUEST_METHOD']!="GET") echo json_encode(array("success"=>"false","text"=>"Only GET requests are available"));

if (!$_REQUEST['sessionId']) die("Invalid data");

$db =  DataBase::getDB();
$query = "SELECT * FROM payments WHERE sessionId={?}";
$res = $db->select($query, array($_REQUEST['sessionId']))[0];
if (!$res) die("Неверная или устаревшая ссылка");
$lifeTime = downcounter($res['date']);
//Время жизни закончилось, очистить сессию
if (!$lifeTime) {
    $query = "UPDATE payments SET sessionId='NULL' WHERE sessionId={?}";
    $res = $db->query($query, array($_REQUEST['sessionId']));
    die("Время жизни сессии закончилось");
}
function downcounter($date){
    $lifeTime = 30;
    $check_time = strtotime($date) - (time() - (60*$lifeTime));
    if($check_time <= 0){
        return false;
    }

    $days = floor($check_time/86400);
    $hours = floor(($check_time%86400)/3600);
    $minutes = floor(($check_time%3600)/60);
    $seconds = $check_time%60; 

    $str = '';
    if($days > 0) $str .= declension($days,array('день','дня','дней')).' ';
    if($hours > 0) $str .= declension($hours,array('час','часа','часов')).' ';
    if($minutes > 0) $str .= declension($minutes,array('минута','минуты','минут')).' ';
    if($seconds > 0) $str .= declension($seconds,array('секунда','секунды','секунд'));

    return $str;
}

function declension($digit,$expr,$onlyword=false){
    if(!is_array($expr)) $expr = array_filter(explode(' ', $expr));
    if(empty($expr[2])) $expr[2]=$expr[1];
    $i=preg_replace('/[^0-9]+/s','',$digit)%100;
    if($onlyword) $digit='';
    if($i>=5 && $i<=20) $res=$digit.' '.$expr[2];
    else
    {
        $i%=10;
        if($i==1) $res=$digit.' '.$expr[0];
        elseif($i>=2 && $i<=4) $res=$digit.' '.$expr[1];
        else $res=$digit.' '.$expr[2];
    }
    return trim($res);
}
?>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Детали оплаты</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body data-gr-c-s-loaded="true">
        <div id="root">
            <div class="text-center">
                <div class="content">
                    <h2>Детали оплаты</h2>
                    <span>Оплата успешно произведена <?=$res['date'] ?></span>
                    <div class="container">
                        <div class="card">
                            <div class="row">
                                <label>Назначение платежа</label>
                                <input disabled name="appointment" required="" value="<?=$res['appointment'] ?>" type="text"/>
                            </div>
                            <div class="row">
                                <label>Номер карты</label>
                                <input disabled name="appointment" required="" value="<?=$res['card_Number'] ?>" type="text"/>
                            </div>
                            <div class="row">
                                <label>Сумма платежа</label>
                                <input disabled name="appointment" required="" value="<?=$res['total'] ?>" type="text"/>
                            </div>
                        </div>
                    </div>
                    <span style="display:flex;">Оставшеемя время жизни сессии <span id="time"><?=$lifeTime?></span></span>
                </div>
            </div>
        </div>
    </body>
</html>
