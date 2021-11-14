<?php

namespace Ordinal;

use Michelf\MarkdownExtra;

class Chapbook {

	/** @var string */
	private $markdown_source;
	private $template = 'templates/pocketfold.php';

	public function loadFromFile(string $filename): self{
		if (!file_exists($filename)){
			throw new ChapbookException("File '$filename' does not exist");
		}
		$markdown_source = file_get_contents($filename);
		if ($markdown_source===false){
			throw new ChapbookException("Could not get contents of '$filename'");
		}
		$this->markdown_source = $markdown_source;

		return $this;
	}

	public function createHtml(): string{
		$pages = $this->splitMarkdownIntoPages();
		$parser = new MarkdownExtra();
		$body = '';
		foreach ($pages as $index => $page){
			$body .= sprintf("\n<div class=\"page p%d\">\n%s\n</div>\n", $index + 1, $parser->transform($page));
		}
		$css = ['pocketfold', 'monospace'];
		ob_start();
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
		$pages = array_map('trim', $pages);

		return $pages;
	}
}