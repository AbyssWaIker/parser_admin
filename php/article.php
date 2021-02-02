<?php

namespace article;
//класс статьи, хранящий ее данные 
//создан для более удобного возврата результата из функции select_articles

class Article
{    
    //id тут не нужна, но на случай, если понадобится расширить функционал
    //(например выбрать/изменить/удалить статью), я сделал
    private $article_id; 
    public $title;
    public $Publication_Date;
    public $Full_Text;
    public $img_path;
    
    function __construct($id,$t,$d,$ft,$img)
    {
       $this->article_id=$id;
       $this->title=$t;
       $this->Publication_Date=$d;
       $this->Full_Text=$ft;
       $this->img_path=$img;
    }
    function get_id()
    {
    	return $this->article_id;
    }

    //функция отображающая статьи
    function display()
    {

        $id = $this->get_id();
        $date_and_time = explode(' ',$this->Publication_Date);
        $formated_datetime=$date_and_time[0].'T'.$date_and_time[1];

        $ThickSpaceTab = '<span>&ThickSpace;&ThickSpace;&ThickSpace;&ThickSpace;&ThickSpace;&ThickSpace;&ThickSpace;&ThickSpace;&ThickSpace;</span>';


        $element = '
        <div id="'.$id.'" class=" article d-flex">
            <br>
            <div class="article-rollup">
                <abbr title="Свернуть статью">
                    <button id="rollup-'.$id.'" class="btn btn-outline-secondary" onclick="rollup('."'".$id."')".'">
                        <i class="fas fa-align-justify"></i>
                    </button>
                </abbr>
            </div>
            <input id="select-'.$id.'" type="checkbox" onclick="add_to_selection(this)" class="selection">
            <div id="details-'.$id.'" class="spoiler panel panel-info bg-purple-darker text-white text-left">
                <div class="panel-heading article-heading clickable text-center">
                    
                    <div class="panel-title" id="title-'.$id.'"  onBlur="update_in_db(this)">'.$this->title.'</div>
                    <br>
                    <input id="Publication_Date-'.$id.'" type="datetime-local" onchange="update_in_db(this)" class=" btn-outline-dark date" value="'.$formated_datetime.'">
                </div>

                <div class="panel-body text-center">

                    <div class="hidden_edit">
                        <img id="img-'.$id.'" class="image" alt="'.$this->title.'" src="'.$this->img_path.'"/>

                        

                        <form class="img-overlay">
                            <input type="hidden" name="img_path" value="'.$this->img_path.'">
                            <label for="file">Добавить замену:'."\t".'</label>
                            <input type="file" id="img_path-'.$id.'" name="file" accept="image/*" onchange="check_size('."'".'img_path-'.$id."'".')">
                            <br>
                            <button type="submit" class="btn btn-outline-success" >
                                <i class="fas fa-edit"></i>
                                Заменить
                            </button>
                        </form>
                    </div>

                    <div id="Full_Text-'.$id.'" onclick="activate('."'Full_Text-".$id."')".'" onBlur="update_in_db(this)">
                        '.$this->Full_Text.'
                    </div>

                </div>

            </div>

            <div class="article-overlay">
                <abbr title="Редактировать заголовок">
                        <button class="btn btn-outline-success" onclick="activate('."'".'title-'.$id."'".')">
                            <i class="fas fa-edit"></i>
                        </button>
                    </abbr>
                <abbr title="Удалить статью"><button class="btn btn-outline-danger" onclick="delete_article('.$id.')"><i class="fas fa-trash"></i></button></abbr>
            </div>
        </div>
        ';
        echo $element;
    }
}

?>
