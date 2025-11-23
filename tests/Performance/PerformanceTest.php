<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Performance;

use DocparserPhp\Model\Core\Parser\ParserComponentFactory;
use DocparserPhp\Model\Core\Validator\ValidatorComponentFactory;
use PHPUnit\Framework\TestCase;

class PerformanceTest extends TestCase
{
    public function test_large_realistic_html_parsing(): void
    {
        // Generate large realistic HTML (~10k tag)
        $content = '';
        for ($i = 1; $i <= 1000; $i++) {
            $content .= "<h1>Heading {$i}</h1>";
            $content .= "<p>Paragraph {$i}</p>";
            $content .= "<ul>";
            for ($j = 1; $j <= 5; $j++) {
                $content .= "<li>List item {$i}-{$j}</li>";
            }
            $content .= "</ul>";
            $content .= "<div><p>Nested paragraph {$i}</p></div>";
        }

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <title>Performance Test</title>
            </head>
            <body>
                {$content}
            </body>
        </html>
        HTML;

        $startTime = microtime(as_float: true);

        // Validation
        $validatorComponent = ValidatorComponentFactory::getValidatorComponent(
            context: $html,
            inputType: 'html'
        );

        $validationResult = $validatorComponent->run();

        // Parsing
        $parserComponent = ParserComponentFactory::getParserComponent(
            context: $html,
            inputType: 'html'
        );

        $parsingResult = $parserComponent->run();

        $endTime = microtime(as_float: true);

        // Average duration observed ~0.05s
        $duration = $endTime - $startTime;

        // Validation and parsing completed
        $this->assertNotNull($parsingResult);

        $this->assertTrue($validationResult->isValid());
        $this->assertGreaterThan(
            0,
            count(value: $parsingResult->getChildren())
        );

        // Check execution time
        $this->assertLessThan(
            2,
            $duration,
        );
    }
}
