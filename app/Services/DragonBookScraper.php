<?php

namespace App\Services;

use Illuminate\Support\Str;

class DragonBookScraper
{
    public function fetch(string $parameter): array
    {
        $page = $this->normalizeParameter($parameter);
        $url = 'https://dragoncity.fandom.com/api.php?action=parse&format=json&page=' . urlencode('Dragon_Book/' . $page);

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36\r\n",
                'timeout' => 30,
                'ignore_errors' => true,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false || trim($response) === '') {
            return [];
        }

        $payload = json_decode($response, true);

        if (!is_array($payload)) {
            return [];
        }

        $html = $payload['parse']['text']['*'] ?? '';

        if (!is_string($html) || trim($html) === '') {
            return [];
        }

        return $this->parseDragonData($html);
    }

    public function parseDragonData(string $html): array
    {
        $rows = [];
        $html = html_entity_decode($html, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $table = $xpath->query('//table[@id="tpt-1"]')->item(0);

        if ($table === null) {
            $table = $xpath->query('//table[contains(@class, "wikitable") or contains(@class, "mw-datatable")]')->item(0);
        }

        if ($table === null) {
            return $rows;
        }

        $rowsNode = $xpath->query('./tbody/tr', $table);
        $headerMap = $this->buildHeaderMap($xpath, $table);

        foreach ($rowsNode as $row) {
            if ($xpath->query('./th', $row)->length > 0) {
                continue;
            }

            $cells = $xpath->query('./td', $row);

            if ($cells->length < 2) {
                continue;
            }

            $numberCell = null;
            $nameNode = null;

            foreach ($cells as $cell) {
                $text = trim($this->getTextContent($cell));

                if ($numberCell === null && preg_match('/^\d+$/', $text)) {
                    $numberCell = $cell;
                }

                if ($nameNode === null && $xpath->query('.//a[@href]', $cell)->length > 0) {
                    $nameNode = $cell;
                }
            }

            if ($numberCell === null || $nameNode === null) {
                continue;
            }

            $number = trim($this->getTextContent($numberCell));
            $anchor = $xpath->query('.//a[@href]', $nameNode)->item(0);

            if ($anchor === null || $nameNode->nodeName === 'th') {
                continue;
            }

            $link = $anchor->getAttribute('href');
            $name = trim($anchor->textContent);
            if ($name === '') {
                $name = trim($anchor->getAttribute('title'));
            }
            $name = preg_replace('/\s+/', ' ', $name) ?: '';

            $element = $this->extractElementsFromColumn($xpath, $cells, $headerMap['element'] ?? null);
            $rarity = $this->extractFromColumn($xpath, $cells, $headerMap['rarity'] ?? null, 'rarity');

            if ($name === '' || str_contains($link, 'action=edit')) {
                continue;
            }

            if ($number === '' || !preg_match('/^\d+$/', $number)) {
                continue;
            }

            $rows[] = [
                'number' => $number,
                'name' => Str::title($name),
                'link' => $link,
                'element' => $element,
                'rarity' => $rarity,
            ];
        }

        return $rows;
    }

    private function getTextContent(\DOMNode $node): string
    {
        $text = '';

        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE || $child->nodeType === XML_CDATA_SECTION_NODE) {
                $text .= $child->nodeValue;
            }
        }

        return $text;
    }

    private function buildHeaderMap(\DOMXPath $xpath, \DOMElement $table): array
    {
        $headerMap = ['element' => null, 'rarity' => null];
        $rows = $xpath->query('./tbody/tr', $table);

        foreach ($rows as $row) {
            $headers = $xpath->query('./th', $row);

            if ($headers->length === 0) {
                continue;
            }

            foreach ($headers as $index => $header) {
                $text = $this->normalizeHeaderText($this->getTextContent($header));

                if ($text === 'element' || $text === 'type') {
                    $headerMap['element'] = $index;
                }

                if ($text === 'rarity') {
                    $headerMap['rarity'] = $index;
                }
            }

            break;
        }

        return $headerMap;
    }

    private function normalizeHeaderText(string $value): string
    {
        return strtolower(preg_replace('/[^a-z0-9]+/', '', $value));
    }

    private function extractFromColumn(\DOMXPath $xpath, \DOMNodeList $cells, ?int $index, string $type): ?string
    {
        if ($index !== null && $index < $cells->length) {
            $value = $this->extractFromCell($xpath, $cells->item($index), $type);

            if ($value !== null) {
                return $value;
            }
        }

        foreach ($cells as $cell) {
            $value = $this->extractFromCell($xpath, $cell, $type);

            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    private function extractElementsFromColumn(\DOMXPath $xpath, \DOMNodeList $cells, ?int $index): array
    {
        $elements = [];
        $targetCells = [];

        if ($index !== null && $index < $cells->length) {
            $targetCells[] = $cells->item($index);
        }

        foreach ($cells as $cell) {
            if ($cell === null) {
                continue;
            }

            $text = $this->normalizeHeaderText($this->getTextContent($cell));
            if ($text === 'element' || $text === 'type') {
                $targetCells[] = $cell;
            }
        }

        if ($targetCells === []) {
            $targetCells = iterator_to_array($cells);
        }

        foreach ($targetCells as $cell) {
            if ($cell === null) {
                continue;
            }

            $cellElements = [];
            $anchors = $xpath->query('.//a[@title]', $cell);

            foreach ($anchors as $anchor) {
                $value = trim($anchor->getAttribute('title'));

                if ($value === '' || !preg_match('/^Category:/', $value)) {
                    continue;
                }

                $normalized = $this->normalizeName($value);

                if ($normalized === '') {
                    continue;
                }

                if (!preg_match('/^(Common|Rare|Epic|Legendary|Mythical|Heroic|Mystic|Ancient|Very Rare|Breeding|Shop)$/i', $normalized)) {
                    $cellElements[] = $normalized;
                }
            }

            foreach ($cellElements as $element) {
                if (!in_array($element, $elements, true)) {
                    $elements[] = $element;
                }
            }
        }

        if ($elements !== []) {
            return $elements;
        }

        foreach ($targetCells as $cell) {
            if ($cell === null) {
                continue;
            }

            $imgs = $xpath->query('.//img', $cell);

            foreach ($imgs as $img) {
                $title = trim($img->getAttribute('title'));
                $alt = trim($img->getAttribute('alt'));
                $candidates = array_values(array_filter([$title, $alt], static fn (string $value): bool => $value !== ''));

                foreach ($candidates as $candidate) {
                    $normalized = $this->normalizeName($candidate);

                    if (str_contains($candidate, 'Type') || str_contains($candidate, 'type')) {
                        if ($normalized !== '' && !in_array($normalized, $elements, true)) {
                            $elements[] = $normalized;
                        }
                    }
                }
            }
        }

        return $elements;
    }

    private function extractFromCell(\DOMXPath $xpath, \DOMNode $cell, string $type): ?string
    {
        $img = $xpath->query('.//img', $cell)->item(0);

        if ($img === null) {
            return null;
        }

        $title = trim($img->getAttribute('title'));
        $alt = trim($img->getAttribute('alt'));
        $candidates = array_values(array_filter([$title, $alt], static fn (string $value): bool => $value !== ''));

        foreach ($candidates as $candidate) {
            $normalized = $this->normalizeName($candidate);

            if ($type === 'rarity') {
                if (preg_match('/^(Common|Rare|Epic|Legendary|Mythical|Heroic|Mystic|Ancient|Very Rare)$/i', $normalized)) {
                    return $normalized;
                }

                if (preg_match('/^Category:/', $candidate) && preg_match('/(Common|Rare|Epic|Legendary|Mythical|Heroic|Mystic|Ancient|Very Rare)/i', $normalized)) {
                    return $normalized;
                }
            }
        }

        return null;
    }

    private function normalizeName(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/^Category:/', '', $value) ?: $value;
        $value = preg_replace('/\s+/', ' ', $value);
        $value = preg_replace('/\s+Dragons$/', '', $value);
        $value = preg_replace('/\s+Type\s+Flag$/', '', $value);
        $value = preg_replace('/\s+Dragon$/', '', $value);
        $value = preg_replace('/\s+Dragons$/', '', $value);

        return trim($value);
    }

    private function normalizeParameter(string $parameter): string
    {
        $parameter = trim($parameter);

        if ($parameter === '') {
            return '0001-0100';
        }

        $clean = preg_replace('/[^0-9-]/', '', $parameter);

        if ($clean === '') {
            return '0001-0100';
        }

        if (preg_match('/^\d{4}-\d{4}$/', $clean)) {
            return $clean;
        }

        if (preg_match('/^\d{4}$/', $clean)) {
            $value = (int) $clean;
            $start = $value;
            $end = $value + 99;

            return sprintf('%04d-%04d', $start, $end);
        }

        if (preg_match('/^(\d{4})-(\d{4})$/', $clean, $matches)) {
            $start = (int) $matches[1];
            $end = (int) $matches[2];

            if ($end < $start) {
                [$start, $end] = [$end, $start];
            }

            return sprintf('%04d-%04d', $start, $end);
        }

        return '0001-0100';
    }
}
