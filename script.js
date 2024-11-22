function showModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

async function handleLogin(event) {
    event.preventDefault();
    
    const username = document.getElementById('loginUsername').value;
    const password = document.getElementById('loginPassword').value;

    try {
        const response = await fetch('auth.php?action=login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username, password })
        });

        const data = await response.json();
        
        if (data.success) {
            document.getElementById('authButtons').style.display = 'none';
            document.getElementById('mainContent').style.display = 'block';
            document.getElementById('welcomeMessage').textContent = `Welcome, ${data.user.username}!`;
            closeModal('loginModal');
            loadWorkouts();
        } else {
            alert(data.error || 'Login failed');
        }
    } catch (error) {
        alert('Error during login');
    }
}

async function handleRegister(event) {
    event.preventDefault();
    
    const username = document.getElementById('regUsername').value;
    const email = document.getElementById('regEmail').value;
    const password = document.getElementById('regPassword').value;

    try {
        const response = await fetch('auth.php?action=register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username, email, password })
        });

        const data = await response.json();
        
        if (data.success) {
            alert('Registration successful! Please login.');
            closeModal('registerModal');
            showModal('loginModal');
        } else {
            alert(data.error || 'Registration failed');
        }
    } catch (error) {
        alert('Error during registration');
    }
}

async function handleLogout() {
    try {
        await fetch('auth.php?action=logout');
        document.getElementById('authButtons').style.display = 'block';
        document.getElementById('mainContent').style.display = 'none';
        document.getElementById('workoutList').innerHTML = '';
    } catch (error) {
        alert('Error during logout');
    }
}

async function handleAddWorkout(event) {
    event.preventDefault();
    
    const workoutData = {
        date: document.getElementById('workoutDate').value,
        exercise: document.getElementById('exercise').value,
        sets: document.getElementById('sets').value,
        reps: document.getElementById('reps').value,
        weight: document.getElementById('weight').value
    };

    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(workoutData)
        });

        const data = await response.json();
        
        if (data.success) {
            closeModal('workoutModal');
            document.getElementById('workoutForm').reset();
            loadWorkouts();
        } else {
            alert(data.error || 'Failed to add workout');
        }
    } catch (error) {
        alert('Error adding workout');
    }
}

async function loadWorkouts() {
    try {
        const response = await fetch('api.php');
        const workouts = await response.json();
        
        const workoutList = document.getElementById('workoutList');
        workoutList.innerHTML = '<h2>Your Workouts</h2>';
        
        workouts.forEach(workout => {
            workoutList.innerHTML += `
                <div class="workout-item">
                    <strong>${workout.exercise_name}</strong> - ${workout.exercise_date}<br>
                    Sets: ${workout.sets} | Reps: ${workout.reps} | Weight: ${workout.weight}kg
                    <button class="btn btn-danger" onclick="deleteWorkout(${workout.id})">Delete</button>
                </div>
            `;
        });

        updateStats(workouts);
    } catch (error) {
        console.error('Error loading workouts:', error);
    }
}

function updateStats(workouts) {
    document.getElementById('totalWorkouts').textContent = workouts.length;
    
    const exerciseCounts = {};
    workouts.forEach(workout => {
        exerciseCounts[workout.exercise_name] = (exerciseCounts[workout.exercise_name] || 0) + 1;
    });
    const mostCommon = Object.entries(exerciseCounts)
        .sort((a, b) => b[1] - a[1])[0];
    document.getElementById('commonExercise').textContent = mostCommon ? mostCommon[0] : '-';
    
    const heaviestWeight = Math.max(...workouts.map(w => w.weight));
    document.getElementById('heaviestWeight').textContent = heaviestWeight + ' kg';
}

async function deleteWorkout(id) {
    if (!confirm('Are you sure you want to delete this workout?')) {
        return;
    }

    try {
        const response = await fetch(`api.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            loadWorkouts();
        } else {
            alert(data.error || 'Failed to delete workout');
        }
    } catch (error) {
        alert('Error deleting workout');
    }
}

async function checkAuth() {
    try {
        const response = await fetch('auth.php?action=check');
        const data = await response.json();
        
        if (data.authenticated) {
            document.getElementById('authButtons').style.display = 'none';
            document.getElementById('mainContent').style.display = 'block';
            document.getElementById('welcomeMessage').textContent = `Welcome, ${data.user.username}!`;
            loadWorkouts();
        }
    } catch (error) {
        console.error('Error checking auth status:', error);
    }
}

document.addEventListener('DOMContentLoaded', checkAuth);

async function loadWorkouts() {
    try {
        const response = await fetch('api.php');
        const workouts = await response.json();
        
        const workoutList = document.getElementById('workoutList');
        workoutList.innerHTML = '<h2>Your Workouts</h2>';
        
        workouts.forEach(workout => {
            workoutList.innerHTML += `
                <div class="workout-item">
                    <strong>${workout.exercise_name}</strong> - ${workout.exercise_date}<br>
                    Sets: ${workout.sets} | Reps: ${workout.reps} | Weight: ${workout.weight}kg
                    <div class="workout-actions">
                        <button class="btn btn-edit btn-small" onclick="showEditWorkout(${workout.id}, '${workout.exercise_date}', '${workout.exercise_name}', ${workout.sets}, ${workout.reps}, ${workout.weight})">Edit</button>
                        <button class="btn btn-danger btn-small" onclick="deleteWorkout(${workout.id})">Delete</button>
                    </div>
                </div>
            `;
        });

        updateStats(workouts);
    } catch (error) {
        console.error('Error loading workouts:', error);
    }
}

function showEditWorkout(id, date, exercise, sets, reps, weight) {
    document.getElementById('editWorkoutId').value = id;
    document.getElementById('editWorkoutDate').value = date;
    document.getElementById('editExercise').value = exercise;
    document.getElementById('editSets').value = sets;
    document.getElementById('editReps').value = reps;
    document.getElementById('editWeight').value = weight;
    showModal('editWorkoutModal');
}

async function handleEditWorkout(event) {
    event.preventDefault();
    
    const workoutData = {
        id: document.getElementById('editWorkoutId').value,
        date: document.getElementById('editWorkoutDate').value,
        exercise: document.getElementById('editExercise').value,
        sets: document.getElementById('editSets').value,
        reps: document.getElementById('editReps').value,
        weight: document.getElementById('editWeight').value
    };

    try {
        const response = await fetch('api.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(workoutData)
        });

        const data = await response.json();
        
        if (data.success) {
            closeModal('editWorkoutModal');
            document.getElementById('editWorkoutForm').reset();
            loadWorkouts();
        } else {
            alert(data.error || 'Failed to update workout');
        }
    } catch (error) {
        alert('Error updating workout');
    }
}
