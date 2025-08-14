<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php?return=' . urlencode('notifications.php'));
  exit;
}

$user_id = (int)$_SESSION['user_id'];

// Ensure schema: add is_read column if missing
$col = $conn->query("SHOW COLUMNS FROM notifications LIKE 'is_read'");
if ($col && $col->num_rows === 0) {
  $conn->query("ALTER TABLE notifications ADD COLUMN is_read TINYINT(1) DEFAULT 0");
}

// Mark all as read if requested
if (isset($_POST['mark_all_read'])) {
  $stmt = $conn->prepare('UPDATE notifications SET is_read = 1 WHERE recipient_id = ?');
  $stmt->bind_param('i', $user_id);
  $stmt->execute();
  $stmt->close();
}

$stmt = $conn->prepare('SELECT n.notification_id, n.item_id, n.message, n.is_read, n.timestamp, i.type, i.description
                         FROM notifications n
                         LEFT JOIN items i ON i.item_id = n.item_id
                         WHERE n.recipient_id = ?
                         ORDER BY n.timestamp DESC');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Notifications - Lost and Found</title>
  <link rel="stylesheet" href="./src/output.css">
  <script src="https://kit.fontawesome.com/79a49acde1.js" crossorigin="anonymous"></script>
</head>
<body class="bg-neutral-100 min-h-screen font-sans">
  <nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <a href="dashboard.php" class="text-2xl font-bold text-black">Lost & Found</a>
      <div class="hidden md:flex space-x-4 items-center text-sm md:text-base">
        <a href="dashboard.php" class="text-gray-800 hover:text-black font-medium transition">Dashboard</a>
        <a href="search.php" class="text-gray-800 hover:text-black font-medium transition">Search</a>
        <a href="report_lost.php" class="text-gray-800 hover:text-black font-medium transition">Report Lost</a>
        <a href="report_found.php" class="text-gray-800 hover:text-black font-medium transition">Report Found</a>
        <a href="my_claims.php" class="text-gray-800 hover:text-black font-medium transition">My Claims</a>
        <a href="notifications.php" class="text-blue-700 font-semibold border-b-2 border-blue-400">Notifications</a>
        <a href="logout.php" class="ml-2 px-4 py-2 bg-black text-white rounded-full hover:bg-gray-900 transition">Logout</a>
      </div>
    </div>
  </nav>

  <main class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-semibold text-gray-900">Notifications</h2>
      <form method="post">
        <button type="submit" name="mark_all_read" class="px-3 py-1 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 text-sm">Mark all as read</button>
      </form>
    </div>

    <div class="bg-white rounded-xl shadow divide-y">
      <?php if (count($rows) > 0): ?>
        <?php foreach ($rows as $n): ?>
          <div class="p-4 flex items-start gap-3 <?= $n['is_read'] ? '' : 'bg-blue-50' ?>">
            <div class="mt-1">
              <i class="fas fa-bell text-gray-500"></i>
            </div>
            <div class="flex-1">
              <div class="text-sm text-gray-800"><?= htmlspecialchars($n['message']) ?></div>
              <div class="text-xs text-gray-500 mt-1">
                <?php if ($n['type']): ?>
                  Related to <?= htmlspecialchars($n['type']) ?>: "<?= htmlspecialchars($n['description']) ?>"
                <?php endif; ?>
                • <?= htmlspecialchars($n['timestamp']) ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="p-6 text-center text-gray-500">No notifications yet.</div>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>


