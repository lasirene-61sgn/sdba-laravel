<?php
$k = env('GEMINI_API_KEY');
$u = 'https://generativelanguage.googleapis.com/v1beta/models?key='.$k.'&pageSize=100';
$ch = curl_init($u);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$r = curl_exec($ch);
$data = json_decode($r, true);
if (isset($data['models'])) {
    foreach($data['models'] as $m) {
        echo $m['name'] . "\n";
    }
}
curl_close($ch);
