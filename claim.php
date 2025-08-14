<?php
require 'config.php';

$item_id = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;
if ($item_id <= 0) {
    header('Location: dashboard.php');
    exit;
}

// If user not logged in, redirect to login/register with return URL
if (!isset($_SESSION['user_id'])) {
    $return = urlencode('claim.php?item_id=' . $item_id);
    header('Location: login.php?return=' . $return);
    exit;
}

// If arriving authenticated, record a pending claim once per user/item
if (isset($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    // Insert claim if not already exists
    $check = $conn->prepare('SELECT claim_id FROM claims WHERE item_id = ? AND user_id = ?');
    $check->bind_param('ii', $item_id, $user_id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows === 0) {
        $check->close();
        $claim_name = $_SESSION['name'] ?? '';
        $claim_email = $_SESSION['email'] ?? '';
        $claim_identifier_type = $_SESSION['identifier_type'] ?? null;
        $claim_identifier_value = $_SESSION['identifier_value'] ?? null;
        $stmtIns = $conn->prepare('INSERT INTO claims (item_id, user_id, claim_name, claim_email, claim_identifier_type, claim_identifier_value) VALUES (?, ?, ?, ?, ?, ?)');
        $stmtIns->bind_param('iissss', $item_id, $user_id, $claim_name, $claim_email, $claim_identifier_type, $claim_identifier_value);
        $stmtIns->execute();
        $stmtIns->close();
    } else {
        $check->close();
    }
}

// Fetch item summary for display
global $conn;
$stmt = $conn->prepare('SELECT item_id, type, description, location, date, status FROM items WHERE item_id = ?');
$stmt->bind_param('i', $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Claim Item</title>
  <link rel="stylesheet" href="./src/output.css">
  <script src="https://kit.fontawesome.com/79a49acde1.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 min-h-screen">
  <nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <a href="dashboard.php" class="text-2xl font-bold text-black">Lost & Found</a>
      <div class="hidden md:flex space-x-4 items-center text-sm md:text-base">
        <a href="dashboard.php" class="text-gray-800 hover:text-black font-medium transition"><i class="fas fa-home mr-1"></i>Dashboard</a>
        <a href="search.php" class="text-gray-800 hover:text-black font-medium transition"><i class="fas fa-search mr-1"></i>Search</a>
        <a href="report_lost.php" class="text-gray-800 hover:text-black font-medium transition"><i class="fas fa-exclamation-circle mr-1"></i>Report Lost</a>
        <a href="report_found.php" class="text-gray-800 hover:text-black font-medium transition"><i class="fas fa-check-circle mr-1"></i>Report Found</a>
        <a href="logout.php" class="ml-2 px-4 py-2 bg-black text-white rounded-full hover:bg-gray-900 transition"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a>
      </div>
    </div>
  </nav>

  <main class="max-w-3xl mx-auto px-4 py-10">
    <div class="bg-white p-6 rounded-xl shadow">
      <h2 class="text-2xl font-semibold mb-4">Claim Item</h2>
      <div class="mb-4 p-4 bg-gray-50 rounded">
        <p class="mb-1"><strong>Type:</strong> <?= htmlspecialchars($item['type']) ?></p>
        <p class="mb-1"><strong>Description:</strong> <?= htmlspecialchars($item['description']) ?></p>
        <p class="mb-1"><strong>Location:</strong> <?= htmlspecialchars($item['location']) ?></p>
        <p class="mb-1"><strong>Date:</strong> <?= htmlspecialchars($item['date']) ?></p>
        <p class="mb-1"><strong>Status:</strong> <?= htmlspecialchars($item['status']) ?></p>
      </div>

      <p class="text-gray-700">Thank you for your interest. An admin will verify ownership details. Your unique identifier on file will be used during verification.</p>
      <div class="mt-6 flex gap-3">
        <a href="dashboard.php" class="px-4 py-2 rounded border border-gray-300 text-gray-800 hover:bg-gray-50">Back</a>
      </div>
    </div>
  </main>
</body>
</html>


