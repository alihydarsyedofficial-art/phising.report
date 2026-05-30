<?php
// api.php - Enterprise Threat Intelligence & Auto-Reporting Engine
header('Content-Type: application/json');
require_once 'config.php';

// =========================================================================
// 🚀 TELEGRAM BOT CONFIGURATION
// =========================================================================
$enableTelegram = true; 
$botToken = "YOUR_TELEGRAM_BOT_TOKEN_HERE"; // BotFather Token
$chatId = "YOUR_CHAT_ID_HERE"; // User ID

function sendTelegramAlert($message) {
    global $botToken, $chatId, $enableTelegram;
    if(!$enableTelegram || empty($botToken) || empty($chatId)) return;
    
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $data = ['chat_id' => $chatId, 'text' => $message, 'parse_mode' => 'HTML'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_exec($ch);
    curl_close($ch);
}
// =========================================================================

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add_target') {
    $url = $_POST['url'] ?? '';
    $countryNode = $_POST['proxy_node'] ?? 'DIRECT';
    
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid URL Format']);
        exit;
    }

    $parsedUrl = parse_url($url);
    $domain = preg_replace('/^www\./', '', $parsedUrl['host'] ?? '');
    $telemetry = getDomainTelemetry($domain);

    // Advanced ASN & Country Lookup
    $asnData = "Unknown";
    $serverCountry = "Unknown";
    $ipApiInfo = @file_get_contents("http://ip-api.com/json/{$telemetry['ip']}?fields=country,isp,as");
    if($ipApiInfo) {
        $ipDetails = json_decode($ipApiInfo, true);
        if(isset($ipDetails['as'])) {
            $asnData = $ipDetails['as'] . ' (' . $ipDetails['isp'] . ')';
            $serverCountry = $ipDetails['country'];
        }
    }

    // Auto Screenshot URL (Evidence)
    $screenshotUrl = "https://image.thum.io/get/width/1200/crop/800/" . $url;

    // Proxy Setup
    $activeProxy = "Direct (No Proxy)";
    if ($countryNode !== 'DIRECT') {
        $fetchedProxy = getFreeProxy($countryNode);
        if ($fetchedProxy) $activeProxy = "Node: {$countryNode} | IP: {$fetchedProxy}";
    }

    try {
        $stmt = $db->prepare("SELECT id, current_ip FROM targets WHERE domain = ?");
        $stmt->execute([$domain]);
        $existing = $stmt->fetch();

        if ($existing) {
            $targetId = $existing['id'];
            if ($existing['current_ip'] !== $telemetry['ip']) {
                $logStmt = $db->prepare("INSERT INTO ip_history (domain_id, old_ip, new_ip) VALUES (?, ?, ?)");
                $logStmt->execute([$targetId, $existing['current_ip'], $telemetry['ip']]);
                $updateStmt = $db->prepare("UPDATE targets SET current_ip = ?, hosting_provider = ?, abuse_email = ? WHERE id = ?");
                $updateStmt->execute([$telemetry['ip'], $telemetry['provider'], $telemetry['abuse_email'], $targetId]);
                
                // Telegram Alert: IP Evasion Detected
                sendTelegramAlert("🚨 <b>IP EVASION DETECTED</b> 🚨\n\n<b>Domain:</b> {$domain}\n<b>Old IP:</b> {$existing['current_ip']}\n<b>New IP:</b> {$telemetry['ip']}\n<b>System:</b> TRICK A4IF Intelligence");
            }
        } else {
            $insertStmt = $db->prepare("INSERT INTO targets (url, domain, current_ip, hosting_provider, abuse_email) VALUES (?, ?, ?, ?, ?)");
            $insertStmt->execute([$url, $domain, $telemetry['ip'], $telemetry['provider'], $telemetry['abuse_email']]);
            $targetId = $db->lastInsertId();
        }

        // Dynamic Mail Generation
        $trackingId = strtoupper(uniqid("TRK-"));
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $subject = "URGENT [Ticket: $trackingId]: Phishing/Fraud Activity on $domain";

        $body = "To the Abuse Team of {$telemetry['provider']},\n\n";
        $body .= "We have identified an active phishing/fraudulent campaign hosted on your infrastructure. Immediate suspension is requested.\n\n";
        $body .= "--- THREAT INTELLIGENCE DATA ---\n";
        $body .= "Reported URL: $url\nRoot Domain: $domain\nResolved IP: {$telemetry['ip']}\n";
        $body .= "Server Location: $serverCountry\nASN Information: $asnData\nRouting Node: $activeProxy\n\n";
        $body .= "--- VISUAL EVIDENCE ---\n";
        $body .= "An automated screenshot of the malicious payload has been captured here:\n$screenshotUrl\n\n";
        $body .= "Please review the evidence and disable the account.\n\nMohammad Mujahidul Islam\nTRICK A4IF Security Network.";
        
        $fromEmail = "node_runner_" . rand(100,999) . "@" . $_SERVER['HTTP_HOST'];
        $headers = "From: TRICK A4IF Alert <$fromEmail>\r\nReply-To: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
        $headers .= "Message-ID: <$trackingId@" . $_SERVER['HTTP_HOST'] . ">\r\n";
        $headers .= "X-Priority: 1\r\n";
        
        $mailSent = @mail($telemetry['abuse_email'], $subject, $body, $headers);
        $deliveryStatus = $mailSent ? 'Dispatched' : 'SMTP Issue';

        $logReportStmt = $db->prepare("INSERT INTO report_logs (domain_id, recipient_email, subject, report_body, proxy_used, delivery_status) VALUES (?, ?, ?, ?, ?, ?)");
        $logReportStmt->execute([$targetId, $telemetry['abuse_email'], $subject, $body, $activeProxy, $deliveryStatus]);

        // Telegram Alert: Report Sent
        sendTelegramAlert("🔥 <b>PAYLOAD DISPATCHED</b> 🔥\n\n<b>Target:</b> {$domain}\n<b>Provider:</b> {$telemetry['provider']}\n<b>Status:</b> {$deliveryStatus}\n<b>Visual:</b> <a href='{$screenshotUrl}'>View Proof</a>");

        echo json_encode([
            'status' => 'success', 'domain' => $domain, 'ip' => $telemetry['ip'],
            'abuse_email' => $telemetry['abuse_email'], 'proxy' => $activeProxy,
            'mail_status' => $deliveryStatus, 'screenshot' => $screenshotUrl
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// API Getters
if ($action === 'get_targets') { echo json_encode($db->query("SELECT * FROM targets ORDER BY id DESC")->fetchAll()); exit; }
if ($action === 'get_ip_history') { echo json_encode($db->query("SELECT h.*, t.domain FROM ip_history h JOIN targets t ON h.domain_id = t.id ORDER BY h.id DESC")->fetchAll()); exit; }
if ($action === 'get_report_logs') { echo json_encode($db->query("SELECT l.*, t.domain FROM report_logs l JOIN targets t ON l.domain_id = t.id ORDER BY l.id DESC")->fetchAll()); exit; }
?>
