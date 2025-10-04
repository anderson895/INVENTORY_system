<?php
require_once('includes/load.php');
// page_require_level(1);
header('Content-Type: application/json');

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['requestType'])) {
        echo json_encode(['status' => 400, 'message' => 'Missing request type.']);
        exit;
    }

    /* ==================== ADD GROUP ==================== */
    if ($_POST['requestType'] == 'Add_group') {
        $req_fields = array('group-name','group-level');
        validate_fields($req_fields);

        if (!empty($errors)) {
            echo json_encode(['status' => 400, 'message' => implode(', ', $errors)]);
            exit;
        }

         $name   = remove_junk($db->escape($_POST['group-name']));
        $level  = remove_junk($db->escape($_POST['group-level']));
        $status = isset($_POST['status']) ? remove_junk($db->escape($_POST['status'])) : 1;

        // ✅ FIXED: Use same working logic as original
        // The old code checks for "false" (not found). So we keep that logic.

        if (find_by_groupName($name) === false) {
            echo json_encode([
                'status' => 400,
                'message' => 'Entered Group Name already exists!'
            ]);
            exit;
        }

        if (find_by_groupLevel($level) === false) {
            echo json_encode([
                'status' => 400,
                'message' => 'Entered Group Level already exists!'
            ]);
            exit;
        }

        $query  = "INSERT INTO user_groups (group_name, group_level, group_status)
                   VALUES ('{$name}', '{$level}', '{$status}')";
        if ($db->query($query)) {
            echo json_encode(['status' => 200, 'message' => 'Group has been created successfully!']);
        } else {
            echo json_encode(['status' => 500, 'message' => 'Failed to create group.']);
        }
        exit;
    }

    /* ==================== UPDATE GROUP ==================== */
    elseif ($_POST['requestType'] == 'Update_group') {
        $req_fields = array('group-id','group-name','group-level');
        validate_fields($req_fields);

        if (!empty($errors)) {
            echo json_encode(['status' => 400, 'message' => implode(', ', $errors)]);
            exit;
        }

        $id = (int)$db->escape($_POST['group-id']);
        $name  = remove_junk($db->escape($_POST['group-name']));
        $level = remove_junk($db->escape($_POST['group-level']));
        $status = isset($_POST['status']) ? remove_junk($db->escape($_POST['status'])) : 1;

        $existing = find_by_id('user_groups', $id);
        if (!$existing) {
            echo json_encode(['status' => 404, 'message' => 'Group not found.']);
            exit;
        }

        // Optional: check duplicates (ignore same record)
        $dupName = $db->query("SELECT id FROM user_groups WHERE group_name='{$name}' AND id!='{$id}' LIMIT 1");
        if ($db->num_rows($dupName) > 0) {
            echo json_encode(['status' => 400, 'message' => 'Group Name already exists!']);
            exit;
        }

        $dupLevel = $db->query("SELECT id FROM user_groups WHERE group_level='{$level}' AND id!='{$id}' LIMIT 1");
        if ($db->num_rows($dupLevel) > 0) {
            echo json_encode(['status' => 400, 'message' => 'Group Level already exists!']);
            exit;
        }

        $query = "UPDATE user_groups SET 
                    group_name='{$name}', 
                    group_level='{$level}', 
                    group_status='{$status}'
                  WHERE id='{$id}'";
        if ($db->query($query)) {
            echo json_encode(['status' => 200, 'message' => 'Group updated successfully!']);
        } else {
            echo json_encode(['status' => 500, 'message' => 'Failed to update group.']);
        }
        exit;
    }else if ($_POST['requestType'] == 'add_user') {
        $name = remove_junk($db->escape($_POST['full-name']));
        $username = remove_junk($db->escape($_POST['username']));
        $email = remove_junk($db->escape($_POST['email']));
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $level = (int)$db->escape($_POST['level']);

        

        $sql = "INSERT INTO users (name, username,email, password, user_level, status)
                VALUES ('{$name}','{$username}','{$email}','{$password}','{$level}','1')";
        if ($db->query($sql)) {
            echo json_encode(['status' => 200, 'message' => 'User added successfully!']);
        } else {
            echo json_encode(['status' => 500, 'message' => 'Failed to add user.']);
        }
        exit;
    }else if ($_POST['requestType'] == 'update_user') {
        $id = (int)$_POST['user_id'];
        $name = remove_junk($db->escape($_POST['name']));
        $username = remove_junk($db->escape($_POST['username']));
        $email = remove_junk($db->escape($_POST['email']));
        $level = (int)$db->escape($_POST['level']);
        $status = (int)$db->escape($_POST['status']);

        $sql = "UPDATE users SET 
                name='{$name}', username='{$username}', email='{$email}', 
                user_level='{$level}', status='{$status}' 
                WHERE id='{$id}'";

        if ($db->query($sql)) {
            echo json_encode(['status' => 200, 'message' => 'User updated successfully!']);
        } else {
            echo json_encode(['status' => 500, 'message' => 'Failed to update user.']);
        }
        exit;
    }else if ($_POST['requestType'] == 'update_categorie') {
            $req_fields = array('categorie-name', 'id');
            validate_fields($req_fields);

            $cat_name = remove_junk($db->escape($_POST['categorie-name']));
            $cat_id   = (int)$_POST['id'];

            if (empty($errors)) {
                $sql = "UPDATE categories SET name = '{$cat_name}' WHERE id = '{$cat_id}' LIMIT 1";
                $result = $db->query($sql);

                if ($result && $db->affected_rows() === 1) {
                    echo json_encode([
                        'status' => 200,
                        'message' => 'Successfully updated category.'
                    ]);
                } else {
                    echo json_encode([
                        'status' => 400,
                        'message' => 'No changes made or update failed.'
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 400,
                    'message' => $errors
                ]);
            }
            exit;
        }



    /* ==================== INVALID TYPE ==================== */
    else {
        echo json_encode(['status' => 400, 'message' => 'Invalid request type.']);
        exit;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {

   if (isset($_GET['requestType']))
    {
        if ($_GET['requestType'] == 'get_user' && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $user = find_by_id('users', $id);
            if ($user) {
                echo json_encode(['status' => 200, 'user' => $user]);
            } else {
                echo json_encode(['status' => 404, 'message' => 'User not found.']);
            }
            exit;
        }else{
            echo "404";
        }
    }else {
        echo 'No GET REQUEST';
    }
}
?>
