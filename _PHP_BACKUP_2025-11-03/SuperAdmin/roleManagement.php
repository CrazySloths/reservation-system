<?php
require_once __DIR__ . '/../config/config.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'create_role':
            $name = trim($_POST['name']);
            try {
                $stmt = $conn->prepare('INSERT INTO roles (name) VALUES (?)');
                $stmt->execute([$name]);
                echo json_encode(['success' => true, 'message' => 'Role created successfully']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;
            
        case 'edit_role':
            $id = $_POST['id'];
            $name = trim($_POST['name']);
            try {
                $stmt = $conn->prepare('UPDATE roles SET name = ? WHERE id = ?');
                $stmt->execute([$name, $id]);
                echo json_encode(['success' => true, 'message' => 'Role updated successfully']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;
            
        case 'delete_role':
            $id = $_POST['id'];
            try {
                $stmt = $conn->prepare('DELETE FROM roles WHERE id = ?');
                $stmt->execute([$id]);
                echo json_encode(['success' => true, 'message' => 'Role deleted successfully']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
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
            
        case 'edit_subsystem_role':
            $id = $_POST['id'];
            $role_name = trim($_POST['role_name']);
            $description = trim($_POST['description']);
            try {
                $stmt = $conn->prepare('UPDATE subsystem_roles SET role_name = ?, description = ? WHERE id = ?');
                $stmt->execute([$role_name, $description, $id]);
                echo json_encode(['success' => true, 'message' => 'Subsystem role updated successfully']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;
            
        case 'delete_subsystem_role':
            $id = $_POST['id'];
            try {
                $stmt = $conn->prepare('DELETE FROM subsystem_roles WHERE id = ?');
                $stmt->execute([$id]);
                echo json_encode(['success' => true, 'message' => 'Subsystem role deleted successfully']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;
            
        case 'bulk_delete_roles':
            $ids = json_decode($_POST['ids'] ?? '[]', true);
            try {
                if (empty($ids)) {
                    echo json_encode(['success' => false, 'message' => 'No roles selected']);
                    exit;
                }
                $placeholders = str_repeat('?,', count($ids) - 1) . '?';
                $stmt = $conn->prepare("DELETE FROM roles WHERE id IN ($placeholders)");
                $stmt->execute($ids);
                echo json_encode(['success' => true, 'message' => count($ids) . ' roles deleted successfully']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;
            
        case 'bulk_delete_subsystem_roles':
            $ids = json_decode($_POST['ids'] ?? '[]', true);
            try {
                if (empty($ids)) {
                    echo json_encode(['success' => false, 'message' => 'No subsystem roles selected']);
                    exit;
                }
                $placeholders = str_repeat('?,', count($ids) - 1) . '?';
                $stmt = $conn->prepare("DELETE FROM subsystem_roles WHERE id IN ($placeholders)");
                $stmt->execute($ids);
                echo json_encode(['success' => true, 'message' => count($ids) . ' subsystem roles deleted successfully']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;
    }
}

require_once __DIR__ . '/sidebar.php';

// Filter parameters
$role_search = $_GET['role_search'] ?? '';
$subsystem_filter = $_GET['subsystem_filter'] ?? '';
$subsystem_role_search = $_GET['subsystem_role_search'] ?? '';

// Pagination settings
$per_page = 10;
$roles_page = max(1, (int)($_GET['roles_page'] ?? 1));
$subsystem_roles_page = max(1, (int)($_GET['subsystem_roles_page'] ?? 1));
$roles_offset = ($roles_page - 1) * $per_page;
$subsystem_roles_offset = ($subsystem_roles_page - 1) * $per_page;

// Build role query with filters
$role_where = '';
$role_params = [];
if ($role_search) {
    $role_where = 'WHERE name LIKE ?';
    $role_params[] = "%$role_search%";
}

// Build subsystem role query with filters
$subsystem_role_where = '';
$subsystem_role_params = [];
if ($subsystem_filter || $subsystem_role_search) {
    $conditions = [];
    if ($subsystem_filter) {
        $conditions[] = 's.id = ?';
        $subsystem_role_params[] = $subsystem_filter;
    }
    if ($subsystem_role_search) {
        $conditions[] = 'sr.role_name LIKE ?';
        $subsystem_role_params[] = "%$subsystem_role_search%";
    }
    $subsystem_role_where = 'WHERE ' . implode(' AND ', $conditions);
}

// Get total counts with filters
$total_roles = $conn->prepare("SELECT COUNT(*) FROM roles $role_where");
$total_roles->execute($role_params);
$total_roles = $total_roles->fetchColumn();

$total_subsystem_roles = $conn->prepare("SELECT COUNT(*) FROM subsystem_roles sr JOIN subsystems s ON sr.subsystem_id = s.id $subsystem_role_where");
$total_subsystem_roles->execute($subsystem_role_params);
$total_subsystem_roles = $total_subsystem_roles->fetchColumn();

$roles_total_pages = ceil($total_roles / $per_page);
$subsystem_roles_total_pages = ceil($total_subsystem_roles / $per_page);

// Get paginated roles with filters
$roles_stmt = $conn->prepare("SELECT * FROM roles $role_where ORDER BY name LIMIT $per_page OFFSET $roles_offset");
$roles_stmt->execute($role_params);
$roles = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);

$subsystem_roles_stmt = $conn->prepare("
    SELECT sr.*, s.name as subsystem_name 
    FROM subsystem_roles sr 
    JOIN subsystems s ON sr.subsystem_id = s.id 
    $subsystem_role_where
    ORDER BY s.name, sr.role_name 
    LIMIT $per_page OFFSET $subsystem_roles_offset
");
$subsystem_roles_stmt->execute($subsystem_role_params);
$subsystem_roles = $subsystem_roles_stmt->fetchAll(PDO::FETCH_ASSOC);
$subsystems = $conn->query('SELECT * FROM subsystems ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Management - LGU1</title>
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
                            <h1 class="h3 fw-bold mb-1">🛡️ Role Management</h1>
                            <p class="mb-0 opacity-90">Manage system roles and permissions</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <main class="container-fluid p-4">
            <!-- Filter Controls -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Search Global Roles</label>
                            <input type="text" class="form-control" id="roleSearch" placeholder="Search roles..." value="<?= htmlspecialchars($role_search) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Filter by Subsystem</label>
                            <select class="form-select" id="subsystemFilter">
                                <option value="">All Subsystems</option>
                                <?php foreach ($subsystems as $subsystem): ?>
                                    <option value="<?= $subsystem['id'] ?>" <?= $subsystem_filter == $subsystem['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($subsystem['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Search Subsystem Roles</label>
                            <input type="text" class="form-control" id="subsystemRoleSearch" placeholder="Search subsystem roles..." value="<?= htmlspecialchars($subsystem_role_search) ?>">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary" onclick="applyFilters()">
                                    <i class="bi bi-funnel"></i> Filter
                                </button>
                                <button class="btn btn-outline-secondary" onclick="clearFilters()">
                                    <i class="bi bi-x-circle"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Global Roles -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-shield-check"></i> Global Roles</h5>
                            <div class="d-flex gap-2">
                                <button class="btn btn-danger btn-sm" id="bulkDeleteRoles" style="display: none;" onclick="bulkDeleteRoles()">
                                    <i class="bi bi-trash"></i> Delete Selected
                                </button>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                                    <i class="bi bi-plus"></i> Add Role
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAllRoles" onchange="toggleAllRoles()"></th>
                                            <th>Role Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($roles as $role): ?>
                                        <tr>
                                            <td><input type="checkbox" class="role-checkbox" value="<?= $role['id'] ?>" onchange="updateBulkDeleteButton()"></td>
                                            <td><?= htmlspecialchars($role['name']) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" onclick="editRole(<?= $role['id'] ?>, '<?= htmlspecialchars($role['name']) ?>')" data-bs-toggle="modal" data-bs-target="#editRoleModal">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" onclick="confirmDeleteRole(<?= $role['id'] ?>, '<?= htmlspecialchars($role['name']) ?>')" data-bs-toggle="modal" data-bs-target="#deleteRoleModal">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Roles Pagination -->
                            <?php if ($roles_total_pages > 1): ?>
                            <nav class="mt-3">
                                <ul class="pagination pagination-sm justify-content-center">
                                    <li class="page-item <?= $roles_page <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?roles_page=<?= $roles_page - 1 ?>&subsystem_roles_page=<?= $subsystem_roles_page ?>&role_search=<?= urlencode($role_search) ?>&subsystem_filter=<?= urlencode($subsystem_filter) ?>&subsystem_role_search=<?= urlencode($subsystem_role_search) ?>">Previous</a>
                                    </li>
                                    <?php for ($i = max(1, $roles_page - 2); $i <= min($roles_total_pages, $roles_page + 2); $i++): ?>
                                    <li class="page-item <?= $i === $roles_page ? 'active' : '' ?>">
                                        <a class="page-link" href="?roles_page=<?= $i ?>&subsystem_roles_page=<?= $subsystem_roles_page ?>&role_search=<?= urlencode($role_search) ?>&subsystem_filter=<?= urlencode($subsystem_filter) ?>&subsystem_role_search=<?= urlencode($subsystem_role_search) ?>"><?= $i ?></a>
                                    </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= $roles_page >= $roles_total_pages ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?roles_page=<?= $roles_page + 1 ?>&subsystem_roles_page=<?= $subsystem_roles_page ?>&role_search=<?= urlencode($role_search) ?>&subsystem_filter=<?= urlencode($subsystem_filter) ?>&subsystem_role_search=<?= urlencode($subsystem_role_search) ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Subsystem Roles -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-building"></i> Subsystem Roles</h5>
                            <div class="d-flex gap-2">
                                <button class="btn btn-danger btn-sm" id="bulkDeleteSubsystemRoles" style="display: none;" onclick="bulkDeleteSubsystemRoles()">
                                    <i class="bi bi-trash"></i> Delete Selected
                                </button>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createSubsystemRoleModal">
                                    <i class="bi bi-plus"></i> Add Role
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAllSubsystemRoles" onchange="toggleAllSubsystemRoles()"></th>
                                            <th>Subsystem</th>
                                            <th>Role</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($subsystem_roles as $role): ?>
                                        <tr>
                                            <td><input type="checkbox" class="subsystem-role-checkbox" value="<?= $role['id'] ?>" onchange="updateBulkDeleteSubsystemButton()"></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($role['subsystem_name']) ?></span></td>
                                            <td><?= htmlspecialchars($role['role_name']) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" onclick="editSubsystemRole(<?= $role['id'] ?>, '<?= htmlspecialchars($role['role_name']) ?>', '<?= htmlspecialchars($role['description']) ?>')" data-bs-toggle="modal" data-bs-target="#editSubsystemRoleModal">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" onclick="confirmDeleteSubsystemRole(<?= $role['id'] ?>, '<?= htmlspecialchars($role['role_name']) ?>')" data-bs-toggle="modal" data-bs-target="#deleteSubsystemRoleModal">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Subsystem Roles Pagination -->
                            <?php if ($subsystem_roles_total_pages > 1): ?>
                            <nav class="mt-3">
                                <ul class="pagination pagination-sm justify-content-center">
                                    <li class="page-item <?= $subsystem_roles_page <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?roles_page=<?= $roles_page ?>&subsystem_roles_page=<?= $subsystem_roles_page - 1 ?>&role_search=<?= urlencode($role_search) ?>&subsystem_filter=<?= urlencode($subsystem_filter) ?>&subsystem_role_search=<?= urlencode($subsystem_role_search) ?>">Previous</a>
                                    </li>
                                    <?php for ($i = max(1, $subsystem_roles_page - 2); $i <= min($subsystem_roles_total_pages, $subsystem_roles_page + 2); $i++): ?>
                                    <li class="page-item <?= $i === $subsystem_roles_page ? 'active' : '' ?>">
                                        <a class="page-link" href="?roles_page=<?= $roles_page ?>&subsystem_roles_page=<?= $i ?>&role_search=<?= urlencode($role_search) ?>&subsystem_filter=<?= urlencode($subsystem_filter) ?>&subsystem_role_search=<?= urlencode($subsystem_role_search) ?>"><?= $i ?></a>
                                    </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= $subsystem_roles_page >= $subsystem_roles_total_pages ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?roles_page=<?= $roles_page ?>&subsystem_roles_page=<?= $subsystem_roles_page + 1 ?>&role_search=<?= urlencode($role_search) ?>&subsystem_filter=<?= urlencode($subsystem_filter) ?>&subsystem_role_search=<?= urlencode($subsystem_role_search) ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Create Role Modal -->
    <div class="modal fade" id="createRoleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus"></i> Create Global Role</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="createRoleForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Role Name</label>
                            <input type="text" class="form-control" name="name" required>
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

    <!-- Edit Role Modal -->
    <div class="modal fade" id="editRoleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editRoleForm">
                    <input type="hidden" name="id" id="editRoleId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Role Name</label>
                            <input type="text" class="form-control" name="name" id="editRoleName" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Role Modal -->
    <div class="modal fade" id="deleteRoleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-trash"></i> Delete Role</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="deleteRoleName"></strong>?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="deleteRole()" data-bs-dismiss="modal">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Subsystem Role Modal -->
    <div class="modal fade" id="createSubsystemRoleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-plus"></i> Create Subsystem Role</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="createSubsystemRoleForm">
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

    <!-- Edit Subsystem Role Modal -->
    <div class="modal fade" id="editSubsystemRoleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Subsystem Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editSubsystemRoleForm">
                    <input type="hidden" name="id" id="editSubsystemRoleId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Role Name</label>
                            <input type="text" class="form-control" name="role_name" id="editSubsystemRoleName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="editSubsystemRoleDescription" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Subsystem Role Modal -->
    <div class="modal fade" id="deleteSubsystemRoleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-trash"></i> Delete Subsystem Role</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="deleteSubsystemRoleName"></strong>?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="deleteSubsystemRole()" data-bs-dismiss="modal">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Filter functions
        function applyFilters() {
            const roleSearch = document.getElementById('roleSearch').value;
            const subsystemFilter = document.getElementById('subsystemFilter').value;
            const subsystemRoleSearch = document.getElementById('subsystemRoleSearch').value;
            
            const params = new URLSearchParams();
            if (roleSearch) params.set('role_search', roleSearch);
            if (subsystemFilter) params.set('subsystem_filter', subsystemFilter);
            if (subsystemRoleSearch) params.set('subsystem_role_search', subsystemRoleSearch);
            
            window.location.href = '?' + params.toString();
        }
        
        function clearFilters() {
            window.location.href = 'roleManagement.php';
        }
        
        // Enter key support for search inputs
        document.getElementById('roleSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') applyFilters();
        });
        
        document.getElementById('subsystemRoleSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') applyFilters();
        });
        
        // Bulk delete functions
        function toggleAllRoles() {
            const selectAll = document.getElementById('selectAllRoles');
            const checkboxes = document.querySelectorAll('.role-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkDeleteButton();
        }
        
        function toggleAllSubsystemRoles() {
            const selectAll = document.getElementById('selectAllSubsystemRoles');
            const checkboxes = document.querySelectorAll('.subsystem-role-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkDeleteSubsystemButton();
        }
        
        function updateBulkDeleteButton() {
            const checked = document.querySelectorAll('.role-checkbox:checked').length;
            document.getElementById('bulkDeleteRoles').style.display = checked > 0 ? 'block' : 'none';
        }
        
        function updateBulkDeleteSubsystemButton() {
            const checked = document.querySelectorAll('.subsystem-role-checkbox:checked').length;
            document.getElementById('bulkDeleteSubsystemRoles').style.display = checked > 0 ? 'block' : 'none';
        }
        
        async function bulkDeleteRoles() {
            const checkboxes = document.querySelectorAll('.role-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);
            
            if (ids.length === 0) return;
            
            const result = await Swal.fire({
                title: 'Delete Selected Roles?',
                text: `Are you sure you want to delete ${ids.length} role(s)? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete them!'
            });
            
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'bulk_delete_roles');
                formData.append('ids', JSON.stringify(ids));
                
                try {
                    const response = await fetch('roleManagement.php', { method: 'POST', body: formData });
                    const result = await response.json();
                    
                    if (result.success) {
                        Swal.fire('Deleted!', result.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error!', result.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error!', 'Failed to delete roles', 'error');
                }
            }
        }
        
        async function bulkDeleteSubsystemRoles() {
            const checkboxes = document.querySelectorAll('.subsystem-role-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);
            
            if (ids.length === 0) return;
            
            const result = await Swal.fire({
                title: 'Delete Selected Subsystem Roles?',
                text: `Are you sure you want to delete ${ids.length} subsystem role(s)? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete them!'
            });
            
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'bulk_delete_subsystem_roles');
                formData.append('ids', JSON.stringify(ids));
                
                try {
                    const response = await fetch('roleManagement.php', { method: 'POST', body: formData });
                    const result = await response.json();
                    
                    if (result.success) {
                        Swal.fire('Deleted!', result.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error!', result.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error!', 'Failed to delete subsystem roles', 'error');
                }
            }
        }
        // Global Role Functions
        document.getElementById('createRoleForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'create_role');

            try {
                const response = await fetch('roleManagement.php', { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire('Success!', result.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Failed to create role', 'error');
            }
        });

        function editRole(id, name) {
            document.getElementById('editRoleId').value = id;
            document.getElementById('editRoleName').value = name;
        }

        document.getElementById('editRoleForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'edit_role');

            try {
                const response = await fetch('roleManagement.php', { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire('Success!', result.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Failed to update role', 'error');
            }
        });

        let deleteRoleId = null;
        function confirmDeleteRole(id, name) {
            deleteRoleId = id;
            document.getElementById('deleteRoleName').textContent = name;
        }

        async function deleteRole() {
            if (!deleteRoleId) return;

            const formData = new FormData();
            formData.append('action', 'delete_role');
            formData.append('id', deleteRoleId);

            try {
                const response = await fetch('roleManagement.php', { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire('Success!', result.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Failed to delete role', 'error');
            }
        }

        // Subsystem Role Functions
        document.getElementById('createSubsystemRoleForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'create_subsystem_role');

            try {
                const response = await fetch('roleManagement.php', { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire('Success!', result.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Failed to create subsystem role', 'error');
            }
        });

        function editSubsystemRole(id, name, description) {
            document.getElementById('editSubsystemRoleId').value = id;
            document.getElementById('editSubsystemRoleName').value = name;
            document.getElementById('editSubsystemRoleDescription').value = description;
        }

        document.getElementById('editSubsystemRoleForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'edit_subsystem_role');

            try {
                const response = await fetch('roleManagement.php', { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire('Success!', result.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Failed to update subsystem role', 'error');
            }
        });

        let deleteSubsystemRoleId = null;
        function confirmDeleteSubsystemRole(id, name) {
            deleteSubsystemRoleId = id;
            document.getElementById('deleteSubsystemRoleName').textContent = name;
        }

        async function deleteSubsystemRole() {
            if (!deleteSubsystemRoleId) return;

            const formData = new FormData();
            formData.append('action', 'delete_subsystem_role');
            formData.append('id', deleteSubsystemRoleId);

            try {
                const response = await fetch('roleManagement.php', { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire('Success!', result.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Failed to delete subsystem role', 'error');
            }
        }
    </script>
</body>
</html>