<?php

use PHPUnit\Framework\TestCase;
use piyo2\util\StrFilter;

final class StrFilterTest extends TestCase
{
	/**
	 * @test
	 */
	public function testNormalize()
	{
		// Basic Latin
		$this->assertEquals(
			' !"#0123ABCabc,.-/\\~',
			StrFilter::normalize(' !"#0123ABCabc,.-/\\~')
		);

		// CJK Compatibility
		$this->assertEquals(
			'㌀㍘㍱㍻㍿㏠㏿',
			StrFilter::normalize('㌀㍘㍱㍻㍿㏠㏿')
		);

		// CJK Unified Ideographs Extension A
		$this->assertEquals(
			'㐀',
			StrFilter::normalize('㐀')
		);

		// CJK Unified Ideographs
		$this->assertEquals(
			'国東愛永袋霊酬今力鷹三鬱',
			StrFilter::normalize('国東愛永袋霊酬今力鷹三鬱')
		);

		// CJK Compatibility Ideographs
		$this->assertEquals(
			"海\xEF\xA9\x85 神\xEF\xA8\x99 ",
			StrFilter::normalize("海\xEF\xA9\x85 神\xEF\xA8\x99 ")
		);

		// CJK Compatibility Forms
		$this->assertEquals(
			'︰︱︲︳︴︵︶﹅﹆﹉﹏',
			StrFilter::normalize('︰︱︲︳︴︵︶﹅﹆﹉﹏')
		);

		// Halfwidth and Fullwidth Forms
		$this->assertEquals(
			'ﾍﾟｰｼﾞ',
			StrFilter::normalize('ﾍﾟｰｼﾞ')
		);
		$this->assertEquals(
			'０１２ＡＢＣａｂｃ！＂＃＄％＆＇，．？ー＝＾〜（）［］＜＞／＼＿｜',
			StrFilter::normalize('０１２ＡＢＣａｂｃ！＂＃＄％＆＇，．？ー＝＾〜（）［］＜＞／＼＿｜')
		);

		// CJK Unified Ideographs Extension B
		$this->assertEquals(
			'吉𠮷丈𠀋',
			StrFilter::normalize('吉𠮷丈𠀋')
		);

		// CJK Unified Ideographs Extension C–G, CJK Compatibility Ideographs Supplement
		$this->assertEquals(
			"𪷤 𫝆 𬇹 \xF0\xAC\xBA\xB0 你 \xF0\xB0\x80\x81",
			StrFilter::normalize("𪷤 𫝆 𬇹 \xF0\xAC\xBA\xB0 你 \xF0\xB0\x80\x81")
		);

		// Combining Character Sequence (ゔ)
		$this->assertEquals(
			"\xE3\x82\x94",
			StrFilter::normalize("\xE3\x82\x94")
		);
		$this->assertEquals(
			"\xE3\x82\x94",
			StrFilter::normalize("\xE3\x81\x86\xE3\x82\x99")
		);

		// Ideographic Variation Sequence
		$this->assertEquals(
			'榊榊󠄀',
			StrFilter::normalize('榊榊󠄀')
		);

		// Emoji
		$this->assertEquals(
			'🥺🐤👰🏻🎅🏿',
			StrFilter::normalize('🥺🐤👰🏻🎅🏿')
		);

		// Invalid UTF-8 Sequence
		$this->assertEquals(
			'',
			StrFilter::normalize("あいう\x0A\x92\xFFえお")
		);

		// Overlong UTF-8 Sequence
		$this->assertEquals(
			'',
			StrFilter::normalize("\xC0\xB2")
		);
		$this->assertEquals(
			'',
			StrFilter::normalize("\xE0\x80\xB2")
		);
		$this->assertEquals(
			'',
			StrFilter::normalize("\xF0\x80\x80\xB2")
		);
	}

	/**
	 * @test
	 */
	public function testStrip()
	{
		$lineSeparator = "\xE2\x80\xA8";
		$paragraphSeparator = "\xE2\x80\xA8";

		$this->assertEquals(
			"_1_\n_2_\n_3_\n_4_\n_5_\n_6_",
			StrFilter::normalizeNewlines("_1_\n_2_\r\n_3_\r_4_{$lineSeparator}_5_{$paragraphSeparator}_6_")
		);

		$this->assertEquals(
			"\t\n\r {$lineSeparator}{$paragraphSeparator}",
			StrFilter::noControlCharacters("\0\1\2\3\4\5\6\7\x8\x9\xA\xB\xC\xD\xE\xF\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F\x20\x7F{$lineSeparator}{$paragraphSeparator}")
		);

		$this->assertEquals(
			"abc def\r\nghi{$lineSeparator}{$paragraphSeparator}jkl",
			StrFilter::noTabs("abc\tdef\r\nghi{$lineSeparator}{$paragraphSeparator}jkl")
		);

		$this->assertEquals(
			"abc\tdef ghi  jkl",
			StrFilter::noNewlines("abc\tdef\r\nghi{$lineSeparator}{$paragraphSeparator}jkl")
		);

		$this->assertEquals(
			"abc def ghi jkl",
			StrFilter::noWritingDirections("abc\xE2\x80\x8F def\xE2\x80\xAB ghi\xE2\x80\xAE jkl")
		);
	}

	/**
	 * @test
	 */
	public function testTransform()
	{
		$this->assertEquals(
			'ABCABCÂÖÚÂÖÚＡＢＣＡＢＣ',
			StrFilter::upper('ABCabcÂÖÚâöúＡＢＣａｂｃ')
		);

		$this->assertEquals(
			'abcabcâöúâöúａｂｃａｂｃ',
			StrFilter::lower('ABCabcÂÖÚâöúＡＢＣａｂｃ')
		);
	}

	/**
	 * @test
	 */
	public function testTransformJapanese()
	{
		// 　！＂＃＄％＆＇（）＊＋，－．／０１２３４５６７８９：；＜＝＞？
		// ＠ＡＢＣＤＥＦＧＨＩＪＫＬＭＮＯＰＱＲＳＴＵＶＷＸＹＺ［＼］＾＿
		// ｀ａｂｃｄｅｆｇｈｉｊｋｌｍｎｏｐｑｒｓｔｕｖｗｘｙｚ｛｜｝～
		// ｡｢｣､･ｦｧｨｩｪｫｬｭｮｯｰｱｲｳｴｵｶｷｸｹｺｻｼｽｾｿﾀﾁﾂﾃﾄﾅﾆﾇﾈﾉﾊﾋﾌﾍﾎﾏﾐﾑﾒﾓﾔﾕﾖﾗﾘﾙﾚﾛﾜﾝﾞﾟ

		$hankakuDigits = '0123456789';
		$zenkakuDigits = '０１２３４５６７８９';
		$hankakuAlpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$zenkakuAlpha = 'ＡＢＣＤＥＦＧＨＩＪＫＬＭＮＯＰＱＲＳＴＵＶＷＸＹＺａｂｃｄｅｆｇｈｉｊｋｌｍｎｏｐｑｒｓｔｕｖｗｘｙｚ';
		$hankakuSymbol = '!"#$%&\'()*+,-./:;<=>?@[]^_`{|}';
		$zenkakuSymbol = '！＂＃＄％＆＇（）＊＋，－．／：；＜＝＞？＠［］＾＿｀｛｜｝';
		$zenkakuOnly = '＼～';

		$this->assertEquals(
			" {$hankakuDigits}{$hankakuAlpha}{$hankakuSymbol}{$zenkakuOnly}",
			StrFilter::hankaku("　{$zenkakuDigits}{$zenkakuAlpha}{$zenkakuSymbol}{$zenkakuOnly}")
		);

		$this->assertEquals(
			"　{$zenkakuDigits}{$hankakuAlpha}{$zenkakuSymbol}{$zenkakuOnly}",
			StrFilter::hankakuAlpha("　{$zenkakuDigits}{$zenkakuAlpha}{$zenkakuSymbol}{$zenkakuOnly}")
		);

		$this->assertEquals(
			"　{$hankakuDigits}{$zenkakuAlpha}{$zenkakuSymbol}{$zenkakuOnly}",
			StrFilter::hankakuDigits("　{$zenkakuDigits}{$zenkakuAlpha}{$zenkakuSymbol}{$zenkakuOnly}")
		);

		$this->assertEquals(
			'。「」、・ヲァィゥェォャュョッーアイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワン゛゜',
			StrFilter::noHankakuKana('｡｢｣､･ｦｧｨｩｪｫｬｭｮｯｰｱｲｳｴｵｶｷｸｹｺｻｼｽｾｿﾀﾁﾂﾃﾄﾅﾆﾇﾈﾉﾊﾋﾌﾍﾎﾏﾐﾑﾒﾓﾔﾕﾖﾗﾘﾙﾚﾛﾜﾝﾞﾟ')
		);

		$this->assertEquals(
			'ゕゖゝゞをぁぃぅぇぉゃゅょっゎーあいうえおかきくけこさしすせそたちつてとなにぬねのはひふへほまみむめもやゆよらりるれろわゐゑんゔがぎぐげござじずぜぞだぢづでどばびぶべぼぱぴぷぺぽ',
			StrFilter::hiragana('ヵヶヽヾヲァィゥェォャュョッヮーアイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヰヱンヴガギグゲゴザジズゼゾダヂヅデドバビブベボパピプペポ')
		);
		$this->assertEquals(
			'をぁぃぅぇぉゃゅょっーあいうえおかきくけこさしすせそたちつてとなにぬねのはひふへほまみむめもやゆよらりるれろわんゔがぎぐげござじずぜぞだぢづでどばびぶべぼぱぴぷぺぽ',
			StrFilter::hiragana('ｦｧｨｩｪｫｬｭｮｯｰｱｲｳｴｵｶｷｸｹｺｻｼｽｾｿﾀﾁﾂﾃﾄﾅﾆﾇﾈﾉﾊﾋﾌﾍﾎﾏﾐﾑﾒﾓﾔﾕﾖﾗﾘﾙﾚﾛﾜﾝｳﾞｶﾞｷﾞｸﾞｹﾞｺﾞｻﾞｼﾞｽﾞｾﾞｿﾞﾀﾞﾁﾞﾂﾞﾃﾞﾄﾞﾊﾞﾋﾞﾌﾞﾍﾞﾎﾞﾊﾟﾋﾟﾌﾟﾍﾟﾎﾟ')
		);
		$this->assertEquals(
			'ヷヸヹヺ',
			StrFilter::hiragana('ヷヸヹヺ')
		);

		$this->assertEquals(
			'ヵヶヽヾヲァィゥェォャュョッーアイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワンヴガギグゲゴザジズゼゾダヂヅデドバビブベボパピプペポ',
			StrFilter::katakana('ゕゖゝゞをぁぃぅぇぉゃゅょっーあいうえおかきくけこさしすせそたちつてとなにぬねのはひふへほまみむめもやゆよらりるれろわんゔがぎぐげござじずぜぞだぢづでどばびぶべぼぱぴぷぺぽ')
		);
		$this->assertEquals(
			'ヲァィゥェォャュョッーアイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワンヴガギグゲゴザジズゼゾダヂヅデドバビブベボパピプペポ',
			StrFilter::katakana('ｦｧｨｩｪｫｬｭｮｯｰｱｲｳｴｵｶｷｸｹｺｻｼｽｾｿﾀﾁﾂﾃﾄﾅﾆﾇﾈﾉﾊﾋﾌﾍﾎﾏﾐﾑﾒﾓﾔﾕﾖﾗﾘﾙﾚﾛﾜﾝｳﾞｶﾞｷﾞｸﾞｹﾞｺﾞｻﾞｼﾞｽﾞｾﾞｿﾞﾀﾞﾁﾞﾂﾞﾃﾞﾄﾞﾊﾞﾋﾞﾌﾞﾍﾞﾎﾞﾊﾟﾋﾟﾌﾟﾍﾟﾎﾟ')
		);
	}
}
