<?php

namespace Manager;

class User
{
    public const limit = 10;

    /**
     * Возвращает пользователей старше заданного возраста.
     * @param int $ageFrom
     * @return array
     */
    public static function getUsersOlderThan(int $ageFrom): array
    {
        return \Gateway\User::getUsersOlderThan($ageFrom);
    }

    /**
     * Возвращает пользователей по списку имен.
     * @return array
     */
    public function getUsersByNames(): array
    {
        $users = [];
        foreach (json_decode($_GET['names']) as $name) {
            $users[] = \Gateway\User::getUserByName($name);
        }

        return $users;
    }

    /**
     * Добавляет пользователей в базу данных.
     * @param $users
     * @return array
     * @throws \Exception
     */
    public static function addUsers($users): array
    {
        $ids = [];
        foreach ($users as $user) {
            try {
                \Gateway\User::getInstance()->beginTransaction();
                \Gateway\User::add($user['name'], $user['lastName'], $user['age']);
                \Gateway\User::getInstance()->commit();
                $ids[] = \Gateway\User::getInstance()->lastInsertId();
            } catch (\Exception $e) {
                \Gateway\User::getInstance()->rollBack();
                throw $e;
            }
        }

        return $ids;
    }
}
