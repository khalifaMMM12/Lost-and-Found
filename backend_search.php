<?php
require_once 'config.php';

function get_search_items($filters) {
    global $conn;
    $categories = ['Electronics', 'Books', 'Clothing', 'Accessories', 'Documents', 'Other'];
    $types = ['lost' => 'Lost', 'found' => 'Found'];

    $filter_category = isset($filters['category']) ? trim($filters['category']) : '';
    $filter_type = isset($filters['type']) ? trim($filters['type']) : '';
    $filter_date = isset($filters['date']) ? trim($filters['date']) : '';
    $filter_location = isset($filters['location']) ? trim($filters['location']) : '';

    $query = "SELECT i.item_id, i.type, i.category, i.description, i.location, i.date, i.status, i.contact_phone, i.contact_email,
        (SELECT img.image_path FROM item_images img WHERE img.item_id = i.item_id LIMIT 1) as image
        FROM items i WHERE i.status IN ('approved', 'pending')";
    $params = [];
    $typestr = '';
    if ($filter_category) {
        $query .= " AND (i.category = ? OR i.description LIKE ?)";
        $params[] = $filter_category;
        $params[] = "%$filter_category%";
        $typestr .= 'ss';
    }
    if ($filter_type) {
        $query .= " AND i.type = ?";
        $params[] = $filter_type;
        $typestr .= 's';
    }
    if ($filter_date) {
        $query .= " AND i.date = ?";
        $params[] = $filter_date;
        $typestr .= 's';
    }
    if ($filter_location) {
        $query .= " AND i.location LIKE ?";
        $params[] = "%$filter_location%";
        $typestr .= 's';
    }
    $query .= " ORDER BY i.date DESC LIMIT 30";

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($typestr, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $items = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $items;
}
