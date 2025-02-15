<?php
/* 'dosage_and_administration' => ' Paracetamol is a commonly used medicine that can help treat pain and reduce a high temperature fever Its typicallyIndications and Usage' => 'Paracetamol is a medicine */$apiEndpoint = 'https://api.fda.gov/drug/label.json';
 
$searchQuery = '';
$searchResults = [];



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $searchQuery = strtolower($_POST['search_query']);

    if (isset($ [$searchQuery])) {
     
        $result = $[$searchQuery];
        displayMedicineInformation($result);
    } else {        
        $queryParameters = [
            'limit' => 1,
            'search' => $searchQuery,
        ];
        $apiUrl = $apiEndpoint . '?' . http_build_query($queryParameters);
        $response = file_get_contents($apiUrl);  
        if ($response !== false) {          
            $searchResults = json_decode($response, true);

         
            if ($searchResults === null && json_last_error() !== JSON_ERROR_NONE) {
                die('Error decoding OpenFDA JSON data: ' . json_last_error_msg());
            }
        } else {
            die('Error accessing OpenFDA API');
        }
        if (!empty($searchResults['results'])) {
            echo '<div class="search-results">';
            echo '<h3>Search Results:</h3>';
            
            foreach ($searchResults['results'] as $result) {
                echo '<div class="result-box">';
                displayMedicineInformation($result);
                echo '</div>';
                echo '<br>';
            }

            echo '</div>';
        } else {
            echo '<p>No results found for the given search query.</p>';
        }
    }
}

// output result on screen
function displayMedicineInformation($result) {
    echo '<div class="search-results">';
    echo '<h3>Medicine Information:</h3>';
    echo '<strong>Indications and Usage:</strong> ' . $result['indications_and_usage'][0] . '<br>';

   
    if (isset($result['warnings'])) {
        echo '<strong>Warnings:</strong> ' . (is_array($result['warnings']) ? implode(", ", $result['warnings']) : $result['warnings']) . '<br>';
    } else {
        echo '<strong>Warnings:</strong> N/A<br>';
    }

   
    $additionalFields = [
        'keep_out_of_reach_of_children',
        'dosage_and_administration',
        'storage_and_handling',
        'package_label_principal_display_panel'
    ];
    foreach ($additionalFields as $field) {
        echo '<strong>' . ucwords(str_replace('_', ' ', $field)) . ':</strong> ' . ($result[$field][0] ?? 'N/A') . '<br>';
    }
    echo '</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenFDA Search</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-image: url('./photo/c.jpg');
            background-size: cover;
            background-position: center;
            color: #fff;
        }

        h2 {
            text-align: center;
            padding: 20px;
        }

        form {
            text-align: center;
            margin-top: 20px;
        }

        label {
            font-size: 18px;
            color: #fff;
        }

        input {
            padding: 10px;
            font-size: 16px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #3498db;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        .search-results {
            text-align: center;
            margin-top: 20px;
        }

        .result-box {
            border: 1px solid #3498db;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .search-results strong {
            font-size: 16px;
            color: #3498db;
        }
        .navbar{
    background-color: #393E46;
    border-radius: 30px;
}
</style>
</head>
<body>
    <h2>Information About Drugs & Medicines</h2>
    <form action="" method="post">
        <label for="search_query">Enter Search Query:</label>
        <input type="text" id="search_query" name="search_query" value="<?php echo htmlspecialchars($searchQuery); ?>" required>
        <button type="submit">Search</button>
    </form>
</body>
</html>
