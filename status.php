<?php
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($subject, $body)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'email-smtp.us-east-1.amazonaws.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'AKIAUCCMZWTQ2CTX7HDP';
        $mail->Password = 'BBzA1meb2xxKimHXD3NylCTQUM/CyvNuWAjgf0EIQyCm';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // remetente - email smartmotopecas
        $mail->setFrom('naoresponda@smartmotopecas.com.br', 'naoresponda');

        // remetente - email brothermotos - para quando quiser mudar de dominio encaminhador
        // $mail->setFrom('naoresponda@brothermotos.com.br', 'naoresponda');

        // remetente - email jlmotopecas - para quando quiser mudar de dominio encaminhador
        // $mail->setFrom('naoresponda@jlmotopecas.com.br', 'naoresponda');

        // email de quem irá receber o alerta, podendo ser uma lista de um grupo de email 
        // ou apenas um email do gestor no caso
        $mail->addAddress('gpatricio.melo@gmail.com');

        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
    } catch (Exception $e) {
        echo "Erro ao enviar e-mail: {$mail->ErrorInfo}";
    }
}

function checkIPStatus($ip, $port)
{
    $online = @fsockopen($ip, $port, $errno, $errstr, 5);
    return ($online) ? true : false;
}

$ip1 = '192.168.0.158';
$port1 = 90;
$ip2 = '192.168.0.25';
$port2 = 80;

$status = [];

$status[] = [
    'ip' => $ip1,
    'online' => checkIPStatus($ip1, $port1),
];
$status[] = [
    'ip' => $ip2,
    'online' => checkIPStatus($ip2, $port2),
];

$offlineIPs = [];

foreach ($status as $ip) {
    if (!$ip['online']) {
        $offlineIPs[] = $ip['ip'];
    }
}

if (!empty($offlineIPs)) {
    $subject = 'Falha de conexao com IP(s) off-line';
    $body = 'Os seguintes IPs estao off-line: ' . implode(', ', $offlineIPs);
    sendEmail($subject, $body);
}

header('Content-Type: application/json');
echo json_encode($status);
?>
