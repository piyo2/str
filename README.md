# piyo2/str

Normalizing/filtering string.

## Features

- Remove control characters (except for tabs or newlines)
- Normalize Unicode composition sequences
- But keep CJK compatibility ideographs
- Block invalid/overlong UTF-8 sequences
- Transform functions:
	- Uppercase <=> lowercase
	- Zenkaku <=> Hankaku
	- Hiragana <=> Katakana (including ゕ ゖ ゝ ゞ)

## Requirements

- PHP ≥ 7.1.0
- Intl PHP Extension
- Mbstring PHP Extension

## Example

```php
$filter = (new Str())->trim()
	->noNewlines()
	->hankakuDigits()
	->hiragana();

$filter->applyTo("　Ａ０３　\nアイス\n");
// => "Ａ03 アイス"
```

Applying user-defined filter:

```php
(new Str())->trim()
	->hankaku()
	->fn(fn ($s) => '#' . $s)
	->applyTo('　１２３');
// => "123"
```

Retain control characters:

```php
(new Str())->applyTo("\0\n");
// => "\n"

(new Str(true))->applyTo("\0\n");
// => "\0\n"
```
