<?php

namespace CallTraitOverriddenMethods;

trait TraitA
{
    public function sameName()
    {
    }
}

trait TraitB
{
    use TraitA {
        sameName as someOtherName;
    }
    public function sameName()
    {
        $this->someOtherName();
    }
}

trait TraitC
{
    use TraitB {
        sameName as YetAnotherName;
    }
    public function sameName()
    {
        $this->YetAnotherName();
    }
}

class SomeClass
{
    use TraitC {
        sameName as wowSoManyNames;
    }

    public function sameName()
    {
        $this->wowSoManyNames();
    }
}
