<?php

namespace Gateway;

use PDO;
use PDOException;

class User
{
    /**
     * @var PDO
     */
    private static PDO $instance;

    /**
     * Реализация singleton
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (is_null(self::$instance)) {
            $dsn = 'mysql:dbname=db;host=127.0.0.1';
            $user = 'dbuser';
            $password = 'dbpass';
            try {
                self::$instance = new PDO($dsn, $user, $password);
            } catch(PDOException $pe) {
                echo $pe->getMessage();
            }
        }

        return self::$instance;
    }

    /**
     * Возвращает список пользователей старше заданного возраста.
     * @param int $ageFrom
     * @return array
     */
    public static function getUsersOlderThan(int $ageFrom): array
    {
        $stmt = self::getInstance()->prepare("SELECT id, name, lastName, `from`, age, settings FROM Users WHERE age > {$ageFrom} LIMIT " . \Manager\User::limit);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($rows as $row) {
            $settings = json_decode($row['settings']);
            $users[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'lastName' => $row['lastName'],
                'from' => $row['from'],
                'age' => $row['age'],
                'key' => $settings['key'],
            ];
        }

        return $users;
    }

    /**
     * Возвращает пользователя по имени.
     * @param string $name
     * @return array
     */
    public static function getUserByName(string $name): array
    {
        $stmt = self::getInstance()->prepare("SELECT id, name, lastName, `from`, age, settings FROM Users WHERE `name` = '{$name}'");
        $stmt->execute();
        $userByName = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'id' => $userByName['id'],
            'name' => $userByName['name'],
            'lastName' => $userByName['lastName'],
            'from' => $userByName['from'],
            'age' => $userByName['age'],
        ];
    }

    /**
     * Добавляет пользователя в базу данных.
     * @param string $name
     * @param string $lastName
     * @param int $age
     * @return int
     */
    public static function add(string $name, string $lastName, int $age): int
    {
        $sth = self::getInstance()->prepare("INSERT INTO Users (name, lastName, age) VALUES (:`name`, :lastName, :age)");
        $sth->execute([':name' => $name, ':lastName' => $lastName, ':age' => $age]);

        return self::getInstance()->lastInsertId();
    }
}
