<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\User;
use App\Entity\WineGame;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $checker)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($resourceClass, $queryBuilder);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($resourceClass, $queryBuilder);
    }

    private function addWhere(string $resourceClass, QueryBuilder $queryBuilder)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof User && WineGame::class === $resourceClass) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder
                ->leftJoin("$rootAlias.user", "wg_user")
                ->andWhere("wg_user.id = :current_user");
            $queryBuilder->setParameter('current_user', $user->getId());}
    }
}