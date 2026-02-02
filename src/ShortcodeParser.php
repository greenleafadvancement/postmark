<?php
/**
 * @deprecated This file is kept for backwards compatibility.
 * Use ShortcodeStartParser and ShortcodeBlockParser instead.
 */

namespace dirtsimple\Postmark;

// Alias for backwards compatibility - the actual implementation
// is now split between ShortcodeStartParser and ShortcodeBlockParser
class_alias(ShortcodeStartParser::class, ShortcodeParser::class);
