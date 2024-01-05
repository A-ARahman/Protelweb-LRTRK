<?php
include 'config.php';

// Initialize arrays to hold the dropdown options
$profileIDs = [];
$loraSeriesOptions = [];
$notifications = [];

// Fetch profile IDs and lora_noseries options from the database
try {
    $profileStmt = $pdo->query("SELECT profile_id FROM profile");
    $profileIDs = $profileStmt->fetchAll(PDO::FETCH_ASSOC);

    $loraStmt = $pdo->query("SELECT lora_noseries FROM lora");
    $loraSeriesOptions = $loraStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch existing notification records to update
    $notificationStmt = $pdo->query("SELECT notification_id, lora_noseries, profile_id, status FROM notification");
    $notifications = $notificationStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log($e->getMessage());
    // Handle the error more gracefully in production code
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $notification_id = $_POST['notification_id']; // The unique identifier for the record to update
    $profile_id = $_POST['profile_id'];
    $lora_noseries = $_POST['lora_noseries'];
    $status = $_POST['status'];

    // SQL to update the existing status
    $sql = "UPDATE notification SET lora_noseries = :lora_noseries, profile_id = :profile_id, status = :status WHERE notification_id = :notification_id";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':notification_id' => $notification_id,
            ':lora_noseries' => $lora_noseries,
            ':profile_id' => $profile_id,
            ':status' => $status
        ]);
        echo "Status updated successfully.";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LORETRACK - Add Status</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-900 p-3">

    <div class="grid grid-cols-4 gap-6">

        <!-- Sidebar -->
       <div class="bg-blue-600 text-white p-4 rounded-lg h-screen">
            <h1 class="text-2xl font-bold mb-4">LORETRACK</h1>
            <nav>
                <a href="dashboard.php" class="block py-2 hover:bg-blue-700">Dashboard</a>
                <a href="regis.php" class="block py-2 hover:bg-blue-700">Registration</a>
                <a href="history.php" class="block py-2 hover:bg-blue-700">History</a>
                <a href="live-tracking.php" class="block py-2 hover:bg-blue-700">Live Tracking</a>
                <a href="notification.php" class="block py-2 hover:bg-blue-700">Notification</a>
                <a href="profile.php" class="block py-2 hover:bg-blue-700">Profile</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="col-span-3 space-y-6">
            <!-- Add Status Form -->
            <div class="w-full max-w-md mx-auto bg-white p-8 border rounded-md">
                <form action="addstatus.php" method="post">
                    <div class="mb-4">
                        <label for="profile_id" class="block text-gray-700 mb-2">Profile ID</label>
                        <select name="profile_id" id="profile_id" required class="w-full p-2 border rounded-md">
                            <?php foreach ($profileIDs as $profile): ?>
                                <option value="<?= htmlspecialchars($profile['profile_id']) ?>">
                                    <?= htmlspecialchars($profile['profile_id']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="lora_noseries" class="block text-gray-700 mb-2">LORA No Series</label>
                        <select name="lora_noseries" id="lora_noseries" required class="w-full p-2 border rounded-md">
                            <?php foreach ($loraSeriesOptions as $lora): ?>
                                <option value="<?= htmlspecialchars($lora['lora_noseries']) ?>">
                                    <?= htmlspecialchars($lora['lora_noseries']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="notification_id" class="block text-gray-700 mb-2">Notification ID</label>
                        <select name="notification_id" id="notification_id" required class="w-full p-2 border rounded-md">
                            <?php foreach ($notifications as $notification): ?>
                                <option value="<?= htmlspecialchars($notification['notification_id']) ?>">
                                    <?= "Profile: " . htmlspecialchars($notification['profile_id']) . " - LORA: " . htmlspecialchars($notification['lora_noseries']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="status" class="block text-gray-700 mb-2">Status</label>
                        <input type="text" name="status" id="status" required class="w-full p-2 border rounded-md">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Add Status</button>
                    </div>
                </form>
            </div>
        </div>

<script src="script.js"></script>
</body>
</html>
