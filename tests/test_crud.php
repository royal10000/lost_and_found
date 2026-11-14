
<?php

require_once __DIR__ . '/../class/crud.php';
require_once __DIR__ . '/../configs/Database.php';

$db = new CRUD();

$passed = 0;
$failed = 0;

function testResult($name, $condition)
{
    global $passed, $failed;

    if ($condition) {
        echo "PASS: $name <br>";
        $passed++;
    } else {
        echo "FAIL: $name <br>";
        $failed++;
    }
}

$testEmail = 'test_' . uniqid() . '@example.com';
$userId = null;
$itemId = null;

try {

    echo "<h2>CRUD TESTING</h2>";

    // 1. TEST INSERT

    $inserted = $db->insert('crud_test_users', [
        'name' => 'John',
        'email' => $testEmail
    ]);

    testResult('INSERT user', $inserted);

    // Find the inserted user

    $user = $db->query(
        "SELECT * FROM crud_test_users WHERE email = :email",
        ['email' => $testEmail],
        true
    );

    $userId = $user['id'] ?? null;

    testResult('FETCH inserted user', $user !== false);


    // 2. TEST SELECT ONE

    $singleUser = $db->query(
        "SELECT * FROM crud_test_users WHERE id = :id",
        ['id' => $userId],
        true
    );

    testResult(
        'SELECT one user',
        $singleUser !== false &&
        $singleUser['name'] === 'John'
    );


    // 3. TEST SELECT ALL

    $users = $db->query(
        "SELECT * FROM crud_test_users WHERE email = :email",
        ['email' => $testEmail]
    );

    testResult(
        'SELECT multiple records',
        count($users) === 1
    );


    // 4. TEST UPDATE

    $updated = $db->update(
        'crud_test_users',
        49,
        [
            'name' => 'Michael'
        ]
    );

    $updatedUser = $db->query(
        "SELECT * FROM crud_test_users WHERE id = :id",
        ['id' => $userId],
        true
    );

    testResult(
        'UPDATE user',
        $updated &&
        $updatedUser['name'] === 'Michael'
    );


    // 5. TEST INSERT ITEM

    $itemInserted = $db->insert('crud_test_items', [
        'user_id' => $userId,
        'title' => 'Black Wallet',
        'status' => 'lost'
    ]);

    testResult('INSERT item', $itemInserted);


    // Find inserted item

    $item = $db->query(
        "SELECT * FROM crud_test_items
         WHERE user_id = :user_id",
        ['user_id' => $userId],
        true
    );

    $itemId = $item['id'] ?? null;


    // 6. TEST JOIN QUERY

    $joinedItem = $db->query(
        "SELECT
            crud_test_items.*,
            crud_test_users.name AS owner_name
         FROM crud_test_items
         JOIN crud_test_users
         ON crud_test_items.user_id = crud_test_users.id
         WHERE crud_test_items.id = :id",
        ['id' => $itemId],
        true
    );

    testResult(
        'JOIN users and items',
        $joinedItem !== false &&
        $joinedItem['owner_name'] === 'Michael'
    );


    // 7. TEST DELETE ITEM

    $deletedItem = $db->delete(
        'crud_test_items',
        $itemId
    );

    testResult('DELETE item', $deletedItem);


    // 8. TEST DELETE USER

    $deletedUser = $db->delete(
        'crud_test_users',
        $userId
    );

    testResult('DELETE user', $deletedUser);


    // 9. VERIFY DELETE

    $deletedCheck = $db->query(
        "SELECT * FROM crud_test_users WHERE id = :id",
        ['id' => $userId],
        true
    );

    testResult(
        'VERIFY deleted user',
        $deletedCheck === false
    );

} catch (Throwable $e) {

    echo "<p>ERROR: " .
        htmlspecialchars($e->getMessage()) .
        "</p>";

} finally {

    // Clean up test records if an earlier test failed

    if ($itemId !== null) {
        try {
            $db->delete('crud_test_items', $itemId);
        } catch (Throwable $e) {
            // Cleanup error
        }
    }

    if ($userId !== null) {
        try {
            $db->delete('crud_test_users', $userId);
        } catch (Throwable $e) {
            // Cleanup error
        }
    }
}

echo "<hr>";

echo "<h3>TEST SUMMARY</h3>";

echo "Passed: $passed <br>";
echo "Failed: $failed <br>";
echo "Total: " . ($passed + $failed);

?>
