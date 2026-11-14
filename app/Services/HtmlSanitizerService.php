<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;

class HtmlSanitizerService
{
    /**
     * Whitelist of safe HTML tags for rich-text descriptions and content blocks.
     */
    protected static array $allowedTags = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'strike', 'sub', 'sup',
        'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote',
        'table', 'thead', 'tbody', 'tr', 'th', 'td', 'span', 'div', 'hr',
        'a', 'img', 'code', 'pre'
    ];

    /**
     * Whitelist of safe attributes by tag (or global).
     */
    protected static array $allowedAttributes = [
        'class', 'style', 'title', 'alt', 'id',
        'width', 'height', 'colspan', 'rowspan', 'border',
        'align', 'target', 'rel', 'href', 'src'
    ];

    /**
     * Sanitize HTML markup, stripping all dangerous tags, scripts, and event handlers.
     */
    public static function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        // Fast path: if there are no HTML tags, return as is
        if (!str_contains($html, '<')) {
            return $html;
        }

        // Pre-strip blacklisted tags that should never exist
        $disallowed = [
            'script', 'style', 'iframe', 'frame', 'frameset',
            'object', 'embed', 'applet', 'meta', 'link',
            'form', 'input', 'button', 'select', 'textarea',
            'svg', 'math', 'base'
        ];

        foreach ($disallowed as $tag) {
            $html = preg_replace('/<' . $tag . '\b[^>]*>(.*?)<\/' . $tag . '>/is', '', $html);
            $html = preg_replace('/<' . $tag . '\b[^>]*\/?>/is', '', $html);
        }

        // Load into DOMDocument
        $dom = new DOMDocument();
        $prev = libxml_use_internal_errors(true);

        // Prefix with UTF-8 encoding hint and wrap in root container
        $wrapped = '<?xml encoding="utf-8" ?><div id="__sanitizer_root__">' . $html . '</div>';
        $dom->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $root = $dom->getElementById('__sanitizer_root__');
        if (!$root) {
            return strip_tags($html, '<' . implode('><', self::$allowedTags) . '>');
        }

        self::cleanNode($root);

        // Extract inner HTML of root container
        $cleanHtml = '';
        foreach ($root->childNodes as $child) {
            $cleanHtml .= $dom->saveHTML($child);
        }

        return $cleanHtml;
    }

    /**
     * Recursively clean a DOM node and its children.
     */
    protected static function cleanNode(DOMNode $node): void
    {
        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($child instanceof DOMElement) {
                $tagName = strtolower($child->tagName);

                if (!in_array($tagName, self::$allowedTags, true)) {
                    while ($child->hasChildNodes()) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);
                    continue;
                }

                if ($child->hasAttributes()) {
                    $attrsToRemove = [];
                    foreach ($child->attributes as $attr) {
                        $name = strtolower($attr->name);
                        $value = trim($attr->value);

                        if (str_starts_with($name, 'on')) {
                            $attrsToRemove[] = $name;
                            continue;
                        }

                        if (!in_array($name, self::$allowedAttributes, true)) {
                            $attrsToRemove[] = $name;
                            continue;
                        }

                        if (in_array($name, ['href', 'src'], true)) {
                            $normalizedVal = preg_replace('/\s+/', '', strtolower($value));
                            if (
                                str_starts_with($normalizedVal, 'javascript:') ||
                                str_starts_with($normalizedVal, 'data:') ||
                                str_starts_with($normalizedVal, 'vbscript:')
                            ) {
                                $attrsToRemove[] = $name;
                                continue;
                            }
                        }

                        if ($name === 'style') {
                            $normalizedStyle = strtolower($value);
                            if (
                                str_contains($normalizedStyle, 'expression') ||
                                str_contains($normalizedStyle, 'javascript:') ||
                                str_contains($normalizedStyle, 'behavior') ||
                                str_contains($normalizedStyle, '-moz-binding')
                            ) {
                                $attrsToRemove[] = $name;
                                continue;
                            }
                        }

                        if ($name === 'target' && strtolower($value) === '_blank') {
                            $child->setAttribute('rel', 'noopener noreferrer');
                        }
                    }

                    foreach ($attrsToRemove as $attrName) {
                        $child->removeAttribute($attrName);
                    }
                }

                self::cleanNode($child);
            }
        }
    }

    /**
     * Sanitize SVG content, removing embedded scripts, active elements, and XML entities (mitigates XXE & XSS).
     */
    public static function sanitizeSvg(string $svg): string
    {
        // 1. Remove XML declarations, DOCTYPE and ENTITY declarations (mitigates XXE & Billion Laughs)
        $svg = preg_replace('/<!ENTITY\b[^>]*>/is', '', $svg);
        $svg = preg_replace('/<!DOCTYPE\b[^>]*>/is', '', $svg);

        // 2. Remove executable / active SVG tags
        $svg = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $svg);
        $svg = preg_replace('/<script\b[^>]*\/?>/is', '', $svg);
        $svg = preg_replace('/<foreignObject\b[^>]*>(.*?)<\/foreignObject>/is', '', $svg);
        $svg = preg_replace('/<animate\b[^>]*>(.*?)<\/animate>/is', '', $svg);
        $svg = preg_replace('/<animate\b[^>]*\/?>/is', '', $svg);
        $svg = preg_replace('/<set\b[^>]*>(.*?)<\/set>/is', '', $svg);
        $svg = preg_replace('/<set\b[^>]*\/?>/is', '', $svg);
        $svg = preg_replace('/<handler\b[^>]*>(.*?)<\/handler>/is', '', $svg);
        $svg = preg_replace('/<handler\b[^>]*\/?>/is', '', $svg);
        $svg = preg_replace('/<use\b[^>]*\/?>/is', '', $svg);
        $svg = preg_replace('/<use\b[^>]*>(.*?)<\/use>/is', '', $svg);
        $svg = preg_replace('/<style\b[^>]*>.*?javascript:.*?<\/style>/is', '', $svg);

        // 3. Strip all event handlers (onload, onclick, onerror, onmouseover, etc.)
        $svg = preg_replace('/\s+on[a-z0-9_-]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/is', '', $svg);

        // 4. Strip dangerous URI schemes from href / xlink:href (javascript:, data:, vbscript:)
        $svg = preg_replace('/(href|xlink:href)\s*=\s*("|\')\s*(javascript|data|vbscript):[^"\']*("|\')/is', '', $svg);

        return $svg;
    }
}

