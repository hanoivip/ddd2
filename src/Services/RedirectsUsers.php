<?php

namespace Hanoivip\Ddd2\Services;

// hanoivip: migrate 6.x to 9.x
trait RedirectsUsers
{
    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }
        
        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }
}
