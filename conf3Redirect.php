<?php
// Config
    define(‘HOST’, ‘localhost’);
    define(‘PORT’, 5038);
    define(‘USER’, ‘admin’);
    define(‘PASS’, ‘xxxx’);
    if(!isset($argv[1]) || !isset($argv[2]) || !isset($argv[3]))
        die(‘Uso: conf3Redirect CHAN1 CHAN2 SALA’);
    $chan1 = $argv[1];
    $chan2 = $argv[2];
    $sala  = $argv[3];
    if(!strstr($chan1, ‘/’))
        die(‘CHAN1 invalido’);
    if(!strstr($chan2, ‘/’))
        die(‘CHAN2 invalido’);
    if(!is_numeric($sala) || $sala < 100 || $sala > 199)
        die(‘SALA invalida [100-199]’);
    // dar tempo dos canais voltarem da transferencia
    sleep(1);
    // Abre conexao com o AMI
    $errno = $errstr = 0;
    $oSocket = @fsockopen(HOST, PORT, &$errno, &$errstr, 20);
    if(!$oSocket)
        die(“Impossível conectar com o AMI [$errstr ($errno)]”);
    fputs($oSocket, “Action: login\r\n” .
                    “Username: “.USER.”\r\n” .
                    “Secret: “.PASS.”\r\n” .
                    “Events: off\r\n\r\n”);
    $authOK = false;
    do {
        $aux = fgets($oSocket);
        if(!$authOK && strstr($aux, ‘Authentication accepted’)) $authOK=true;
    } while($aux != “\r\n”);
    if(!$authOK)
        die(‘Impossível logar no AMI’);
    fputs($oSocket, “Action: Redirect\r\n” .
                    “Channel: $chan1\r\n” .
                    “ExtraChannel: $chan2\r\n” .
                    “Context: conf3-sala\r\n” .
                    “Exten: s\r\n” .
                    “Priority: 1\r\n\r\n”);
    $cmdOK = false;
    do {
        $aux = fgets($oSocket);
        if(!$cmdOK && strstr($aux, ‘Success’)) $cmdOK=true;
    } while($aux != “\r\n”);
    if($cmdOK) {
        // Redirect OK – Incrementar sala
        $sala++;
        if($sala >= 199) $sala = 100;
        // dar tempo dos canais entrarem na sala
        sleep(1);
        fputs($oSocket, “Action: DBPut\r\n” .
                        “Family: conf3\r\n” .
                        “Key: sala\r\n” .
                        “Val: $sala\r\n\r\n”);
        while( fgets($oSocket) != “\r\n” ) {}
    }
    fputs($oSocket, “Action: logoff\r\n\r\n”);
    while( fgets($oSocket) != “\r\n” ) {}
    return 0;
?>