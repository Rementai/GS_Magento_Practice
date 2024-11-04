<?php
namespace Training\Myapi\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\LocalizedException;
use Magento\Authorization\Model\UserContextInterface;

class CustomMessage implements ResolverInterface
{
    private $userContext;

    public function __construct(
        UserContextInterface $userContext
    ) {
        $this->userContext = $userContext;
    }

    public function resolve(
        $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if ($this->userContext->getUserType() != UserContextInterface::USER_TYPE_CUSTOMER) {
            throw new LocalizedException(__('You must be logged in to access this resource.'));
        }

        return [
            'message' => 'Hello, authenticated user!'
        ];
    }
}
