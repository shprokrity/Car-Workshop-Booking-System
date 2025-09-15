<?php
require_once 'config.php';
requireLogin();
requireAdmin();

if (!isset($_GET['id'])) {
    header('Location: admin_panel.php');
    exit;
}

$mechanic_id = $_GET['id'];

// Get mechanic details
$stmt = $pdo->prepare("
    SELECT * FROM mechanics 
    WHERE mechanic_id = ?
");
$stmt->execute([$mechanic_id]);
$mechanic = $stmt->fetch();

if (!$mechanic) {
    header('Location: admin_panel.php');
    exit;
}

// Get mechanic's bookings and availability
$stmt = $pdo->prepare("
    SELECT 
        b.booking_date,
        GROUP_CONCAT(DISTINCT b2.services) as services,
        COUNT(b2.id) as total_bookings
    FROM (
        SELECT DISTINCT booking_date 
        FROM bookings 
        WHERE mechanic_id = ? 
        AND status IN ('pending', 'confirmed')
        AND booking_date >= CURDATE()
    ) b
    LEFT JOIN bookings b2 ON b2.booking_date = b.booking_date 
        AND b2.mechanic_id = ? 
        AND b2.status IN ('pending', 'confirmed')
    GROUP BY b.booking_date
    ORDER BY b.booking_date ASC
");
$stmt->execute([$mechanic_id, $mechanic_id]);
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mechanic Schedule - <?php echo htmlspecialchars($mechanic['name']); ?></title>
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
        
        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .mechanic-header {
            background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .mechanic-header h1 {
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }
        
        .mechanic-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .info-item {
            background: rgba(255,255,255,0.1);
            padding: 1rem;
            border-radius: 10px;
        }
        
        .info-item strong {
            display: block;
            color: #feca57;
        }
        
        .table-container {
            background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            border-radius: 15px;
            padding: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            color: white;
            margin-top: 1rem;
        }
        
        .table th {
            text-align: left;
            padding: 1rem;
            border-bottom: 2px solid rgba(255,255,255,0.1);
            font-weight: 600;
            color: #feca57;
        }
        
        .table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .availability {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.9rem;
            display: inline-block;
        }
        
        .availability.available {
            background: rgba(46,213,115,0.2);
            color: #2ed573;
        }
        
        .availability.busy {
            background: rgba(255,71,87,0.2);
            color: #ff4757;
        }
        
        .availability.moderate {
            background: rgba(255,165,2,0.2);
            color: #ffa502;
        }
        
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .btn-secondary:hover {
            background: rgba(255,255,255,0.2);
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo">🔧 Mechanic Schedule</div>
            <div class="nav-buttons">
                <a href="admin_panel.php" class="btn btn-secondary">Back to Admin Panel</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="mechanic-header">
            <h1><?php echo htmlspecialchars($mechanic['name']); ?>'s Schedule</h1>
            <div class="mechanic-info">
                <div class="info-item">
                    <strong>Specialty</strong>
                    <?php echo htmlspecialchars($mechanic['specialty']); ?>
                </div>
                <div class="info-item">
                    <strong>Contact</strong>
                    <?php echo htmlspecialchars($mechanic['email']); ?><br>
                    <?php echo htmlspecialchars($mechanic['phone']); ?>
                </div>
                <div class="info-item">
                    <strong>Status</strong>
                    <?php echo ucfirst(htmlspecialchars($mechanic['status'])); ?>
                </div>
            </div>
        </div>

        <div class="table-container">
            <h2 style="color: white; margin-bottom: 1rem;">Booking Schedule</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Booked Date</th>
                        <th>Booked Services</th>
                        <th>Availability</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></td>
                            <td>
                                <?php 
                                if (!empty($booking['services'])) {
                                    $services = array_unique(explode(',', $booking['services']));
                                    echo htmlspecialchars(implode(', ', $services));
                                } else {
                                    echo '<em style="color: rgba(255,255,255,0.5);">No services booked yet</em>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $availability = 4 - $booking['total_bookings'];
                                if ($availability <= 0) {
                                    echo '<span class="availability busy">Fully Booked</span>';
                                } elseif ($availability <= 2) {
                                    echo '<span class="availability moderate">' . $availability . ' slots left</span>';
                                } else {
                                    echo '<span class="availability available">' . $availability . ' slots available</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="3" style="text-align: center;">No bookings scheduled</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
