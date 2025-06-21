<?php
require 'config.php';

// Fetch filter options
$categories = ['Electronics', 'Books', 'Clothing', 'Accessories', 'Documents', 'Other'];
$types = ['lost' => 'Lost', 'found' => 'Found'];

// Get filter values
$filter_category = isset($_GET['category']) ? trim($_GET['category']) : '';
$filter_type = isset($_GET['type']) ? trim($_GET['type']) : '';
$filter_date = isset($_GET['date']) ? trim($_GET['date']) : '';
$filter_location = isset($_GET['location']) ? trim($_GET['location']) : '';

// Build query
$query = "SELECT item_id, type, description, location, date, status, image FROM items WHERE status = 'approved'";
$params = [];
$typestr = '';
if ($filter_category) {
    $query .= " AND description LIKE ?";
    $params[] = "%$filter_category%";
    $typestr .= 's';
}
if ($filter_type) {
    $query .= " AND type = ?";
    $params[] = $filter_type;
    $typestr .= 's';
}
if ($filter_date) {
    $query .= " AND date = ?";
    $params[] = $filter_date;
    $typestr .= 's';
}
if ($filter_location) {
    $query .= " AND location LIKE ?";
    $params[] = "%$filter_location%";
    $typestr .= 's';
}
$query .= " ORDER BY date DESC LIMIT 30";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($typestr, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Items - Lost and Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .search-form { max-width: 900px; margin: 40px auto 20px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .item-card { min-height: 350px; }
        .item-img { max-height: 180px; object-fit: cover; }
    </style>
</head>
<body>
    <div class="search-form">
        <h2 class="mb-4 text-center">Search Lost & Found Items</h2>
        <form method="get" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="">All</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php if ($filter_category == $cat) echo 'selected'; ?>><?php echo $cat; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">All</option>
                    <?php foreach ($types as $val => $label): ?>
                        <option value="<?php echo $val; ?>" <?php if ($filter_type == $val) echo 'selected'; ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($filter_date); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-control" placeholder="Enter location" value="<?php echo htmlspecialchars($filter_location); ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>
    </div>
    <div class="container">
        <div class="row">
            <?php if (count($items) > 0): ?>
                <?php foreach ($items as $item): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card item-card">
                            <?php if ($item['image']): ?>
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" class="card-img-top item-img" alt="Item image">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/400x180?text=No+Image" class="card-img-top item-img" alt="No image">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title text-capitalize"><?php echo htmlspecialchars($item['type']); ?> Item</h5>
                                <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                                <p class="mb-1"><strong>Location:</strong> <?php echo htmlspecialchars($item['location']); ?></p>
                                <p class="mb-1"><strong>Date:</strong> <?php echo htmlspecialchars($item['date']); ?></p>
                                <span class="badge bg-<?php echo $item['type'] == 'lost' ? 'danger' : 'success'; ?> text-uppercase"><?php echo htmlspecialchars($item['type']); ?></span>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($item['status']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">No items found matching your criteria.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 