<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Service;

class ParserService
{
    public function parseUploadedFile(array $file): array
    {
        $content = file_get_contents(filename: $file['tmp_name']);

        return $this->parseText(text: $content);
    }

    public function parseText(string $text): array
    {
        // Logica di parsing del testo (esistente)
        return ['length' => strlen($text), 'preview' => substr($text, 0, 100)];
    }

    public function parseJson(array $data): array
    {
        return ['keys' => array_keys($data), 'count' => count($data)];
    }
}
