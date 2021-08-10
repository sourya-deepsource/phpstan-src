<?php

namespace ConstantConditionNotPhpDoc;

class BooleanAnd
{
    /**
     * @param string|\DateTimeInterface $time
     * @return string
     */
    public function doFoo($time): void
    {
        if (!\is_string($time) && (!$time instanceof \DateTimeInterface)) {
        }
    }

    /**
     * @param object $object
     */
    public function doBar(self $self, $object): void
    {
        if ($self && rand(0, 1)) {
        }
        if ($object && rand(0, 1)) {
        }
        if (rand(0, 1) && $self) {
        }
        if (rand(0, 1) && $object) {
        }
    }
}
