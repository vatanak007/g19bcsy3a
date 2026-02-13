<?php
$oldPasswd = $newPasswd = $confirmNewPasswd = '';
$oldPasswdErr = $newPasswdErr = '';

if (isset($_POST['changePasswd'], $_POST['oldPasswd'], $_POST['newPasswd'], $_POST['confirmNewPasswd'])) {
    $oldPasswd = trim($_POST['oldPasswd']);
    $newPasswd = trim($_POST['newPasswd']);
    $confirmNewPasswd = trim($_POST['confirmNewPasswd']);
    if (empty($oldPasswd)) {
        $oldPasswdErr = 'please input your old password';
    }
    if (empty($newPasswd)) {
        $newPasswdErr = 'please input your new password';
    }
    if ($newPasswd !== $confirmNewPasswd) {
        $newPasswdErr = 'password does not match';
    } else {
        if (!isUserHasPassword($oldPasswd)) {
            $oldPasswdErr = 'password is incorrect';
        }
    }
    if (empty($oldPasswdErr) && empty($newPasswdErr)) {
        if (setUserNewPassword($newPasswd)) {
            unset($_SESSION['user_id']);
            echo '<div class="alert alert-success" role="alert">
                password changed successfully. <a href="./?page=login">click here</a> to login again.
                </div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">
                try aggain.
                </div>';
        }
    }
}


if (isset($_POST['uploadPhoto'])) {
    if (isset($_FILES['photo'])) {
        $result = uploadUserPhoto($_FILES['photo']);
        $msg = $result['message'];
        $msg_type = $result['success'] ? 'success' : 'danger';
        echo '<div class="alert alert-' . ($result['success'] ? 'success' : 'danger') . '">' . $result['message'] . '</div>';

    }
}


if (isset($_POST['deletePhoto'])) {
    $result = deleteUserPhoto();
    $msg = $result['message'];


    $msg_type = $result['success'] ? 'success' : 'danger';
    echo '<div class="alert alert-' . $msg_type . ' mt-3">' . $msg . '</div>';
}

$current_photo = getUserPhoto();



// if (isset($_POST['uploadPhoto'])) {
//     if (isset($_FILES['photo'])) {
//         $result = uploadUserPhoto($_FILES['photo']);
//         echo '<div class="alert alert-' . ($result['success'] ? 'success' : 'danger') . '">' . $result['message'] . '</div>';
//         exit;
//     }
// }

// // 2. Handle Delete Logic
// if (isset($_POST['deletePhoto'])) {
//     $result = deleteUserPhoto();
//     echo '<div class="alert alert-danger">' . $result['message'] . '</div>';
//     exit;
// }

// // 3. Get Current Photo
// $current_photo = getUserPhoto();





// if (isset($_POST['uploadPhoto'])) {
//     if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
//         $result = uploadUserPhoto($_FILES['photo']);
//         // ប្រើ JavaScript ដើម្បី Refresh ទាញយករូបភាពថ្មីភ្លាមៗ
//         echo "<script>alert('".$result['message']."'); window.location.href='./?page=profile';</script>";
//         exit;
//     }
// }

// if (isset($_POST['deletePhoto'])) {
//     $result = deleteUserPhoto();
//     echo "<script>window.location.href='./?page=profile';</script>";
//     exit;
// }

// $current_photo = getUserPhoto();







?>

<div class="row">
    <div class="col-md-6">
        <form id="profileForm" method="post" action="./?page=profile" enctype="multipart/form-data"
            onsubmit="return validateUpload(event)">
            <div class="d-flex justify-content-center">
                <input name="photo" type="file" id="profileUpload" hidden accept="image/*"
                    onchange="previewImage(this)">

                <label role="button" for="profileUpload">
                    <img id="imagePreview" src="<?php echo $current_photo; ?>" class="rounded-circle" width="200"
                        height="200" style="object-fit: cover; border: 3px solid #ddd;" alt="profile photo">
                </label>
            </div>

            <div class="d-flex justify-content-center mt-3">
                <button type="submit" name="deletePhoto" class="btn btn-danger"
                    onclick="return confirmDelete();">Delete</button>
                <button type="submit" name="uploadPhoto" class="btn btn-success">Upload</button>
            </div>
        </form>
    </div>



    <script>

        // function validateUpload(event) {
        //     const buttonClicked = event.submitter.name;
        //     if (buttonClicked === 'uploadPhoto') {
        //         const fileInput = document.getElementById('profileUpload');
        //         if (fileInput.files.length === 0) {
        //             alert("Please select a photo to upload."); // Pop-up alert
        //             return false;
        //         }
        //     }
        //     return true;
        // }

        // Logic for Instant Image Preview
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('imagePreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        function confirmDelete() {
            return confirm('Are you sure you want to delete your profile photo?');
        }
    </script>


    <div class="col-6">
        <form method="post" action="./?page=profile" class="col-md-8 col-lg-6 mx-auto">
            <h3>Change Password</h3>
            <div class="mb-3">
                <label class="form-label">Old Password</label>
                <input value="<?php echo $oldPasswd ?>" name="oldPasswd" type="password" class="form-control 
                <?php echo empty($oldPasswdErr) ? '' : 'is-invalid' ?>">
                <div class="invalid-feedback">
                    <?php echo $oldPasswdErr ?>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input name="newPasswd" type="password" class="form-control 
                <?php echo empty($newPasswdErr) ? '' : 'is-invalid' ?>">
                <div class="invalid-feedback">
                    <?php echo $newPasswdErr ?>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input name="confirmNewPasswd" type="password" class="form-control">
            </div>
            <button type="submit" name="changePasswd" class="btn btn-primary">Change Password</button>
        </form>
    </div>
</div>