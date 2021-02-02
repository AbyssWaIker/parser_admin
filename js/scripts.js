
var upload_max_filesize = 2;//mb
//вначале я хотел сделать дефолтным макс значением 5мб,
//но переменная upload_max_filesize в php.ini 
//имеет значение по умолчанию в 2мб
//и на другой системе, без имзенения настроек по умолчанию
//такое значение будет просто вызывать ошибки 
//при закачке больших картинок
//
//для легкости смены, я вынес эту переменную в "заголовок"



var is_orderby_desc = true;
var current_page = 0;
var page_LIMIT = 10;
var selection = [];


function change_orderby_desc_button()
{
    var desc = document.getElementById('orderby_desc_button');
    if(desc.innerText=="самых новых")
        desc.innerText="самых старых";       
    else 
        desc.innerText="самых новых";
}


function notify(message) 
{
  if (!("Notification" in window)) 
  {
    alert(message);
    return;
  }

  if (Notification.permission === "default") 
  {
    Notification.requestPermission();
  }

  if (Notification.permission === "granted") 
  {
    var notification = new Notification(message);
    
  }
}

function add_to_history(message)
{
    var elem = document.createElement("div");
    elem.innerHTML = message;
    
    
    var history = document.getElementById('history_html');
    history.appendChild(elem);
}

function key_pressed(event) 
{
    // Enter
    if (event.keyCode === 13) 
    {

        event.preventDefault();
        var active =  document.getElementsByClassName('active');

        active[0].blur();
        return;
    }
} 

function get_title_by_id(id)
{
    var title =  document.getElementById("title-"+id);
    return title.innerText;
}

var backup_copy_for_title;
var backup_copy_for_text;

function save_value(element)
{
    data = element.id.split('-');
    column_name = data[0];

    if(column_name=="title")
            backup_copy_for_title = element.innerText;
        else backup_copy_for_text = element.innerText;
}

function backup_value(element, column_name)
{
    if(column_name=="title")
            element.innerText = backup_copy_for_title;
        else element.innerText = backup_copy_for_text;
}

function update_in_db(element)
{
    element.removeEventListener("keydown", key_pressed);
    element.addEventListener('click',add_spoiler_functionality);

    element.classList.remove("active");
    element.contentEditable = "false";


    var data = element.id.split('-');
    var column_name = data[0];
    var value;
    if(column_name=='Publication_Date')
    {
        var date_and_time = element.value.split('T');
        value=date_and_time[0]+' '+date_and_time[1];

    }
    else
    {
        value=element.innerHTML;

        if(element.innerText.trim()=="")
        {
            backup_value(element,data[0]);
            return;
        }
    }
    title = get_title_by_id(data[1]);
    


    $.ajax(
    {
        url:'php/update_article.php',
        type:'post',
        data:
        {
            value: value,
            column_name: data[0],
            id: data[1],
            title:title
        },
        success:function(php_results)
        {
            l = php_results.length;
            if(php_results.substr(l-7,l-1)!="успешно"&&column_name!='Publication_Date') /*console.log(php_results.substr(l-7,l-1));*/
                backup_value(element,data[0]);
            add_to_history(php_results);

            if(column_name=='Publication_Date')
                get_articles();
        }
    });
}


function get_extension(path)
{
	var path_string = path;
	var path_and_exit = path_string.split('.');
	return path_and_exit[path_and_exit.length-1];
}

function set_extension(path, new_extension)
{
	var path_string = path;
	var path_and_exit = path_string.split('.');

	var new_name;
	for (var i = 0; i < path_and_exit.length-1; i++) 
	{
		new_name += '.' + path_and_exit[i];
	}

	return new_name += '.' + new_extension;
}

function extentions_are_different(old_path,new_path)
{
	var old_ext = get_extension(old_path);
	var new_ext = get_extension(new_path);


	return (old_ext!=new_ext);

}


function change_picture(event)
{
    event.preventDefault();
    
    
    var element = event.target;

    var old_path = element[0].value;
    var file = element[1].files[0];

    var need_change = extentions_are_different(old_path,file.name);

    var formData = new FormData();
    formData.append('img_path',old_path);
    formData.append('file', file);
    formData.append('need_change',need_change);
    formData.append('new_path_ext', get_extension(file.name));


    var path_to_img_edit = element[1].id;
    var parts = path_to_img_edit.split('-');
    var id = parts[1];
    formData.append('id', id);

    var title = get_title_by_id(element.parentNode.parentNode.parentNode.parentNode.id);
    formData.append('title', title);
    $.ajax(
        {
           url:'php/change_picture.php',
           type: "POST",
           enctype: 'multipart/form-data',
           data: formData,
           processData: false,
           contentType: false,
           cache: false,
           timeout: 800000,
           success:function(php_results)
           {
               add_to_history(php_results);

               var img = document.getElementById('img-' + id);

               if(need_change)
               {
               		img.src = set_extension(img.src,get_extension(file.name));
               }
               
           		img.src = img.src + '?random';

               //часть после знака вопроса может игнорироваться браузером,
               //но именно она позволяет не страдать из-за 
               //"картинка по этому пути уже есть в кеше = обновлять ее не надо"
               

               
           }
        });
};



function activate(id)
{

    var element = document.getElementById(id);

    save_value(element);

    element.contentEditable = "true";
    element.focus();

    element.classList.add("active");
    var key_is_down=false;
    element.addEventListener("keydown", key_pressed, false);
    element.removeEventListener("click", add_spoiler_functionality);
}




function rollup(id)
{
    var $panel = $('#details-'+id); 
    var $the_rollup = $panel.prev();

    var $panel_body = $panel.children('.panel-body');
    var $display = $panel_body.css('display');

    if ($display == 'block') 
    {
        $panel_body.slideUp();
        $the_rollup.css('width',0);

    } else if($display == 'none') 
    {
        $panel_body.slideDown();
        $the_rollup.css('width','40px');

    }
}

//Я не использовал details, а в ручную превращал div-ы в спойлеры, потому что
//details при редактировании заголовка перехватывает пробел и enter
//ивентом открытия закрытия спойлера. Танцами с бубном мне удавалось 
//вставлять пробел при редактуре текста, но остановить сворачивание/разворачивание нет
//так что я вернулся к div-ам
function add_spoiler_functionality(elem)
{
    elem.addEventListener('click', function(e)
    {
        var $this = $(this);
        var $panel = $this.parent('.panel');
        var $article = $panel.parent('.article');

        rollup($article[0].id);
    });
    
}
function make_spoilers_clickable()
{
    var headers = document.getElementsByClassName("article-heading");
    for(var i =0; i< headers.length;i++)
    {
        add_spoiler_functionality(headers[i]);
    }
}


function check_size(id)
{
    var element = document.getElementById(id);
    var file = element.files[0];
    var file_is_too_big = file.size>1024*1024*upload_max_filesize;

    element.nextSibling.nextSibling.disabled = file_is_too_big;

    if(file_is_too_big)
    {
        notify("Файл слишком большой. \nМаксимальный размер - "+upload_max_filesize+"мб");
    }

}
//предотвращает отправку формы, превращая ее в ajax запрос
function sanitize_forms()
{
    var forms = document.getElementsByClassName("img-overlay");
    
    for(var i =0; i< forms.length;i++)
    {
        var id = forms[i].id;
        forms[i].onsubmit = change_picture;
    }
};

//Mozilla FireFox, Safari и IE не поддерживают iput типа datetime-local 
//(а datetime устарела и тем более не поддреживается)
function clean_datetime()
{
    var uAgent = navigator.userAgent;

    var is_supported = (uAgent.indexOf("Chrome") > -1)||(uAgent.indexOf("Opera") > -1)||(uAgent.indexOf("Edge") > -1);
    
    if(is_supported)
        return;


    var datetime = document.getElementsByClassName('date');
    for(var i = 0; i < datetime.length; i++)
    {
        var date_and_time = datetime[i].value.split('T');
        value=date_and_time[0]+' '+date_and_time[1];
        datetime[i].value = value;
        datetime[i].readOnly = true;
    }

}

// function add_value_to_select(chooser, current_value)
// {
//     var option = document.createElement("option");
//     option.value = current_value;
//     option.text = current_value+1;

//     chooser.add(option); 


//     var current = (current_value == current_page);
//     chooser[current_value].selected = current;
//     chooser[current_value].disabled = current;
    
// }


function create_page_button(page_number, current_page)
{
    var page_button = document.createElement("button");
    page_button.classList.add('btn','btn-outline-secondary', 'page-button');
    page_button.innerText = page_number+1;
    page_button.addEventListener('click',function(){change_page(page_number);});
    page_button.disabled = (page_number == current_page);
    return page_button;
}


function create_page_list_div(number_of_pages, current_page)
{
    var current_page_offset = 3;
    var edge_offset = 2;

    var page_select = document.createElement("div");
    page_select.classList.add('text-white', 'd-flex');


    var dots = document.createElement("div");
    dots.innerHTML='&ThickSpace; ... &ThickSpace;';


    var counter = 0;
    var first_pages_block_size = edge_offset;
    var middle_range_start = current_page-current_page_offset;
    var middle_range_end = current_page+current_page_offset;
    var end_pages_block_start = number_of_pages-edge_offset;

    if(first_pages_block_size > current_page)
        first_pages_block_size = middle_range_end;

    for (counter; counter < first_pages_block_size  && counter < number_of_pages; counter++) 
    {
        var button = create_page_button(counter, current_page);
        page_select.appendChild(button); 
    }


    if(counter<middle_range_start)
    {
        counter=middle_range_start;
        var dot_dot_dot = dots.cloneNode(true);
        page_select.appendChild(dot_dot_dot);
    }

    for(counter; counter <= middle_range_end && counter < number_of_pages; counter++)
    {
        var button = create_page_button(counter, current_page);
        page_select.appendChild(button); 
    }


    if(counter<end_pages_block_start)
    {
        counter=end_pages_block_start;
        var dot_dot_dot = dots.cloneNode(true);
        page_select.appendChild(dot_dot_dot);

    }

    for(counter; counter < number_of_pages; counter++)
    {
        var button = create_page_button(counter, current_page);
        page_select.appendChild(button); 
    }
    return page_select;
}




function create_page_select(total_articles)
{


    var total_pages = Math.ceil(total_articles/page_LIMIT);
    
    
    var prev_page_button = document.createElement("button");
    prev_page_button.innerHTML = "<i class='fas fa-angle-left'></i>Предыдущая страница";
    prev_page_button.disabled = (current_page == 0);
	prev_page_button.classList.add('btn','btn-outline-success');

    var next_page_button = document.createElement("button");
    next_page_button.innerHTML='Следующая страница<i class="fas fa-angle-right"></i>'
    next_page_button.disabled = (current_page == (total_pages-1));
	next_page_button.classList.add('btn','btn-outline-success');

    var page_select = create_page_list_div(total_pages, current_page)


    prev_page_button.addEventListener('click',function(){change_page(current_page-1);});
    next_page_button.addEventListener('click',function(){change_page(current_page+1);});


    var div = document.createElement('div');
    div.id='page_list';
    div.appendChild(prev_page_button);
    div.appendChild(page_select);
    div.appendChild(next_page_button);
    div.classList.add('text-white', 'd-flex', 'justify-content-center');

    var page_list = document.getElementById('page_list_html');

    page_list.removeChild(page_list.firstChild);
    page_list.appendChild(div);
}


function create_page_list()
{
     $.ajax(
        {
            url:'php/articles_count.php',
            type:'post',
            success:function(php_results)
            {
                var number_of_pages = Number(php_results);
                if(number_of_pages > page_LIMIT && page_LIMIT != 0)
                    create_page_select(number_of_pages);
            }
        });
}


function get_articles()
{
    var search_query = '*';
    var field = document.getElementById('searching');
    field.value = field.value.trim();
    if(field.value!="") search_query = field.value;
    $.ajax(
        {
            url:'php/show_articles.php',
            type:'post',
            data:
            {
                is_orderby_desc: is_orderby_desc,
                current_page: current_page,
                page_LIMIT: page_LIMIT,
                search:search_query
            },
            success:function(php_results)
            {
                $('#articles_html').html(php_results);
                make_spoilers_clickable();
                sanitize_forms();
                clean_datetime();

                create_page_list();
            }
        });
}


function update_articles_view()
{
    var desc = document.getElementById('orderby_desc_button');
    is_orderby_desc = (desc.innerText=="самых новых");

    var select=document.getElementById('limit');
    page_LIMIT = Number(select.value);

    current_page = 0;


    get_articles();

}


function change_page(new_page)
{
    current_page=new_page;
    get_articles();
    document.getElementById('history').scrollIntoView();
}



function save_article(url)
{
    $.ajax(
        {
            
            url:'php/save_article.php',
           type:'post',
           data:
           {
               url: url
           },
           success:function(php_results)
           {
               get_articles();
               add_to_history(php_results);           
           }
        });
}
function get_new_articles()
{
    $.getJSON( "php/url_reciever.php", function( data ) 
    {
        var keys = Object.keys(data);
        if(keys.length==0)
             $.post('php/date_timestamp.php',function(server_time)
                   {
                       add_to_history(server_time+" Новых статей нет");
                   });
        
        for(var i=0; i<keys.length;i++)
        {
            
            // console.log(data[keys[i]]);
            save_article(data[keys[i]]);
        }
    });
}


function  delete_article(id)
{
    var img = document.getElementById('img-'+id);
    
    var data = img.src.split('img');
    var tmp_src = '/img'+data[1];


    //мусор вроде ?random нужен, чтобы браузер понял, 
    //что картинка по этому пути не та же, что в кеше и обновил ее
    var src_and_junk = tmp_src.split('?random');
    var src = src_and_junk[0];



    var title = get_title_by_id(id);
    $.ajax(
    {
        url:'php/delete_article.php',
        type:'post',
        data:
        {
            id: id,
            src:src,
            title:title
        },
        success:function(php_results)
        {
            add_to_history(php_results);
            var element = document.getElementById(id);
            element.parentNode.removeChild(element);
            get_articles();
        }
    });
}
function key_pressed_on_search(event) 
{
    // Enter
    if (event.keyCode === 13) 
    {
        event.preventDefault();
        var active =  document.getElementsByClassName('active');
        active.removeEventListener('keyup',key_pressed);
        active,classList.remove('active');
        update_articles_view();
        return;
    }
} 

function enable_search_field()
{
    var field = document.getElementById('searching');

    field.size = 50;
    field.classList.add('active');
    field.addEventListener('keyup',key_pressed);
}

function disable_empty_search_field()
{
    var field = document.getElementById('searching');
    field.value = field.value.trim();
    if(field.value=="") 
    {
        field.size = 5;
        return;
    }
    field.size = field.value.length;

    field.classList.remove('active');
    update_articles_view();

}

function exit()
{
    window.location ="php/exit.php";
}

function toggle_selection()
{
	var checkboxes = document.getElementsByClassName('selection');
    for(var i = 0; i < checkboxes.length; i++)
    {
        checkboxes[i].style.width =  (checkboxes[i].style.width != '100px') ? '100px' : '0';
    }

    var button = document.getElementById('mass_clear');
    button.style.display = (button.style.display!="inline-block") ?"inline-block" : "none" ;


    var button = document.getElementById('toggle_selection');
    button.style.display = (button.style.display!="none") ?  "none" : "inline-block" ;
}

function add_to_selection(element)
{
	selection.push(element.parentNode.id);
}


function clear_selection()
{
	for(var i = 0; i < selection.length; i++)
    {
        delete_article(selection[i]); 
    }
    selection = [];
    toggle_selection();
}
get_articles();
