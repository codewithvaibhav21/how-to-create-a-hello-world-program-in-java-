<?php
session_start();

// Initialize game variables if it's the start of a new game
if (!isset($_SESSION['player'])) {
    $_SESSION['player'] = [
        'name' => 'Hero',
        'health' => 100,
        'level' => 1,
        'experience' => 0,
    ];
    $_SESSION['enemy'] = createEnemy();
    $_SESSION['message'] = "Welcome to the Adventure Game! You are a brave hero.";
}

// Function to create a random enemy
function createEnemy() {
    $enemyNames = ['Goblin', 'Skeleton', 'Zombie', 'Bandit'];
    $name = $enemyNames[array_rand($enemyNames)];
    $health = rand(20, 40) + $_SESSION['player']['level'] * 5;
    return [
        'name' => $name,
        'health' => $health,
    ];
}

// Attack function for player and enemy
function attack($attacker, &$defender) {
    $damage = rand(5, 15);
    $defender['health'] -= $damage;
    return $damage;
}

// Level up function for the player
function levelUp() {
    $_SESSION['player']['level']++;
    $_SESSION['player']['health'] += 20;
    $_SESSION['player']['experience'] = 0;
    $_SESSION['message'] = "You leveled up! You are now level " . $_SESSION['player']['level'] . ".";
}

// Game logic based on user input
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'attack') {
        // Player attacks enemy
        $playerDamage = attack($_SESSION['player'], $_SESSION['enemy']);
        $_SESSION['message'] = "You attacked the " . $_SESSION['enemy']['name'] . " for $playerDamage damage.";

        // Check if the enemy is defeated
        if ($_SESSION['enemy']['health'] <= 0) {
            $_SESSION['player']['experience'] += 20;
            $_SESSION['enemy'] = createEnemy();
            $_SESSION['message'] .= " You defeated the enemy! A new enemy appears.";

            // Check if player levels up
            if ($_SESSION['player']['experience'] >= 50) {
                levelUp();
            }
        } else {
            // Enemy attacks back
            $enemyDamage = attack($_SESSION['enemy'], $_SESSION['player']);
            $_SESSION['message'] .= " The " . $_SESSION['enemy']['name'] . " attacked you for $enemyDamage damage.";

            // Check if the player is defeated
            if ($_SESSION['player']['health'] <= 0) {
                $_SESSION['message'] = "You have been defeated! Game Over.";
                session_destroy();
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'restart') {
        session_destroy();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP Adventure Game</title>
</head>
<body>
    <h1>PHP Adventure Game</h1>
    <p><?php echo $_SESSION['message']; ?></p>

    <?php if (isset($_SESSION['player'])): ?>
        <p><strong>Your Stats:</strong></p>
        <ul>
            <li>Name: <?php echo $_SESSION['player']['name']; ?></li>
            <li>Health: <?php echo $_SESSION['player']['health']; ?></li>
            <li>Level: <?php echo $_SESSION['player']['level']; ?></li>
            <li>Experience: <?php echo $_SESSION['player']['experience']; ?></li>
        </ul>

        <p><strong>Enemy:</strong></p>
        <ul>
            <li>Name: <?php echo $_SESSION['enemy']['name']; ?></li>
            <li>Health: <?php echo $_SESSION['enemy']['health']; ?></li>
        </ul>

        <form method="post">
            <button type="submit" name="action" value="attack">Attack</button>
            <button type="submit" name="action" value="restart">Restart Game</button>
        </form>
    <?php endif; ?>
</body>
</html>
