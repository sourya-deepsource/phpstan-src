<?php

namespace Bug2885;

class Test
{
    /**
     * @return static
     */
    public function do()
    {
        return $this->do()->do();
    }
}
