<?php
// Include database connection for AJAX requests
require_once __DIR__ . '/../config/config.php';



// Handle AJAX requests first to avoid HTML output interference
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'create_user':
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $full_name = trim($_POST['full_name']);
            $password = $_POST['password'];
            $role_id = !empty($_POST['role_id']) ? $_POST['role_id'] : null;
            $subsystem_id = !empty($_POST['subsystem_id']) ? $_POST['subsystem_id'] : null;
            $subsystem_role_id = !empty($_POST['subsystem_role_id']) ? $_POST['subsystem_role_id'] : null;
            
            // Debug logging
            error_log("Create user - role_id: $role_id, subsystem_id: $subsystem_id, subsystem_role_id: $subsystem_role_id");
            
            try {
                // Check if user exists
                $stmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
                $stmt->execute([$username, $email]);
                if ($stmt->fetch()) {
                    echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
                    exit;
                }
                
                // Create user with roles
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('INSERT INTO users (username, email, full_name, password_hash, role_id, subsystem_id, subsystem_role_id, status, is_email_verified, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, "active", 1, NOW())');
                $stmt->execute([$username, $email, $full_name, $password_hash, $role_id, $subsystem_id, $subsystem_role_id]);
                $user_id = $conn->lastInsertId();
                
                error_log("User created with ID: $user_id, role_id: $role_id, subsystem_id: $subsystem_id, subsystem_role_id: $subsystem_role_id");
                

                
                echo json_encode(['success' => true, 'message' => 'User created successfully']);
            } catch (Exception $e) {
                error_log("Create user error: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;
            
        case 'edit_user':
            $user_id = $_POST['user_id'];
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $full_name = trim($_POST['full_name']);
            $status = $_POST['status'];
            $role_id = !empty($_POST['role_id']) ? $_POST['role_id'] : null;
            $subsystem_id = !empty($_POST['subsystem_id']) ? $_POST['subsystem_id'] : null;
            $subsystem_role_id = !empty($_POST['subsystem_role_id']) ? $_POST['subsystem_role_id'] : null;
            
            try {
                // Check if username/email already exists for other users
                $stmt = $conn->prepare('SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?');
                $stmt->execute([$username, $email, $user_id]);
                if ($stmt->fetch()) {
                    echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
                    exit;
                }
                
                // Update user with roles
                $stmt = $conn->prepare('UPDATE users SET username = ?, email = ?, full_name = ?, status = ?, role_id = ?, subsystem_id = ?, subsystem_role_id = ? WHERE id = ?');
                $stmt->execute([$username, $email, $full_name, $status, $role_id, $subsystem_id, $subsystem_role_id, $user_id]);
                

                
                echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;
            
        case 'delete_user':
            $user_id = $_POST['user_id'];
            try {
                $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
                $stmt->execute([$user_id]);
                echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;
            
        case 'get_user_roles':
            $user_id = $_POST['user_id'];
            try {
                // Get user roles directly from users table
                $stmt = $conn->prepare('SELECT role_id, subsystem_id, subsystem_role_id FROM users WHERE id = ?');
                $stmt->execute([$user_id]);
                $user_roles = $stmt->fetch();
                
                echo json_encode([
                    'success' => true,
                    'global_role_id' => $user_roles['role_id'] ?? null,
                    'subsystem_id' => $user_roles['subsystem_id'] ?? null,
                    'subsystem_role_id' => $user_roles['subsystem_role_id'] ?? null
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
            
        case 'create_subsystem_role':
            $subsystem_id = $_POST['subsystem_id'];
            $role_name = trim($_POST['role_name']);
            $description = trim($_POST['description']);
            
            try {
                $stmt = $conn->prepare('INSERT INTO subsystem_roles (subsystem_id, role_name, description) VALUES (?, ?, ?)');
                $stmt->execute([$subsystem_id, $role_name, $description]);
                echo json_encode(['success' => true, 'message' => 'Subsystem role created successfully']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;
            
        case 'get_subsystem_roles':
            $subsystem_id = $_POST['subsystem_id'];
            try {
                // Debug: Check if subsystem exists
                $stmt = $conn->prepare('SELECT name FROM subsystems WHERE id = ?');
                $stmt->execute([$subsystem_id]);
                $subsystem = $stmt->fetch();
                
                if (!$subsystem) {
                    echo json_encode(['error' => 'Subsystem not found']);
                    exit;
                }
                
                // Get roles for this subsystem
                $stmt = $conn->prepare('SELECT id, role_name, description FROM subsystem_roles WHERE subsystem_id = ?');
                $stmt->execute([$subsystem_id]);
                $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode($roles);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
    }
}

// Include sidebar after AJAX handling (this also includes config but won't cause issues)
require_once __DIR__ . '/sidebar.php';

// Get filter parameters
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role_filter'] ?? '';
$status_filter = $_GET['status_filter'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Build query
$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(u.username LIKE ? OR u.email LIKE ? OR u.full_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($role_filter) {
    $where_conditions[] = "r.name = ?";
    $params[] = $role_filter;
}

if ($status_filter) {
    $where_conditions[] = "u.status = ?";
    $params[] = $status_filter;
}

$where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
$count_query = "SELECT COUNT(u.id) FROM users u 
                LEFT JOIN roles r ON u.role_id = r.id 
                $where_clause";
$stmt = $conn->prepare($count_query);
$stmt->execute($params);
$total_users = $stmt->fetchColumn();
$total_pages = ceil($total_users / $per_page);

// Get users with roles
$query = "SELECT u.*, r.name as role_name, s.name as subsystem_name, sr.role_name as subsystem_role_name
          FROM users u 
          LEFT JOIN roles r ON u.role_id = r.id
          LEFT JOIN subsystems s ON u.subsystem_id = s.id
          LEFT JOIN subsystem_roles sr ON u.subsystem_role_id = sr.id
          $where_clause
          ORDER BY u.created_at DESC 
          LIMIT $per_page OFFSET $offset";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get roles and subsystems for dropdowns
$roles = $conn->query('SELECT * FROM roles ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$subsystems = $conn->query('SELECT * FROM subsystems ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - LGU1</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --lgu-bg: #f2f7f5;
            --lgu-headline: #00473e;
            --lgu-paragraph: #475d5b;
            --lgu-highlight: #faae2b;
        }
        body { background-color: var(--lgu-bg); font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .sidebar-offset { margin-left: 280px; }
        @media (max-width: 991.98px) { .sidebar-offset { margin-left: 0; } }
        .hero-section {
            background: linear-gradient(135deg, var(--lgu-headline) 0%, var(--lgu-paragraph) 100%);
            color: white;
        }
        .card { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn-primary { background: var(--lgu-highlight); border-color: var(--lgu-highlight); color: var(--lgu-headline); }
        .btn-primary:hover { background: #e09900; border-color: #e09900; }
        .table th { background: var(--lgu-bg); color: var(--lgu-headline); font-weight: 600; }
        .badge { font-size: 0.75rem; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="sidebar-offset min-vh-100">
        <section class="hero-section py-4">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-auto d-flex align-items-center">
                        <button id="mobile-sidebar-toggle" class="btn btn-link d-lg-none text-white p-0 me-3">
                            <i class="bi bi-list fs-4"></i>
                        </button>
                        <div>
                            <h1 class="h3 fw-bold mb-1">👥 User Management</h1>
                            <p class="mb-0 opacity-90">Manage system users and roles</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <main class="container-fluid p-4">
            <!-- Controls -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Search Users</label>
                            <input type="text" class="form-control" id="searchInput" placeholder="Search by name, email, username..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Role</label>
                            <select class="form-select" id="roleFilter">
                                <option value="">All Roles</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['name'] ?>" <?= $role_filter === $role['name'] ? 'selected' : '' ?>><?= $role['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $status_filter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="banned" <?= $status_filter === 'banned' ? 'selected' : '' ?>>Banned</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                                    <i class="bi bi-person-plus"></i> Create User
                                </button>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                                    <i class="bi bi-plus-circle"></i> Create Role
                                </button>
                                <button class="btn btn-outline-secondary" onclick="applyFilters()">
                                    <i class="bi bi-funnel"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th>Subsystems</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($user['full_name']) ?></strong>
                                            <br><small class="text-muted">@<?= htmlspecialchars($user['username']) ?></small>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <?php if ($user['role_name']): ?>
                                            <span class="badge bg-primary me-1"><?= htmlspecialchars($user['role_name']) ?></span>
                                        <?php endif; ?>
                                        <?php if ($user['subsystem_role_name']): ?>
                                            <span class="badge bg-info me-1"><?= htmlspecialchars($user['subsystem_role_name']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!$user['role_name'] && !$user['subsystem_role_name']): ?>
                                            <span class="text-muted">No roles</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($user['subsystem_name']): ?>
                                            <span class="badge bg-secondary me-1"><?= htmlspecialchars($user['subsystem_name']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : ($user['status'] === 'inactive' ? 'warning' : 'danger') ?>">
                                            <?= ucfirst($user['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="editUser(<?= $user['id'] ?>)" data-bs-toggle="modal" data-bs-target="#editUserModal">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="confirmDelete(<?= $user['id'] ?>, '<?= htmlspecialchars($user['full_name']) ?>')" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&role_filter=<?= urlencode($role_filter) ?>&status_filter=<?= urlencode($status_filter) ?>">Previous</a>
                            </li>
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&role_filter=<?= urlencode($role_filter) ?>&status_filter=<?= urlencode($status_filter) ?>"><?= $i ?></a>
                            </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&role_filter=<?= urlencode($role_filter) ?>&status_filter=<?= urlencode($status_filter) ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Create User Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Create New User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="createUserForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-person"></i> Username</label>
                            <input type="text" class="form-control" name="username" required placeholder="Enter username">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-envelope"></i> Email</label>
                            <input type="email" class="form-control" name="email" required placeholder="Enter email address">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-person-vcard"></i> Full Name</label>
                            <input type="text" class="form-control" name="full_name" required placeholder="Enter full name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-lock"></i> Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" id="createPassword" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('createPassword', 'createPasswordIcon')">
                                    <i class="bi bi-eye" id="createPasswordIcon"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-shield-check"></i> Global Role (Optional)</label>
                            <select class="form-select" name="role_id">
                                <option value="">Select Global Role</option>
                                <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-building"></i> Subsystem (Optional)</label>
                            <select class="form-select" name="subsystem_id" onchange="loadSubsystemRoles(this.value, 'create')">
                                <option value="">Select Subsystem</option>
                                <?php foreach ($subsystems as $subsystem): ?>
                                <option value="<?= $subsystem['id'] ?>"><?= htmlspecialchars($subsystem['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-person-badge"></i> Subsystem Role</label>
                            <select class="form-select" name="subsystem_role_id" disabled>
                                <option value="">Select Subsystem Role</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editUserForm">
                    <input type="hidden" name="user_id" id="editUserId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="editUsername" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="editEmail" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="full_name" id="editFullName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="editStatus">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="banned">Banned</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-shield-check"></i> Global Role (Optional)</label>
                            <select class="form-select" name="role_id" id="editRole">
                                <option value="">Select Global Role</option>
                                <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-building"></i> Subsystem</label>
                            <select class="form-select" name="subsystem_id" id="editSubsystem" onchange="loadSubsystemRoles(this.value, 'edit')">
                                <option value="">Select Subsystem</option>
                                <?php foreach ($subsystems as $subsystem): ?>
                                <option value="<?= $subsystem['id'] ?>"><?= htmlspecialchars($subsystem['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-person-badge"></i> Subsystem Role</label>
                            <select class="form-select" name="subsystem_role_id" id="editSubsystemRole" disabled>
                                <option value="">Select Subsystem Role</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-trash"></i> Delete User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="deleteUserName"></strong>?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="deleteUser()" data-bs-dismiss="modal">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Role Modal -->
    <div class="modal fade" id="createRoleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Create Subsystem Role</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="createRoleForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Subsystem</label>
                            <select class="form-select" name="subsystem_id" required>
                                <option value="">Select Subsystem</option>
                                <?php foreach ($subsystems as $subsystem): ?>
                                <option value="<?= $subsystem['id'] ?>"><?= htmlspecialchars($subsystem['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role Name</label>
                            <input type="text" class="form-control" name="role_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const role = document.getElementById('roleFilter').value;
            const status = document.getElementById('statusFilter').value;
            
            const params = new URLSearchParams();
            if (search) params.set('search', search);
            if (role) params.set('role_filter', role);
            if (status) params.set('status_filter', status);
            
            window.location.href = '?' + params.toString();
        }

        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') applyFilters();
        });

        document.getElementById('createUserForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'create_user');

            try {
                const response = await fetch('users.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire('Success!', 'User created successfully!', 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Failed to create user', 'error');
            }
        });

        async function loadSubsystemRoles(subsystemId, context) {
            let roleSelect;
            if (context === 'create') {
                roleSelect = document.querySelector('#createUserModal select[name="subsystem_role_id"]');
            } else if (context === 'edit') {
                roleSelect = document.getElementById('editSubsystemRole');
            } else {
                roleSelect = document.querySelector('select[name="subsystem_role_id"]');
            }
            
            if (!roleSelect) {
                console.error('Role select element not found');
                return;
            }
            
            roleSelect.innerHTML = '<option value="">Loading...</option>';
            roleSelect.disabled = !subsystemId;
            
            if (!subsystemId) {
                roleSelect.innerHTML = '<option value="">Select Subsystem Role</option>';
                return;
            }

            try {
                const formData = new FormData();
                formData.append('subsystem_id', subsystemId);
                
                const response = await fetch('../api/get_subsystem_roles.php', {
                    method: 'POST',
                    body: formData
                });
                const roles = await response.json();
                
                console.log('Subsystem ID:', subsystemId);
                console.log('Roles response:', roles);
                
                if (roles.error) {
                    roleSelect.innerHTML = '<option value="">Error: ' + roles.error + '</option>';
                    return;
                }
                
                roleSelect.innerHTML = '<option value="">Select Subsystem Role</option>';
                if (Array.isArray(roles) && roles.length > 0) {
                    roles.forEach(role => {
                        roleSelect.innerHTML += `<option value="${role.id}">${role.role_name}</option>`;
                    });
                    roleSelect.disabled = false;
                } else {
                    console.log('No roles found for subsystem:', subsystemId);
                    roleSelect.innerHTML = '<option value="">No roles found</option>';
                    roleSelect.disabled = true;
                }
            } catch (error) {
                console.error('Error loading roles:', error);
                roleSelect.innerHTML = '<option value="">Error loading roles</option>';
            }
        }

        async function editUser(userId) {
            const user = <?= json_encode($users) ?>.find(u => u.id == userId);
            console.log('Editing user:', user);
            
            if (!user) {
                console.error('User not found:', userId);
                return;
            }
            
            document.getElementById('editUserId').value = user.id;
            document.getElementById('editUsername').value = user.username;
            document.getElementById('editEmail').value = user.email;
            document.getElementById('editFullName').value = user.full_name;
            document.getElementById('editStatus').value = user.status;
            
            // Get user's current roles
            try {
                const formData = new FormData();
                formData.append('action', 'get_user_roles');
                formData.append('user_id', userId);
                
                const response = await fetch('users.php', {
                    method: 'POST',
                    body: formData
                });
                
                const responseText = await response.text();
                let userRoles;
                try {
                    userRoles = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Invalid JSON response:', responseText);
                    return;
                }
                
                if (userRoles.success) {
                    // Set global role (empty if user has no global role)
                    document.getElementById('editRole').value = userRoles.global_role_id || '';
                    
                    // Set subsystem (empty if user has no subsystem)
                    document.getElementById('editSubsystem').value = userRoles.subsystem_id || '';
                    
                    // Load and set subsystem role
                    if (userRoles.subsystem_id) {
                        await loadSubsystemRoles(userRoles.subsystem_id, 'edit');
                        // Small delay to ensure options are loaded
                        setTimeout(() => {
                            document.getElementById('editSubsystemRole').value = userRoles.subsystem_role_id || '';
                        }, 100);
                    } else {
                        // Reset subsystem role if no subsystem
                        document.getElementById('editSubsystemRole').innerHTML = '<option value="">Select Subsystem Role</option>';
                        document.getElementById('editSubsystemRole').disabled = true;
                    }
                } else {
                    // If get_user_roles failed, reset all role fields to empty
                    document.getElementById('editRole').value = '';
                    document.getElementById('editSubsystem').value = '';
                    document.getElementById('editSubsystemRole').innerHTML = '<option value="">Select Subsystem Role</option>';
                    document.getElementById('editSubsystemRole').disabled = true;
                }
            } catch (error) {
                console.error('Error loading user roles:', error);
                // Reset all role fields on error
                document.getElementById('editRole').value = '';
                document.getElementById('editSubsystem').value = '';
                document.getElementById('editSubsystemRole').innerHTML = '<option value="">Select Subsystem Role</option>';
                document.getElementById('editSubsystemRole').disabled = true;
            }
        }

        document.getElementById('editUserForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'edit_user');

            try {
                const response = await fetch('users.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire('Success!', 'User updated successfully!', 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Failed to update user', 'error');
            }
        });

        let deleteUserId = null;

        function confirmDelete(userId, userName) {
            deleteUserId = userId;
            document.getElementById('deleteUserName').textContent = userName;
        }

        async function deleteUser() {
            if (!deleteUserId) return;

            const formData = new FormData();
            formData.append('action', 'delete_user');
            formData.append('user_id', deleteUserId);

            try {
                const response = await fetch('users.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire('Success!', 'User deleted successfully!', 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Failed to delete user', 'error');
            }
        }

        document.getElementById('createRoleForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'create_subsystem_role');

            try {
                const response = await fetch('users.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire('Success!', 'Role created successfully!', 'success');
                    this.reset();
                    bootstrap.Modal.getInstance(document.getElementById('createRoleModal')).hide();
                } else {
                    Swal.fire('Error!', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Failed to create role', 'error');
            }
        });

        // Debug function to test role loading
        window.testRoleLoading = async function(subsystemId) {
            try {
                const formData = new FormData();
                formData.append('subsystem_id', subsystemId);
                
                const response = await fetch('../api/get_subsystem_roles.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                console.log('Role loading result:', result);
                return result;
            } catch (error) {
                console.error('Error testing role loading:', error);
            }
        };

        // Password toggle function
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const passwordIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'bi bi-eye';
            }
        }

        // Mobile sidebar toggle
        document.getElementById('mobile-sidebar-toggle')?.addEventListener('click', function() {
            const sidebar = document.getElementById('admin-sidebar');
            if (sidebar) {
                sidebar.classList.toggle('show-mobile');
            }
        });
    </script>
</body>
</html>