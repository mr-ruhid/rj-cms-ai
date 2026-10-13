<?php

namespace App\Widgets\Contracts;

interface WidgetContract
{
    public function id(): string;

    public function name(): string;

    public function icon(): string;

    public function category(): string;

    public function description(): string;

    public function schema(): array;

    public function defaults(): array;

    public function render(array $settings): string;
}