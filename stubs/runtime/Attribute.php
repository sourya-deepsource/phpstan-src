<?php

if (\PHP_VERSION_ID < 80000) {
    if (class_exists('Attribute', false)) {
        return;
    }

    #[Attribute(Attribute::TARGET_CLASS)]
    class Attribute
    {
        /** @var int */
        public $flags;

        /**
         * Marks that attribute declaration is allowed only in classes.
         */
        public const TARGET_CLASS = 1;

        /**
         * Marks that attribute declaration is allowed only in functions.
         */
        public const TARGET_FUNCTION = 1 << 1;

        /**
         * Marks that attribute declaration is allowed only in class methods.
         */
        public const TARGET_METHOD = 1 << 2;

        /**
         * Marks that attribute declaration is allowed only in class properties.
         */
        public const TARGET_PROPERTY = 1 << 3;

        /**
         * Marks that attribute declaration is allowed only in class constants.
         */
        public const TARGET_CLASS_CONSTANT = 1 << 4;

        /**
         * Marks that attribute declaration is allowed only in function or method parameters.
         */
        public const TARGET_PARAMETER = 1 << 5;

        /**
         * Marks that attribute declaration is allowed anywhere.
         */
        public const TARGET_ALL = (1 << 6) - 1;

        /**
         * Notes that an attribute declaration in the same place is
         * allowed multiple times.
         */
        public const IS_REPEATABLE = 1 << 6;

        /**
         * @param int $flags A value in the form of a bitmask indicating the places
         * where attributes can be defined.
         */
        public function __construct($flags = self::TARGET_ALL)
        {
            $this->flags = $flags;
        }
    }
}
