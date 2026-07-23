<?php

declare(strict_types=1);

namespace App\Parser\Service;

use DomainException;
use Psr\Log\LoggerInterface;

final class DocumentBodyFetcher
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }
    public function __invoke(string $htmlContent): string
    {
        if (!preg_match('/<script id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/is', $htmlContent, $matches)) {
            $this->logger->error('Скрипт __NEXT_DATA__ не найден на странице');
            throw new DomainException('__NEXT_DATA__ not found');
        }

        $jsonData = json_decode($matches[1], true);
        if (!is_array($jsonData)) {
            $this->logger->error('Ошибка декодирования JSON из __NEXT_DATA__');
            throw new DomainException('Invalid JSON in __NEXT_DATA__');
        }

        $documentNodes = $this->findDocumentChildren($jsonData);

        if (empty($documentNodes)) {
            $this->logger->error('Контент документа не найден внутри JSON AST');
            throw new DomainException('Document content not found in JSON');
        }
        $html = '';
        foreach ($documentNodes as $node) {
            $html .= $this->renderAstToHtml($node);
        }

        return $html;
    }

    private function findDocumentChildren(array $jsonData): array
    {
        // Перебираем все запросы в dehydratedState, чтобы найти тот, который содержит документ
        $queries = $jsonData['props']['dehydratedState']['queries'] ?? [];
        foreach ($queries as $query) {
            $body = $query['state']['data']['document']['content']['body'] ?? null;
            if (is_array($body) && isset($body['children'])) {
                return $body['children'];
            }
        }
        return [];
    }

    private function renderAstToHtml(array $node): string
    {
        $type = $node['type'] ?? 'unknown';

        // Базовый случай: если это текст, просто возвращаем его значение
        if ($type === 'text') {
            return htmlspecialchars((string)($node['options']['value'] ?? ''), ENT_QUOTES, 'UTF-8');
        }

        // ИСКЛЮЧЕНИЕ МУСОРА: Пропускаем узлы formtip (подсказки редактора на сайте),
        // чтобы они не попали в финальный Word-документ
        if ($type === 'formtip') {
            return '';
        }

        // Рекурсивно обрабатываем всех потомков текущего узла
        $innerHtml = '';
        if (isset($node['children']) && is_array($node['children'])) {
            foreach ($node['children'] as $child) {
                $innerHtml .= $this->renderAstToHtml($child);
            }
        }

        // Оборачиваем собранный текст потомков в соответствующие HTML-теги
        switch ($type) {
            case 'p':
                return "<p>{$innerHtml}</p>\n";
            case 'strong':
                return "<strong>{$innerHtml}</strong>";
            case 'list':
                return "<ul>\n{$innerHtml}</ul>\n";
            case 'li':
                return "<li>{$innerHtml}</li>\n";
            case 'table':
                return "<table border=\"1\">\n{$innerHtml}</table>\n";
            case 'thead':
                return "<thead>\n{$innerHtml}</thead>\n";
            case 'tbody':
                return "<tbody>\n{$innerHtml}</tbody>\n";
            case 'tr':
                return "<tr>\n{$innerHtml}</tr>\n";
            case 'td':
                return "<td>{$innerHtml}</td>\n";
            case 'h1':
                return "<h1>{$innerHtml}</h1>\n";
            case 'h2':
                return "<h2>{$innerHtml}</h2>\n";
            case 'h3':
                return "<h3>{$innerHtml}</h3>\n";
            default:
                return $innerHtml; // Пропускаем обертки вроде 'phrase', 'anchor', 'fill', 'linkGroup'
        }
    }
}
