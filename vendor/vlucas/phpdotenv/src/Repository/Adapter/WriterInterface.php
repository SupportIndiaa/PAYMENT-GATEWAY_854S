<?php

declare(strict_types=1);

namespace Dotenv\Repository\Adapter;

interface WriterInterface
{
    /**
     * Write to an environment variable, if possible.
     *
     * @param non-empty-string $name
     * @param string           $value
     *
     * @return bool
     */
    public function write(string $name, string $value);

    /**
     * Deletand then environment variable, if possible.
     *
     * @param non-empty-string $name
     *
     * @return bool
     */
    public function delete(string $name);
}
