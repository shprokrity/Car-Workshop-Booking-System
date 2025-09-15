<?php
require_once 'config.php';
requireLogin();

// Don't allow admin to access this page
if (isAdmin()) {
    header('Location: admin_panel.php');
    exit;
}

// Get all available mechanics
$stmt = $pdo->query("
    SELECT mechanic_id, name, specialty 
    FROM mechanics 
    WHERE status = 'available' 
    ORDER BY name ASC
");
$mechanics = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Mechanics - Multi Brand Workshop</title>
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
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .mechanics-section {
            background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            color: white;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .mechanics-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .mechanics-header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #feca57, #ff9ff3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .mechanics-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .mechanics-table th {
            background: rgba(255,255,255,0.1);
            color: #feca57;
            font-weight: 600;
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid rgba(255,255,255,0.1);
        }
        
        .mechanics-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .mechanics-table tr:hover {
            background: rgba(255,255,255,0.05);
        }
        
        .btn {
            display: inline-block;
            padding: 0.7rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .btn-secondary:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .specialty-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            background: linear-gradient(135deg, rgba(102,126,234,0.2), rgba(118,75,162,0.2));
            border: 1px solid rgba(255,255,255,0.1);
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .mechanics-table th:nth-child(1),
            .mechanics-table td:nth-child(1) {
                display: none;
            }
            
            .mechanics-table {
                font-size: 0.9rem;
            }
            
            .specialty-badge {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo">🔧 Our Mechanics</div>
            <div class="nav-buttons">
                <a href="index.php" class="btn btn-secondary">Back to Home</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="mechanics-section">
            <div class="mechanics-header">
                <h1>Our Expert Mechanics</h1>
                <p>Meet our team of skilled professionals ready to serve you</p>
            </div>

            <table class="mechanics-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Expertise</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mechanics as $mechanic): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($mechanic['mechanic_id']); ?></td>
                            <td><?php echo htmlspecialchars($mechanic['name']); ?></td>
                            <td><span class="specialty-badge"><?php echo htmlspecialchars($mechanic['specialty']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($mechanics)): ?>
                        <tr>
                            <td colspan="3" style="text-align: center;">No mechanics available at the moment.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
