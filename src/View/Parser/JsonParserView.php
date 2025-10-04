<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\View\Parser;

use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\View\RenderableInterface;

class JsonParserView implements RenderableInterface
{
    public function __construct(
        private readonly ?Node $tree,
    ) {
    }

    /**
     * Render the node tree in JSON.
     * 
     * @return string
     */
    public function render(): string
    {
	$result = "{\n\"success\": false,\n\"error\": \"An error occurred while rendering JSON\"\n}";

	if (null !== $this->tree) {
	    $encodedResult = json_encode(
                value: $this->tree->toArray(),
                flags: JSON_PRETTY_PRINT
	    );

	    if (false !== $encodedResult) {
	        $result = $encodedResult; 
	    }
	}

	return $result;
    }
}
