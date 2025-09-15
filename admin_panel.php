<?php
require_once 'config.php';
requireLogin();
requireAdmin();

$success = '';
$error = '';

// Temporary debug output
error_log("Starting admin panel page load");

// Fetch users list
$stmt = $pdo->prepare("
    SELECT id, name, email, role,
           COALESCE(is_disabled, 0) as is_disabled
    FROM users 
    WHERE role != 'admin'
    ORDER BY id DESC
");

$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug output
foreach ($users as $user) {
    error_log(sprintf(
        "User ID: %d, Name: %s, is_disabled value: [%s]",
        $user['id'],
        $user['name'],
        var_export($user['is_disabled'], true)
    ));
}

// Debug each user's disabled status
foreach ($users as $user) {
    error_log("User {$user['id']} - is_disabled: " . var_export($user['is_disabled'], true));
}

// To handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'toggle_user_status':
                $user_id = $_POST['user_id'];
                
                error_log("Processing toggle_user_status for user_id: " . $user_id);
                
                // First, get current status
                $stmt = $pdo->prepare("SELECT id, is_disabled FROM users WHERE id = ? AND role != 'admin'");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
                
                if ($user) {
                    // Get current status and convert to integer
                    $current_status = (int)$user['is_disabled'];
                    $new_status = $current_status ? 0 : 1;  // Toggle between 0 and 1
                    
                    error_log(sprintf(
                        "Toggling user %d - Current status: [%d], New status: [%d]",
                        $user_id,
                        $current_status,
                        $new_status
                    ));
                    
                    // Update with explicit integer value
                    $stmt = $pdo->prepare("UPDATE users SET is_disabled = ? WHERE id = ? AND role != 'admin'");
                    
                    if ($stmt->execute([$new_status, $user_id])) {
                        // Verify the update
                        $verify = $pdo->prepare("SELECT is_disabled FROM users WHERE id = ?");
                        $verify->execute([$user_id]);
                        $updated = $verify->fetch();
                        
                        error_log(sprintf(
                            "Update verification - User %d new status: [%s]",
                            $user_id,
                            var_export($updated['is_disabled'], true)
                        ));
                        
                        $success = 'User ' . ($new_status ? 'disabled' : 'enabled') . ' successfully';
                    } else {
                        $error = 'Failed to update user status';
                        error_log("Database update failed for user " . $user_id);
                    }
                } else {
                    $error = 'User not found or cannot be disabled';
                    error_log("User not found or is admin: " . $user_id);
                }
                break;
            case 'update_booking':
                //$name = $booking['name'];
                //$address = $booking['address'];
                //$phone = $booking['phone'];
                //$car_license = $booking['car_license'];
                //$car_engine = $booking['car_engine'];
                $booking_id = $_POST['booking_id'];
                $new_mechanic_id = $_POST['new_mechanic_id'];
                $new_date = $_POST['new_date'];
                $new_time = $_POST['new_time'];
                $new_status = $_POST['new_status'];
                
                // To get original booking details
                $stmt = $pdo->prepare("
                    SELECT b.*, u.email, u.username, m.name as mechanic_name, rs.service_name
                    FROM bookings b 
                    JOIN users u ON b.user_id = u.id 
                    JOIN mechanics m ON b.mechanic_id = m.mechanic_id 
                    JOIN repair_services rs ON b.service_id = rs.id 
                    WHERE b.id = ?
                ");
                $stmt->execute([$booking_id]);
                $original_booking = $stmt->fetch();
                
                if ($original_booking) {
                    $name = isset($_POST['name']) && $_POST['name'] !== '' ? trim($_POST['name']) : $original_booking['name'];
                    $address = isset($_POST['address']) && $_POST['address'] !== '' ? trim($_POST['address']) : $original_booking['address'];
                    $phone = isset($_POST['phone']) && $_POST['phone'] !== '' ? trim($_POST['phone']) : $original_booking['phone'];
                    $car_license = isset($_POST['car_license']) && $_POST['car_license'] !== '' ? trim($_POST['car_license']) : $original_booking['car_license'];
                    $car_engine = isset($_POST['car_engine']) && $_POST['car_engine'] !== '' ? trim($_POST['car_engine']) : $original_booking['car_engine'];                   
                    // Check if mechanic is available
                    if ($new_mechanic_id != $original_booking['mechanic_id']) {
                        $stmt = $pdo->prepare("
                            SELECT COUNT(*) as current_orders 
                            FROM bookings 
                            WHERE mechanic_id = ? AND booking_date = ? AND status IN ('pending', 'confirmed')
                        ");
                        $stmt->execute([$new_mechanic_id, $new_date]);
                        $mechanic_load = $stmt->fetch();
                        
                        if ($mechanic_load['current_orders'] >= 4) {
                            $error = 'Selected mechanic has reached maximum capacity (4 jobs) for this date';
                            break;
                        }
                    }
                    
                    // Update booking
                    $stmt = $pdo->prepare("
                        UPDATE bookings 
                        SET name = ?, address = ?, phone = ?, car_license = ?, car_engine = ?,
                            mechanic_id = ?, booking_date = ?, booking_time = ?, status = ?, updated_at = NOW()
                        WHERE id = ?
                    "); 

                    if ($stmt->execute([
                        $name, $address, $phone, $car_license, $car_engine,
                        $new_mechanic_id, $new_date, $new_time, $new_status, $booking_id
                    ])) {
                        //new mechanic name
                        $stmt = $pdo->prepare("SELECT name FROM mechanics WHERE mechanic_id = ?");
                        $stmt->execute([$new_mechanic_id]);
                        $new_mechanic = $stmt->fetch();
                        
                        // Send email notification to user :) jodi host korte pari :) 
                        $subject = "Booking Update - Workshop Service";
                        $message = "
                            <h2>Booking Update</h2>
                            <p>Dear {$original_booking['username']},</p>
                            <p>Your booking #{$booking_id} has been updated:</p>
                            <h3>New Details:</h3>
                            <ul>
                                <li><strong>Service:</strong> {$original_booking['service_name']}</li>
                                <li><strong>Mechanic:</strong> {$new_mechanic['name']}</li>
                                <li><strong>Date:</strong> " . date('M j, Y', strtotime($new_date)) . "</li>
                                <li><strong>Time:</strong> " . date('g:i A', strtotime($new_time)) . "</li>
                                <li><strong>Status:</strong> " . ucfirst($new_status) . "</li>
                            </ul>
                            <p>If you have any questions, please contact us.</p>
                        ";
                        
                        sendEmail($original_booking['email'], $subject, $message);
                        $success = 'Booking updated successfully and user notified via email.';
                    } else {
                        $error = 'Failed to update booking.';
                    }
                }
                break;
                
            case 'toggle_service':
                $service_id = $_POST['service_id'];
                $new_status = $_POST['new_status'];
                
                $stmt = $pdo->prepare("UPDATE repair_services SET status = ? WHERE id = ?");
                if ($stmt->execute([$new_status, $service_id])) {
                    $success = 'Service status updated successfully.';
                } else {
                    $error = 'Failed to update service status.';
                }
                break;
                
            case 'edit_mechanic':
                $mechanic_id = $_POST['mechanic_id'];
                $name = trim($_POST['name']);
                $email = trim($_POST['email']);
                $phone = trim($_POST['phone']);
                $specialty = trim($_POST['specialty']);
                
                if (empty($name) || empty($email) || empty($phone) || empty($specialty)) {
                    $error = 'All fields are required.';
                    break;
                }
                
                $stmt = $pdo->prepare("
                    UPDATE mechanics 
                    SET name = ?, email = ?, phone = ?, specialty = ? 
                    WHERE mechanic_id = ?
                ");
                
                if ($stmt->execute([$name, $email, $phone, $specialty, $mechanic_id])) {
                    $success = 'Mechanic information updated successfully.';
                } else {
                    $error = 'Failed to update mechanic information.';
                }
                break;

            case 'toggle_mechanic':
                $mechanic_id = $_POST['mechanic_id'];
                $new_status = $_POST['new_status'];
                
                $stmt = $pdo->prepare("UPDATE mechanics SET status = ? WHERE mechanic_id = ?");
                if ($stmt->execute([$new_status, $mechanic_id])) {
                    $success = 'Mechanic status updated successfully.';
                } else {
                    $error = 'Failed to update mechanic status.';
                }
                break;
        }
    }
}

// bookings with details
$stmt = $pdo->query("
    SELECT b.*, u.username, u.email, m.name as mechanic_name
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    JOIN mechanics m ON b.mechanic_id = m.mechanic_id 
    ORDER BY b.created_at DESC
");
$bookings = $stmt->fetchAll();

// mechanics with their current workload (by date)
$stmt = $pdo->query("
    SELECT m.*, 
           COALESCE(COUNT(b.id), 0) as current_orders
    FROM mechanics m 
    LEFT JOIN bookings b ON m.mechanic_id = b.mechanic_id 
                        AND b.status IN ('pending', 'confirmed')
    GROUP BY m.mechanic_id
    ORDER BY m.name ASC
");
$mechanics = $stmt->fetchAll();

// repair services
$stmt = $pdo->query("SELECT * FROM repair_services ORDER BY service_name");
$services = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Multi Brand Workshop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 25%, #667eea 50%, #764ba2 75%, #8b5a3c 100%);
            min-height: 100vh;
            line-height: 1.6;
        }
        
        .header {
            background: linear-gradient(135deg, rgba(30,30,30,0.95) 0%, rgba(70,50,40,0.95) 50%, rgba(20,20,20,0.95) 100%);
            backdrop-filter: blur(10px);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        
        .nav {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            background: linear-gradient(45deg, #ff6b6b, #feca57, #48dbfb, #ff9ff3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .nav-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
            color: rgba(255,255,255,0.9);
        }
        
        .btn {
            padding: 0.7rem 1.5rem;
            text-decoration: none;
            border-radius: 25px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid #667eea;
        }
        
        .btn-success {
            background: linear-gradient(45deg, #00d2d3, #54a0ff);
            color: white;
        }
        
        .btn-warning {
            background: linear-gradient(45deg, #feca57, #ff9f43);
            color: white;
        }
        
        .btn-danger {
            background: linear-gradient(45deg, #ff6b6b, #ee5a52);
            color: white;
        }
        
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            margin: 0 0.2rem;
        }

        form[method="POST"] {
            display: inline-block;
            margin: 0 0.2rem;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .notification-bar {
            background: linear-gradient(90deg, #48dbfb, #0abde3);
            color: white;
            padding: 1rem;
            text-align: center;
            margin-bottom: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .notification-bar.success {
            background: linear-gradient(90deg, #00d2d3, #54a0ff);
        }
        
        .notification-bar.error {
            background: linear-gradient(90deg, #ff6b6b, #ee5a52);
        }
        
        .admin-header {
            text-align: center;
            margin-bottom: 2rem;
            color: white;
        }
        
        .admin-header h1 {
            font-size: 3rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #feca57, #ff9ff3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.25), rgba(255,255,255,0.1));
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.2);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            color: white;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: #feca57;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .admin-sections {
            display: grid;
            gap: 3rem;
        }
        
        .section {
            background: linear-gradient(135deg, rgba(255,255,255,0.25), rgba(255,255,255,0.1));
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .section-header {
            padding: 2rem;
            background: linear-gradient(135deg, rgba(0,0,0,0.1), rgba(0,0,0,0.05));
            border-bottom: 1px solid rgba(255,255,255,0.1);
            color: white;
        }
        
        .section-header h2 {
            margin: 0;
            font-size: 1.8rem;
        }
        
        .section-content {
            padding: 2rem;
            overflow-x: auto;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table th, .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            font-size: 0.9rem;
            color: white;
        }
        
        .table th {
            background: linear-gradient(135deg, rgba(0,0,0,0.2), rgba(0,0,0,0.1));
            font-weight: bold;
            color: #feca57;
        }
        
        .table tr:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .status {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status.pending {
            background: linear-gradient(45deg, #feca57, #ff9f43);
            color: white;
        }
        
        .status.confirmed {
            background: linear-gradient(45deg, #00d2d3, #54a0ff);
            color: white;
        }
        
        .status.completed {
            background: linear-gradient(45deg, #5f27cd, #00d2d3);
            color: white;
        }
        
        .status.cancelled {
            background: linear-gradient(45deg, #ff6b6b, #ee5a52);
            color: white;
        }
        
        .status.available {
            background: linear-gradient(45deg, #00d2d3, #54a0ff);
            color: white;
        }
        
        .status.unavailable {
            background: linear-gradient(45deg, #ff6b6b, #ee5a52);
            color: white;
        }
        
        .form-inline {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .form-inline select, .form-inline input {
            padding: 0.4rem 0.8rem;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            font-size: 0.8rem;
            background: rgba(255,255,255,0.1);
            color: white;
            backdrop-filter: blur(10px);
        }
        
        .form-inline select option {
            background: #2a2a2a;
            color: white;
        }
        
        .form-inline input::placeholder {
            color: rgba(255,255,255,0.6);
        }
        
        .workload {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: bold;
        }
        
        .workload.low {
            background: linear-gradient(45deg, #00d2d3, #54a0ff);
            color: white;
        }
        
        .workload.medium {
            background: linear-gradient(45deg, #feca57, #ff9f43);
            color: white;
        }
        
        .workload.high {
            background: linear-gradient(45deg, #ff6b6b, #ee5a52);
            color: white;
        }
        
        .real-time-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            background: #00d2d3;
            border-radius: 50%;
            margin-right: 0.5rem;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .last-updated {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.6);
            text-align: right;
            margin-top: 1rem;
        }
        
        @media (max-width: 768px) {
            .form-inline {
                flex-direction: column;
                align-items: stretch;
            }
            
            .form-inline select, .form-inline input {
                margin-bottom: 0.5rem;
            }
            
            .table {
                font-size: 0.8rem;
            }
            
            .table th, .table td {
                padding: 0.5rem;
            }
            
            .table td em {
                color: rgba(255,255,255,0.5);
                font-style: italic;
            }
            
            .table td:nth-child(12) { /* Notes column */
                max-width: 200px;
                white-space: pre-wrap;
                word-wrap: break-word;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo">🔧 Admin Panel - Multi Brand Workshop</div>
            <div class="nav-buttons">
                <span>Welcome, Admin <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="index.php" class="btn btn-secondary">Home</a>
                <a href="logout.php" class="btn btn-primary">Logout</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="admin-header">
            <h1><span class="real-time-indicator"></span>Workshop Management Dashboard</h1>
            <p>Real-time booking and resource management</p>
        </div>

        <?php if ($error): ?>
            <div class="notification-bar error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="notification-bar success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($bookings, function($b) { return $b['status'] === 'pending'; })); ?></div>
                <div class="stat-label">Pending Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($bookings, function($b) { return $b['status'] === 'confirmed'; })); ?></div>
                <div class="stat-label">Confirmed Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($mechanics, function($m) { return $m['status'] === 'available'; })); ?></div>
                <div class="stat-label">Available Mechanics</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($services, function($s) { return $s['status'] === 'available'; })); ?></div>
                <div class="stat-label">Active Services</div>
            </div>
        </div>

        <div class="admin-sections">
            <!-- Bookings Management -->
            <div class="section">
                <div class="section-header">
                    <h2>📅 Real-time Booking Management</h2>
                </div>
                <div class="section-content">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Car License</th>
                                <th>Car Engine</th>
                                <th>Service</th>
                                <th>Mechanic</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th>Price</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="bookings-table">
                            <?php foreach ($bookings as $booking): ?>
                                <tr data-booking-id="<?php echo $booking['id']; ?>">
                                    <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['address']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['car_license']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['car_engine']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['services']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['mechanic_name']); ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($booking['booking_date'] . ' ' . $booking['booking_time'])); ?></td>
                                    <td><span class="status <?php echo $booking['status']; ?>"><?php echo ucfirst($booking['status']); ?></span></td>
                                    <td>BDT<?php echo number_format($booking['total_price'], 2); ?></td>
                                    <td><?php echo !empty($booking['notes']) ? htmlspecialchars($booking['notes']) : '<em>No additional notes</em>'; ?></td>
                                    <td>
                                        <form method="POST" class="form-inline">
                                            <input type="hidden" name="action" value="update_booking">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            
                                            <select name="new_mechanic_id" required onchange="updateMechanicAvailability(this, '<?php echo $booking['booking_date']; ?>')">
                                                <?php foreach ($mechanics as $mechanic): ?>
                                                    <option value="<?php echo $mechanic['mechanic_id']; ?>" 
                                                            <?php echo ($mechanic['mechanic_id'] == $booking['mechanic_id']) ? 'selected' : ''; ?>
                                                            data-specialty="<?php echo htmlspecialchars($mechanic['specialty']); ?>">
                                                        <?php echo htmlspecialchars($mechanic['name']); ?> - <?php echo htmlspecialchars($mechanic['specialty']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            
                                            <input type="date" name="new_date" value="<?php echo $booking['booking_date']; ?>" required onchange="updateDateAvailability(this)">
                                            <input type="time" name="new_time" value="<?php echo $booking['booking_time']; ?>" required>
                                            
                                            <select name="new_status" required>
                                                <option value="pending" <?php echo ($booking['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                                <option value="confirmed" <?php echo ($booking['status'] === 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                                <option value="completed" <?php echo ($booking['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                                                <option value="cancelled" <?php echo ($booking['status'] === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            
                                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="last-updated">
                        Last updated: <span id="bookings-last-updated"><?php echo date('g:i:s A'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Mechanics Management -->
            <div class="section">
                <div class="section-header">
                    <h2>👨‍🔧 Mechanics Management</h2>
                </div>
                <div class="section-content">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Specialty</th>
                                <th>Contact</th>
                                <th>Current Workload</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="mechanics-table">
                            <?php foreach ($mechanics as $mechanic): ?>
                                <?php
                                    $workload_class = 'low';
                                    if ($mechanic['current_orders'] >= 3) $workload_class = 'high';
                                    elseif ($mechanic['current_orders'] >= 2) $workload_class = 'medium';
                                ?>
                                <tr data-mechanic-id="<?php echo $mechanic['mechanic_id']; ?>">
                                    <td><?php echo htmlspecialchars($mechanic['name']); ?></td>
                                    <td><?php echo htmlspecialchars($mechanic['specialty']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($mechanic['email']); ?><br>
                                        <small><?php echo htmlspecialchars($mechanic['phone']); ?></small>
                                    </td>
                                    <td>
                                        <a href="mechanic_details.php?id=<?php echo $mechanic['mechanic_id']; ?>" class="btn btn-info btn-sm">View Schedule</a>
                                    </td>
                                    <td><span class="status <?php echo $mechanic['status']; ?>"><?php echo ucfirst($mechanic['status']); ?></span></td>
                                    <td>
                                        <form method="POST" class="form-inline">
                                            <input type="hidden" name="action" value="toggle_mechanic">
                                            <input type="hidden" name="mechanic_id" value="<?php echo $mechanic['mechanic_id']; ?>">
                                            <input type="hidden" name="new_status" value="<?php echo ($mechanic['status'] === 'available') ? 'unavailable' : 'available'; ?>">
                                            <button type="submit" class="btn <?php echo ($mechanic['status'] === 'available') ? 'btn-warning' : 'btn-success'; ?> btn-sm">
                                                <?php echo ($mechanic['status'] === 'available') ? 'Disable' : 'Enable'; ?>
                                            </button>
                                        </form>
                                        <button onclick="openEditMechanicModal(<?php 
                                            echo htmlspecialchars(json_encode([
                                                'id' => $mechanic['mechanic_id'],
                                                'name' => $mechanic['name'],
                                                'email' => $mechanic['email'],
                                                'phone' => $mechanic['phone'],
                                                'specialty' => $mechanic['specialty']
                                            ]));
                                        ?>)" class="btn btn-primary btn-sm" style="margin-left: 5px;">Edit</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="last-updated">
                        Last updated: <span id="mechanics-last-updated"><?php echo date('g:i:s A'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Services Management -->
            <div class="section">
                <div class="section-header">
                    <h2>🔧 Services Management</h2>
                </div>
                <div class="section-content">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Service Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $service): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                                    <td><?php echo htmlspecialchars($service['description']); ?></td>
                                    <td>BDT<?php echo number_format($service['price'], 2); ?></td>
                                    <td><?php echo $service['duration_hours']; ?> hour(s)</td>
                                    <td><span class="status <?php echo $service['status']; ?>"><?php echo ucfirst($service['status']); ?></span></td>
                                    <td>
                                        <form method="POST" class="form-inline">
                                            <input type="hidden" name="action" value="toggle_service">
                                            <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                            <input type="hidden" name="new_status" value="<?php echo ($service['status'] === 'available') ? 'unavailable' : 'available'; ?>">
                                            <button type="submit" class="btn <?php echo ($service['status'] === 'available') ? 'btn-warning' : 'btn-success'; ?> btn-sm">
                                                <?php echo ($service['status'] === 'available') ? 'Disable' : 'Enable'; ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="last-updated">
                        Last updated: <span id="services-last-updated"><?php echo date('g:i:s A'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Users Section -->
            <div class="section">
                <h2>User Management</h2>
                <div class="section-content">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <?php 
                                    $is_disabled = (bool)$user['is_disabled'];
                                ?>
                                <tr class="<?php echo $is_disabled ? 'disabled-user' : ''; ?>">
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo ucfirst($user['role']); ?></td>
                                    <td>
                                        <span class="status <?php echo $is_disabled ? 'cancelled' : 'confirmed'; ?>">
                                            <?php echo $is_disabled ? 'Disabled' : 'Active'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="user_profile.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            View Profile
                                        </a>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="toggle_user_status">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" 
                                                    onclick="return confirm('Are you sure you want to <?php echo $is_disabled ? 'enable' : 'disable'; ?> this user?');"
                                                    class="btn <?php echo $is_disabled ? 'btn-success' : 'btn-danger'; ?> btn-sm">
                                                <?php echo $is_disabled ? 'Enable' : 'Disable'; ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- User Profile Modal -->
    <div id="userProfileModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUserProfileModal()">&times;</span>
            <h3>User Profile</h3>
            <div class="user-profile-details">
                <div class="profile-row">
                    <strong>Name:</strong>
                    <span id="modal-name"></span>
                </div>
                <div class="profile-row">
                    <strong>Email:</strong>
                    <span id="modal-email"></span>
                </div>
                <div class="profile-row">
                    <strong>Role:</strong>
                    <span id="modal-role"></span>
                </div>
                <div class="profile-row">
                    <strong>Status:</strong>
                    <span id="modal-status"></span>
                </div>
            </div>
            <div class="modal-buttons">
                <button onclick="closeUserProfileModal()" class="btn btn-primary">Close</button>
            </div>
        </div>
    </div>

    <!-- Edit Mechanic Modal -->
    <div id="editMechanicModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditMechanicModal()">&times;</span>
            <h3>Edit Mechanic Details</h3>
            <form method="POST" class="edit-mechanic-form">
                <input type="hidden" name="action" value="edit_mechanic">
                <input type="hidden" name="mechanic_id" id="edit_mechanic_id">
                
                <div class="form-group">
                    <label for="edit_name">Name:</label>
                    <input type="text" id="edit_name" name="name" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="edit_email">Email:</label>
                    <input type="email" id="edit_email" name="email" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="edit_phone">Phone:</label>
                    <input type="text" id="edit_phone" name="phone" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="edit_specialty">Specialty:</label>
                    <input type="text" id="edit_specialty" name="specialty" required class="form-control">
                </div>
                
                <div class="modal-buttons">
                    <button type="button" onclick="closeEditMechanicModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85));
            margin: 10% auto;
            padding: 2rem;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .close {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 28px;
            font-weight: bold;
            color: #666;
            cursor: pointer;
        }
        
        .close:hover {
            color: #333;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
        }
    </style>

    <script>
        function openEditMechanicModal(mechanic) {
            document.getElementById('edit_mechanic_id').value = mechanic.id;
            document.getElementById('edit_name').value = mechanic.name;
            document.getElementById('edit_email').value = mechanic.email;
            document.getElementById('edit_phone').value = mechanic.phone;
            document.getElementById('edit_specialty').value = mechanic.specialty;
            document.getElementById('editMechanicModal').style.display = 'block';
        }
        
        function closeEditMechanicModal() {
            document.getElementById('editMechanicModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editMechanicModal');
            if (event.target == modal) {
                closeEditMechanicModal();
            }
        }
        // Real-time updates simulation
        function updateTimestamps() {
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            
            document.getElementById('bookings-last-updated').textContent = timeString;
            document.getElementById('mechanics-last-updated').textContent = timeString;
            document.getElementById('services-last-updated').textContent = timeString;
        }

        // Update mechanic availability when date changes
        function updateDateAvailability(dateInput) {
            const selectedDate = dateInput.value;
            const row = dateInput.closest('tr');
            const mechanicSelect = row.querySelector('select[name="new_mechanic_id"]');
            
            if (selectedDate) {
                // Here you would typically make an AJAX call to get real-time availability
                // For this demo, we'll simulate the update
                console.log('Checking availability for date:', selectedDate);
                
                // Simulate real-time update
                setTimeout(() => {
                    const options = mechanicSelect.querySelectorAll('option');
                    options.forEach(option => {
                        if (option.value) {
                            // Simulate different availability
                            const random = Math.random();
                            if (random > 0.7) {
                                option.textContent = option.textContent.replace(/\(\d+\/4\)/, '(4/4 - FULL)');
                                option.disabled = true;
                            } else {
                                const available = Math.floor(random * 4) + 1;
                                option.textContent = option.textContent.replace(/\(\d+\/4.*?\)/, `(${available}/4)`);
                                option.disabled = false;
                            }
                        }
                    });
                    updateTimestamps();
                }, 500);
            }
        }

        // Update mechanic workload display
        function updateMechanicAvailability(mechanicSelect, date) {
            const mechanicId = mechanicSelect.value;
            console.log('Selected mechanic:', mechanicId, 'for date:', date);
            
            // Simulate checking mechanic's current workload for the specific date
            setTimeout(() => {
                updateTimestamps();
            }, 300);
        }

        // Auto-refresh functionality (every 30 seconds)
        setInterval(() => {
            updateTimestamps();
            console.log('Dashboard refreshed automatically');
        }, 30000);

        // Auto-hide notifications
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification-bar');
            notifications.forEach(notification => {
                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateY(-20px)';
                    setTimeout(() => {
                        notification.style.display = 'none';
                    }, 300);
                }, 5000);
            });

            // Initial timestamp update
            updateTimestamps();
        });

        // Add confirmation for status changes
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const action = this.querySelector('input[name="action"]');
                if (action && (action.value === 'toggle_mechanic' || action.value === 'toggle_service')) {
                    const confirmMessage = action.value === 'toggle_mechanic' 
                        ? 'Are you sure you want to change this mechanic\'s status?'
                        : 'Are you sure you want to change this service\'s status?';
                    
                    if (!confirm(confirmMessage)) {
                        e.preventDefault();
                    }
                }
            });
        });

        // User Profile Modal Functions
        function openUserProfileModal(user) {
            document.getElementById('modal-name').textContent = user.name;
            document.getElementById('modal-email').textContent = user.email;
            document.getElementById('modal-role').textContent = user.role;
            document.getElementById('modal-status').textContent = user.is_disabled ? 'Disabled' : 'Active';
            document.getElementById('userProfileModal').style.display = 'block';
        }

        function closeUserProfileModal() {
            document.getElementById('userProfileModal').style.display = 'none';
        }



        // Update window click handler to include user profile modal
        window.onclick = function(event) {
            const editModal = document.getElementById('editMechanicModal');
            const userModal = document.getElementById('userProfileModal');
            if (event.target == editModal) {
                closeEditMechanicModal();
            }
            if (event.target == userModal) {
                closeUserProfileModal();
            }
        }
    </script>

    <style>
        /* User List Section Styles */
        .user-list-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            margin-top: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .user-list-section h2 {
            color: #333;
            margin-bottom: 1.5rem;
        }

        .disabled-user {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-badge.active {
            background-color: #28a745;
            color: white;
        }

        .status-badge.disabled {
            background-color: #dc3545;
            color: white;
        }

        /* User Profile Modal Styles */
        .user-profile-details {
            margin-top: 1.5rem;
        }

        .profile-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }

        .profile-row:last-child {
            border-bottom: none;
        }

        .profile-row strong {
            color: #555;
            min-width: 100px;
        }
    </style>
</body>
</html>