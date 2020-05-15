<?php

declare(strict_types = 1);

namespace BrighteCapital\EncryptedJson;

use Ambta\DoctrineEncryptBundle\Encryptors\EncryptorInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class EncryptedJsonType extends \Doctrine\DBAL\Types\JsonType
{

    /**
     * Appended to end of encrypted value
     */
    protected const ENCRYPTION_MARKER = '<ENC>';

    /** @var \Ambta\DoctrineEncryptBundle\Encryptors\EncryptorInterface */
    protected $encryptor;

    public function setEncryptor(EncryptorInterface $encryptor): void
    {
        $this->encryptor = $encryptor;
    }

    //phpcs:disable SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
    //phpcs:disable SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint

    /**
     * @return string name of column type
     */
    public function getName()
    {
        return 'json_encrypted';
    }

    /**
     * Field type should not be native json because it is encrypted.
     *
     * @param string[] $fieldDeclaration
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * Field type always needs comment because it is encrypted.
     *
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return bool
     */
    public function requiresSqlCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    /**
     * @param string $value
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $value = parent::convertToDatabaseValue($value, $platform);
        $value = $this->encrypt((string) $value);

        return $value;
    }

    /**
     * @param string $value
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return string
     */
    public function convertToPhpValue($value, AbstractPlatform $platform)
    {
        $value = $this->decrypt((string) $value);
        $value = parent::convertToPHPValue($value, $platform);

        return $value;
    }

    private function encrypt(string $value): string
    {
        return $this->encryptor->encrypt($value) . self::ENCRYPTION_MARKER;
    }

    private function decrypt(string $value): string
    {
        if (substr($value, -strlen(self::ENCRYPTION_MARKER)) !== self::ENCRYPTION_MARKER) {
            return $value;
        }

        return $this->encryptor->decrypt(substr($value, 0, -strlen(self::ENCRYPTION_MARKER)));
    }

}
