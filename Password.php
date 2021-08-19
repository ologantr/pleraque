<?php
namespace Pleraque;

final class Password
{
    private $enc;
    private const MIN_PASSWORD_LENGTH = 8;

    private function __construct(string $enc)
    {
        $this->enc = $enc;
    }

    public static function fromPlaintext(string $plain) : self
    {
        if(strlen($plain) < self::MIN_PASSWORD_LENGTH)
            throw new RestException(StatusCodes::BAD_REQUEST,
                                    "password must be at least " .
                                    self::MIN_PASSWORD_LENGTH .
                                    " characters long");

        return new self(password_hash($plain, \PASSWORD_DEFAULT));
    }

    public static function fromHash(string $enc) : self
    {
        // PASSWORD_DEFAULT/BCRYPT, TODO: PASSWORD_ARGON
        if(!(new Regex("#^\$2[aby]?\$\d{1,2}\$[.\/A-Za-z0-9]{53}$#"))
           ->match($enc))
            throw new \InvalidArgumentException("invalid hash");

        return new self($enc);
    }

    public function __toString()
    {
        return $this->enc;
    }

    public function matchWithPlaintext(string $plain) : bool
    {
        return password_verify($plain, $this->enc);
    }
}
?>
