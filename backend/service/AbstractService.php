<?php
namespace Service;

use Core\Interfaces\ServiceInterface;
use Core\Interfaces\RepositoryInterface;
use InvalidArgumentException;

/**
 * 🧩 AbstractService
 * ------------------------------
 * Lớp cha cho tất cả các Service.
 * - Giúp chia sẻ logic CRUD cơ bản.
 * - Mỗi service con (TodoService, UserService, …)
 *   chỉ cần kế thừa và truyền Repository tương ứng.
 */
abstract class AbstractService implements ServiceInterface
{
    protected RepositoryInterface $repo;

    public function __construct(RepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    // ============================================================
    // 📋 ĐỌC DỮ LIỆU CHUNG
    // ============================================================

    public function getAll(): array
    {
        return $this->repo->findAll();
    }

    public function getById(int $id): ?array
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid ID.');
        }

        return $this->repo->findById($id);
    }

    // ============================================================
    // ✏️ GHI DỮ LIỆU CHUNG
    // ============================================================

    public function create(array $data): int
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Cannot create empty data.');
        }

        return $this->repo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid ID.');
        }
        if (empty($data)) {
            throw new InvalidArgumentException('Cannot update with empty data.');
        }

        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid ID.');
        }

        return $this->repo->delete($id);
    }
}