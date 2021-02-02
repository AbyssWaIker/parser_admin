<?php 
namespace parser;
class Parser
{
  //наш главный инструмент, который достает все значения
  private function extract_substring_between_words($str, $starting_word, $ending_word) 
  { 
      $subtring_start = strpos($str, $starting_word); 
      $subtring_start += strlen($starting_word);   

      $size = strpos($str, $ending_word, $subtring_start) - $subtring_start;

      return substr($str, $subtring_start, $size);   
  }


  //инструмент помошник
  private function extract_substring_after_word($str, $word) 
  { 
      $subtring_start = strpos($str, $word); 
      $subtring_start += strlen($word);  

      $size = strlen($str) - $subtring_start;  

      return substr($str, $subtring_start, $size);   
  }



  //функции достающие нужную информацию из статьи
  //
  //Так как, чтобы получить полный текст статьи, 
  //ее все равно надо загружать,
  //то из этой же страницы можно (и удобнее)
  //вытащить остальную информацию

  public function get_article_title($article_html)
  {
    $class_and_title = $this->extract_substring_between_words($article_html, '<h1 ', '</h1>');
    $title = $this->extract_substring_after_word($class_and_title, '>');
    return $title;
  }

  public function get_article_date($article_html)
  {
    $class_and_date = $this->extract_substring_between_words($article_html, '<time ', '</time>');
    $date = $this->extract_substring_after_word($class_and_date, '>');
    return $date;
  }

  public function get_article_text($article_html)
  {
    $text = $this->extract_substring_between_words($article_html,'</header>','</article>');
    return $text;
  }

  public function get_article_img($article_html)
  {

    $picture_html = $this->extract_substring_between_words($article_html, '<img ', '</picture>');
    $picture_src = $this->extract_substring_between_words($picture_html, 'src="', '?');

    $picture = file_get_contents($picture_src);

    $picture_name = $this->extract_substring_after_word($picture_src, "/images/");
    $picture_path = './img/'.$picture_name;

    $picture_handle = fopen('.'.$picture_path, 'w');
    fwrite($picture_handle, $picture);
    fclose($picture_handle);

    return $picture_path;
    
  }


  function get_links($url)
  {
	$page = file_get_contents($url);
	$news = $this->extract_substring_between_words($page, '</h1></div>', '</main>'); 

	preg_match_all('/role="article" href="(.*?)"/', $news , $matches);

	$links = array_unique ($matches[1]);
	return $links;
  }
}
  

?>
