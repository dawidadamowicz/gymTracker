<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

class WorkoutHandler {
    private $config;

    public function __construct() {
        $this->config = new Config();
    }

    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $user_id = $this->checkAuth();

        switch ($method) {
            case 'GET':
                $this->getWorkouts($user_id);
                break;
            case 'POST':
                $this->addWorkout($user_id);
                break;
            case 'PUT':
                $this->updateWorkout($user_id);
                break;
            case 'DELETE':
                $this->deleteWorkout($user_id);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
    }

    public function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Not authenticated']);
            exit;
        }
        return $_SESSION['user_id'];
    }

    public function getWorkouts($user_id) {
        try {
            $pdo = $this->config->getConnection();
            $stmt = $pdo->prepare("SELECT * FROM workouts WHERE user_id = ? ORDER BY exercise_date DESC");
            $stmt->execute([$user_id]);
            $workouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($workouts);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function addWorkout($user_id) {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $pdo = $this->config->getConnection();
            $stmt = $pdo->prepare("INSERT INTO workouts (exercise_date, exercise_name, sets, reps, weight, user_id) 
                                  VALUES (:date, :exercise, :sets, :reps, :weight, :user_id)");
            $stmt->execute([
                ':date' => $data['date'],
                ':exercise' => $data['exercise'],
                ':sets' => $data['sets'],
                ':reps' => $data['reps'],
                ':weight' => $data['weight'],
                ':user_id' => $user_id
            ]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateWorkout($user_id) {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID is required']);
            return;
        }

        try {
            $pdo = $this->config->getConnection();
            $checkStmt = $pdo->prepare("SELECT id FROM workouts WHERE id = ? AND user_id = ?");
            $checkStmt->execute([$id, $user_id]);

            if ($checkStmt->rowCount() === 0) {
                http_response_code(403);
                echo json_encode(['error' => 'Not authorized to edit this workout']);
                return;
            }

            $stmt = $pdo->prepare("UPDATE workouts 
                                  SET exercise_date = :date,
                                      exercise_name = :exercise,
                                      sets = :sets,
                                      reps = :reps,
                                      weight = :weight
                                  WHERE id = :id AND user_id = :user_id");
            $stmt->execute([
                ':date' => $data['date'],
                ':exercise' => $data['exercise'],
                ':sets' => $data['sets'],
                ':reps' => $data['reps'],
                ':weight' => $data['weight'],
                ':id' => $id,
                ':user_id' => $user_id
            ]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteWorkout($user_id) {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID is required']);
            return;
        }

        try {
            $pdo = $this->config->getConnection();
            $stmt = $pdo->prepare("DELETE FROM workouts WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $user_id]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(403);
                echo json_encode(['error' => 'Not authorized to delete this workout']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

$workoutHandler = new WorkoutHandler();
$workoutHandler->handleRequest();
?>
