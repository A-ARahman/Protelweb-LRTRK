document.addEventListener('DOMContentLoaded', function () {
    let profileIcon = document.querySelector('.profile-icon');
    let dropdownMenu = document.querySelector('.profile-dropdown');

    // Toggle the dropdown when the profile icon is clicked
    profileIcon.addEventListener('click', function (e) {
        e.stopPropagation(); // prevent the event from triggering on parent elements
        dropdownMenu.classList.toggle('hidden'); // toggle visibility
    });

    // Hide the dropdown when anywhere else on the page is clicked
    document.addEventListener('click', function () {
        dropdownMenu.classList.add('hidden');
    });
});


  