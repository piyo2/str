<?php

namespace piyo2\util;

use Normalizer;

class StrFilter
{
	private const ENCODING = 'UTF-8';

	private const COMPOSITION_EXCLUSION_PATTERN = '/[^\x{0340}\x{0341}\x{0343}\x{0344}\x{0374}\x{037E}\x{0387}\x{0958}-\x{095F}\x{09DC}\x{09DD}\x{09DF}\x{0A33}\x{0A36}\x{0A59}-\x{0A5B}\x{0A5E}\x{0B5C}\x{0B5D}\x{0F43}\x{0F4D}\x{0F52}\x{0F57}\x{0F5C}\x{0F69}\x{0F73}\x{0F75}\x{0F76}\x{0F78}\x{0F81}\x{0F93}\x{0F9D}\x{0FA2}\x{0FA7}\x{0FAC}\x{0FB9}\x{1F71}\x{1F73}\x{1F75}\x{1F77}\x{1F79}\x{1F7B}\x{1F7D}\x{1FBB}\x{1FBE}\x{1FC9}\x{1FCB}\x{1FD3}\x{1FDB}\x{1FE3}\x{1FEB}\x{1FEE}\x{1FEF}\x{1FF9}\x{1FFB}\x{1FFD}\x{2000}\x{2001}\x{2126}\x{212A}\x{212B}\x{2329}\x{232A}\x{2ADC}\x{F900}-\x{FA0D}\x{FA10}\x{FA12}\x{FA15}-\x{FA1E}\x{FA20}\x{FA22}\x{FA25}\x{FA26}\x{FA2A}-\x{FA6D}\x{FA70}-\x{FAD9}\x{FB1D}\x{FB1F}\x{FB2A}-\x{FB36}\x{FB38}-\x{FB3C}\x{FB3E}\x{FB40}\x{FB41}\x{FB43}\x{FB44}\x{FB46}-\x{FB4E}\x{1D15E}-\x{1D164}\x{1D1BB}-\x{1D1C0}\x{2F800}-\x{2FA1D}]+/u';

	private const CONTROL_CHARACTERS_PATTERN = '/[^\P{C}\r\n\t]+/u';

	private const NEWLINES_PATTERN = '/(?:\r\n?|[\n\x{2028}\x{2029}])/u';

	private const WRITING_DIRECTIONS_PATTERN = '/[\x{200E}\x{200F}\x{202A}-\x{202E}]/u';

	private const KATAKANA_HIRAGANA_MAP = [
		'う゛' => 'ゔ',
		'ヴ' => 'ゔ',
		'ヵ' => 'ゕ',
		'ヶ' => 'ゖ',
		'ヽ' => 'ゝ',
		'ヾ' => 'ゞ',
	];

	private const HIRAGANA_KATAKANA_MAP = [
		'ゔ' => 'ヴ',
		'ゕ' => 'ヵ',
		'ゖ' => 'ヶ',
		'ゝ' => 'ヽ',
		'ゞ' => 'ヾ',
	];

	public static function normalize(string $s): string
	{
		// Unicode
		return \preg_replace_callback(self::COMPOSITION_EXCLUSION_PATTERN, function ($matches) {
			return Normalizer::normalize($matches[0], Normalizer::FORM_C);
		}, $s) ?? '';
	}

	public static function normalizeNewlines(string $s): string
	{
		return \preg_replace(self::NEWLINES_PATTERN, "\n", $s);
	}

	public static function noControlCharacters(string $s): string
	{
		return \preg_replace(self::CONTROL_CHARACTERS_PATTERN, '', $s);
	}

	public static function noTabs(string $s): string
	{
		return \str_replace("\t", ' ', $s);
	}

	public static function noNewlines(string $s): string
	{
		return \preg_replace(self::NEWLINES_PATTERN, ' ', $s);
	}

	public static function noWritingDirections(string $s): string
	{
		return \preg_replace(self::WRITING_DIRECTIONS_PATTERN, '', $s);
	}

	public static function trim(string $s): string
	{
		return \preg_replace('/\\A\\s+|\\s+\\z/u', '', $s);
	}

	public static function upper(string $s): string
	{
		return \mb_strtoupper($s, self::ENCODING);
	}

	public static function lower(string $s): string
	{
		return \mb_strtolower($s, self::ENCODING);
	}

	public static function hankaku(string $s): string
	{
		$s = \mb_convert_kana($s, 'as', self::ENCODING);
		return \strtr($s, ['＂' => '"', '＇' => "'"]);
	}

	public static function hankakuAlpha(string $s): string
	{
		return \mb_convert_kana($s, 'r', self::ENCODING);
	}

	public static function hankakuDigits(string $s): string
	{
		return \mb_convert_kana($s, 'n', self::ENCODING);
	}

	public static function noHankakuKana(string $s): string
	{
		return \mb_convert_kana($s, 'KV', self::ENCODING);
	}

	public static function hiragana(string $s): string
	{
		$s = \mb_convert_kana($s, 'cHV', self::ENCODING);
		$s = \strtr($s, self::KATAKANA_HIRAGANA_MAP);
		return $s;
	}

	public static function katakana(string $s): string
	{
		$s = \mb_convert_kana($s, 'CKV', self::ENCODING);
		$s = \strtr($s, self::HIRAGANA_KATAKANA_MAP);
		return $s;
	}
}
