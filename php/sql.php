<?php
namespace db_connection;


// // дебаг
// function logs($data)
// {
//     $log = fopen('log.txt', 'a');
//     fwrite($log, $data);
//     fclose($log);

// }

class db
{
  public $conn;

  private function is_table_valid($conn)
  {
    $result = $conn->query("SHOW COLUMNS FROM `articles` LIKE 'article_url'");
    $exists = ($result->num_rows !== 0)?TRUE:FALSE;
    return $exists;
  }

  //таблица хранящая информацию
  private function create_tables($conn)
  {

    $sql = ' CREATE TABLE IF NOT EXISTS `articles` 
              (
                `article_id` int(10) UNSIGNED AUTO_INCREMENT NOT NULL,
                `article_url` varchar(350) UNIQUE NOT NULL,
                `title` varchar(300) NOT NULL,
                `Publication_Date` datetime NOT NULL,
                `Full_Text` TEXT NOT NULL,
                `img_path` varchar(200) NOT NULL,
                 PRIMARY KEY (article_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
              
            ';
    if ($conn->query($sql) !== TRUE) 
      die("Не удалось создать таблицу: ".$conn->error);
    
  }

  //подключение к базе данных
  private function connect_to_db($servername,$username,$password)
  {
      $conn = mysqli_connect($servername, $username, $password);

      if (!$conn) die("Не удалось подключиться: " . mysqli_connect_error());
      
      $sql = "CREATE DATABASE IF NOT EXISTS Parser";
      if ($conn->query($sql) !== TRUE) die("Не удалось создать базу данных: " . $conn->error);
      
      $sql = "USE Parser";
      if ($conn->query($sql) !== TRUE) die("Не удалось войти в базу данных: " . $conn->error);
      
      $this->create_tables($conn);
      if(!$this->is_table_valid($conn))
      {
        if($conn->query('DROP TABLE `articles`')!== TRUE) 
          die('ошибка при сбросе таблицы' . $conn->error);
        $this->create_tables($conn);
      }

      $this->conn = $conn;
  }

  public function __construct($servername,$username,$password)
  {
    $this->connect_to_db($servername,$username,$password);
  }

  public function dissconnect()
  {
    $this->conn->close();
  }


  public function delete_article($id)
  {
    $sql = 'DELETE FROM articles 
            WHERE article_id = ?
            LIMIT 1;';

    $stmt = $this->conn->prepare($sql);
    if(!$stmt) die( $this->conn->error);

    $stmt->bind_param('i', $id);
            
    if(!$stmt->execute()) die($this->conn->error);
    $stmt->close(); 
  }



  //функция достающая статьи из базы данных
  function select_articles($current_page = 0, $Page_LIMIT=25, $isDesc, $search = '*')
  {
      if($search!='*') $search = '%'.$search.'%';

      $start_at = $current_page * $Page_LIMIT;
      $desc = ($isDesc=='true') ? 'DESC' : 'ASC';
      $sql = 'SELECT article_id, title, Publication_Date, Full_Text, img_path 
              FROM articles ';

      if($search!='*') $sql .= ' WHERE (title LIKE ?) OR (Full_Text LIKE ?) ';

      $sql .= 'ORDER BY Publication_Date '.$desc;

      if($Page_LIMIT) $sql .= ' LIMIT '.$start_at.' , '.$Page_LIMIT;

      // logs("\t\n\n$sql \n");
      $query = $this->conn->prepare($sql);

      if($search!='*') $query->bind_param("ss", $search, $search);
      if(!$query->execute())die($this->conn->error."\n $sql");
      $result = $query->get_result();

      if ($result->num_rows == 0) die('Нет данных');

      $results = array();

      while($row = $result->fetch_assoc()) 
      {
          array_push($results, $row);
      }
      return $results;
  }


  function select_articles_count()
  {
      $sql = 'SELECT count(1) 
              FROM articles';
      
      $result = $this->conn->query($sql);

      $row = $result->fetch_assoc();
      
      return $row['count(1)'];
  }

  function select_url($LIMIT=50)
  {
      $sql = 'SELECT article_url 
              FROM articles 
              ORDER BY Publication_Date DESC
              LIMIT '.$LIMIT;
              
      $result = $this->conn->query($sql);

      $results = array();

      if(!$result) return $results;

      while($row = $result->fetch_assoc()) 
      {;
          array_push($results, $row["article_url"]);
      }

      return $results;
  }

  //сохранение информации в таблицу
  function insert_articles($url, $title, $date, $Full_Text, $img_path)
  {
    $stmt = $this->conn->prepare('INSERT INTO articles (article_url, title, Publication_Date, Full_Text, img_path) 
                            VALUES (?, ?, STR_TO_DATE(?,"%b %d, %Y, %h:%i %p"), ?, ?)'
                          );
    if(!$stmt) die($this->conn->error);
    $stmt->bind_param('sssss', $url, $title, $date, $Full_Text, $img_path);

    $result = $stmt->execute();
    if(!$result) die($stmt->error);

    $stmt->close();
    return $result; 
  }


  function update_article($column_name, $value, $id)
  {

    $sql = 'UPDATE articles 
            SET '.$column_name.' = ? 
            WHERE article_id = ?
            LIMIT 1;';

    $stmt= $this->conn->prepare($sql);
    if(!$stmt) die($this->conn->error);

    $stmt->bind_param('si',$value,$id);
            
    if(!$stmt->execute()) die($conn->error);
  }
}

?>
