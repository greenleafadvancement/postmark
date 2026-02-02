<?php

namespace dirtsimple\Postmark;

use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;

/**
 * Block continue parser for WordPress shortcodes.
 * Creates an HtmlBlock containing the shortcode text.
 */
class ShortcodeBlockParser extends AbstractBlockContinueParser
{
    private HtmlBlock $block;
    private string $content;

    public function __construct(string $shortcodeContent)
    {
        $this->block = new HtmlBlock(HtmlBlock::TYPE_7_MISC_ELEMENT);
        $this->content = $shortcodeContent;
    }

    public function getBlock(): HtmlBlock
    {
        return $this->block;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        // Shortcode blocks are single-line only, never continue
        return BlockContinue::none();
    }

    public function closeBlock(): void
    {
        $this->block->setLiteral($this->content);
    }
}
