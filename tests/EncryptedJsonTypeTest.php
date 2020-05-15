<?php

declare(strict_types = 1);

namespace BrighteCapital\Tests\EncryptedJson;

use Ambta\DoctrineEncryptBundle\Encryptors\HaliteEncryptor;
use BrighteCapital\EncryptedJson\EncryptedJsonType;
use Doctrine\DBAL\Platforms\MySqlPlatform;

class EncryptedJsonTypeTest extends \PHPUnit\Framework\TestCase
{

  /** @var \Ambta\DoctrineEncryptBundle\Encryptors\EncryptorInterface */
    private $encryptor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->encryptor = new HaliteEncryptor(__DIR__ . '/var/keyfile');
    }
  /** @covers \BrighteCapital\EncryptedJson\EncryptedJsonType */
    public function test(): void
    {
        $platform = new MySqlPlatform;
        $type = new EncryptedJsonType;
        $this->assertEquals('json_encrypted', $type->getName());
        $this->assertTrue($type->requiresSQLCommentHint($platform));
        $this->assertEquals('VARCHAR(255)', $type->getSQLDeclaration([], $platform));
        $type->setEncryptor($this->encryptor);
        $value = [1, 2, 3, 4];
        $dbValue = $type->convertToDatabaseValue($value, $platform);
        $this->assertStringContainsString('<ENC>', $dbValue);
        $phpValue = $type->convertToPhpValue($dbValue, $platform);
        $this->assertEquals($value, $phpValue);
        $this->assertEquals($value, $type->convertToPhpValue('[1,2,3,4]', $platform));
    }

}
