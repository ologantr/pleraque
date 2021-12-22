<?php
namespace Pleraque;

final class Password implements \Stringable
{
    private string $enc;
    private static int $minPasswordLength = 8;
    private static int $hashCost = 10;

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

    // This function is to be called in a test environment
    // to test your server and set the optimal hash cost
    public static function getOptimalHashCost(float $timeTarget = 0.08) : int
    {
        $cost = 8;

        do
        {
            $cost++;
            $start = microtime(true);
            password_hash("mdUapvHP9yxA98kP", \PASSWORD_DEFAULT,
                          ["cost" => $cost]);
            $end = microtime(true);
        } while(($end - $start) < $timeTarget);

        return $cost;
    }

    public static function fromHash(string $enc) : self
    {
        return new self($enc);
    }

    public function __toString() : string
    {
        return $this->enc;
    }

    public function matchWithPlaintext(string $plain) : bool
    {
        return password_verify($plain, $this->enc);
    }
}
?>
