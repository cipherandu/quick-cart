<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require '../src/vendor/autoload.php';

$app = new \Slim\App;

// POST /register endpoint to register a new user
$app->post('/register', function ($request, $response, $args) {

    $data = json_decode($request->getBody());

    $fname = $data->first_name;
    $lname = $data->last_name;
    $email = $data->email;
    $username = $data->username;
    $password = md5($data->password); // Hash password using md5
    $account_type_id = $data->account_type_id;

    $servername = "localhost";
    $dbusername = "root"; 
    $dbpassword = "";     
    $dbname = "quick_cart_db"; 

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "INSERT INTO users (first_name, last_name, email, username, password, account_type_id)
                VALUES ('$fname', '$lname', '$email', '$username', '$password', '$account_type_id')";
        $conn->exec($sql);
        $response->getBody()->write(json_encode(array("status" => "success", "data" => null)));
    } catch(PDOException $e) {
        $response->getBody()->write(json_encode(array("status" => "error", "message" => $e->getMessage())));
    }
    $conn = null;
    return $response;
});

// POST /login endpoint to login a new user
$app->post('/login', function ($request, $response, $args) {

    $data = json_decode($request->getBody());

    $username = $data->username;
    $password = $data->password;

    $servername = "localhost";
    $dbusername = "root"; 
    $dbpassword = "";     
    $dbname = "quick_cart_db"; 

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // var_dump($user['password']);
        // exit;

        // Check if the user exists and verify the password
        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['username'] = $user['username'];
            $response->getBody()->write(json_encode(["status" => "success", "message" => "Login successful!"]));
        }
        else {
            $response->getBody()->write(json_encode(["status" => "error", "message" => "Login failed. Incorrect username or password."]));
        }
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(["status" => "error", "message" => "Database connection failed: " . $e->getMessage()]));
    }

    $conn = null;
    return $response;
});

$app->run();