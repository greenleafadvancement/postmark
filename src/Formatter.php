<?php
namespace dirtsimple\Postmark;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Inline\InlineParserInterface;

class Formatter {

	protected static $converter;

	static function format($doc, $field, $value) {
		static $converter = null;
		$converter = $converter ?: static::formatter();
		$markdown = apply_filters('postmark_markdown', $value, $doc, $field);
		$html = $converter->convert($markdown)->getContent();
		return apply_filters('postmark_html', $html, $doc, $field);
	}

	protected static function formatter() {
		$cfg = array(
			'renderer' => array(
				'block_separator' => "",
				'inner_separator' => "",
				'soft_break' => "",
			),
		);

		$extensions = array(
			AutolinkExtension::class => null,
			TableExtension::class => null,
			TaskListExtension::class => null,
			AttributesExtension::class => null,
			StrikethroughExtension::class => null,
			SmartPunctExtension::class => null,
			ShortcodeStartParser::class => null,
		);

		$env = new Environment($cfg);
		$env->addExtension(new CommonMarkCoreExtension());

		$extensions = apply_filters('postmark_formatter_extensions', $extensions, $env);
		static::addExtensions($env, $extensions);

		return new MarkdownConverter($env);
	}

	protected static function addExtensions($env, $exts) {
		foreach ($exts as $ext => $args) {
			if ( false === $args ) continue;
			$extClass = new \ReflectionClass($ext);
			static::addExtension($env, $ext, $extClass->newInstanceArgs((array) $args));
		}
	}

	protected static function addExtension($env, $name, $ext) {
		switch (true) {
		case $ext instanceof ExtensionInterface           : $env->addExtension($ext);         break;
		case $ext instanceof BlockStartParserInterface    : $env->addBlockStartParser($ext);  break;
		case $ext instanceof InlineParserInterface        : $env->addInlineParser($ext);      break;
		default: throw new \Exception(sprintf(__('Unrecognized extension type: %s', 'postmark'), $name));
		}
	}

}
