<?php

namespace piyo2\util;

use InvalidArgumentException;

class Str
{
	/** @var array<callable> */
	protected $filters = [];

	/**
	 * Create filter.
	 *
	 * @param boolean $retainControlCharacters
	 */
	public function __construct(bool $retainControlCharacters = false)
	{
		$this->filters[] = [StrFilter::class, 'normalize'];
		if (!$retainControlCharacters) {
			$this->filters[] = [StrFilter::class, 'normalizeNewlines'];
			$this->filters[] = [StrFilter::class, 'noControlCharacters'];
		}
	}

	/**
	 * Apply filters to string
	 *
	 * @param string|null $str
	 * @return string
	 */
	public function applyTo(string $str = null): string
	{
		foreach ($this->filters as $filter) {
			$str = \call_user_func($filter, $str);
		}
		return $str;
	}

	/**
	 * Add user-defined filter
	 *
	 * @param callable $fn
	 * @return Str
	 */
	public function fn(callable $fn): Str
	{
		if (!\is_callable($fn)) {
			throw new InvalidArgumentException('$fn is not callable.');
		}
		$this->filters[] = $fn;
		return $this;
	}

	public function normalize(): Str
	{
		$this->filters[] = [StrFilter::class, 'normalize'];
		return $this;
	}

	public function normalizeNewlines(): Str
	{
		$this->filters[] = [StrFilter::class, 'normalizeNewlines'];
		return $this;
	}

	public function noControlCharacters(): Str
	{
		$this->filters[] = [StrFilter::class, 'noControlCharacters'];
		return $this;
	}

	public function noTabs(): Str
	{
		$this->filters[] = [StrFilter::class, 'noTabs'];
		return $this;
	}

	public function noNewlines(): Str
	{
		$this->filters[] = [StrFilter::class, 'noNewlines'];
		return $this;
	}

	public function noWritingDirections(): Str
	{
		$this->filters[] = [StrFilter::class, 'noWritingDirections'];
		return $this;
	}

	public function trim(): Str
	{
		$this->filters[] = [StrFilter::class, 'trim'];
		return $this;
	}

	public function upper(): Str
	{
		$this->filters[] = [StrFilter::class, 'upper'];
		return $this;
	}

	public function lower(): Str
	{
		$this->filters[] = [StrFilter::class, 'lower'];
		return $this;
	}

	public function hankaku(): Str
	{
		$this->filters[] = [StrFilter::class, 'hankaku'];
		return $this;
	}

	public function hankakuAlpha(): Str
	{
		$this->filters[] = [StrFilter::class, 'hankakuAlpha'];
		return $this;
	}

	public function hankakuDigits(): Str
	{
		$this->filters[] = [StrFilter::class, 'hankakuDigits'];
		return $this;
	}

	public function noHankakuKana(): Str
	{
		$this->filters[] = [StrFilter::class, 'noHankakuKana'];
		return $this;
	}

	public function hiragana(): Str
	{
		$this->filters[] = [StrFilter::class, 'hiragana'];
		return $this;
	}

	public function katakana(): Str
	{
		$this->filters[] = [StrFilter::class, 'katakana'];
		return $this;
	}
}
