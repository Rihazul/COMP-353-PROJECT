<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Files</title>
    <style>
        body { 
            font-family: Tahoma, sans-serif; 
            background-color: #fac3da; 
            margin: 0; 
            padding: 0; 
        }
        header { 
            background-color: #9e34eb; 
            color: white; 
            padding: 20px; 
            text-align: center; 
        }
        header h1 { 
            margin: 0; 
        }
        .container { 
            width: 80%; 
            margin: auto; 
            padding: 20px; 
        }
        .file-list { 
            background-color: white; 
            border-radius: 10px; 
            box-shadow: 0px 0px 10px 0px #ccc; 
            padding: 20px; 
        }
        .file-item { 
            padding: 10px; 
            border-bottom: 1px solid #ccc; 
            text-align: left; 
            transition: background-color 0.3s; 
        }
        .file-item:last-child { 
            border-bottom: none; 
        }
        .file-item:hover { 
            background-color: #e0d4f7; 
        }
        .file-item a { 
            text-decoration: none; 
            color: #9e34eb; 
            font-weight: bold; 
        }
        .file-item a:hover { 
            color: #7a29b8; 
        }
    </style>
</head>
<body>
    <header>
        <h1>Available PHP Files</h1>
    </header>

    <div class="container">
        <div class="file-list">
            <?php
            $directory = __DIR__; // The current directory
            $files = scandir($directory); // Get all files in the directory

            $phpFiles = array_filter($files, function ($file) {
                return is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php';
            });

            if (!empty($phpFiles)): 
                foreach ($phpFiles as $file): ?>
                    <div class="file-item">
                        <a href="<?php echo htmlspecialchars($file); ?>" target="_blank">
                            <?php echo htmlspecialchars($file); ?>
                        </a>
                    </div>
                <?php endforeach; 
            else: ?>
                <p>No PHP files found in this directory.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
