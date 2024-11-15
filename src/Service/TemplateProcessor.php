<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class TemplateProcessor
{
    private $logger;

    public function __construct(LoggerInterface $templateProcessorLogger)
    {
        $this->logger = $templateProcessorLogger;
    }
    public function processTemplate(string $template, array $customerData): string
    {
        //$this->logger->debug('Processing template', ['customerData' => $customerData]);

        // Przetwarzanie warunków
        $template = $this->processConditionals($template, $customerData);
        //$this->logger->debug('Template after processing conditionals', ['template' => $template]);

        // Podstawianie zmiennych
        $result = preg_replace_callback('/\$\$(.*?)\$\$/', function($matches) use ($customerData) {
            $key = $matches[1];
            //$this->logger->debug('Replacing key', ['key' => $key, 'value' => ($customerData[$key] ?? 'NOT FOUND')]);
            return $customerData[$key] ?? $matches[0];
        }, $template);

        //$this->logger->debug('Final processed template', ['result' => $result]);
        return $result;
    }

    private function processConditionals(string $template, array $customerData): string
    {
        return preg_replace_callback('/\$\$if:(.*?)\$\$(.*?)(?:\$\$else\$\$(.*?))?\$\$endif\$\$/s', function($matches) use ($customerData) {
            $condition = $matches[1];
            $trueContent = $matches[2];
            $falseContent = $matches[3] ?? '';

//            $this->logger->debug('Processing condition', [
//                'condition' => $condition,
//                'trueContent' => $trueContent,
//                'falseContent' => $falseContent
//            ]);

            list($key, $operator, $value) = $this->parseCondition($condition);

            if (!isset($customerData[$key])) {
                //$this->logger->debug('Key not found in customer data', ['key' => $key]);
                return $falseContent;
            }

            $customerValue = $customerData[$key];
            $conditionMet = $this->evaluateCondition($customerValue, $operator, $value);

//            $this->logger->debug('Condition evaluation', [
//                'key' => $key,
//                'customerValue' => $customerValue,
//                'operator' => $operator,
//                'value' => $value,
//                'conditionMet' => $conditionMet
//            ]);

            return $conditionMet ? $trueContent : $falseContent;
        }, $template);
    }

    private function replaceVariables(string $template, array $customerData): string
    {
        return preg_replace_callback('/\$\$(.*?)\$\$/', function($matches) use ($customerData) {
            $key = $matches[1];
            return $customerData[$key] ?? $matches[0];
        }, $template);
    }

    private function parseCondition(string $condition): array
    {
        if (strpos($condition, '=') !== false) {
            list($key, $value) = explode('=', $condition, 2);
            return [trim($key), '=', trim($value)];
        } elseif (strpos($condition, '!=') !== false) {
            list($key, $value) = explode('!=', $condition, 2);
            return [trim($key), '!=', trim($value)];
        }
        return [trim($condition), '=', 'true'];
    }

    private function evaluateCondition($customerValue, string $operator, $value): bool
    {
        switch ($operator) {
            case '=':
                return $customerValue == $value;
            case '!=':
                return $customerValue != $value;
        }
        return false;
    }
}