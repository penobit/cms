<?php

namespace App\Utils;

/**
 * A simple HTML minifier.
 *
 * This class can be used to minify HTML content. It can remove comments,
 * whitespace, and more, depending on the specified options.
 */
class HtmlMinifier {
    private $options;
    private $output;
    private $build;
    private $skip;
    private $skipName;
    private $head;
    private $elements;

    public function __construct(array $options = []) {
        $this->options = $options;
        $this->output = '';
        $this->build = [];
        $this->skip = 0;
        $this->skipName = '';
        $this->head = false;
        $this->elements = [
            'skip' => [
                'code',
                'pre',
                'script',
                'textarea',
            ],
            'inline' => [
                'a',
                'abbr',
                'acronym',
                'b',
                'bdo',
                'big',
                'br',
                'cite',
                'code',
                'dfn',
                'em',
                'i',
                'img',
                'kbd',
                'map',
                'object',
                'samp',
                'small',
                'span',
                'strong',
                'sub',
                'sup',
                'tt',
                'var',
                'q',
            ],
            'hard' => [
                '!doctype',
                'body',
                'html',
            ],
        ];
    }

    /**
     * Run minifier.
     *
     * @param string $html the HTML to minify
     *
     * @return string the minified HTML
     */
    public function minify(string $html): string {
        if (!isset($this->options['disable_comments'])
            || !$this->options['disable_comments']) {
            $html = $this->removeComments($html);
        }

        $rest = $html;

        while (!empty($rest)) {
            $parts = explode('<', $rest, 2);
            $this->walk($parts[0]);
            $rest = (isset($parts[1])) ? $parts[1] : '';
        }

        return $this->output;
    }

    /**
     * Walk through HTML.
     *
     * @param string &$part The HTML part to walk through
     */
    private function walk(&$part) {
        $tag_parts = explode('>', $part);
        $tag_content = $tag_parts[0];

        if (!empty($tag_content)) {
            $name = $this->findName($tag_content);
            $element = $this->toElement($tag_content, $part, $name);
            $type = $this->toType($element);

            if ('head' == $name) {
                $this->head = 'open' === $type;
            }

            $this->build[] = [
                'name' => $name,
                'content' => $element,
                'type' => $type,
            ];

            $this->setSkip($name, $type);

            if (!empty($tag_content)) {
                $content = (isset($tag_parts[1])) ? $tag_parts[1] : '';
                if ('' !== $content) {
                    $this->build[] = [
                        'content' => $this->compact($content, $name, $element),
                        'type' => 'content',
                    ];
                }
            }

            $this->buildHtml();
        }
    }

    /**
     * Remove comments from HTML.
     *
     * @param string $content the HTML to remove comments from
     *
     * @return string the HTML without comments
     */
    private function removeComments(string $content = ''): string {
        return preg_replace('/(?=<!--)([\s\S]*?)-->/', '', $content);
    }

    /**
     * Check if a string contains another string.
     *
     * @param string $needle the string to search for
     * @param string $haystack the string to search in
     *
     * @return bool true if the needle is found, false otherwise
     */
    private function contains(string $needle, string $haystack): bool {
        return strpos($haystack, $needle) !== false;
    }

    /**
     * Get the type of an element.
     *
     * @param string $element the element to get the type of
     *
     * @return string the type of the element ('open' or 'close')
     */
    private function toType(string $element): string {
        return (substr($element, 1, 1) == '/') ? 'close' : 'open';
    }

    /**
     * Create an HTML element.
     *
     * @param string $element the HTML element to create
     * @param string $noll the full HTML element (including the new element)
     * @param string $name the name of the HTML element
     *
     * @return string the created HTML element
     */
    private function toElement(string $element, string $noll, string $name): string {
        $element = $this->stripWhitespace($element);
        $element = $this->addChevrons($element, $noll);
        // $element = $this->removeSelfSlash($element);

        return $this->removeMeta($element, $name);
    }

    /**
     * Remove unneeded element meta.
     *
     * @param string $element the HTML element to remove meta from
     * @param string $name the name of the HTML element
     *
     * @return string the HTML element without meta
     */
    private function removeMeta(string $element, string $name): string {
        if ('style' == $name) {
            $element = str_replace(
                [
                    ' type="text/css"',
                    "' type='text/css'",
                ],
                ['', ''],
                $element
            );
        } elseif ('script' == $name) {
            $element = str_replace(
                [
                    ' type="text/javascript"',
                    " type='text/javascript'",
                ],
                ['', ''],
                $element
            );
        }

        return $element;
    }

    /**
     * Strip whitespace from an HTML element.
     *
     * @param string $element the HTML element to strip whitespace from
     *
     * @return string the HTML element with whitespace stripped
     */
    private function stripWhitespace(string $element): string {
        if (0 == $this->skip) {
            $element = preg_replace('/\s+/', ' ', $element);
        }

        return trim($element);
    }

    /**
     * Add chevrons around an HTML element.
     *
     * @param string $element the HTML element to add chevrons around
     * @param string $noll the full HTML element (including the new element)
     *
     * @return string the HTML element with chevrons added
     */
    private function addChevrons(string $element, string $noll): string {
        if (empty($element)) {
            return $element;
        }
        $char = ($this->contains('>', $noll)) ? '>' : '';

        return '<'.$element.$char;
    }

    /**
     * Remove unneeded self slash.
     *
     * @param string $element the HTML element to remove self slash from
     *
     * @return string the HTML element with self slash removed
     */
    private function removeSelfSlash(string $element): string {
        if (substr($element, -3) == ' />') {
            $element = substr($element, 0, -3).'>';
        }

        return $element;
    }

    /**
     * Compact content.
     *
     * @param string $content the HTML content to compact
     * @param string $name the name of the HTML element
     * @param string $element the HTML element to compact
     *
     * @return string the compacted HTML content
     */
    private function compact(string $content, string $name, string $element): string {
        if (0 != $this->skip) {
            $name = $this->skipName;
        } else {
            $content = preg_replace('/\s+/', ' ', $content);
        }

        if (in_array($name, $this->elements['skip'])) {
            return $content;
        }
        if (in_array($name, $this->elements['hard']) || $this->head) {
            return $this->minifyHard($content);
        }

        return $this->minifyKeepSpaces($content);
    }

    /**
     * Build html.
     *
     * Remove comments and whitespace from the build array and set output.
     */
    private function buildHtml(): void {
        foreach ($this->build as $build) {
            if (!empty($this->options['collapse_whitespace'])) {
                if (strlen(trim($build['content'])) == 0) {
                    continue;
                }

                if ('content' != $build['type'] && !in_array($build['name'], $this->elements['inline'])) {
                    trim($build['content']);
                }
            }

            $this->output .= $build['content'];
        }

        $this->build = [];
    }

    /**
     * Find name by part.
     *
     * Extract the name from a part of HTML element.
     *
     * @param string $part the HTML part to extract name from
     *
     * @return string the extracted name of the HTML element
     */
    private function findName(string $part): string {
        $name_cut = explode(' ', $part, 2)[0];
        $name_cut = explode('>', $name_cut, 2)[0];
        $name_cut = explode("\n", $name_cut, 2)[0];
        $name_cut = preg_replace('/\s+/', '', $name_cut);

        return strtolower(str_replace('/', '', $name_cut));
    }

    /**
     * Set skip if elements are blocked from minification.
     *
     * Set skip counter if elements are blocked from minification.
     *
     * @param string $name the name of the HTML element
     * @param string $type the type of the HTML element ('open' or 'close')
     */
    private function setSkip(string $name, string $type): void {
        foreach ($this->elements['skip'] as $element) {
            if ($element == $name && 0 == $this->skip) {
                $this->skipName = $name;
            }
        }
        if (in_array($name, $this->elements['skip'])) {
            if ('open' == $type) {
                ++$this->skip;
            }
            if ('close' == $type) {
                --$this->skip;
            }
        }
    }

    /**
     * Minify all, even spaces between elements.
     *
     * Minify HTML content and remove spaces between elements.
     *
     * @param string $element the HTML content to minify
     *
     * @return string the minified HTML content
     */
    private function minifyHard(string $element): string {
        $element = preg_replace('!\s+!', ' ', $element);
        $element = trim($element);

        return trim($element);
    }

    /**
     * Strip but keep one space.
     *
     * Strip HTML content but keep one space between elements.
     *
     * @param string $element the HTML content to strip but keep one space between elements
     *
     * @return string the stripped HTML content
     */
    private function minifyKeepSpaces(string $element): string {
        return preg_replace('!\s+!', ' ', $element);
    }
}