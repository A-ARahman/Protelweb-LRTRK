<?php
include 'config.php';
date_default_timezone_set('Asia/Jakarta');

// Function to calculate the distance between two points (Haversine formula)
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    // Your distance calculation code here
}

// Function to get the nearest post for a given user position
function getNearestPost($userLat, $userLon, $pdo) {
    $nearestPost = null;
    $shortestDistance = PHP_FLOAT_MAX;

    // Your code to find the nearest post here

    return $nearestPost; // Should return post ID or some identifier
}

// Function to update the history with the nearest post
function updateHistoryWithNearestPost($pdo, $profileId, $userLat, $userLon) {
    $nearestPostId = getNearestPost($userLat, $userLon, $pdo);
    
    if ($nearestPostId) {
        $stmt = $pdo->prepare("INSERT INTO history (profile_id, pos_id, date, time) VALUES (?, ?, CURDATE(), CURTIME())");
        return $stmt->execute([$profileId, $nearestPostId]);
    }
    
    return false;
}

// Check if the request is an AJAX POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assume AJAX POST sends JSON with profile ID, latitude, and longitude
    $data = json_decode(file_get_contents('php://input'), true);
    $profileId = $data['profile_id'];
    $userLat = $data['latitude'];
    $userLon = $data['longitude'];

    // Update history with the nearest post
    if (updateHistoryWithNearestPost($pdo, $profileId, $userLat, $userLon)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unable to update history']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
