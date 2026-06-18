<?php
/**
 * Admin Dashboard
 * 
 * Complete admin panel to manage product inquiries
 * Features:
 * - View all product inquiries from users
 * - See user info (name, email)
 * - Update inquiry status (pending/reviewed/rejected)
 * - Add admin notes/responses
 * - View statistics (total, pending, reviewed, rejected counts)
 */

session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Database configuration
require_once '../config/database.php';

// Initialize database connection
$db = new Database();
$conn = $db->connect();

if (!$conn) {
    die('Database connection failed');
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'update_status') {
        $inquiry_id = sanitize_input($_POST['inquiry_id']);
        $status = sanitize_input($_POST['status']);
        $admin_notes = sanitize_input($_POST['admin_notes'] ?? '');
        
        if (in_array($status, ['pending', 'reviewed', 'rejected'])) {
            $query = "UPDATE inquiries SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $conn->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param('ssi', $status, $admin_notes, $inquiry_id);
                $stmt->execute();
                $stmt->close();
                
                $update_message = "Inquiry status updated successfully.";
            }
        }
    }
}

// Fetch statistics
$stats = [
    'total' => 0,
    'pending' => 0,
    'reviewed' => 0,
    'rejected' => 0
];

$stats_query = "SELECT status, COUNT(*) as count FROM inquiries GROUP BY status";
$stats_result = $conn->query($stats_query);

if ($stats_result && $stats_result->num_rows > 0) {
    while ($row = $stats_result->fetch_assoc()) {
        $stats[$row['status']] = $row['count'];
    }
}

// Calculate total
$total_query = "SELECT COUNT(*) as total FROM inquiries";
$total_result = $conn->query($total_query);
if ($total_result) {
    $total_row = $total_result->fetch_assoc();
    $stats['total'] = $total_row['total'];
}

// Fetch all inquiries with user information
$inquiries = [];
$inquiries_query = "SELECT i.id, i.product_name, i.inquiry_message, i.status, 
                           i.admin_notes, i.created_at, i.updated_at,
                           u.name, u.email
                    FROM inquiries i
                    JOIN users u ON i.user_id = u.id
                    ORDER BY i.created_at DESC";

$inquiries_result = $conn->query($inquiries_query);

if ($inquiries_result && $inquiries_result->num_rows > 0) {
    while ($row = $inquiries_result->fetch_assoc()) {
        $inquiries[] = $row;
    }
}

// Sanitize input function
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Pastimes</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 28px;
        }

        .logout-btn {
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }

        .statistics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-card h3 {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            color: #2c3e50;
        }

        .stat-card.total {
            border-left: 4px solid #3498db;
        }

        .stat-card.pending {
            border-left: 4px solid #f39c12;
        }

        .stat-card.reviewed {
            border-left: 4px solid #27ae60;
        }

        .stat-card.rejected {
            border-left: 4px solid #e74c3c;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: none;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }

        .inquiries-section {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .inquiries-section h2 {
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 24px;
        }

        .inquiry-item {
            border: 1px solid #ecf0f1;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: #fafafa;
        }

        .inquiry-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .inquiry-product {
            font-weight: bold;
            color: #2c3e50;
            font-size: 16px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-reviewed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .inquiry-user {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .inquiry-user strong {
            color: #2c3e50;
        }

        .inquiry-message {
            background-color: white;
            padding: 10px;
            border-left: 3px solid #3498db;
            margin-bottom: 10px;
            font-size: 14px;
            line-height: 1.5;
        }

        .inquiry-timestamp {
            font-size: 12px;
            color: #95a5a6;
            margin-bottom: 10px;
        }

        .update-form {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2c3e50;
            font-size: 14px;
        }

        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-family: inherit;
            font-size: 14px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 12px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn-update {
            background-color: #27ae60;
            color: white;
        }

        .btn-update:hover {
            background-color: #229954;
        }

        .btn-cancel {
            background-color: #95a5a6;
            color: white;
        }

        .btn-cancel:hover {
            background-color: #7f8c8d;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }

        .empty-state p {
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .statistics {
                grid-template-columns: repeat(2, 1fr);
            }

            .inquiry-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .status-badge {
                margin-top: 10px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Admin Dashboard</h1>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </header>

        <?php if (isset($update_message)): ?>
            <div class="message success">
                <?php echo $update_message; ?>
            </div>
        <?php endif; ?>

        <section class="statistics">
            <div class="stat-card total">
                <h3>Total Inquiries</h3>
                <div class="number"><?php echo $stats['total']; ?></div>
            </div>
            <div class="stat-card pending">
                <h3>Pending</h3>
                <div class="number"><?php echo $stats['pending']; ?></div>
            </div>
            <div class="stat-card reviewed">
                <h3>Reviewed</h3>
                <div class="number"><?php echo $stats['reviewed']; ?></div>
            </div>
            <div class="stat-card rejected">
                <h3>Rejected</h3>
                <div class="number"><?php echo $stats['rejected']; ?></div>
            </div>
        </section>

        <section class="inquiries-section">
            <h2>Product Inquiries</h2>
            
            <?php if (empty($inquiries)): ?>
                <div class="empty-state">
                    <p>No inquiries yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($inquiries as $inquiry): ?>
                    <div class="inquiry-item">
                        <div class="inquiry-header">
                            <div class="inquiry-product">
                                <?php echo sanitize_input($inquiry['product_name']); ?>
                            </div>
                            <span class="status-badge status-<?php echo $inquiry['status']; ?>">
                                <?php echo ucfirst($inquiry['status']); ?>
                            </span>
                        </div>

                        <div class="inquiry-user">
                            <strong><?php echo sanitize_input($inquiry['name']); ?></strong> 
                            (<?php echo sanitize_input($inquiry['email']); ?>)
                        </div>

                        <div class="inquiry-timestamp">
                            Submitted: <?php echo date('F j, Y \a\t g:i A', strtotime($inquiry['created_at'])); ?>
                            <?php if ($inquiry['updated_at'] !== $inquiry['created_at']): ?>
                                | Updated: <?php echo date('F j, Y \a\t g:i A', strtotime($inquiry['updated_at'])); ?>
                            <?php endif; ?>
                        </div>

                        <div class="inquiry-message">
                            <strong>Inquiry:</strong><br>
                            <?php echo nl2br(sanitize_input($inquiry['inquiry_message'])); ?>
                        </div>

                        <?php if (!empty($inquiry['admin_notes'])): ?>
                            <div class="inquiry-message" style="border-left-color: #27ae60;">
                                <strong>Admin Notes:</strong><br>
                                <?php echo nl2br(sanitize_input($inquiry['admin_notes'])); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="update-form">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="inquiry_id" value="<?php echo $inquiry['id']; ?>">

                            <div class="form-group">
                                <label for="status_<?php echo $inquiry['id']; ?>">Update Status:</label>
                                <select name="status" id="status_<?php echo $inquiry['id']; ?>" required>
                                    <option value="pending" <?php echo $inquiry['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="reviewed" <?php echo $inquiry['status'] === 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                    <option value="rejected" <?php echo $inquiry['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="notes_<?php echo $inquiry['id']; ?>">Admin Notes/Response:</label>
                                <textarea name="admin_notes" id="notes_<?php echo $inquiry['id']; ?>" placeholder="Add your notes or response here..."><?php echo isset($inquiry['admin_notes']) ? sanitize_input($inquiry['admin_notes']) : ''; ?></textarea>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-update">Update Inquiry</button>
                                <button type="reset" class="btn btn-cancel">Cancel</button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
