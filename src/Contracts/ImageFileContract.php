<?php

namespace FileProcessor\Contracts;

interface ImageFileContract
{
    /**
     * Path without leading or trailing slashes
     *
     * @return string
     */
    public function path(): string;

    /**
     * @return string
     */
    public function extension(): string;

    /**
     * @return string
     */
    public function mimeType(): string;

    /**
     * @return string
     */
    public function secureUrl(): string;

    /**
     * @return string
     */
    public function insecureUrl(): string;
}
