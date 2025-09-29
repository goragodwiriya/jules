<?php
header("Content-Type: application/json; charset=UTF-8");

$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? '';

$response = [
    'intent' => 'unknown',
    'entities' => [],
    'reply' => 'ขออภัยครับ ผมไม่เข้าใจคำสั่งของคุณ'
];

// --- Mock AI Logic ---
// This is a simplified simulation of Natural Language Understanding.

// Try to detect intent to create an appointment
if (preg_match('/(นัด|จอง|บันทึก).*(ประชุม|หาหมอ|ธุระ|นัดหมาย)/u', $message)) {
    $response['intent'] = 'create_appointment';

    // Mock entity extraction for title
    $title = 'นัดหมาย'; // Default title
    if (preg_match('/(เรื่อง|ว่า|เกี่ยวกับ) ([\p{L}\s\d]+)/u', $message, $matches)) {
        $title = trim($matches[2]);
    }

    // Mock entity extraction for date/time
    // This is very basic. A real AI would handle "พรุ่งนี้", "มะรืน", "บ่ายสอง" etc.
    $datetime = null;
    if (preg_match('/(\d{1,2}\/\d{1,2}\/\d{4}) (\d{1,2}:\d{2})/', $message, $matches)) {
        // Matches format like "DD/MM/YYYY HH:MM"
        $dateParts = explode('/', $matches[1]);
        $timeParts = explode(':', $matches[2]);
        // Note: This assumes Thai DD/MM/YYYY. PHP's `strtotime` needs MM/DD/YYYY.
        $isoDateStr = "{$dateParts[1]}/{$dateParts[0]}/{$dateParts[2]} {$timeParts[0]}:{$timeParts[1]}";
        $datetime = date('Y-m-d\TH:i:s', strtotime($isoDateStr));
    }

    if ($title && $datetime) {
        $response['entities'] = [
            'title' => $title,
            'datetime' => $datetime
        ];
        $response['reply'] = "รับทราบครับ กำลังบันทึกนัดหมาย: '{$title}'";
    } else {
        // If details are missing, switch intent to clarify
        $response['intent'] = 'clarify_appointment';
        $response['reply'] = 'ได้ครับ ต้องการนัดหมายเรื่องอะไร และวันเวลาใดครับ?';
    }
}

echo json_encode($response);
?>