<?php
require_once 'config.php';
requireLogin(); // Ensure session is started and user is logged in

header('Content-Type: application/json');

if ($_POST && isset($_POST['date'])) {
    $selected_date = $_POST['date'];
    
    // Validate date format and ensure it's not in the past
    if (!strtotime($selected_date) || strtotime($selected_date) < strtotime('today')) {
        echo json_encode([
            'error' => 'invalid_date',
            'message' => 'Invalid or past date selected.'
        ]);
        exit;
    }
    
    // Check if current user already has a booking on this date
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as duplicate_booking
        FROM bookings 
        WHERE user_id = ? 
        AND booking_date = ? 
        AND car_license = ?
        AND car_engine = ?
        AND status IN ('pending', 'confirmed')
    ");
    $stmt->execute([$_SESSION['user_id'], $selected_date, $_POST['car_license'] ?? '', $_POST['car_engine'] ?? '']);
    $duplicate_check = $stmt->fetch();
    
    $has_duplicate = ($duplicate_check['duplicate_booking'] > 0);
    
    // ✅ Get all mechanics with their bookings for the specific date
    $stmt = $pdo->prepare("
        SELECT 
            m.mechanic_id,
            m.name,
            m.specialty,
            m.email,
            m.phone,
            m.status,
            COALESCE(COUNT(b.id), 0) as bookings_on_date
        FROM mechanics m 
        LEFT JOIN bookings b 
            ON m.mechanic_id = b.mechanic_id 
            AND b.booking_date = ?
            AND b.status IN ('pending', 'confirmed')
        WHERE m.status = 'available'
        GROUP BY m.mechanic_id, m.name, m.specialty, m.email, m.phone, m.status
        ORDER BY bookings_on_date ASC, m.name ASC
    ");
    
    $stmt->execute([$selected_date]);
    $mechanics = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'mechanics' => $mechanics,
        'date' => $selected_date,
        'has_duplicate' => $has_duplicate,
        'duplicate_message' => $has_duplicate ? 'You already have a booking for this car on this date. Please choose another date or car.' : null
    ]);
} else {
    echo json_encode([
        'error' => 'no_date',
        'message' => 'Date not provided'
    ]);
}
?>
