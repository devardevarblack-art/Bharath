const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');

const app = express();
const db = require('./db');

app.use(cors());
app.use(bodyParser.json());

const PORT = 3000;


app.post('/api/teacher/login', (req, res) => {
  const { username, password } = req.body;

  const sql = `
    SELECT 
      t.teacher_id,
      t.teacher_name,
      t.years_of_experience,
      t.specialised_subject,
      d.dept_name
    FROM teacher t
    JOIN department d ON t.dept_id = d.dept_id
    WHERE t.username = ? AND t.password = ?
  `;

  db.query(sql, [username, password], (err, result) => {
    if (err) return res.status(500).json(err);
    if (result.length === 0)
      return res.status(404).json({ message: 'Invalid username or password' });

    res.json(result[0]);
  });
});

app.post('/api/student/login', (req, res) => {
  const { username, password } = req.body;

  const sql = `
    SELECT 
      s.student_id,
      s.student_name,
      s.dob,
      d.dept_name,
      c.class_name
    FROM student s
    JOIN department d ON s.dept_id = d.dept_id
    JOIN class c ON s.class_id = c.class_id
    WHERE s.username = ? AND s.password = ?
  `;

  db.query(sql, [username, password], (err, result) => {
    if (err) return res.status(500).json(err);
    if (result.length === 0)
      return res.status(404).json({ message: 'Invalid username or password' });

    res.json(result[0]);
  });
});



app.post('/api/meta/departments-classes', (req, res) => {
  const deptQuery = `SELECT dept_id, dept_name FROM department`;
  const classQuery = `
    SELECT c.class_id, c.class_name, d.dept_id, d.dept_name
    FROM class c
    JOIN department d ON c.dept_id = d.dept_id
  `;

  db.query(deptQuery, (err, departments) => {
    if (err) return res.status(500).json(err);

    db.query(classQuery, (err, classes) => {
      if (err) return res.status(500).json(err);

      res.json({
        departments,
        classes
      });
    });
  });
});


app.post('/api/attendance/add', (req, res) => {
  const {
    teacher_id,
    class_id,
    dept_id,
    hour_id,
    attendance_date,
    attendance
  } = req.body;

  const checkSql = `
    SELECT COUNT(*) AS count
    FROM attendance
    WHERE class_id = ?
      AND dept_id = ?
      AND hour_id = ?
      AND attendance_date = ?
  `;

  db.query(
    checkSql,
    [class_id, dept_id, hour_id, attendance_date],
    (err, result) => {
      if (err) return res.status(500).json(err);

      if (result[0].count > 0) {
        return res.json({ message: 'Attendance already entered' });
      }

      const values = attendance.map(a => [
        a.student_id,
        teacher_id,
        class_id,
        dept_id,
        hour_id,
        attendance_date,
        a.status
      ]);

      const insertSql = `
        INSERT INTO attendance
        (student_id, teacher_id, class_id, dept_id, hour_id, attendance_date, status)
        VALUES ?
      `;

      db.query(insertSql, [values], err => {
        if (err) return res.status(500).json(err);
        res.json({ message: 'Attendance added successfully' });
      });
    }
  );
});


app.post('/api/student/report', (req, res) => {
  const { student_id, from_date, to_date } = req.body;

  const sql = `
    SELECT 
      a.attendance_date,
      h.hour_name,
      a.status,
      d.dept_name,
      c.class_name,
      t.teacher_name
    FROM attendance a
    JOIN hour h ON a.hour_id = h.hour_id
    JOIN department d ON a.dept_id = d.dept_id
    JOIN class c ON a.class_id = c.class_id
    JOIN teacher t ON a.teacher_id = t.teacher_id
    WHERE a.student_id = ?
      AND a.attendance_date BETWEEN ? AND ?
    ORDER BY a.attendance_date, a.hour_id
  `;

  db.query(sql, [student_id, from_date, to_date], (err, result) => {
    if (err) return res.status(500).json(err);
    res.json(result);
  });
});


app.post('/api/student/add', (req, res) => {
  const {
    roll_no,
    student_name,
    dob,
    username,
    password,
    dept_id,
    class_id
  } = req.body;

  if (!student_name || !username || !password || !dept_id || !class_id) {
    return res.status(400).json({ message: 'Missing required fields' });
  }

  // 🔍 Check if username already exists
  const checkSql = `SELECT student_id FROM student WHERE username = ?`;

  db.query(checkSql, [username], (err, result) => {
    if (err) return res.status(500).json(err);

    if (result.length > 0) {
      return res.status(409).json({
        message: 'Student with this username already exists'
      });
    }

    // ✅ Insert student
    const insertSql = `
      INSERT INTO student
      (roll_no,student_name, dob, username, password, dept_id, class_id)
      VALUES (?, ?, ?, ?, ?, ?)
    `;

    db.query(
      insertSql,
      [roll_no,student_name, dob, username, password, dept_id, class_id],
      (err, result) => {
        if (err) return res.status(500).json(err);

        res.json({
          message: 'Student added successfully',
          student_id: result.insertId
        });
      }
    );
  });
});



app.post('/api/student/delete', (req, res) => {
  const { student_id } = req.body;

  const deleteAttendanceSql = `
    DELETE FROM attendance WHERE student_id = ?
  `;

  const deleteStudentSql = `
    DELETE FROM student WHERE student_id = ?
  `;

  db.query(deleteAttendanceSql, [student_id], err => {
    if (err) return res.status(500).json(err);

    db.query(deleteStudentSql, [student_id], err => {
      if (err) return res.status(500).json(err);

      res.json({ message: 'Student deleted successfully' });
    });
  });
});

app.post('/api/student/update', (req, res) => {
  const {
    student_id,
    student_name,
    dob,
    username,
    password,
    dept_id,
    class_id
  } = req.body;

  if (!student_id) {
    return res.status(400).json({ message: 'student_id is required' });
  }

  // 🔍 Check username conflict (exclude current student)
  const checkSql = `
    SELECT student_id
    FROM student
    WHERE username = ?
      AND student_id != ?
  `;

  db.query(checkSql, [username, student_id], (err, result) => {
    if (err) return res.status(500).json(err);

    if (result.length > 0) {
      return res.status(409).json({
        message: 'Another student already uses this username'
      });
    }

    // ✅ Update student
    const updateSql = `
      UPDATE student
      SET
        student_name = ?,
        dob = ?,
        username = ?,
        password = ?,
        dept_id = ?,
        class_id = ?
      WHERE student_id = ?
    `;

    db.query(
      updateSql,
      [
        student_name,
        dob,
        username,
        password,
        dept_id,
        class_id,
        student_id
      ],
      (err, result) => {
        if (err) return res.status(500).json(err);

        if (result.affectedRows === 0) {
          return res.status(404).json({ message: 'Student not found' });
        }

        res.json({ message: 'Student updated successfully' });
      }
    );
  });
});

app.post('/api/teacher/add', (req, res) => {
  const {
    teacher_name,
    years_of_experience,
    username,
    password,
    specialised_subject,
    dept_id
  } = req.body;

  if (!teacher_name || !username || !password || !dept_id) {
    return res.status(400).json({ message: 'Missing required fields' });
  }

  const checkSql = `SELECT teacher_id FROM teacher WHERE username = ?`;

  db.query(checkSql, [username], (err, result) => {
    if (err) return res.status(500).json(err);

    if (result.length > 0) {
      return res.status(409).json({
        message: 'Teacher with this username already exists'
      });
    }

    // ✅ Insert teacher
    const insertSql = `
      INSERT INTO teacher
        (teacher_name, years_of_experience, username, password, specialised_subject, dept_id)
      VALUES (?, ?, ?, ?, ?, ?)
    `;

    db.query(
      insertSql,
      [
        teacher_name,
        years_of_experience || 0,
        username,
        password,
        specialised_subject || null,
        dept_id
      ],
      (err, result) => {
        if (err) return res.status(500).json(err);

        res.json({
          message: 'Teacher added successfully',
          teacher_id: result.insertId
        });
      }
    );
  });
});


app.post('/api/teacher/remove', (req, res) => {
  const { teacher_id } = req.body;

  if (!teacher_id) {
    return res.status(400).json({ message: 'teacher_id is required' });
  }

  const sql = `DELETE FROM teacher WHERE teacher_id = ?`;

  db.query(sql, [teacher_id], (err, result) => {
    if (err) return res.status(500).json(err);

    if (result.affectedRows === 0) {
      return res.status(404).json({ message: 'Teacher not found' });
    }

    res.json({ message: 'Teacher removed successfully' });
  });
});

app.post('/api/teacher/update', (req, res) => {
  const {
    teacher_id,
    teacher_name,
    years_of_experience,
    specialised_subject,
    username,
    password,
    dept_id
  } = req.body;

  if (!teacher_id) {
    return res.status(400).json({ message: 'teacher_id is required' });
  }

  // 🔍 Check username conflict (exclude current teacher)
  const checkSql = `
    SELECT teacher_id
    FROM teacher
    WHERE username = ?
      AND teacher_id != ?
  `;

  db.query(checkSql, [username, teacher_id], (err, result) => {
    if (err) return res.status(500).json(err);

    if (result.length > 0) {
      return res.status(409).json({
        message: 'Another teacher already uses this username'
      });
    }

    // ✅ Update teacher
    const updateSql = `
      UPDATE teacher
      SET
        teacher_name = ?,
        years_of_experience = ?,
        specialised_subject = ?,
        username = ?,
        password = ?,
        dept_id = ?
      WHERE teacher_id = ?
    `;

    db.query(
      updateSql,
      [
        teacher_name,
        years_of_experience,
        specialised_subject,
        username,
        password,
        dept_id,
        teacher_id
      ],
      (err, result) => {
        if (err) return res.status(500).json(err);

        if (result.affectedRows === 0) {
          return res.status(404).json({ message: 'Teacher not found' });
        }

        res.json({ message: 'Teacher updated successfully' });
      }
    );
  });
});


app.post('/api/attendance/students', (req, res) => {
  const { dept_id, class_id, hour_id, attendance_date } = req.body;

  const sql = `
    SELECT 
      s.student_id,
      s.student_name,
      IFNULL(a.status, 'A') AS status
    FROM student s
    LEFT JOIN attendance a
      ON s.student_id = a.student_id
      AND a.hour_id = ?
      AND a.attendance_date = ?
      AND a.class_id = ?
      AND a.dept_id = ?
    WHERE s.dept_id = ?
      AND s.class_id = ?
    ORDER BY s.student_name
  `;

  db.query(
    sql,
    [hour_id, attendance_date, class_id, dept_id, dept_id, class_id],
    (err, result) => {
      if (err) return res.status(500).json(err);
      res.json(result);
    }
  );
});

// GET all teachers with department name
app.get('/api/teacher/all', (req, res) => {
  const sql = `
    SELECT 
      t.teacher_id,
      t.teacher_name,
      t.username,
      t.password,
      t.specialised_subject,
      t.years_of_experience,
      d.dept_name
    FROM teacher t
    JOIN department d ON t.dept_id = d.dept_id
    ORDER BY t.teacher_id
  `;

  db.query(sql, (err, result) => {
    if (err) return res.status(500).json(err);
    res.json(result);
  });
});

//Fetch All students
app.get('/api/student/all', (req, res) => {
  const sql = `
    SELECT
      s.student_id,
      s.student_name,
      s.dob,
      s.username,
      s.password,
      s.dept_id,
      s.class_id,
      d.dept_name,
      c.class_name
    FROM student s
    JOIN department d ON s.dept_id = d.dept_id
    JOIN class c ON s.class_id = c.class_id
    ORDER BY s.student_name
  `;

  db.query(sql, (err, result) => {
    if (err) return res.status(500).json(err);
    res.json(result);
  });
});

// FETCH FULL REPORT
app.post('/api/report/attendance/department-wise', (req, res) => {
  const { from_date, to_date } = req.body;

  const sql = `
    SELECT
      d.dept_name,
      c.class_name,
      s.student_id,
      s.student_name,
      a.attendance_date,
      h.hour_name,
      a.status
    FROM attendance a
    JOIN student s ON a.student_id = s.student_id
    JOIN department d ON a.dept_id = d.dept_id
    JOIN class c ON a.class_id = c.class_id
    JOIN hour h ON a.hour_id = h.hour_id
    WHERE a.attendance_date BETWEEN ? AND ?
    ORDER BY d.dept_name, c.class_name, s.student_name, a.attendance_date
  `;

  db.query(sql, [from_date, to_date], (err, rows) => {
    if (err) return res.status(500).json(err);

    const result = [];

    rows.forEach(r => {
      let dept = result.find(d => d.dept_name === r.dept_name);
      if (!dept) {
        dept = { dept_name: r.dept_name, classes: [] };
        result.push(dept);
      }

      let cls = dept.classes.find(c => c.class_name === r.class_name);
      if (!cls) {
        cls = { class_name: r.class_name, students: [] };
        dept.classes.push(cls);
      }

      let student = cls.students.find(s => s.student_id === r.student_id);
      if (!student) {
        student = {
          student_id: r.student_id,
          student_name: r.student_name,
          attendance: []
        };
        cls.students.push(student);
      }

      student.attendance.push({
        attendance_date: r.attendance_date,
        hour_name: r.hour_name,
        status: r.status
      });
    });

    res.json(result);
  });
});

app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});
