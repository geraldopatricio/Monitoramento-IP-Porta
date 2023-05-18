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
        $mail->Host = 'SMTP.XXXXXX.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'USUARIO_XXXXXX';
        $mail->Password = 'SENHA_XXXXXX';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // remetente - email 
        $mail->setFrom('naoresponda@DOMINIO-XXX.com.br', 'naoresponda');

        // email de quem irá receber o alerta, podendo ser uma lista de um grupo de email 
        // ou apenas um email do gestor no caso
        $mail->addAddress('EMAIL-DESTINO-XXX@gmail.com');

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

$ip1 = '192.168.0.XXX';
$port1 = PORTA_AQUI_XXX;
$ip2 = '192.168.0.XXX';
$port2 = PORTA_AQUI_XXX;

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
