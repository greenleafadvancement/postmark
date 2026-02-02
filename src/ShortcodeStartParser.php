<?php

namespace dirtsimple\Postmark;

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;

/**
 * Block start parser for WordPress shortcodes.
 * Detects lines that are entirely composed of shortcodes like [shortcode] or [[shortcode]].
 */
class ShortcodeStartParser implements BlockStartParserInterface
{
    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        if ($cursor->getNextNonSpaceCharacter() !== '[') {
            return BlockStart::none();
        }

        $savedState = $cursor->saveState();
        $cursor->advanceToNextNonSpaceOrTab();
        $line = $cursor->getRemainder();

        // Match lines that consist entirely of shortcodes
        if (preg_match('/^(\[[^\[\]]+\]|\[\[[^\[\]]+\]\])+$/', $line)) {
            return BlockStart::of(new ShortcodeBlockParser($line))->at($cursor);
        }

        $cursor->restoreState($savedState);
        return BlockStart::none();
    }
}
