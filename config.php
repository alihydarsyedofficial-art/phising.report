<?php
// config.php - Core Engine with Proxy & Database Support

try {
    // SQLite Database Connection
    $db = new PDO('sqlite:phishshield_db.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die(json_encode(['status' => 'error', 'message' => 'Database failure: ' . $e->getMessage()]));
}

// Create Tables
$db->exec("CREATE TABLE IF NOT EXISTS targets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    url TEXT NOT NULL,
    domain TEXT NOT NULL UNIQUE,
    current_ip TEXT,
    hosting_provider TEXT,
    abuse_email TEXT,
    status TEXT DEFAULT 'Active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS ip_history (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    domain_id INTEGER,
    old_ip TEXT,
    new_ip TEXT,
    detected_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(domain_id) REFERENCES targets(id)
)");

$db->exec("CREATE TABLE IF NOT EXISTS report_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    domain_id INTEGER,
    recipient_email TEXT,
    subject TEXT,
    report_body TEXT,
    proxy_used TEXT,
    delivery_status TEXT,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(domain_id) REFERENCES targets(id)
)");

// DNS Routing & Abuse Email Extractor
function getDomainTelemetry($domain) {
    $ip = gethostbyname($domain);
    $provider = 'Unknown ISP';
    $abuseEmail = 'abuse@' . $domain;

    $nsRecords = dns_get_record($domain, DNS_NS);
    if (!empty($nsRecords)) {
        $nsTarget = strtolower($nsRecords[0]['target']);
        if (strpos($nsTarget, 'cloudflare') !== false) {
            $abuseEmail = 'abuse@cloudflare.com'; $provider = 'Cloudflare';
        } elseif (strpos($nsTarget, 'namecheap') !== false) {
            $abuseEmail = 'abuse@namecheap.com'; $provider = 'Namecheap';
        } elseif (strpos($nsTarget, 'hostinger') !== false) {
            $abuseEmail = 'abuse@hostinger.com'; $provider = 'Hostinger';
        } elseif (strpos($nsTarget, 'namesilo') !== false) {
            $abuseEmail = 'abuse@namesilo.com'; $provider = 'NameSilo';
        } elseif (strpos($nsTarget, 'godaddy') !== false || strpos($nsTarget, 'domaincontrol') !== false) {
            $abuseEmail = 'abuse@godaddy.com'; $provider = 'GoDaddy';
        }
    }
    return ['ip' => $ip, 'provider' => $provider, 'abuse_email' => $abuseEmail];
}

// Free Proxy Fetcher API
function getFreeProxy($countryCode) {
    $apiUrl = "https://api.proxyscrape.com/v2/?request=displayproxies&protocol=http&timeout=10000&country={$countryCode}&ssl=all&anonymity=all";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $result = curl_exec($ch);
    curl_close($ch);
    
    if ($result) {
        $proxies = explode("\n", trim($result));
        if (count($proxies) > 0 && !empty($proxies[0])) {
            return trim($proxies[array_rand($proxies)]); 
        }
    }
    return false;
}
?>
