<?php
namespace Pleraque;

final class Password
{
    private $enc;
    private static $minPasswordLength = 8;
    private static $hashCost = 10;

    private function __construct(string $enc)
    {
        $this->enc = $enc;
    }

    public static function fromPlaintext(string $plain) : self
    {
        if(strlen($plain) < self::$minPasswordLength)
            throw new RestException(StatusCodes::BAD_REQUEST,
                                    "password must be at least " .
                                    self::$minPasswordLength .
                                    " characters long");

        return new self(password_hash($plain,
                                      \PASSWORD_DEFAULT,
                                      ["cost" => self::$hashCost]));
    }

    public static function setMinPasswordLength(int $length) : void
    {
        self::$minPasswordLength = $length;
    }

    public static function setHashCost(int $cost) : void
    {
        self::$hashCost = $cost;
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
