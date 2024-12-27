const express = require('express');
const bodyParser = require('body-parser');
const mysql = require('mysql2');
const path = require('path');

const app = express();

app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());
app.use(express.static(path.join(__dirname, 'public')));

// Database credentials
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '', // Leave empty for WAMP's default setup
    database: 'conference'
});

// Connect to the database
db.connect(err => {
    if (err) {
        console.error('Connection failed: ' + err.stack);
        return;
    }
    console.log('Connected to database.');
});

app.post('/loginu', (req, res) => {
    const { email, password } = req.body;

    // Prepare SQL query
    const query = 'SELECT * FROM registration WHERE email = ?';
    db.query(query, [email], (err, results) => {
        if (err) {
            return res.status(500).send('Error executing query.');
        }

        if (results.length > 0) {
            // User found
            const user = results[0];
            if (password === user.userpwd) { // Compare plain text passwords
                if (user.role === 'admin' || password === 'admin') {
                    res.redirect('/admin/ademinu');
                } else {
                    res.redirect('/dashboard/user');
                }
            } else {
                res.send('Invalid email or password.');
            }
        } else {
            // User not found
            res.send('Invalid email or password.');
        }
    });
});

app.listen(3000, () => {
    console.log('Server is running on port 3000.');
});