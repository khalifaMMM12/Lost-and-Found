<?php
require_once 'config.php';

function get_dashboard_items() {
    global $conn;
    $sql = 'SELECT i.item_id, i.type, i.description, i.location, i.date, i.status, i.contact_phone, i.contact_email, GROUP_CONCAT(img.image_path) as images,
            (SELECT COUNT(*) FROM claims c WHERE c.item_id = i.item_id AND c.status = "pending") as pending_claims
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
