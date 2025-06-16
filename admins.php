<?php
// ==============================================
// STEP 6: ADMIN PANEL (admins.php)
// ==============================================
session_start();
require 'db.php';

// Strict admin check with proper redirect
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    $_SESSION['admin_access_denied'] = true;
    header("Location: log-in.php");
    exit();
}

// Initialize messages
$action_message = '';
$action_status = '';

// Handle admin actions with CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $action_message = "Invalid request";
        $action_status = "error";
    } else {
        try {
            if (isset($_POST['delete_user'])) {
                $user_id = (int)$_POST['user_id'];
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $action_message = "User deleted successfully";
                $action_status = "success";
            }
            
            if (isset($_POST['make_admin'])) {
                $user_id = (int)$_POST['user_id'];
                $stmt = $pdo->prepare("UPDATE users SET is_admin = 1 WHERE id = ?");
                $stmt->execute([$user_id]);
                $action_message = "User promoted to admin";
                $action_status = "success";
            }
        } catch (PDOException $e) {
            $action_message = "Action failed: " . $e->getMessage();
            $action_status = "error";
        }
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Get all users with pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

try {
    // Get total users count
    $count_stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $total_users = $count_stmt->fetchColumn();
    $total_pages = ceil($total_users / $limit);

    // Get users for current page
    $stmt = $pdo->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            color: #333;
        }
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: 600;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .action-btn {
            padding: 6px 12px;
            margin: 0 3px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.3s;
        }
        .make-admin {
            background-color: #17a2b8;
            color: white;
        }
        .make-admin:hover {
            background-color: #138496;
        }
        .delete-user {
            background-color: #dc3545;
            color: white;
        }
        .delete-user:hover {
            background-color: #c82333;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .pagination a {
            color: #007bff;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 4px;
            border-radius: 4px;
        }
        .pagination a.active {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }
        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .back-link:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <h1>User Management</h1>
    
    <?php if (!empty($action_message)): ?>
        <div class="message <?php echo $action_status; ?>">
            <?php echo htmlspecialchars($action_message); ?>
        </div>
    <?php endif; ?>

    <?php if (count($users) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Status</th>
                <th>Registered</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <<td><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($user['email'] ?? 'N/A') ?></td>
                <td><?= $user['is_admin'] ? '✅ Admin' : '❌ Regular' ?></td>
                <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                <td>
                    <?php if (!$user['is_admin']): ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button type="submit" name="make_admin" class="action-btn make-admin">Make Admin</button>
                    </form>
                    <?php endif; ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button type="submit" name="delete_user" class="action-btn delete-user">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>" <?= $i === $page ? 'class="active"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <p>No users found in database.</p>
    <?php endif; ?>
    
    <a href="dash-board.php" class="back-link">← Back to Dashboard</a>
</body>
</html>
