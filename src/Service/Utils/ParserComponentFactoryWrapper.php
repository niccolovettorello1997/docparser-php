<?php

declare(strict_types=1);

namespace DocparserPhp\Service\Utils;

use DocparserPhp\Model\Core\Parser\ParserComponent;
use DocparserPhp\Model\Core\Parser\ParserComponentFactory;
use Symfony\Component\Yaml\Exception\ParseException;

class ParserComponentFactoryWrapper
{
    /**
     * Wrapper function needed in order to make static function testable.
     * Returns the correct instance of ParserComponent.
     *
     * @param string $context
     * @param string $inputType
     *
     * @throws \RuntimeException
     * @throws ParseException
     *
     * @return ParserComponent
     *
     * @codeCoverageIgnore
     */
    public function createParserComponent(string $context, string $inputType): ParserComponent
    {
        return ParserComponentFactory::getParserComponent(
            context: $context,
            inputType: $inputType
        );
    }
}
