<?php
session_start();

// Include your existing database connection
require_once __DIR__ . '/../includes/db.php';

// Handle login form submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_first_name = trim($_POST['first_name'] ?? '');
    $input_last_name = trim($_POST['last_name'] ?? '');
    $input_employee_id = trim($_POST['employee_id'] ?? '');
    
    if (!empty($input_first_name) && !empty($input_last_name) && !empty($input_employee_id)) {
        // Use your existing database connection
        $conn = db();
        
        // Check if employee exists with matching first name, last name, and employee ID
        $stmt = $conn->prepare("SELECT e.*, ec.department, ec.position 
                               FROM employees e 
                               JOIN emp_classification ec ON e.classification_id = ec.classification_id 
                               WHERE e.first_name = ? AND e.last_name = ? AND e.employee_id = ?");
        $stmt->bind_param("ssi", $input_first_name, $input_last_name, $input_employee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user) {
            // Login successful - set all session variables
            $_SESSION['employee_id'] = $user['employee_id'];
            $_SESSION['username'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['contact_no'] = $user['contact_no'] ?? '';
            $_SESSION['department'] = $user['department'] ?? 'Administration';
            $_SESSION['position'] = $user['position'] ?? 'Employee';
            
            // Debug logging
            error_log("Login successful for: " . $_SESSION['name'] . " (ID: " . $_SESSION['employee_id'] . ")");
            
            // Redirect to attendance page
            header('Location: attendance.php');
            exit();
        } else {
            $error = 'Invalid credentials. Please check your name, surname, and employee ID.';
        }
        
        $stmt->close();
    } else {
        $error = 'Please enter all fields: first name, last name, and employee ID';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Attendance Tracker</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        /* Your existing CSS remains the same */
        :root {
            --header-bg: #06c3a7; 
            --button-text: #ffffff;
            --bg-color: #ebfffd;
            --panel-bg: #ffffff;
            --text-color: #064e44;
            --subtext-color: #4b6b66;
            --accent-color: #06c3a7;
            --button-text: #ffffff;
            --input-bg: rgba(255, 255, 255, 0.95);
            --border-color: rgba(6, 195, 167, 0.3);
        }

        [data-theme="dark"] {
            --header-bg: #243238; 
            --button-text: #ebfffd; 
            --bg-color: #1f292e;
            --panel-bg: #243238;
            --text-color: #ebfffd;
            --subtext-color: #c8d5d4;
            --accent-color: #06c3a7;
            --button-text: #ebfffd;
            --input-bg: #2c3b41;
            --border-color: rgba(235, 255, 253, 0.2);
        }

        body {
            margin: 0;
            padding: 0;
            font-family: "Inter", sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.4s ease, color 0.4s ease;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        button {
            transition: all 0.3s ease;
        }

        /* Login specific styles */
        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            width: 100%;
            padding: 1rem;
        }

        .login-card {
            background: var(--panel-bg);
            border-radius: 16px;
            padding: 3rem 2.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            border: 1px solid var(--border-color);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-header h1 {
            color: var(--accent-color);
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
        }

        .login-header p {
            color: var(--subtext-color);
            margin: 0;
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 500;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-color);
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(6, 195, 167, 0.1);
        }

        .form-control::placeholder {
            color: var(--subtext-color);
            opacity: 0.7;
        }

        .login-button {
            width: 100%;
            background-color: var(--accent-color);
            color: var(--button-text);
            border: none;
            border-radius: 8px;
            padding: 14px 20px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(6, 195, 167, 0.4);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .error-message {
            background-color: rgba(255, 92, 92, 0.1);
            color: #ff5c5c;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 92, 92, 0.3);
            text-align: center;
            font-size: 0.95rem;
        }

        .demo-accounts {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }

        .demo-accounts h3 {
            color: var(--accent-color);
            font-size: 1rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .demo-account {
            background-color: var(--input-bg);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 0.8rem;
            border: 1px solid var(--border-color);
        }

        .demo-account:last-child {
            margin-bottom: 0;
        }

        .demo-account strong {
            color: var(--accent-color);
        }

        .demo-account div {
            font-size: 0.9rem;
            color: var(--subtext-color);
            margin-top: 4px;
        }

        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--panel-bg);
            border: 1px solid var(--border-color);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--accent-color);
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            transform: rotate(30deg);
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
            }
            
            .login-header h1 {
                font-size: 2rem;
            }
        }

        .name-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 480px) {
            .name-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <button class="theme-toggle" onclick="toggleTheme()">
        <i class="fas fa-moon" id="theme-icon"></i>
    </button>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Welcome Back</h1>
                <p>Sign in with your employee details</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="name-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" 
                               id="first_name" 
                               name="first_name" 
                               class="form-control" 
                               placeholder="Enter your first name"
                               value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" 
                               id="last_name" 
                               name="last_name" 
                               class="form-control" 
                               placeholder="Enter your last name"
                               value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                               required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="employee_id">Employee ID</label>
                    <input type="text" 
                           id="employee_id" 
                           name="employee_id" 
                           class="form-control" 
                           placeholder="Enter your employee ID"
                           value="<?php echo htmlspecialchars($_POST['employee_id'] ?? ''); ?>"
                           required>
                </div>

                <button type="submit" class="login-button">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div class="demo-accounts">
                <h3>Demo Accounts</h3>
                <div class="demo-account">
                    <strong>Sarah Daniels</strong>
                    <div>Employee ID: 1</div>
                </div>
                <div class="demo-account">
                    <strong>Michael Smith</strong>
                    <div>Employee ID: 2</div>
                </div>
                <div class="demo-account">
                    <strong>Aisha Khan</strong>
                    <div>Employee ID: 3</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Theme toggle functionality
        function toggleTheme() {
            const currentTheme = document.body.getAttribute('data-theme');
            const themeIcon = document.getElementById('theme-icon');
            
            if (currentTheme === 'dark') {
                document.body.removeAttribute('data-theme');
                themeIcon.className = 'fas fa-moon';
                localStorage.setItem('theme', 'light');
            } else {
                document.body.setAttribute('data-theme', 'dark');
                themeIcon.className = 'fas fa-sun';
                localStorage.setItem('theme', 'dark');
            }
        }

        // Load saved theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            const themeIcon = document.getElementById('theme-icon');
            
            if (savedTheme === 'dark') {
                document.body.setAttribute('data-theme', 'dark');
                themeIcon.className = 'fas fa-sun';
            }
        });

        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
        });

        // Auto-capitalize first letter of names
        document.getElementById('first_name').addEventListener('blur', function() {
            this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();
        });

        document.getElementById('last_name').addEventListener('blur', function() {
            this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();
        });
    </script>
</body>
</html>