<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('KMKDT_ADMIN_SESSION');
    session_start();
}

// Load database configuration
$configPath = 'C:\xampp\htdocs\kmkdt-Library\app\config\config.php';
if (file_exists($configPath)) {
    include_once($configPath);
} else {
    die("Database Configuration layer missing target lookup at routing anchor.");
}

if (!isset($conn)) {
    die("Database access connection context lost.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

$action = $_POST['action'] ?? '';
$uploadDir = 'C:/xampp/htdocs/kmkdt-Library/app/uploads/covers/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

function handleCoverImageUpload($fileArray, $uploadDir) {
    if (!isset($fileArray) || $fileArray['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $fileTmpPath = $fileArray['tmp_name'];
    $originalName = $fileArray['name'];
    
    $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $justName = pathinfo($originalName, PATHINFO_FILENAME);
    
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($fileExtension, $allowedExtensions)) {
        return false;
    }
    
    $cleanName = strtolower($justName);
    $cleanName = preg_replace("/[^a-z0-9_\-]/", "_", $cleanName);
    $cleanName = preg_replace("/_+/", "_", $cleanName); 
    $cleanName = trim($cleanName, '_');
    
    $newFileName = $cleanName . '.' . $fileExtension;
    $dest_path = $uploadDir . $newFileName;
    
    $counter = 1;
    while (file_exists($dest_path)) {
        $newFileName = $cleanName . '_' . $counter . '.' . $fileExtension;
        $dest_path = $uploadDir . $newFileName;
        $counter++;
    }
    
    if (move_uploaded_file($fileTmpPath, $dest_path)) {
        return $newFileName;
    }
    
    return false;
}

// --- CRUD ROUTING INTERCEPTIONS ---

switch ($action) {
    case 'add_category':
        header('Content-Type: application/json');
        $categoryName = trim($_POST['category_name'] ?? '');

        if ($categoryName === "") {
            echo json_encode(['success' => false, 'message' => 'Category entry configuration value cannot be blank.']);
            exit();
        }

        // Keep the natural casing but normalize typos or formatting structures
        $normalizedCheck = trim($categoryName);
        if (strtolower($normalizedCheck) === 'non fiction' || strtolower($normalizedCheck) === 'non-ficion' || strtolower($normalizedCheck) === 'non-fiction') {
            $normalizedCheck = 'Non-Fiction';
        } else if (strtolower($normalizedCheck) === 'case studies') {
            $normalizedCheck = 'Case Studies';
        } else {
            $normalizedCheck = ucwords(strtolower($normalizedCheck));
        }

        echo json_encode([
            'success' => true,
            'category' => $normalizedCheck
        ]);
        exit();

    case 'create':
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $genre = trim($_POST['genre'] ?? '');
        $status = trim($_POST['status'] ?? 'available');
        $description = trim($_POST['description'] ?? '');
        $userId = $_SESSION['user_id'] ?? null;

        if (empty($title) || empty($author)) {
            $_SESSION['error'] = "Required entry form attributes cannot be left empty.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // --- DUPLICATE CHECK ENGINE (Supports both PDO & MySQLi) ---
        $isDuplicate = false;
        if ($conn instanceof PDO) {
            $dupStmt = $conn->prepare("SELECT id FROM books WHERE LOWER(title) = LOWER(?) AND LOWER(author) = LOWER(?) LIMIT 1");
            $dupStmt->execute([$title, $author]);
            if ($dupStmt->fetch()) {
                $isDuplicate = true;
            }
        } else {
            $dupStmt = $conn->prepare("SELECT id FROM books WHERE LOWER(title) = LOWER(?) AND LOWER(author) = LOWER(?) LIMIT 1");
            $dupStmt->bind_param("ss", $title, $author);
            $dupStmt->execute();
            $dupResult = $dupStmt->get_result();
            if ($dupResult->num_rows > 0) {
                $isDuplicate = true;
            }
        }

        if ($isDuplicate) {
            $_SESSION['error'] = "The book titled '$title' by '$author' already exists in the library catalog.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // MULTI-CATEGORY FIXED SQL ORDER PROCESSING (CAPITALIZED FORMAT)
        if (isset($_POST['category']) && is_array($_POST['category'])) {
            $masterOrder = ['Fiction', 'Non-Fiction', 'Research', 'Case Studies', 'Reserve', 'Online'];

            $cleanCategories = array_map(function($cat) {
                $c = trim($cat);
                $lower = strtolower($c);
                if ($lower === 'non fiction' || $lower === 'non-ficion' || $lower === 'non-fiction') return 'Non-Fiction';
                if ($lower === 'case studies') return 'Case Studies';
                if ($lower === 'fiction') return 'Fiction';
                if ($lower === 'research') return 'Research';
                if ($lower === 'reserve') return 'Reserve';
                if ($lower === 'online') return 'Online';
                return ucwords($lower);
            }, $_POST['category']);

            usort($cleanCategories, function($a, $b) use ($masterOrder) {
                $posA = array_search($a, $masterOrder);
                $posB = array_search($b, $masterOrder);
                $posA = ($posA === false) ? 999 : $posA;
                $posB = ($posB === false) ? 999 : $posB;
                return $posA <=> $posB;
            });
            
            $category = implode(', ', $cleanCategories);
        } else {
            $rawCat = trim($_POST['category'] ?? '');
            $lowerCat = strtolower($rawCat);
            if ($lowerCat === 'non fiction' || $lowerCat === 'non-ficion' || $lowerCat === 'non-fiction') {
                $category = 'Non-Fiction';
            } else if ($lowerCat === 'case studies') {
                $category = 'Case Studies';
            } else {
                $category = ucwords($lowerCat);
            }
        }

        if (empty($_FILES['cover_image']['name'])) {
            $_SESSION['error'] = "Submission rejected. A book cover image file is strictly required.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $uploaded = handleCoverImageUpload($_FILES['cover_image'], $uploadDir);
        if ($uploaded === false) {
            $_SESSION['error'] = "Invalid file payload type or save error. Only JPG, PNG, and WEBP are authorized.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
        $coverImageFile = $uploaded;

        if (empty($category)) {
            $_SESSION['error'] = "Required entry form attributes cannot be left empty.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        if ($conn instanceof PDO) {
            $stmt = $conn->prepare("INSERT INTO books (title, author, genre, category, status, description, cover_image, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $executed = $stmt->execute([$title, $author, $genre, $category, $status, $description, $coverImageFile, $userId]);
        } else {
            $stmt = $conn->prepare("INSERT INTO books (title, author, genre, category, status, description, cover_image, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssi", $title, $author, $genre, $category, $status, $description, $coverImageFile, $userId);
            $executed = $stmt->execute();
        }

        if ($executed) {
            $_SESSION['success'] = "Book profile along with file image assets mapped cleanly to record index.";
        } else {
            $_SESSION['error'] = "Database insertion processing error encountered.";
        }
        break;

    case 'update':
        $bookId = intval($_POST['book_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $genre = trim($_POST['genre'] ?? '');
        $status = trim($_POST['status'] ?? 'available');
        $description = trim($_POST['description'] ?? '');

        // MULTI-CATEGORY FIXED SQL ORDER PROCESSING (CAPITALIZED FORMAT)
        if (isset($_POST['category']) && is_array($_POST['category'])) {
            $masterOrder = ['Fiction', 'Non-Fiction', 'Research', 'Case Studies', 'Reserve', 'Online'];

            $cleanCategories = array_map(function($cat) {
                $c = trim($cat);
                $lower = strtolower($c);
                if ($lower === 'non fiction' || $lower === 'non-ficion' || $lower === 'non-fiction') return 'Non-Fiction';
                if ($lower === 'case studies') return 'Case Studies';
                if ($lower === 'fiction') return 'Fiction';
                if ($lower === 'research') return 'Research';
                if ($lower === 'reserve') return 'Reserve';
                if ($lower === 'online') return 'Online';
                return ucwords($lower);
            }, $_POST['category']);

            usort($cleanCategories, function($a, $b) use ($masterOrder) {
                $posA = array_search($a, $masterOrder);
                $posB = array_search($b, $masterOrder);
                $posA = ($posA === false) ? 999 : $posA;
                $posB = ($posB === false) ? 999 : $posB;
                return $posA <=> $posB;
            });
            
            $category = implode(', ', $cleanCategories);
        } else {
            $rawCat = trim($_POST['category'] ?? '');
            $lowerCat = strtolower($rawCat);
            if ($lowerCat === 'non fiction' || $lowerCat === 'non-ficion' || $lowerCat === 'non-fiction') {
                $category = 'Non-Fiction';
            } else if ($lowerCat === 'case studies') {
                $category = 'Case Studies';
            } else {
                $category = ucwords($lowerCat);
            }
        }

        if ($bookId <= 0 || empty($title) || empty($author) || empty($category)) {
            $_SESSION['error'] = "Update operation aborted due to missing or invalid required field criteria.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $sql = "UPDATE books SET title = ?, author = ?, genre = ?, category = ?, status = ?, description = ?";
        $paramArray = [$title, $author, $genre, $category, $status, $description];
        $types = "ssssss";

        if (!empty($_FILES['cover_image']['name'])) {
            $uploaded = handleCoverImageUpload($_FILES['cover_image'], $uploadDir);
            if ($uploaded === false) {
                $_SESSION['error'] = "Invalid file formatting structure payload provided.";
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }
            $sql .= ", cover_image = ?";
            $paramArray[] = $uploaded;
            $types .= "s";
        }

        $sql .= " WHERE id = ?";
        $paramArray[] = $bookId;
        $types .= "i";

        if ($conn instanceof PDO) {
            $stmt = $conn->prepare($sql);
            $executed = $stmt->execute($paramArray);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$paramArray);
            $executed = $stmt->execute();
        }

        if ($executed) {
            $_SESSION['success'] = "Book property attributes structural variations modified successfully.";
        } else {
            $_SESSION['error'] = "Update target tracking pipeline failure mapped.";
        }
        break;

    case 'delete':
        $bookId = intval($_POST['book_id'] ?? 0);

        if ($bookId <= 0) {
            $_SESSION['error'] = "Target clearing identifier reference points broken.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        if ($conn instanceof PDO) {
            $getImg = $conn->prepare("SELECT cover_image FROM books WHERE id = ?");
            $getImg->execute([$bookId]);
            $oldImg = $getImg->fetchColumn();

            $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
            $executed = $stmt->execute([$bookId]);
        } else {
            $getImg = $conn->prepare("SELECT cover_image FROM books WHERE id = ?");
            $getImg->bind_param("i", $bookId);
            $getImg->execute();
            $result = $getImg->get_result()->fetch_assoc();
            $oldImg = $result['cover_image'] ?? null;

            $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
            $stmt->bind_param("i", $bookId);
            $executed = $stmt->execute();
        }

        if ($executed) {
            if (!empty($oldImg) && file_exists($uploadDir . $oldImg)) {
                unlink($uploadDir . $oldImg);
            }
            $_SESSION['success'] = "Target property record and linked storage file asset cleared down successfully.";
        } else {
            $_SESSION['error'] = "Clearance operation database execution error intercepted.";
        }
        break;

    default:
        $_SESSION['error'] = "Invalid mapping command execution routing requested.";
        break;
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
