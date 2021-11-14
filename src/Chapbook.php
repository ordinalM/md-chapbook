<?php

namespace OrdinalM;

use Michelf\MarkdownExtra;

class Chapbook {

	/** @var string */
	private $markdown_source;
	/** @var string */
	private $template = __DIR__.'/../templates/basic.php';
	/** @var string */
	private $body;
	/** @var string[] */
	private $css = ['pocketfold'];

	/**
	 * @throws ChapbookException
	 */
	public function loadFromFile(string $filename): self{
		self::assertFile($filename, 'Markdown source file');
		$markdown_source = file_get_contents($filename);
		if ($markdown_source===false){
			throw new ChapbookException("Could not read contents of $filename");
		}
		$this->markdown_source = $markdown_source;

		return $this;
	}

	/**
	 * @throws ChapbookException
	 */
	public function createHtml(): string{
		$pages = $this->splitMarkdownIntoPages();
		$parser = new MarkdownExtra();
		$this->body = '';
		foreach ($pages as $index => $page){
			$this->body .= sprintf("\n<div class=\"page p%d\">\n%s\n</div>\n", $index + 1, $parser->transform($page));
		}
		ob_start();
		self::assertFile($this->template, 'Template file');
		include $this->template;
		$html = ob_get_clean();
		ob_end_clean();

		return $html;
	}

	/**
	 * @return string[]
	 */
	private function splitMarkdownIntoPages(): array{
		$pages = preg_split('/\n---\n/', $this->markdown_source);

		return array_map('trim', $pages);
	}

	/**
	 * @throws ChapbookException
	 */
	private static function assertFile(string $filename, string $name = 'File'): void{
		if (!$filename){
			throw new ChapbookException("$name is empty or not set");
		}
		if (!is_readable($filename)){
			throw new ChapbookException("$name $filename is not readable");
		}
	}

	public function setMarkdownSource(string $markdown_source): self{
		$this->markdown_source = $markdown_source;

		return $this;
	}

	public function setTemplate(string $template): self{
		$this->template = $template;
		echo "Template set to $this->template\n";

		return $this;
	}

	public function addCss(string $css): Chapbook{
		$this->css[] = $css;
		$this->css = array_unique($this->css);

		return $this;
	}

	/**
	 * @param string[] $css
	 */
	public function setCss(array $css): Chapbook{
		$this->css = $css;

		return $this;
	}

	/**
	 * @throws ChapbookException
	 */
	private function getCssIncludes(): string{
		$includes = '';
		foreach ($this->css ?? [] as $css_filename){
			if (!file_exists($css_filename)){
				$css_filename = sprintf('%s/../css/%s.css', __DIR__, $css_filename);
			}
			self::assertFile($css_filename, 'CSS file');
			$css = file_get_contents($css_filename);
			if ($css===false){
				throw new ChapbookException("Failed to load CSS file $css_filename");
			}
			$includes .= $css."\n";
		}

		return $includes;
	}
}