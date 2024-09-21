<?php
namespace App;
use PDO;
use InvalidArgumentException;
class user
{
    private $name;
    private $username;
    private $password;
    public function __construct($name, $username, $password)
    {
        $this->name = $name;
        $this->username = $username;
        $this->password = $password;
    }
    public static function getUsers(PDO $pdo)
    {
        $stmt = $pdo->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getUserByLogin(PDO $pdo, $username)
    {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        if (!$stmt->execute(['username' => $username])) {
            print_r($stmt->errorInfo());
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function addUser(PDO $pdo, user $user)
    {
        $stmt = $pdo->prepare("INSERT INTO users (name, username, password) VALUES (:name, :username, :password)");
        $stmt->execute([
            'name' => $user->name,
            'username' => $user->username,
            'password' => $user->password
        ]);
    }
    public function verifyPassword($password)
    {
        return password_verify($password, $this->password);
    }
    public static function fromArray(PDO $pdo, array $data)
    {
        if (!isset($data['name'], $data['username'], $data['password'])) {
            throw new InvalidArgumentException('Missing required keys in array');
        }
        return new self($data['name'], $data['username'], $data['password']);
    }
    public function getPassword()
    {
        return $this->password;
    }
}

