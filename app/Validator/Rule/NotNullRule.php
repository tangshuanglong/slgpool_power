<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Validator\Rule;

use App\Annotation\Mapping\NotNull;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class NotNullRule
 *
 * @Bean(NotNull::class)
 */
class NotNullRule implements RuleInterface
{
    /**
     * @param array $data
     * @param string $propertyName
     * @param object $item
     * @param null $default
     *
     * @param bool $strict
     * @return array
     * @throws ValidatorException
     */
    public function validate(array $data, string $propertyName, $item, $default = null, $strict = false): array
    {
        $message = $item->getMessage();

        if (isset($data[$propertyName]) && $data[$propertyName] !== NULL) {
            return $data;
        }

        $message = (empty($message)) ? sprintf('%s must be a not null', $propertyName) : $message;
        throw new ValidatorException($message);
    }
}
