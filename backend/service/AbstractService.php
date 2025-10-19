<?php
namespace Service;

use Core\Interfaces\ServiceInterface;
use Core\Interfaces\RepositoryInterface;
use InvalidArgumentException;

/**
 * Class AbstractService
 * ---------------------------------------------------------
 * Base class for all service-layer implementations.
 *
 * Responsibilities:
 * - Acts as a generic business layer providing reusable CRUD operations.
 * - Validates input parameters before delegating to the repository layer.
 * - Defines a clean abstraction between controllers and repositories.
 *
 * Each concrete service (e.g., TodoService) extends this class to inherit
 * standard operations and override or extend business logic where necessary.
 *
 * @package Service
 */
abstract class AbstractService implements ServiceInterface
{
    /**
     * @var RepositoryInterface The repository instance handling persistence.
     */
    protected RepositoryInterface $repo;

    /**
     * AbstractService constructor.
     *
     * @param RepositoryInterface $repo The concrete repository instance to be used.
     */
    public function __construct(RepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Retrieves all records.
     *
     * @return array A list of all entities from the repository.
     */
    public function getAll(): array
    {
        return $this->repo->findAll();
    }

    /**
     * Retrieves a single record by its ID.
     *
     * @param int $id The unique identifier of the record.
     * @return array|null The record data if found, or null if not found.
     *
     * @throws InvalidArgumentException If the provided ID is invalid.
     */
    public function getById(int $id): ?array
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid ID.');
        }

        return $this->repo->findById($id);
    }

    /**
     * Creates a new record in the repository.
     *
     * @param array $data The associative array of record data.
     * @return int The ID of the newly created record.
     *
     * @throws InvalidArgumentException If the data array is empty.
     */
    public function create(array $data): int
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Cannot create empty data.');
        }

        return $this->repo->create($data);
    }

    /**
     * Updates an existing record.
     *
     * @param int $id   The record ID to update.
     * @param array $data The updated field values.
     * @return bool True if the record was successfully updated, false otherwise.
     *
     * @throws InvalidArgumentException If the ID or data is invalid.
     */
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

    /**
     * Deletes a record by ID.
     *
     * @param int $id The record ID to delete.
     * @return bool True if the deletion was successful, false otherwise.
     *
     * @throws InvalidArgumentException If the provided ID is invalid.
     */
    public function delete(int $id): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid ID.');
        }

        return $this->repo->delete($id);
    }
}