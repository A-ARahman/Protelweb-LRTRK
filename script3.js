function updateHistory() {
    fetch('history.php', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(historyData => {
        const tableBody = document.querySelector('table tbody');
        tableBody.innerHTML = '';
        historyData.forEach(record => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-4 py-2 border">${record.date}</td>
                <td class="px-4 py-2 border">${record.time}</td>
                <td class="px-4 py-2 border">${record.lora_noseries}</td>
                <td class="px-4 py-2 border">${record.profile_id}</td>
                <td class="px-4 py-2 border">${record.pos_id}</td> 
            `;
            tableBody.appendChild(row);
        });
    })
    .catch(error => {
        console.error('Failed to update history:', error);
    });
}

updateHistory();
setInterval(updateHistory, 5000);
