<?php
 
header('Access-Control-Allow-Origin: http://localhost:5173'); 
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require 'vendor/autoload.php';

use Kreait\Firebase\Factory;




$allowedOrigins = ['http://localhost:5173'];

$databaseURL = 'https://portfoliomsg-9ab2a-default-rtdb.firebaseio.com'; 

$factory = (new Factory)
    ->withServiceAccount('portfoliomsg-9ab2a-firebase-adminsdk-ns6op-7ac961f983.json')
    ->withDatabaseUri($databaseURL);

$database = $factory->createDatabase();




function storeData($inputs)
{
   
    global $database;
    $data = [
        'name' => $inputs['name'],
        'email' => $inputs['email'],
        'message' => $inputs['msg'],
        'timestamp' => time() 
    ];

    try {
        // Store the data under the "Messages" table
        $newPost = $database->getReference('Messages')->push($data);
        return ['postId' => $newPost->getKey()];
    } catch (Exception $e) {
        // Return an error message in case of any exceptions
        return ['error' => 'Error storing data: ' . $e->getMessage()];
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputs = json_decode(file_get_contents('php://input'), true);

    
    $name = isset($inputs['name']) ? $inputs['name'] : '';
    $email = isset($inputs['email']) ? $inputs['email'] : '';
    $msg = isset($inputs['msg']) ? $inputs['msg'] : '';

    // Call the function to store the data
    $result = storeData(['name' => $name, 'email' => $email, 'msg' => $msg]);

    // Return the result (postId or error) back to the frontend as JSON
    header('Content-Type: application/json');
    if (isset($result['error'])) {
        echo json_encode(['error' => $result['error']]);
    } else {
        echo json_encode(['postId' => $result['postId']]);
    }
}
