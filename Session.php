<?php

namespace Framework;

use Framework\Contracts\Traits\SingletonTrait;

class Session
{
    use SingletonTrait;

    private bool $isStarted = false;
    private bool $isExpired = false;
    private ?int $expirationDelay = 100000;
    
    private function configureSession() : void
    {
        // https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html#session-id-generation-and-verification-permissive-and-strict-session-management
        if (!ini_get('session.use_strict_mode')) {
            ini_set('session.use_strict_mode', '1');
        }
        // https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html#secure-attribute
        session_set_cookie_params([
            'lifetime' => 0,
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        //https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html#session-id-name-fingerprinting
        session_name('id');
    }

    public function start() 
    {
        $this->configureSession();
        session_start();
        if(!isset($_SESSION['expiration'])) $_SESSION['expiration'] = strtotime('now');
        
        $this->isStarted = (session_status() === PHP_SESSION_ACTIVE);
        $this->isExpired = ($_SESSION['expiration'] + $this->expirationDelay < strtotime('now'));

        if($this->isExpired) {
            session_unset();
            $this->regenerateId();
        }
    }

    public function unset() : void
    {
        session_unset();
    }

    // https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html#renew-the-session-id-after-any-privilege-level-change
    public function regenerateId()
    {
        session_regenerate_id(true);
    }

    public function getIsStarted() : bool
    {
        return $this->isStarted;
    }

    public function getIsExpired() : bool
    {
        return $this->isExpired;
    }

    public function resetExpirationDelay() : void
    {
        $_SESSION['expiration'] = $this->expirationDelay;
    }

    public function get(string $key) : mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public function set(string $key, mixed $value) : void
    {
        $_SESSION[$key] = $value;
    }
}