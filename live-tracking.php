<?php

// Include the database connection
include 'config.php';
date_default_timezone_set('Asia/Jakarta');
$dateSelected = date('Y-m-d');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LORETRACK - Live Tracking</title>
    <!-- Tailwind CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
</head>

<body class="bg-blue-900 p-3">

    <div class="grid grid-cols-4 gap-6">

        <!-- Sidebar -->
        <div class="bg-blue-600 text-white p-4 rounded-lg h-screen">
            <h1 class="text-2xl font-bold mb-4">LORETRACK</h1>
            <nav>
                <a href="dashboard.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">Dashboard</a>
                <a href="regis.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">Registration</a>
                <a href="history.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">History</a>
                <a href="live-tracking.php" class="block py-2 px-2 bg-blue-900 hover:bg-blue-700 rounded-lg">Live Tracking</a>
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

            <!-- Live Tracking Search and Display -->
            <div class="bg-white p-6 rounded-lg">
            <style>
            /* This sets the map height to take the remaining space after a 6rem header */
            #map {
                height: calc(90vh - 6rem); /* Adjust the 6rem to match your header/footer total height */
            }
            </style>

            <!-- Map Container -->
            <div id="map" class="w-full border rounded-md"></div>

            </div>

        </div>
    </div>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="script.js"></script>
    <script src="script2.js"></script>
   
</body>

</html>
