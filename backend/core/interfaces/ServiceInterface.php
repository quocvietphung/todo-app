<?php
namespace Core\Interfaces;

interface ServiceInterface
{
    public function getAll(): array;
    public function getById(int $id): ?array;
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}