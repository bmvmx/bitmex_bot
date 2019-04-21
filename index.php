<?php
include "BitMex.php";
include "MovingAverage.php";
include "OrderHandler.php";
include "TelegramHandler.php";
include "Donchian.php";
include "WMA.php";
include "DBHandler.php";
include "Logger.php";
include "Validator.php";

//INSERT BITMEX API KEY HERE
$key = "";
$secret = "";

$PERIODI = 16;
$LEVA = 1;

$bitmex = new BitMex($key, $secret);
$MA = new MovingAverage($bitmex);
$orderHandler = new OrderHandler($bitmex);
$donchian = new Donchian($bitmex);
$WMA = new WMA($bitmex);
$telegramHandler = new TelegramHandler();
$DBHandler = new DBHandler();

$tick = $bitmex->getTicker();
$bandWidth = $donchian->getBandWidth();

//INIT CONTRACTS
$saldo = floatval($bitmex->getWallet()['amount']) / 100000000;
$contrattiInvestiti = 0;
$posizioniAperte = $bitmex->getOpenPositions();

if (count($posizioniAperte) > 0) {
    $contrattiInvestiti = intval($posizioniAperte[0]['currentQty']) > 0 ? intval($posizioniAperte[0]['currentQty']) : -(intval($posizioniAperte[0]['currentQty']));
}

$contrattiAcq = (round(floatval($saldo) * floatval($tick['last']) * 0.9 * ($LEVA)) - $contrattiInvestiti);
$contrattiAcqAll = round(floatval($saldo) * floatval($tick['last']) * 0.9 * ($LEVA));
$fixedContracts = 30;
$prezzoAcq = intval($tick['last']);
$step = (int) $DBHandler->getField("numero_operazioni");
$verso = (int) $DBHandler->getField("verso");
$bandaH = $donchian->getBandaH();
$bandaL = $donchian->getBandaL();

$logger = new Logger($tick['last'], $bandWidth, $bandaH, $bandaL);
$validator = new Validator($tick, $posizioniAperte);

if (!isset($tick) || !isset($saldo) || !isset($bandaH) || !isset($bandaL) || !isset($bandWidth)) {
    $logger->logData();
    $telegramHandler->sendTelegramMessage("Validator success: chiamata a Bitmex ignorata per errori nella risposta.");
    exit();
}

//LONG
if ($verso != 2) {
    if ($step == 0 && $MA->isPositive() && $WMA->isPositive() && $tick['last'] > $bandaH && $bandWidth > 0.025) {
        $orderHandler->openLong($prezzoAcq, $fixedContracts, $LEVA);
        $telegramHandler->sendTelegramMessage("Entrato long a: " . $tick['last'] . " step numero " . ($step + 1));
        $DBHandler->updateField("numero_operazioni", ($step + 1));
        $DBHandler->updateField("prezzo_entrata", $prezzoAcq);
        $DBHandler->updateField("verso", 1);
        $logger -> logData();

    } else if ($step > 0 && $step < 3 && $tick['last'] >= ($DBHandler->getField("prezzo_entrata") * 1.05)) {
        $orderHandler->openLong($prezzoAcq, $fixedContracts, $LEVA);
        $telegramHandler->sendTelegramMessage("Entrato long a: " . $tick['last'] . " step numero " . ($step + 1));
        $DBHandler->updateField("numero_operazioni", ($step + 1));
        $logger -> logData();

    } else if ($step >= 3 && $tick['last'] >= ($DBHandler->getField("prezzo_entrata") * 1.2)) {
        $orderHandler->closeAll();
        $telegramHandler->sendTelegramMessage("Chiuso il long a: " . $tick['last'] . " per profit");
        $DBHandler->updateField("numero_operazioni", 0);
        $DBHandler->updateField("prezzo_entrata", 0);
        $logger -> logData();
    
    } else if ($step > 0 && $tick['last'] < $bandaL && !($WMA->isPositive())) {
        $orderHandler->closeAll();
        $telegramHandler->sendTelegramMessage("Chiuso il long a: " . $tick['last'] . " in perdita");
        $DBHandler->updateField("numero_operazioni", 0);
        $DBHandler->updateField("prezzo_entrata", 0);
        $DBHandler->updateField("verso", 0);
        $logger -> logData();
    }
}

//SHORT
if ($verso != 1) {
    if ($step == 0 && !($MA->isPositive()) && !($WMA->isPositive()) && $tick['last'] < $bandaL) {
        $orderHandler->openShort($prezzoAcq, $fixedContracts, $LEVA);
        $telegramHandler->sendTelegramMessage("Entrato short a: " . $tick['last'] . " step numero " . ($step + 1));
        $DBHandler->updateField("numero_operazioni", ($step + 1));
        $DBHandler->updateField("prezzo_entrata", $prezzoAcq);
        $DBHandler->updateField("verso", 2);
        $logger -> logData();

    } else if ($step > 0 && $step < 3 && $tick['last'] <= ($DBHandler->getField("prezzo_entrata") * 0.95)) {
        $orderHandler->openShort($prezzoAcq, $fixedContracts, $LEVA);
        $telegramHandler->sendTelegramMessage("Entrato short a: " . $tick['last'] . " step numero " . ($step + 1));
        $DBHandler->updateField("numero_operazioni", ($step + 1));
        $logger -> logData();

    } else if ($step >= 3 && $tick['last'] <= ($DBHandler->getField("prezzo_entrata") * 0.8)) {
        $orderHandler->closeAll();
        $telegramHandler->sendTelegramMessage("Chiuso lo short a: " . $tick['last'] . " per profit");
        $DBHandler->updateField("numero_operazioni", 0);
        $DBHandler->updateField("prezzo_entrata", 0);
        $logger -> logData();

    } else if ($step > 0 && $tick['last'] > $bandaH && $WMA->isPositive()) {
        $orderHandler->closeAll();
        $telegramHandler->sendTelegramMessage("Chiuso lo short a: " . $tick['last'] . " in perdita");
        $DBHandler->updateField("numero_operazioni", 0);
        $DBHandler->updateField("prezzo_entrata", 0);
        $DBHandler->updateField("verso", 0);
        $logger -> logData();
    }
}

?>