### Introduction
The code you provided is part of a web application built with PHP that allows users to register, log in, and manage their workout records. It is divided into four main parts: handling workouts (`api.php`), user authentication (`auth.php`), configuration (`config.php`), and the front-end (`script.js`). Below is an explanation of each file, broken down in a way that's easier to understand for someone with no coding experience.

---

### **1. `api.php` – Managing Workouts**

This file handles all requests related to workouts. Depending on the action the user wants to perform, it either fetches workout records, adds a new workout, updates an existing one, or deletes a workout.

#### Key Functions in `api.php`:

- **Session Handling and Authentication**:
  - `session_start()` begins a session to track logged-in users.
  - The `checkAuth()` function ensures the user is logged in before allowing them to manage workouts. If the user is not logged in, they are sent a "Not authenticated" message.

  ```php
  public function checkAuth() {
      if (!isset($_SESSION['user_id'])) {
          http_response_code(401);
          echo json_encode(['error' => 'Not authenticated']);
          exit;
      }
      return $_SESSION['user_id'];
  }
  ```

- **Handling Requests**:
  The `handleRequest()` method checks the type of HTTP request (GET, POST, PUT, DELETE) and calls the corresponding function (e.g., `getWorkouts`, `addWorkout`).

  ```php
  public function handleRequest() {
      $method = $_SERVER['REQUEST_METHOD'];
      $user_id = $this->checkAuth();

      switch ($method) {
          case 'GET': $this->getWorkouts($user_id); break;
          case 'POST': $this->addWorkout($user_id); break;
          case 'PUT': $this->updateWorkout($user_id); break;
          case 'DELETE': $this->deleteWorkout($user_id); break;
          default:
              http_response_code(405);
              echo json_encode(['error' => 'Method not allowed']);
      }
  }
  ```

- **Workouts Functions**:
  - **Get Workouts**: Retrieves all workouts for the logged-in user from the database.
  
    ```php
    public function getWorkouts($user_id) {
        $pdo = $this->config->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM workouts WHERE user_id = ? ORDER BY exercise_date DESC");
        $stmt->execute([$user_id]);
        $workouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($workouts);
    }
    ```

  - **Add a Workout**: Adds a new workout to the database, including exercise date, name, sets, reps, and weight.
  
    ```php
    public function addWorkout($user_id) {
        $data = json_decode(file_get_contents('php://input'), true);
        // Insert data into the database
    }
    ```

  - **Update a Workout**: Allows the user to update a specific workout's details (sets, reps, etc.), but only if they own the workout.
  
    ```php
    public function updateWorkout($user_id) {
        // Check if the user is authorized and update the workout
    }
    ```

  - **Delete a Workout**: Deletes a workout based on its ID, only if the workout belongs to the logged-in user.
  
    ```php
    public function deleteWorkout($user_id) {
        // Delete the workout from the database
    }
    ```

---

### **2. `auth.php` – User Authentication**

This file handles user registration, login, and logout functionality.

#### Key Functions in `auth.php`:

- **Registration**:
  Allows new users to create an account by providing a username, email, and password. If the username or email already exists, an error is returned.

  ```php
  public function handleRegistration() {
      // Check if username/email already exists, then insert user data into the database
  }
  ```

- **Login**:
  Users can log in by providing their username and password. If successful, the user is logged in and their session is created.

  ```php
  public function handleLogin() {
      // Verify user credentials and start a session
  }
  ```

- **Logout**:
  Logs the user out by destroying their session.

  ```php
  public function handleLogout() {
      session_destroy();
      echo json_encode(['success' => true]);
  }
  ```

- **Check Authentication Status**:
  This function checks if the user is logged in by verifying their session.

  ```php
  public function checkAuthStatus() {
      if (isset($_SESSION['user_id'])) {
          echo json_encode(['authenticated' => true]);
      } else {
          echo json_encode(['authenticated' => false]);
      }
  }
  ```

---

### **3. `config.php` – Database Configuration**

This file contains the configuration for connecting to the database.

#### Key Components in `config.php`:

- **Environment Variables**:
  The database credentials (host, name, user, password) are stored in an `.env` file using the `Dotenv` library.

  ```php
  $dotenv = Dotenv::createImmutable(__DIR__);
  $dotenv->load();
  ```

- **Database Connection**:
  The `Config` class connects to the database using PDO (PHP Data Objects), a way to interact with databases securely.

  ```php
  public function connect() {
      try {
          $this->pdo = new PDO(
              "mysql:host={$this->host};dbname={$this->dbname}",
              $this->username,
              $this->password
          );
          $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
          throw new Exception("Connection failed: " . $e->getMessage());
      }
  }
  ```

- **Returning the Database Connection**:
  The `getConnection()` function provides access to the database connection for other files (like `api.php`).

  ```php
  public function getConnection() {
      return $this->pdo;
  }
  ```

---

### **4. `script.js` – Front-End JavaScript**

This file contains the JavaScript code that interacts with the user interface. It sends HTTP requests to the server (using `fetch`) and displays the results on the web page.

#### Key Functions in `script.js`:

- **Login and Registration**:
  - `handleLogin` and `handleRegister` send the user's login or registration information to the server and handle the server's response (success or error).
  
    ```javascript
    async function handleLogin(event) {
        const response = await fetch('auth.php?action=login', {
            method: 'POST',
            body: JSON.stringify({ username, password })
        });
    }
    ```

- **Loading and Displaying Workouts**:
  The `loadWorkouts` function fetches the user's workouts from the server and displays them on the webpage.

  ```javascript
  async function loadWorkouts() {
      const response = await fetch('api.php');
      const workouts = await response.json();
      // Display workouts in HTML
  }
  ```

- **Adding, Editing, and Deleting Workouts**:
  Functions like `handleAddWorkout` and `deleteWorkout` allow users to modify their workout data, sending the changes to the server.

  ```javascript
  async function handleAddWorkout(event) {
      const workoutData = { /* workout data */ };
      const response = await fetch('api.php', { method: 'POST', body: JSON.stringify(workoutData) });
  }
  ```

---

### Conclusion

This web application handles user authentication and workout management through a combination of PHP (back-end) and JavaScript (front-end). The server handles requests like logging in, registering, adding/updating/deleting workouts, while JavaScript handles user interactions and displays the results on the webpage.
