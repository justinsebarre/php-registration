<?php
session_start();

// Initialize registered students array
if(!isset($_SESSION['students'])){
    $_SESSION['students'] = [];
}

$errors = [];
$showSummary = false;

/* ==========================
   BACKEND VALIDATION (PHP)
========================== */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {

    // Required fields
    $required = ["firstname","middlename","lastname","studentno","email","contact","address","course","age"];
    foreach($required as $field){
        if(empty($_POST[$field])){
            $errors[$field] = ucfirst($field)." is required.";
        }
    }

    // Name validation – letters only
    $nameFields = ["firstname","middlename","lastname"];
    foreach($nameFields as $name){
        if(!empty($_POST[$name]) && !preg_match("/^[A-Za-z ]+$/", $_POST[$name])){
            $errors[$name] = ucfirst($name)." must contain letters only.";
        }
    }

    // Student number – 24-00000 or 27-00000
    if(!empty($_POST["studentno"])) {
        if(!preg_match("/^(24|27)-\d{5}$/", $_POST["studentno"])){
            $errors["studentno"] = "Student number must start with 24- or 27- and have 5 digits.";
        }
    }

    // Age validation
    if(!empty($_POST["age"])) {
        if(!filter_var($_POST["age"], FILTER_VALIDATE_INT, ["options"=>["min"=>1,"max"=>120]])){
            $errors["age"] = "Age must be between 1 and 120.";
        }
    }

    // Contact – 11 digits only
    if(!empty($_POST["contact"]) && !preg_match("/^[0-9]{11}$/", $_POST["contact"])){
        $errors["contact"] = "Contact number must be exactly 11 digits.";
    }

    // Email validation
    if(!empty($_POST["email"]) && !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
        $errors["email"] = "Invalid email address.";
    }

    // If no errors → save student
    if(empty($errors)){
        $_SESSION['students'][] = [
            'firstname'=>$_POST['firstname'],
            'middlename'=>$_POST['middlename'],
            'lastname'=>$_POST['lastname'],
            'studentno'=>$_POST['studentno'],
            'age'=>$_POST['age'],
            'gender'=>$_POST['gender'] ?? '',
            'address'=>$_POST['address'],
            'contact'=>$_POST['contact'],
            'email'=>$_POST['email'],
            'course'=>$_POST['course'],
        ];
        $showSummary = true;
    }
}

// Back home
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['home'])) {
    $showSummary = false;
    $_POST = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Registration</title>

<!-- ==========================
     JAVASCRIPT VALIDATION
========================== -->
<script>
function validateName(input) {
    input.value = input.value.replace(/[^A-Za-z ]/g, "");  
}

function validateStudentNumber(input) {
    input.value = input.value.replace(/[^0-9-]/g, "");
}

function validateContact(input) {
    input.value = input.value.replace(/[^0-9]/g, "");
    if (input.value.length > 11) {
        input.value = input.value.slice(0, 11);
    }
}
</script>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f1f6f1;
    margin: 0;
    padding: 0;
}

.header {
    background: #0b3d0b;
    color: white;
    padding: 20px 0;
    text-align: center;
}

.header img {
    width: 120px;
    margin-bottom: 10px;
}

.header h1 {
    margin: 0;
    font-size: 26px;
    font-weight: bold;
}

.container {
    max-width: 900px;
    margin: 30px auto;
    background: white;
    padding: 30px 40px;
    border-radius: 10px;
    border-top: 8px solid #d4af37;
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
}

label {
    font-weight: bold;
    display: block;
    margin-top: 15px;
    color: #0b3d0b;
}

input, select {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #aaa;
    margin-top: 5px;
    font-size: 14px;
}

input:focus, select:focus {
    border-color: #0b3d0b;
    outline: none;
    box-shadow: 0 0 5px rgba(11, 61, 11, 0.4);
}

button {
    width: 100%;
    background: #0b3d0b;
    color: white;
    padding: 14px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    margin-top: 25px;
    cursor: pointer;
    font-weight: bold;
}

button:hover {
    background: #145214;
}

.error {
    color: #b30000;
    font-size: 13px;
}

.table-container {
    margin-top: 20px;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

thead {
    background: #0b3d0b;
    color: white;
}

th, td {
    padding: 12px;
    border: 1px solid #ccc;
}

tr:nth-child(even) { background: #f1f6f1; }
tr:nth-child(odd) { background: #ffffff; }

tr:hover { background: #e4ffe4; }

</style>
</head>

<body>

<div class="header">
    <img src="c.png" alt="C.png">
    <h1>Colegio de Montalban – Student Registration</h1>
</div>

<div class="container">

<?php if(!$showSummary): ?>

<form method="POST">

<label>First Name *</label>
<input type="text" name="firstname" oninput="validateName(this)"
       value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>">
<div class="error"><?= $errors['firstname'] ?? '' ?></div>

<label>Middle Name *</label>
<input type="text" name="middlename" oninput="validateName(this)"
       value="<?= htmlspecialchars($_POST['middlename'] ?? '') ?>">
<div class="error"><?= $errors['middlename'] ?? '' ?></div>

<label>Last Name *</label>
<input type="text" name="lastname" oninput="validateName(this)"
       value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>">
<div class="error"><?= $errors['lastname'] ?? '' ?></div>

<label>Student Number *</label>
<input type="text" name="studentno" placeholder="24-00000 or 27-00000"
       oninput="validateStudentNumber(this)"
       value="<?= htmlspecialchars($_POST['studentno'] ?? '') ?>">
<div class="error"><?= $errors['studentno'] ?? '' ?></div>

<label>Age *</label>
<input type="number" name="age" min="1" max="120"
       value="<?= htmlspecialchars($_POST['age'] ?? '') ?>">
<div class="error"><?= $errors['age'] ?? '' ?></div>

<label>Gender</label>
<select name="gender">
<option value="">--Select--</option>
<option value="Male" <?= (($_POST['gender'] ?? '')=='Male')?'selected':'' ?>>Male</option>
<option value="Female" <?= (($_POST['gender'] ?? '')=='Female')?'selected':'' ?>>Female</option>
<option value="Other" <?= (($_POST['gender'] ?? '')=='Other')?'selected':'' ?>>Other</option>
</select>

<label>Address *</label>
<input type="text" name="address"
       value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
<div class="error"><?= $errors['address'] ?? '' ?></div>

<label>Contact Number *</label>
<input type="text" name="contact" placeholder="11 digits"
       maxlength="11" oninput="validateContact(this)"
       value="<?= htmlspecialchars($_POST['contact'] ?? '') ?>">
<div class="error"><?= $errors['contact'] ?? '' ?></div>

<label>Email *</label>
<input type="text" name="email"
       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
<div class="error"><?= $errors['email'] ?? '' ?></div>

<label>Course *</label>
<select name="course">
<option value="">--Select--</option>

<option value="Bachelor of Science in Information Technology">Bachelor of Science in Information Technology</option>
<option value="Bachelor of Science in Computer Engineering">Bachelor of Science in Computer Engineering</option>
<option value="Bachelor of Secondary Education Major in Science">Bachelor of Secondary Education Major in Science</option>
<option value="Bachelor of Elementary Education (Generalist)">Bachelor of Elementary Education (Generalist)</option>
<option value="Bachelor of Technology and Livelihood Education Major in ICT">Bachelor of Technology and Livelihood Education Major in ICT</option>
<option value="Teacher Certificate Program (18 Units)">Teacher Certificate Program (18 Units)</option>
<option value="BSBA Major in HRM">BSBA Major in HRM</option>
<option value="Bachelor of Science in Entrepreneurship">Bachelor of Science in Entrepreneurship</option>

</select>
<div class="error"><?= $errors['course'] ?? '' ?></div>

<button type="submit" name="register">Register</button>

</form>

<?php else: ?>

<h2 style="text-align:center; color:#0b3d0b;">Registered Students</h2>

<div class="table-container">
<table>
<thead>
<tr>
<th>Student No</th>
<th>Name</th>
<th>Age</th>
<th>Gender</th>
<th>Address</th>
<th>Contact</th>
<th>Email</th>
<th>Course</th>
</tr>
</thead>
<tbody>
<?php foreach($_SESSION['students'] as $s): ?>
<tr>
<td><?= htmlspecialchars($s['studentno']) ?></td>
<td><?= htmlspecialchars($s['firstname'].' '.$s['middlename'].' '.$s['lastname']) ?></td>
<td><?= htmlspecialchars($s['age']) ?></td>
<td><?= htmlspecialchars($s['gender']) ?></td>
<td><?= htmlspecialchars($s['address']) ?></td>
<td><?= htmlspecialchars($s['contact']) ?></td>
<td><?= htmlspecialchars($s['email']) ?></td>
<td><?= htmlspecialchars($s['course']) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<form method="POST">
<button type="submit" name="home">Register Another Student</button>
</form>

<?php endif; ?>

</div>
</body>
</html>
