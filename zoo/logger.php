<?php
// logger.php

/**
 * Schreibt einen Log-Eintrag in die Datei app.log.
 *
 * @param string $message Die zu protokollierende Aktion/Information
 */
function writeLog($message)
{
    // Pfad zur Log-Datei anpassen, falls sie woanders liegen soll.
    $logFile = __DIR__ . '/app.log';

    // Zeitstempel erzeugen
    $date = date('Y-m-d H:i:s');

    // Log-Eintrag zusammenbauen
    $entry = "[" . $date . "] " . $message . PHP_EOL;

    // In Datei anhängen
    file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
}
