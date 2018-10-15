<?php

declare(strict_types=1);

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class SerializerContextBuilder implements SerializerContextBuilderInterface
{
    /**
     * @var \ApiPlatform\Core\Serializer\SerializerContextBuilder
     */
    private $serializerContextBuilder;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(\ApiPlatform\Core\Serializer\SerializerContextBuilder $serializerContextBuilder, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->serializerContextBuilder = $serializerContextBuilder;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->serializerContextBuilder->createFromRequest($request, $normalization, $extractedAttributes);

        if (!isset($context['groups'])) {
            $context['groups'] = [];
        }

        $isCollection = isset($extractedAttributes['collection_operation_name']);
        $baseGroup = $extractedAttributes[$isCollection ? 'collection_operation_name' : 'item_operation_name'];

        // If property is allowed on collection, it must be allowed on single item too.
        $context['groups'][] = "{$baseGroup}s";

        if (!$isCollection || !$normalization) {
            $context['groups'][] = $baseGroup;
        }

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $context['groups'] = \array_merge($context['groups'], \array_map(static function (string $group) {
                return "{$group}:admin";
            }, $context['groups']));
        }

        return $context;
    }
}
