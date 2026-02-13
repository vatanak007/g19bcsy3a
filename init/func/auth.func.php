<?php

function usernameExists($username)
{
    global $db;
    $query = $db->prepare('SELECT * FROM tbl_users WHERE username = ?');
    $query->bind_param('s', $username);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows) {
        return true;
    }
    return false;
}

function registerUser($name, $username, $passwd)
{
    global $db;
    $query = $db->prepare('INSERT INTO tbl_users (name,username,passwd) VALUES (?,?,?)');
    $query->bind_param('sss', $name, $username, $passwd);
    $query->execute();
    if ($db->affected_rows) {
        return true;
    }
    return false;
}

function logUserIn($username, $passwd)
{
    global $db;
    $query = $db->prepare('SELECT * FROM tbl_users WHERE username = ? AND passwd = ?');
    $query->bind_param('ss', $username, $passwd);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows) {
        return $result->fetch_object();
    }
    return false;
}

function loggedInUser()
{
    global $db;
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    $user_id = $_SESSION['user_id'];
    $query = $db->prepare('SELECT * FROM tbl_users WHERE id = ?');
    $query->bind_param('i', $user_id);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows) {
        return $result->fetch_object();
    }
    return null;
}



function isAdmin()
{
    $user = loggedInUser();
    if ($user && $user->level === 'admin') {
        return true;
    }
    return false;
}

function isUserHasPassword($passwd)
{
    global $db;
    $user = loggedInUser();
    if (!$user) {
        return false;
    }
    $query = $db->prepare(
        "SELECT * FROM tbl_users WHERE id = ? AND passwd = ?"
    );
    $query->bind_param("is", $user->id, $passwd);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows) {
        return true;
    }
    return false;
}

function setUserNewPassword($passwd)
{
    global $db;
    $user = loggedInUser();
    if (!$user) {
        return false;
    }
    $query = $db->prepare(
        "UPDATE tbl_users SET passwd = ? WHERE id = ?"
    );
    $query->bind_param("si", $passwd, $user->id);
    $query->execute();
    if ($db->affected_rows) {
        return true;
    }
    return false;
}


// // Function to get photo or default
// function getUserPhoto() {
//     $user = loggedInUser();
//     // Use object notation -> because $user is an stdClass object
//     if ($user && isset($user->photo) && !empty($user->photo) && file_exists('./' . $user->photo)) {
//         return './' . $user->photo;
//     }
//     return './assets/images/emptyprofile.jpg';
// }






// // Function to upload and update database
// function uploadUserPhoto($file): array {
//     global $db;
//     $user = loggedInUser();

//     if (!$user) return array('success' => false, 'message' => 'User not logged in');

//     // Basic Validation
//     if (!isset($file['name']) || $file['error'] === 4) {
//         return array('success' => false, 'message' => 'Please select a photo to upload.');
//     }

//     $allowed_ext = array('jpg', 'jpeg', 'png');
//     $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

//     // Set Directories
//     $upload_dir = rtrim(dirname(__DIR__, 2), '/\\') . '/assets/images/';
//     $random_name = bin2hex(random_bytes(8)) . '.' . $file_ext;
//     $file_path = $upload_dir . $random_name;
//     $db_save_path = 'assets/images/' . $random_name;

//     if (in_array($file_ext, $allowed_ext)) {
//         if (move_uploaded_file($file['tmp_name'], $file_path)) {

//             // Delete old physical file if it exists
//             if (!empty($user->photo) && file_exists('./' . $user->photo)) {
//                 unlink('./' . $user->photo);
//             }

//             // Update tbl_users
//             $stmt = $db->prepare("UPDATE tbl_users SET photo = ? WHERE id = ?");
//             $stmt->bind_param("si", $db_save_path, $user->id);
//             $stmt->execute();

//             return array('success' => true, 'message' => 'Photo uploaded successfully!');
//         }
//     }
//     return array('success' => false, 'message' => 'Invalid file type or upload failed.');
// }

// // Function to delete photo
// function deleteUserPhoto() {
//     global $db;
//     $user = loggedInUser();

//     if (!empty($user->photo) && file_exists('./' . $user->photo)) {
//         unlink('./' . $user->photo);
//     }

//     $stmt = $db->prepare("UPDATE tbl_users SET photo = NULL WHERE id = ?");
//     $stmt->bind_param("i", $user->id);
//     $stmt->execute();

//     return array('success' => true, 'message' => 'Photo deleted successfully');
// }























function uploadUserPhoto($file)
{
    global $db;
    $user = loggedInUser();

    
    if (!isset($file['name']) || $file['name'] == '') {
        return array('success' => false, 'message' => 'Please select a photo to upload.');
    }

    $max_size = 5 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        return array('success' => false, 'message' => 'File size limit 5MB');
    }

    
    $allowed_extensions = array('jpg', 'jpeg', 'png');
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_extensions)) {
        return array('success' => false, 'message' => 'File extension is not allowed!!');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowed_mimes = array('image/jpeg', 'image/png');
    if (!in_array($mime, $allowed_mimes)) {
        return array('success' => false, 'message' => 'Only JPG, JPEG, and PNG files are allowed');
    }

    
    try {
        $random_name = bin2hex(random_bytes(8)) . '.' . $file_ext;
    } catch (Exception $e) {
        $random_name = uniqid('img_', true) . '.' . $file_ext;
    }

    $upload_dir = rtrim(dirname(__DIR__, 2), '/\\') . '/assets/images/';
    $file_path = $upload_dir . $random_name;
    $db_photo_path = 'assets/images/' . $random_name;

    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true) && !is_dir($upload_dir)) {
            return array('success' => false, 'message' => 'Failed to create upload directory');
        }
    }

    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        return array('success' => false, 'message' => 'Failed to upload file');
    }

    
    $old_photo_query = $db->prepare('SELECT photo FROM tbl_users WHERE id = ?');
    $old_photo_query->bind_param('i', $user->id);
    $old_photo_query->execute();
    $result = $old_photo_query->get_result();
    if ($result && $result->num_rows && $row = $result->fetch_assoc()) {
        if (!empty($row['photo'])) {
            $old_file = $upload_dir . basename($row['photo']);
            if (file_exists($old_file)) {
                @unlink($old_file);
            }
        }
    }

    
    $query = $db->prepare('UPDATE tbl_users SET photo = ? WHERE id = ?');
    $query->bind_param('ss', $db_photo_path, $user->id);
    $query->execute();

    if ($db->affected_rows) {
        return array('success' => true, 'message' => 'Profile photo Upload successfully', 'photo' => $db_photo_path);
    } else {
        unlink($file_path);
        return array('success' => false, 'message' => 'Failed to save photo information');
    }
    if (empty($file) || !is_array($file) || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'message' => 'Please select a photo to upload.'];
    }
}

function deleteUserPhoto()
{
    global $db;
    $user = loggedInUser();

    
    $query = $db->prepare('SELECT photo FROM tbl_users WHERE id = ?');
    $query->bind_param('s', $user->id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows && $row = $result->fetch_assoc()) {
        $photo = $row['photo'];
        if ($photo && !empty($photo)) {
            
            $upload_dir = rtrim(dirname(__DIR__, 2), '/\\') . '/assets/images/';
            $filename = basename($photo);
            $file_path = $upload_dir . $filename;
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            
            $update_query = $db->prepare('UPDATE tbl_users SET photo = NULL WHERE id = ?');
            $update_query->bind_param('d', $user->id);
            $update_query->execute();

            if ($db->affected_rows) {
                return array('success' => true, 'message' => 'Photo deleted successfully');
            } else {
                return array('success' => false, 'message' => 'Failed to delete photo from database');
            }
        } else {
            return array('success' => false, 'message' => 'No photo to delete');
        }
    }
    return array('success' => false, 'message' => 'Error retrieving photo information');
}

function getUserPhoto()
{
    $user = loggedInUser();
    if ($user && isset($user->photo) && file_exists('./' . $user->photo)) {
        return './' . $user->photo;
    }
    return './assets/images/emptyprofile.jpg';
}