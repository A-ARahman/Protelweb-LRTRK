<?php

// Include the database connection
include 'config.php';


$posStatus = [];

// Fetch POS status
try {
    $stmt = $pdo->query("SELECT pos_name, status FROM pos");
    $posStatus = $stmt->fetchAll();
    // Count active POS
    $active_pos_count = $pdo->query("SELECT COUNT(*) FROM pos WHERE status = 'Active'")->fetchColumn();
} catch (Exception $e) {
    // Handle exception
    error_log($e->getMessage());
    // Consider providing feedback to the user
}

// Initialize counts
$totalClimbers = 0;
$activeClimbers = 0;
$successfulClimbers = 0;

// Total Climbers
try {
    $totalClimbers = $pdo->query("SELECT COUNT(*) FROM profile")->fetchColumn();
} catch (PDOException $e) {
    die("Error fetching total climbers: " . $e->getMessage());
}

// Active Climbers
try {
    $activeClimbers = $pdo->query("SELECT COUNT(*) FROM notification WHERE status != 'Success'")->fetchColumn();
} catch (PDOException $e) {
    die("Error fetching active climbers: " . $e->getMessage());
}

// Successful Climbers
try {
    $successfulClimbers = $pdo->query("SELECT COUNT(*) FROM notification WHERE status = 'Success'")->fetchColumn();
} catch (PDOException $e) {
    die("Error fetching successful climbers: " . $e->getMessage());
}

date_default_timezone_set('Asia/Jakarta');
$dateSelected = date('Y-m-d');

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LORETRACK Dashboard</title>
    <!-- Tailwind CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-blue-900 p-3">

    <div class="grid grid-cols-4 gap-6">

        <!-- Sidebar -->
        <div class="bg-blue-600 text-white p-4 rounded-lg h-screen">
            <h1 class="text-2xl font-bold mb-4">LORETRACK</h1>
            <nav>
                <a href="dashboard.php" class="block py-2 px-2 bg-blue-900 hover:bg-blue-700 rounded-lg">Dashboard</a>
                <a href="regis.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">Registration</a>
                <a href="history.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">History</a>
                <a href="live-tracking.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">Live Tracking</a>
                <a href="health-status.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">Health Status</a>
                <a href="notification.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">Status</a>
                <a href="profile.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">Climbers Profile</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="col-span-3 space-y-6">
            <div class="flex justify-between items-center">
            <form action="" method="post">
            <input type="date" name="selectedDate" value="<?= $dateSelected ?>" class="p-2 border rounded-md" onchange="this.form.submit()">
            </form>
                <div class="flex items-center">
                    <input type="search" placeholder="Search" class="p-2 border rounded-md">
                    <!-- Notification, Profile, and Settings Icons -->
                    <a href="notification.php" class="ml-3 bg-gray-300 rounded-full h-8 w-8 flex items-center justify-center text-gray-600">N</a>
                    <!-- ... -->
                    <div class="ml-3 relative">
                        <div class="bg-gray-300 rounded-full h-8 w-8 flex items-center justify-center text-gray-600 cursor-pointer profile-icon">P</div>
                        <!-- Profile Dropdown -->
                        <div class="absolute right-0 w-48 mt-2 py-2 bg-white border rounded shadow-xl profile-dropdown hidden">
                            <a href="user-setting.php" class="block px-4 py-2 text-gray-800 hover:bg-indigo-500 hover:text-white">Account Settings</a>
                            <a href="login.php" class="block px-4 py-2 text-gray-800 hover:bg-indigo-500 hover:text-white">Logout</a>
                        </div>
                    </div>
                        <!-- ... -->

                    <a href="#settings" class="ml-3 bg-gray-300 rounded-full h-8 w-8 flex items-center justify-center text-gray-600">S</a>
                </div>
            </div>

            <!-- Statistics Boxes -->
            <div class="grid grid-cols-3 gap-6">
                <!-- Total Climbers -->
                <div class="bg-white p-6 rounded-lg flex flex-col items-center justify-center">
                    <h2 class="text-2xl font-semibold">Total Climbers</h2>
                    <span class="text-4xl mt-2"><?= $totalClimbers ?></span>
                </div>
                <!-- Active Climbers -->
                <div class="bg-white p-6 rounded-lg flex flex-col items-center justify-center">
                    <h2 class="text-2xl font-semibold">Active Climbers</h2>
                    <span class="text-4xl mt-2"><?= $activeClimbers ?></span>
                </div>
                <!-- Successful Climbers -->
                <div class="bg-white p-6 rounded-lg flex flex-col items-center justify-center">
                    <h2 class="text-2xl font-semibold">Successful Climbers</h2>
                    <span class="text-4xl mt-2"><?= $successfulClimbers ?></span>
                </div>
            </div>

            <!-- Pos Status -->
            <div class="bg-white p-6 rounded-lg">
                <h2 class="text-xl font-semibold mb-4">Pos Status</h2>
                <div class="grid grid-cols-2 gap-6">
                    <?php foreach ($posStatus as $pos): ?>
                        <div class="bg-<?= ($pos['status'] == 'Active') ? 'blue' : (($pos['status'] == 'Error') ? 'red' : 'gray') ?>-200 p-4 rounded-lg">
                            <?= htmlspecialchars($pos['pos_name']) ?><br>
                            <span class="font-semibold text-<?= ($pos['status'] == 'Active') ? 'blue' : (($pos['status'] == 'Error') ? 'red' : 'gray') ?>-700">
                                <?= htmlspecialchars($pos['status']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: Include scripts for the dropdown to work -->
    <script>
        // You can add interactivity for the dropdown here if needed.
    </script>
    <script src="script.js"></script>

</body>

</html>
