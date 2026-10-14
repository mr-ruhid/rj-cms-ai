<?php

namespace App\DynamicTags\Contracts;

interface TagContract
{
    public function id(): string;

    public function name(): string;

    public function category(): string;

    public function icon(): string;

    public function description(): string;

    public function resolve(array $options = []): string;

    public function options(): array;
}