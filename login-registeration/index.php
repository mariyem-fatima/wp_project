<?php
session_start();
require_once 'config.php';

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content']) && isset($_SESSION['user_id'])) {
    $content = trim($_POST['content']);
    if (!empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, content) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $content]);
        header('Location: index.php');
        exit();
    }
}

// Fetch all comments with usernames
$stmt = $pdo->query("SELECT comments.*, users.username 
                     FROM comments 
                     JOIN users ON comments.user_id = users.id 
                     ORDER BY comments.id DESC");
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - User System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <nav>
            <a href="index.php">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="flash-message">
                <?php 
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>

        <h1>Welcome to the Comment System</h1>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="comment-form">
                <h2>Post a Comment</h2>
                <form method="POST">
                    <div class="form-group">
                        <textarea name="content" rows="4" required></textarea>
                    </div>
                    <button type="submit">Post Comment</button>
                </form>
            </div>
        <?php else: ?>
            <p>Please <a href="login.php">login</a> to post comments.</p>
        <?php endif; ?>

        <h2>Comments</h2>
        <?php if ($comments): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <p><strong><?php echo htmlspecialchars($comment['username']); ?></strong> says:</p>
                    <p><?php echo htmlspecialchars($comment['content']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No comments yet. Be the first to comment!</p>
        <?php endif; ?>
    </div>
</body>
</html> 