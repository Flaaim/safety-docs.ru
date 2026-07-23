<?php

declare(strict_types=1);

namespace App\Parser\Service;

use DomainException;
use GuzzleHttp\ClientInterface;
use League\HTMLToMarkdown\Environment;
use League\HTMLToMarkdown\HtmlConverter;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;
use Psr\Log\LoggerInterface;
use League\HTMLToMarkdown\Converter\TableConverter;

final class DocumentAiRewriter
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly ClientInterface $client,
        private readonly LoggerInterface $logger,
        private readonly string $apiKey
    ) {
    }

    public function generateDocxFromHtml(string $htmlContent, string $targetFilePath): void
    {
        $cleanHtml = preg_replace('/<div\b[^>]*>/i', '<p>', $htmlContent);
        if ($cleanHtml === null) {
            throw new DomainException('Failed to parse HTML content');
        }
        $environment = new Environment([
            'strip_tags' => true
        ]);

        $converter = new HtmlConverter($environment);
        $converter->getEnvironment()->addConverter(new TableConverter());

        $markdownContent = $converter->convert($cleanHtml);

        if (empty(trim($markdownContent))) {
            throw new DomainException('Не удалось извлечь текст из HTML-разметки.');
        }

        $chunks = $this->chunkText($markdownContent, 1500);
        $rewrittenMarkdown = '';

        foreach ($chunks as $chunk) {
            $rewrittenChunk = $this->callLlmApi($chunk);
            if ($rewrittenChunk === null) {
                throw new DomainException('Ошибка API нейросети при обработке текста.');
            }
            $rewrittenMarkdown .= $rewrittenChunk . "\n\n";
        }

        $newPhpWord = new PhpWord();
        $newPhpWord->getDocInfo()->setCreator('safety-docs.ru');
        $newPhpWord->getDocInfo()->setCompany('safety-docs.ru');
        $newPhpWord->setDefaultFontName('Times New Roman');
        $newPhpWord->setDefaultFontSize(12);

        $newPhpWord->addTableStyle('SafetyTableStyle', [
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
            'alignment' => JcTable::CENTER
        ]);

        $section = $newPhpWord->addSection();

        $cleanText = str_replace(['```markdown', '```html', '```'], '', $rewrittenMarkdown);
        $lines = explode("\n", $cleanText);

        $inTable = false;
        $table = null;

        foreach ($lines as $line) {
            $line = trim($line);

            // Если пустая строка — значит таблица закончилась
            if ($line === '') {
                $inTable = false;
                continue;
            }

            // ОБРАБОТКА ТАБЛИЦ (строки, начинающиеся и заканчивающиеся на |)
            if (preg_match('/^\|(.*)\|$/', $line, $matches)) {
                // Игнорируем разделительные строки Markdown (типа |---|---|)
                if (preg_match('/^[\-\|\s\:]+$/', $line)) {
                    continue;
                }

                // Если это первая строка таблицы, создаем новую таблицу
                if (!$inTable) {
                    $inTable = true;
                    $table = $section->addTable('SafetyTableStyle');
                }

                if ($table === null) {
                    throw new DomainException('Table object is not initialized');
                }

                $table->addRow();
                $cells = explode('|', trim($matches[1]));

                foreach ($cells as $cellText) {
                    // Используем valign, чтобы текст в ячейке был по центру по вертикали
                    $cell = $table->addCell(null, ['valign' => 'center']);
                    $textRun = $cell->addTextRun();
                    $this->parseInlineMarkdown($textRun, trim($cellText));
                }
                continue; // Переходим к следующей строке, минуя обычный абзац
            } else {
                $inTable = false;
            }

            // Обрабатываем Заголовки (### Текст)
            if (preg_match('/^(#{1,6})\s+(.*)$/', $line, $matches)) {
                $textRun = $section->addTextRun();
                $textRun->addText(htmlspecialchars($matches[2], ENT_XML1, 'UTF-8'), ['bold' => true, 'size' => 14]);
                continue;
            }

            // Обрабатываем Маркированные списки (- Текст или * Текст)
            if (preg_match('/^[\-\*]\s+(.*)$/', $line, $matches)) {
                $textRun = $section->addTextRun();
                $textRun->addText('• ', ['bold' => true]);
                $this->parseInlineMarkdown($textRun, $matches[1]);
                continue;
            }

            // Обрабатываем Обычный абзац (включая 1.1. нумерованные списки)
            $textRun = $section->addTextRun();
            $this->parseInlineMarkdown($textRun, $line);
        }

        // 6. Сохраняем результат
        $writer = IOFactory::createWriter($newPhpWord, 'Word2007');

        $tempPath = $targetFilePath . '.tmp';
        $writer->save($tempPath);

        if (file_exists($targetFilePath)) {
            @unlink($targetFilePath);
        }
        rename($tempPath, $targetFilePath);
        @chmod($targetFilePath, 0666);
    }

    private function parseInlineMarkdown($textRun, string $text): void
    {
        $parts = explode('**', $text);
        foreach ($parts as $index => $part) {
            if ($part === '') {
                continue;
            }
            // Текст между двойными звездочками всегда имеет нечетный индекс в массиве
            $isBold = ($index % 2 !== 0);
            $textRun->addText(htmlspecialchars($part, ENT_XML1, 'UTF-8'), $isBold ? ['bold' => true] : null);
        }
    }
    private function chunkText(string $text, int $maxLength): array
    {
        $chunks = [];
        $paragraphs = explode("\n", $text);
        $currentChunk = '';

        foreach ($paragraphs as $p) {
            $p = trim($p);
            if ($p === '') {
                continue;
            }

            if (mb_strlen($currentChunk) + mb_strlen($p) > $maxLength && $currentChunk !== '') {
                $chunks[] = trim($currentChunk);
                $currentChunk = $p . "\n";
            } else {
                $currentChunk .= $p . "\n";
            }
        }

        if (trim($currentChunk) !== '') {
            $chunks[] = trim($currentChunk);
        }

        return $chunks;
    }

    private function callLlmApi(string $text): ?string
    {
        try {
            $response = $this->client->request('POST', 'https://api.proxyapi.ru/openai/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'temperature' => 0.4,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Ты — профессиональный эксперт по охране труда. Твоя задача — сделать глубокий рерайт текста, предоставленного в формате Markdown.'
                        ],
                        [
                            'role' => 'user',
                            'content' => "Перепиши следующий текст, изменив структуру предложений и синонимы, НО СТРОГО соблюдай следующие правила:\n1. СОХРАНИ всю разметку Markdown (заголовки #, списки -, нумерацию 1. 2.).\n2. СОХРАНИ таблицы строго в формате Markdown (| столбец | столбец |).\n3. СОХРАНИ все технические термины, номера законов, ГОСТов и цифры.\n4. Верни ТОЛЬКО перефразированный текст в формате Markdown без вступлений и пояснений:\n\n Если есть названия компании такие как ООО 'Альфа', ООО 'Гамма' и другие, их надо убрать из текста. Если в тексте встречаются фамилии, то их надо заменить на другие. Все даты обновить на 2026 год. Верни ТОЛЬКО перефразированный текст в формате Markdown без вступлений и пояснений:\n\n" . $text
                        ]
                    ]
                ],
                'timeout' => 60.0,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return trim($data['choices'][0]['message']['content'] ?? '');
        } catch (\Throwable $throwable) {
            $this->logger->warning('Ошибка генерации AI: ' . $throwable->getMessage());
            return null;
        }
    }
}
