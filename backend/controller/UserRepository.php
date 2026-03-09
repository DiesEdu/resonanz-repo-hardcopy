<?php

declare(strict_types=1);

namespace App;

use PDO;

final class UserRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAllUsers(): array
    {
        $stmt = $this->pdo->query('SELECT id, name, email, role, created_at, updated_at FROM `users` ORDER BY id DESC');

        return $stmt->fetchAll();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getUserById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, email, role, created_at, updated_at FROM `users` WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return $row;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getUserByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, email, role, password_hash, created_at, updated_at FROM `users` WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => strtolower(trim($email))]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return $row;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function verifyUserCredentials(string $email, string $password): ?array
    {
        $user = $this->getUserByEmail($email);
        if ($user === null) {
            return null;
        }

        $hash = (string) ($user['password_hash'] ?? '');
        if ($hash === '' || !password_verify($password, $hash)) {
            return null;
        }

        return $this->sanitizeUser($user);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function addUser(array $data): int
    {
        $payload = [
            'name' => $data['name'] ?? null,
            'email' => strtolower(trim((string) $data['email'])),
            'role' => $data['role'] ?? 'member',
            'password_hash' => password_hash((string) $data['password'], PASSWORD_DEFAULT),
        ];

        $stmt = $this->pdo->prepare('INSERT INTO `users` (name, email, role, password_hash) VALUES (:name, :email, :role, :password_hash)');
        $stmt->execute($payload);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateUser(int $id, array $data): bool
    {
        $payload = [];

        if (array_key_exists('name', $data)) {
            $payload['name'] = $data['name'];
        }

        if (array_key_exists('email', $data)) {
            $payload['email'] = $data['email'];
        }

        if (array_key_exists('role', $data)) {
            $payload['role'] = $data['role'];
        }

        if (array_key_exists('password', $data)) {
            $payload['password_hash'] = password_hash((string) $data['password'], PASSWORD_DEFAULT);
        }

        if ($payload === []) {
            return true;
        }

        $assignments = implode(', ', array_map(
            static fn(string $field): string => sprintf('`%s` = :%s', $field, $field),
            array_keys($payload)
        ));

        $sql = sprintf('UPDATE `users` SET %s WHERE id = :id', $assignments);
        $payload['id'] = $id;

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($payload);
    }

    public function deleteUser(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM `users` WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }

    /**
     * @param array<string, mixed> $user
     * @return array<string, mixed>
     */
    private function sanitizeUser(array $user): array
    {
        unset($user['password_hash']);

        return $user;
    }
}
