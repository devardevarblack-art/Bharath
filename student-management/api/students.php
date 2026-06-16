<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // ── GET: List all or search students ─────────────────────────────────────
    case 'GET':
        $conn  = getConnection();
        $query = '';

        if (!empty($_GET['search'])) {
            $search = '%' . $conn->real_escape_string($_GET['search']) . '%';
            $sql    = "SELECT * FROM students 
                       WHERE name      LIKE '$search'
                          OR email     LIKE '$search'
                          OR course    LIKE '$search'
                          OR department LIKE '$search'
                          OR phone     LIKE '$search'
                       ORDER BY created_at DESC";
        } elseif (!empty($_GET['id'])) {
            $id  = (int) $_GET['id'];
            $sql = "SELECT * FROM students WHERE id = $id LIMIT 1";
        } else {
            $sql = "SELECT * FROM students ORDER BY created_at DESC";
        }

        $result = $conn->query($sql);

        if (!$result) {
            sendResponse(['error' => 'Query failed: ' . $conn->error], 500);
        }

        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }

        if (!empty($_GET['id'])) {
            sendResponse($students[0] ?? null);
        }

        sendResponse([
            'success'  => true,
            'count'    => count($students),
            'students' => $students
        ]);
        break;

    // ── POST: Add new student ─────────────────────────────────────────────────
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            sendResponse(['error' => 'Invalid JSON data'], 400);
        }

        $required = ['name', 'email', 'phone', 'dob', 'gender', 'course', 'department', 'year'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                sendResponse(['error' => "Field '$field' is required"], 400);
            }
        }

        $conn       = getConnection();
        $name       = $conn->real_escape_string(trim($data['name']));
        $email      = $conn->real_escape_string(trim($data['email']));
        $phone      = $conn->real_escape_string(trim($data['phone']));
        $dob        = $conn->real_escape_string($data['dob']);
        $gender     = $conn->real_escape_string($data['gender']);
        $course     = $conn->real_escape_string(trim($data['course']));
        $department = $conn->real_escape_string(trim($data['department']));
        $year       = (int) $data['year'];
        $address    = $conn->real_escape_string(trim($data['address'] ?? ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse(['error' => 'Invalid email address'], 400);
        }

        // Check duplicate email
        $check = $conn->query("SELECT id FROM students WHERE email = '$email'");
        if ($check->num_rows > 0) {
            sendResponse(['error' => 'Email already exists'], 409);
        }

        $sql = "INSERT INTO students (name, email, phone, dob, gender, course, department, year, address)
                VALUES ('$name','$email','$phone','$dob','$gender','$course','$department',$year,'$address')";

        if ($conn->query($sql)) {
            sendResponse([
                'success' => true,
                'message' => 'Student added successfully',
                'id'      => $conn->insert_id
            ], 201);
        } else {
            sendResponse(['error' => 'Failed to add student: ' . $conn->error], 500);
        }
        break;

    // ── PUT: Update student ───────────────────────────────────────────────────
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || empty($data['id'])) {
            sendResponse(['error' => 'Student ID is required'], 400);
        }

        $required = ['name', 'email', 'phone', 'dob', 'gender', 'course', 'department', 'year'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                sendResponse(['error' => "Field '$field' is required"], 400);
            }
        }

        $conn       = getConnection();
        $id         = (int) $data['id'];
        $name       = $conn->real_escape_string(trim($data['name']));
        $email      = $conn->real_escape_string(trim($data['email']));
        $phone      = $conn->real_escape_string(trim($data['phone']));
        $dob        = $conn->real_escape_string($data['dob']);
        $gender     = $conn->real_escape_string($data['gender']);
        $course     = $conn->real_escape_string(trim($data['course']));
        $department = $conn->real_escape_string(trim($data['department']));
        $year       = (int) $data['year'];
        $address    = $conn->real_escape_string(trim($data['address'] ?? ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse(['error' => 'Invalid email address'], 400);
        }

        // Check duplicate email (exclude self)
        $check = $conn->query("SELECT id FROM students WHERE email = '$email' AND id != $id");
        if ($check->num_rows > 0) {
            sendResponse(['error' => 'Email already exists'], 409);
        }

        $sql = "UPDATE students SET
                    name='$name', email='$email', phone='$phone',
                    dob='$dob', gender='$gender', course='$course',
                    department='$department', year=$year, address='$address'
                WHERE id=$id";

        if ($conn->query($sql)) {
            if ($conn->affected_rows > 0) {
                sendResponse(['success' => true, 'message' => 'Student updated successfully']);
            } else {
                sendResponse(['error' => 'Student not found or no changes made'], 404);
            }
        } else {
            sendResponse(['error' => 'Failed to update: ' . $conn->error], 500);
        }
        break;

    // ── DELETE: Remove student ────────────────────────────────────────────────
    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        $id   = (int) ($data['id'] ?? $_GET['id'] ?? 0);

        if (!$id) {
            sendResponse(['error' => 'Student ID is required'], 400);
        }

        $conn = getConnection();
        $sql  = "DELETE FROM students WHERE id = $id";

        if ($conn->query($sql)) {
            if ($conn->affected_rows > 0) {
                sendResponse(['success' => true, 'message' => 'Student deleted successfully']);
            } else {
                sendResponse(['error' => 'Student not found'], 404);
            }
        } else {
            sendResponse(['error' => 'Failed to delete: ' . $conn->error], 500);
        }
        break;

    default:
        sendResponse(['error' => 'Method not allowed'], 405);
}
?>
