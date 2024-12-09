<?php
session_start();

// Reset the game if "reset" is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Initialize the game
if (!isset($_SESSION['cards'])) {
    $animals = [
        'Panda' => '/img/_Panda.JPG',
        'Tiger' => '/img/images.jpg',
        'Elephant' => '/img/1033551-elephant.jpg',
        'Rhino' => '/img/3yuabfu3jq_white_rhino_42993643.jpg',
        'Turtle' => '/img/hawksbill_sea_turtle.jpg',
        'Penguin' => '/img/pinguin.jpg',
    ];

    $names = array_keys($animals);
    $images = array_values($animals);

    $cards = array_merge($names, $images); // Combine names and images
    shuffle($cards);

    $_SESSION['cards'] = $cards;
    $_SESSION['flipped'] = [];
    $_SESSION['matched'] = [];
}

// Handle card flip
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['card_index'])) {
    $index = (int)$_POST['card_index'];

    if (!in_array($index, $_SESSION['flipped']) && !in_array($index, $_SESSION['matched'])) {
        $_SESSION['flipped'][] = $index;

        // Check for match if two cards are flipped
        if (count($_SESSION['flipped']) === 2) {
            $first = $_SESSION['flipped'][0];
            $second = $_SESSION['flipped'][1];

            $firstCard = $_SESSION['cards'][$first];
            $secondCard = $_SESSION['cards'][$second];

            // Check if one is a name and the other is its corresponding image
            $animals = [
                'Panda' => 'img/_Panda.JPG',
                'Tiger' => 'img/images.jpg',
                'Elephant' => 'img/1033551-elephant.jpg',
                'Rhino' => 'img/3yuabfu3jq_white_rhino_42993643.jpg',
                'Turtle' => 'img/hawksbill_sea_turtle.jpg',
                'Penguin' => 'img/pinguin.jpg',
            ];

            if ((isset($animals[$firstCard]) && $animals[$firstCard] === $secondCard) ||
                (array_search($firstCard, $animals) === $secondCard)) {
                $_SESSION['matched'] = array_merge($_SESSION['matched'], $_SESSION['flipped']);
            }
            // Reset flipped cards
            $_SESSION['flipped'] = [];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Endangered Animals Memory Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(4, 120px);
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
        .card {
            width: 120px;
            height: 120px;
            background-color: #4CAF50;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
        }
        .card img {
            max-width: 100%;
            max-height: 100%;
        }
        .matched {
            background-color: #888888;
            cursor: default;
        }
        .actions {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Memory Game: Endangered Animals</h1>
    <p>Match the names with their pictures!</p>
    <form method="post">
        <div class="grid">
            <?php foreach ($_SESSION['cards'] as $index => $card): ?>
                <button type="submit" name="card_index" value="<?= $index ?>"
                        class="card <?= in_array($index, $_SESSION['matched']) ? 'matched' : '' ?>"
                        <?= in_array($index, $_SESSION['matched']) ? 'disabled' : '' ?>>
                    <?php if (in_array($index, $_SESSION['flipped']) || in_array($index, $_SESSION['matched'])): ?>
                        <?php if (str_ends_with($card, '.jpg')): ?>
                            <img src="images/<?= $card ?>" alt="Image of <?= $card ?>">
                        <?php else: ?>
                            <?= $card ?>
                        <?php endif; ?>
                    <?php else: ?>
                        ?
                    <?php endif; ?>
                </button>
            <?php endforeach; ?>
        </div>
        <div class="actions">
            <button type="submit" name="reset">Reset Game</button>
        </div>
    </form>
    <?php if (count($_SESSION['matched']) === count($_SESSION['cards'])): ?>
        <h2>Congratulations! You matched all the animals!</h2>
        <form method="post">
            <button type="submit" name="reset">Restart Game</button>
        </form>
        <?php session_destroy(); ?>
    <?php endif; ?>
</body>
</html>
