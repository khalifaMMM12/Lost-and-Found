<?php
require_once 'config.php';

function get_dashboard_items() {
    global $conn;
    $sql = 'SELECT i.item_id, i.type, i.description, i.location, i.date, i.status, GROUP_CONCAT(img.image_path) as images
        FROM items i
        LEFT JOIN item_images img ON i.item_id = img.item_id
        GROUP BY i.item_id
        ORDER BY i.created_at DESC';
    $result = $conn->query($sql);
    $items = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row['images'] = $row['images'] ? explode(',', $row['images']) : [];
            $items[] = $row;
        }
    }
    return $items;
}
