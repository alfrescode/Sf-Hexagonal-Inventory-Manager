<?php
namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Product\Product;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\Contract\ProductRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Entity\ProductEntity;
use App\Infrastructure\Persistence\Doctrine\Mapping\ProductEntityMapper;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineProductRepository implements ProductRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function save(Product $product): void
    {
        $entity = ProductEntityMapper::toEntity($product);
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function find(ProductId $id): ?Product
    {
        $entity = $this->em->getRepository(ProductEntity::class)->find($id->value());
        return $entity ? ProductEntityMapper::toDomain($entity) : null;
    }

    public function findAll(int $page = 1, int $limit = 10): array
    {
        $repository = $this->em->getRepository(ProductEntity::class);
        
        // Calcular el offset para la paginación
        $offset = ($page - 1) * $limit;
        
        // Obtener el total de productos
        $total = $repository->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
        
        // Obtener los productos paginados
        $entities = $repository->createQueryBuilder('p')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
        
        $products = array_map([ProductEntityMapper::class, 'toDomain'], $entities);
        
        // Retornar productos y total como un array
        return [$products, $total];
    }
    
    public function delete(ProductId $id): void
    {
        $entity = $this->em->getRepository(ProductEntity::class)->find($id->value());
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
        }
    }
}