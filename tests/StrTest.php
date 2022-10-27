<?php

use PHPUnit\Framework\TestCase;
use piyo2\util\Str;

final class StrTest extends TestCase
{
	/**
	 * @test
	 */
	public function testTransform()
	{
		$this->assertEquals(
			'abcabcâöúâöú かーぶ',
			(new Str())->trim()
				->lower()
				->hiragana()
				->applyTo(' ABCabcÂÖÚâöú カーブ　')
		);

		$this->assertEquals(
			'#123',
			(new Str())->trim()
				->hankaku()
				->fn(function ($s) {
					return  '#' . $s;
				})
				->applyTo('　１２３')
		);
	}
}
