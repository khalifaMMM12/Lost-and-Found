<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php?return=' . urlencode('my_claims.php'));
  exit;
}

$user_id = (int)$_SESSION['user_id'];

global $conn;
$sql = 'SELECT c.claim_id, c.status, c.claim_identifier_type, c.claim_identifier_value, c.created_at,
               i.item_id, i.type, i.description, i.location, i.date,
               (SELECT img.image_path FROM item_images img WHERE img.item_id = i.item_id ORDER BY img.image_id ASC LIMIT 1) AS image
        FROM claims c
        JOIN items i ON i.item_id = c.item_id
        WHERE c.user_id = ?
        ORDER BY c.created_at DESC';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$claims = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Claims - Lost and Found</title>
  <link rel="stylesheet" href="./src/output.css">
  <script src="https://kit.fontawesome.com/79a49acde1.js" crossorigin="anonymous"></script>
</head>
<body class="bg-neutral-100 min-h-screen font-sans">

  <nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <a href="dashboard.php" class="text-2xl font-bold text-black">Lost & Found</a>

      <div class="hidden md:flex space-x-4 items-center text-sm md:text-base">
        <a href="dashboard.php" class="text-gray-800 hover:text-black font-medium transition"><i class="fas fa-home mr-1"></i>Dashboard</a>
        <a href="search.php" class="text-gray-800 hover:text-black font-medium transition"><i class="fas fa-search mr-1"></i>Search</a>
        <a href="report_lost.php" class="text-gray-800 hover:text-black font-medium transition"><i class="fas fa-exclamation-circle mr-1"></i>Report Lost</a>
        <a href="report_found.php" class="text-gray-800 hover:text-black font-medium transition"><i class="fas fa-check-circle mr-1"></i>Report Found</a>
        <a href="my_claims.php" class="text-blue-700 font-semibold border-b-2 border-blue-400"><i class="fas fa-hand-paper mr-1"></i>My Claims</a>
        <a href="logout.php" class="ml-2 px-4 py-2 bg-black text-white rounded-full hover:bg-gray-900 transition"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a>
      </div>
    </div>
  </nav>

  <main class="max-w-7xl mx-auto px-4 py-10">
    <h2 class="text-2xl font-semibold text-gray-900 mb-6">My Claims</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php if (count($claims) > 0): ?>
        <?php foreach ($claims as $c): ?>
          <div class="bg-white rounded-xl shadow p-4">
            <?php if (!empty($c['image'])): ?>
              <img src="uploads/<?= htmlspecialchars($c['image']) ?>" alt="Item Image" class="w-full h-40 object-cover rounded mb-3">
            <?php else: ?>
              <div class="w-full h-40 bg-gray-100 rounded flex items-center justify-center text-gray-400">No Image</div>
            <?php endif; ?>
            <div>
              <div class="mb-1"><span class="inline-block px-2 py-1 rounded-full text-xs font-semibold <?= $c['type'] === 'lost' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>"><?= htmlspecialchars(ucfirst($c['type'])) ?></span></div>
              <div class="font-semibold text-gray-800 mb-1"><?= htmlspecialchars($c['description']) ?></div>
              <div class="text-sm text-gray-600 mb-1"><strong>Location:</strong> <?= htmlspecialchars($c['location']) ?></div>
              <div class="text-sm text-gray-600 mb-1"><strong>Date:</strong> <?= htmlspecialchars($c['date']) ?></div>
              <div class="text-sm mt-2">
                <span class="bg-gray-200 text-gray-800 text-xs font-medium px-2 py-1 rounded-full">Status: <?= htmlspecialchars($c['status']) ?></span>
              </div>
              <div class="text-xs text-gray-500 mt-2">
                Identifier: <?= htmlspecialchars($c['claim_identifier_type'] ?: '-') ?> <?= $c['claim_identifier_value'] ? '(' . htmlspecialchars($c['claim_identifier_value']) . ')' : '' ?>
              </div>
              <div class="mt-3 flex gap-2">
                <a href="claim.php?item_id=<?= (int)$c['item_id'] ?>" class="px-3 py-1 rounded border text-sm">View</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-span-full text-center text-gray-500">You have not made any claims yet.</div>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>


