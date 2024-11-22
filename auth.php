<?php
session_start();
require_once 'config.php';

class AuthHandler {
    private $config;

    public function __construct() {
        $this->config = new Config();
    }

    public function handleAuth() {
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'register':
                $this->handleRegistration();
                break;
            case 'login':
                $this->handleLogin();
                break;
            case 'logout':
                $this->handleLogout();
                break;
            case 'check':
                $this->checkAuthStatus();
                break;
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Invalid action']);
        }
    }

    public function handleRegistration() {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $pdo = $this->config->getConnection();
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$data['username'], $data['email']]);
            
            if ($stmt->rowCount() > 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Username or email already exists']);
                return;
            }
            
            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $stmt->execute([$data['username'], $data['email'], $password_hash]);

            echo json_encode(['success' => true, 'message' => 'Registration successful']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Registration failed']);
        }
    }

    public function handleLogin() {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $pdo = $this->config->getConnection();
            $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
            $stmt->execute([$data['username']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($data['password'], $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                echo json_encode([
                    'success' => true,
                    'user' => ['id' => $user['id'], 'username' => $user['username']]
                ]);
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid credentials']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Login failed']);
        }
    }

    public function handleLogout() {
        session_destroy();
        echo json_encode(['success' => true]);
    }

    public function checkAuthStatus() {
        if (isset($_SESSION['user_id'])) {
            echo json_encode([
                'authenticated' => true,
                'user' => [
                    'id' => $_SESSION['user_id'],
                    'username' => $_SESSION['username']
                ]
            ]);
        } else {
            echo json_encode(['authenticated' => false]);
        }
    }
}

$authHandler = new AuthHandler();
$authHandler->handleAuth();
?>
