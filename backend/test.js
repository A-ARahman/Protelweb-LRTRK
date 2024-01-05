// Use a dynamic import to load the fetch function
async function loadFetch() {
    const { default: fetch } = await import('node-fetch');
    return fetch;
  }
  
  // Example usage
  async function fetchData(url) {
    const fetch = await loadFetch();
    const response = await fetch(url);
    const data = await response.json();
    return data;
  }
  
  // Call fetchData function with the desired URL
  fetchData('https://api.example.com/data')
    .then(data => console.log(data))
    .catch(err => console.error(err));
  



const data = {
  Sender: "0xa1",
  Destination: "0xff",
  User: "0xa5",
  Last_Node: "0xa1",
  Message: {
    Lat: -7.287217,
    Lon: 112.8037,
    HR: [70, 75, 73, 78, 80],
    SPO2: [70],
    Temp: [70]
  }
};

fetch('http://localhost:3000/', { // Replace with your actual server URL
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify(data),
})
.then(response => response.json())
.then(data => {
  console.log('Success:', data);
})
.catch((error) => {
  console.error('Error:', error);
});
