# Encrypted JSON Type

A custom type for storing json arrays as encrypted strings using mysql.

Depends on michaeldegroot/doctrine-encrypt-bundle for encryptors, interface.

### Authors
Chris Young <chris.young@brighte.com.au>

## Installation

composer require michaeldegroot/doctrine-encrypt-bundle

```
\Doctrine\DBAL\Types\Type::addType('json_encrypted', EncryptedJsonType::class);
\Doctrine\DBAL\Types\Type::getType('json_encrypted', EncryptedJsonType::class)->setEncryptor(new HalineEncryptor());
```

## Contributing
- Never commit directly to master. Always make pull requests to the Authors
- This repository does not use git flow. There is no develop branch, only master and features. 
- PHP codesniffer linting must pass. Do not use disable annotations.
- All tests must pass. 90% code coverage is required.


