const express = require('express');
const mysql = require('mysql2');
const bodyParser = require('body-parser');

const app = express();
app.use(bodyParser.json());

const cors = require('cors');
app.use(cors()); // Use this before your routes are set up

// Create a connection to the database
const db = mysql.createConnection({
  host: 'localhost', // or your database host
  user: 'root', // or your database username
  password: '', // or your database password
  database: 'loretrack' // your database name
});

// Connect to the database
db.connect((err) => {
  if (err) throw err;
  console.log('Connected to database!');
});

// Define routes here...

const PORT = 3000;

app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});
// Get all user positions
// Get all user positions
app.get('/user-positions', (req, res) => {
    const query = `
        SELECT up.*, p.name, p.address, p.phone_number
        FROM user_position up
        JOIN profile p ON up.profile_id = p.profile_id
    `;
    db.query(query, (err, results) => {
      if (err) {
        return res.status(500).send(err);
      }
      res.status(200).json(results);
    });
});

app.post('/', (req, res) => {
  const data = req.body;
  console.log(data);
  const user = data.User; // LORA_noSeries
  const lastNode = data.Last_Node; // Last POS Node

  // Get Profile_ID for User With LORA_noSeries
  const getLastProfileIdQuery = `
    SELECT profile_id FROM profile
    WHERE lora_noseries = ?
    ORDER BY date DESC, time DESC
    LIMIT 1
  `;

  // Get the last profile_id from the loretrack_profile table
  db.query(getLastProfileIdQuery, [user], (err, profileResults) => {
    if (err) {
      console.error('Error fetching profile ID:', err);
      return res.status(500).json({ error: 'Error fetching profile ID' });
    }

    if (profileResults.length === 0) {
      console.error('Profile not found for the provided lora_noseries:', user);
      return res.status(404).json({ error: 'Profile not found for the provided lora_noseries' });
    }

    const lastProfileId = profileResults[0].profile_id;
    const HRArray = data.Message.HR; // Heart Rate Data
    const SPO2Array = data.Message.SPO2; // SPO2 Data
    const TempArray = data.Message.Temp; // Temperature Data
    const Lat = data.Message.Lat; // Latitude Data
    const Lon = data.Message.Lon; // Longitude Data

    // Find the pos_id corresponding to the Last_Node
    const getPosIdQuery = `
      SELECT pos_id FROM pos_position
      WHERE pos_id = (
        SELECT pos_id FROM pos
        WHERE lora_noseries = ?
      )
    `;

    db.query(getPosIdQuery, [lastNode], (posError, posResults) => {
      if (posError) {
        console.error('Error fetching pos ID:', posError);
        return res.status(500).json({ error: 'Error fetching pos ID' });
      }

      if (posResults.length === 0) {
        console.error('POS not found for the provided Last_Node:', lastNode);
        return res.status(404).json({ error: 'POS not found for the provided Last_Node' });
      }

      const posId = posResults[0].pos_id;

      // Insert into history table
      const insertHistoryQuery = `
        INSERT INTO history (lora_noseries, profile_id, pos_id, date, time)
        VALUES (?, ?, ?, CURDATE(), CURTIME())
      `;

      db.query(insertHistoryQuery, [user, lastProfileId, posId], (historyError, historyResults) => {
        if (historyError) {
          console.error('Error updating history:', historyError);
          return res.status(500).json({ error: 'Error updating history' });
        }

        // If all the above operations were successful, send a success response
        res.status(200).json({ message: 'History updated successfully' });
      });
    });
  });
});




// Get all positions'
app.get('/positions', (req, res) => {
    const query = `
        SELECT pos.*, pp.latitude, pp.longitude
        FROM pos pos
        JOIN pos_position pp ON pos.pos_id = pp.pos_id
    `;
    db.query(query, (err, results) => {
      if (err) {
        return res.status(500).send(err);
      }
      res.status(200).json(results);
    });
});

app.get('/latest-positions', (req, res) => {
  const query = `
      SELECT up.*
      FROM user_position up
      INNER JOIN (
          SELECT lora_noseries, MAX(CONCAT(date, ' ', time)) AS max_datetime
          FROM user_position
          GROUP BY lora_noseries
      ) AS latest
      ON up.lora_noseries = latest.lora_noseries
      AND CONCAT(up.date, ' ', up.time) = latest.max_datetime;
  `;

  db.query(query, (err, results) => {
      if (err) {
          return res.status(500).send(err);
      }
      res.status(200).json(results);
  });
});

app.get('/position-history/:lora_noseries/:profile_id', (req, res) => {
  const { lora_noseries, profile_id } = req.params;
  
  const query = `
      SELECT * FROM user_position
      WHERE lora_noseries = ? AND profile_id = ?
      ORDER BY date DESC, time DESC
  `;
  
  db.query(query, [lora_noseries, profile_id], (err, results) => {
      if (err) {
          return res.status(500).send(err);
      }
      res.status(200).json(results);
  });
});


// Endpoint to get the latest history entries since the last known entry
app.get('/latest-history', (req, res) => {
  const query = `
    SELECT * FROM history
    ORDER BY date DESC, time DESC
  `;

  db.query(query, (err, results) => {
    if (err) {
      console.error('Error fetching latest history:', err);
      return res.status(500).json({ error: 'Error fetching latest history' });
    }

    // Log the results to the console to make sure they are correct
    console.log(results);
    
    res.json(results);
  });
});

