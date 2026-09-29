<?php
$xmlFile = 'books.xml';
$xmlData = null;
$errorMessage = '';

// Check if the XML file exists before loading
if (file_exists($xmlFile)) {
    // Load XML file
    $xmlData = simplexml_load_file($xmlFile);
    if ($xmlData === false) {
        $errorMessage = "Failed to parse the XML file.";
    }
} else {
    $errorMessage = "The XML file '$xmlFile' was not found.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Catalog (XML Reader)</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #f4f7f6; 
            margin: 0; 
            padding: 40px; 
        }
        .container { 
            max-width: 850px; 
            margin: auto; 
            background: #fff; 
            padding: 25px; 
            border-radius: 10px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
        }
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        h2 { 
            color: #0d6efd; 
            margin: 0;
        }
        .btn-add {
            background: #0d6efd;
            color: #fff;
            padding: 8px 14px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }
        .btn-add:hover {
            background: #0b5ed7;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        th, td { 
            padding: 12px 15px; 
            border: 1px solid #ddd; 
            text-align: left; 
        }
        th { 
            background-color: #f8f9fa; 
            color: #333; 
        }
        tr:nth-child(even) { 
            background-color: #f9f9f9; 
        }
        .error { 
            color: #dc3545; 
            background: #f8d7da; 
            padding: 10px; 
            border-radius: 5px; 
            text-align: center; 
        }
    </style>
</head>
<body>

  <div class="container">
    <div class="header-flex">
        <h2>📚 Book Catalog</h2>
        <a href="add_book.php" class="btn-add">+ Add New Book</a>
    </div>

    <?php if (!empty($errorMessage)): ?>
        <div class="error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Year</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($xmlData->book as $book): ?>
                <tr>
                    <td><?php echo htmlspecialchars($book['id']); ?></td>
                    <td><?php echo htmlspecialchars($book->title); ?></td>
                    <td><?php echo htmlspecialchars($book->author); ?></td>
                    <td><?php echo htmlspecialchars($book->year); ?></td>
                    <td>$<?php echo number_format((float)$book->price, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
  </div>

</body>
</html>