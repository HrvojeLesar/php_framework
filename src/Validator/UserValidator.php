<?php

namespace Hrvoje\PhpFramework\Validator;

use Hrvoje\PhpFramework\Exceptions\ValidationException;

class UserValidator
{
    /**
     * @throws ValidationException
     * @return void
     * @param array $formData
     */
    public static function validate(array $formData): void
    {
        static::validateString($formData, 'first_name');
        static::validateString($formData, 'last_name');
    }

    /**
     * @throws ValidationException
     * @return void
     * @param array $formData
     */
    protected static function checkRequiredFields(array $formData): void
    {
        if (! isset($formData['first_name'])) {
            throw new ValidationException('first_name must be set!');
        }
        if (! isset($formData['last_name'])) {
            throw new ValidationException('last_name must be set!');
        }
        if (! isset($formData['dob'])) {
            throw new ValidationException('dob must be set!');
        }
    }

    /**
     * @throws ValidationException
     * @return void
     * @param array $formData
     */
    protected static function validateString(array $formData, string $formValueKey): void
    {
        $value = $formData[$formValueKey];
        if (strlen($value) < 2) {
            throw new ValidationException("$formValueKey must be at least 2 characters long!");
        }
    }
}
