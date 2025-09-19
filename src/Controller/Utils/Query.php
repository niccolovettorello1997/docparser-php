<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Controller\Utils;

class Query
{
    public function __construct(
        private readonly string $context,
        private readonly string $type,
    ) {
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Build query from file or textarea.
     * 
     * @param  array $data
     * @param  array $files
     * @throws \InvalidArgumentException
     * @return ?Query
     */
    public static function getQuery(array $data, array $files): ?Query
    {
        $result = null;

        // Get type
        $type = $data['type'];

        // Get the file content if the files array is not empty
        if (!empty($files['file']['name']) && !empty($files['file']['tmp_name'])) {
            // Check if the format is valid
            $hasCorrectFormat = str_ends_with(
                haystack: basename(path: $files['file']['name']),
                needle: '.html'
            );

            if ($hasCorrectFormat) {
                $result = new Query(
                    context: file_get_contents(filename: $files['file']['tmp_name']),
                    type: $type,
                );
            }
        } else {    // Otherwise get it from form data
            // If context is empty or not set throw an exception
            if (!isset($data['context']) || empty($data['context'])) {
                throw new \InvalidArgumentException(message: 'Empty context');
            }

            $result = new Query(
                context: $data['context'],
                type: $type
            );
        }

        return $result;
    }
}
