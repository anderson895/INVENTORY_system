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
    }

    /* ==================== INVALID TYPE ==================== */
    else {
        echo json_encode(['status' => 400, 'message' => 'Invalid request type.']);
        exit;
    }
} else {
    echo json_encode(['status' => 403, 'message' => 'Access denied.']);
    exit;
}
?>
