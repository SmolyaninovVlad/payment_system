<?
require_once 'db.class.php';

class API {
    protected $method = '';
    protected $endpoint = '';
    protected $verb = '';
    protected $args = Array();
    // Создаём объект базы данных
    protected $db = '';

    public function __construct($request) {
        // Объявление хедеров
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
        $this->db =  DataBase::getDB();
        $this->args = explode('/', rtrim($request, '/'));
        $this->endpoint = array_shift($this->args);
        //Получаем тип запроса
        $this->method = $_SERVER['REQUEST_METHOD'];
        switch($this->method) {
            case 'DELETE':
            case 'POST':
                $this->data = $this->_cleanInputs(json_decode(file_get_contents("php://input"), true));
            break;
            case 'GET':
                $this->data = $this->_cleanInputs($_GET);
            break;
            case 'PUT':
                $this->data = $this->_cleanInputs($_GET);
            break;
            default:
                $this->_response('Invalid Method', 405);
            break;
        }
    }
    public function processAPI() {
        //Начинаем отработку запроса
        if (method_exists($this, $this->endpoint)) {
            return $this->_response($this->{$this->endpoint}($this->args));
        }
        return $this->_response("Неизвестный метод: $this->endpoint", 404);
    }
    private function _response($data, $status = 200) {
        if ($this->statusCode) $status=$this->statusCode;
        header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
        $data = array(
            'status'=> $status==200? 'success':'error',
            'result'=> $data
        );
        return json_encode($data);
    }
    private function _cleanInputs($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }
    private function _requestStatus($code) {
        $status = array(
            200 => 'OK',
            400 => 'Bad Request',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code])?$status[$code]:$status[500];
    }
    private function _badData($text=null){
        $this->statusCode=400;
        if (!$text) $text = implode('; ', $this->errors);
        return $text;
    }
    private function _determinate($text){
        switch ($text) {
            case "card_Number":
                return "Номер карты";
                break;
            case "total":
                return "Сумма платежа";
                break;
            case "appointment":
                return "Название платежа";
                break;
            case "fromDate":
                return "Начальная дата";
                break;
            case "toDate":
                return "Конечная дата";
                break;
        }
        return $text;
    }

    private function _isDataValid(){
        $this->errors = [];
        $success = true;

        foreach ($this->data as $key => $value) {
            //Переменная для исключения повторяющихся ошибок по одному и томуже полю
            $currentError = false;
            //Проверка на заполненность данных
            if (strlen($value)==0) {
                $this->errors[]= $this->_determinate($key)." - не заполнено";
                $currentError =true;
            }
            //Определённые проверки для определённых полей
            switch ($key) {
                case "card_Number":
                    if ($currentError) break;
                    if (!is_numeric($value)) $this->errors[]= $this->_determinate($key)." - не число";
                    //мин. кол-во в банковских картах (загуглил)
                    if (strlen($value)<13) $this->errors[]= $this->_determinate($key)." - мин. 13 цифр";
                    break;
                case "total":
                    if ($currentError) break;
                    if (!is_numeric($value)) $this->errors[]= $this->_determinate($key)." - не число";
                    $this->data[$key] = abs($value);
                    break;
                case "fromDate":
                case "toDate":
                    if ($currentError) break;
                    if (!$this->_is_Date($this->data[$key])) $this->errors[]= $this->_determinate($key)." - не дата";
                    break;
            }
        }
        if (count($this->errors)>0) $success=false;

        return $success;
    }
    private function _algorithmLuna(){
        //Переворачиваем номер карты
        $number = strrev(preg_replace('/[^\d]+/', '', $this->data['card_Number']));
        $sum = 0;
        for ($index = 0, $length = strlen($number); $index < $length; $index++) {
            if (($index % 2) == 0) {
                //чётные числа
                $currentValue = $number[$index];
            } else {
                //нечётные числа
                $currentValue = $number[$index] * 2;
                if ($currentValue > 9)  {
                    //Число больше больше 9 - отнимаем 9 ( по алгоритму )
                    $currentValue -= 9;
                }
            }
            $sum += $currentValue;
        }
        $result = (($sum % 10) === 0);
        if (!$result) $this->errors[]= "Алгоритм Луна: ошибка проверки";
        return $result;
    }
    private function _setHashPayment($res){
        if (!intval($res)) return false;
        $id_hash = "pay-".md5($res);
        $query = "UPDATE payments SET sessionId={?} WHERE id={?}";
        $res = $this->db->query($query, array($id_hash, $res));
        if ($res) $this->hash_payment = "http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']."/payments/?sessionId=".$id_hash;
        return $res;
    }
    private function _is_Date($str){
        return is_numeric(strtotime($str));
    }
    private function _sendAsyncRequest($url, $params){
        //Набор необходимых параметров
        foreach ($params as $key => &$val) {
            if (is_array($val)) $val = implode(',', $val);
            $post_params[] = $key.'='.urlencode($val);
        }
        $post_string = implode('&', $post_params);
    
        $parts=parse_url($url);
    
        $fp = fsockopen($parts['host'],isset($parts['port'])?$parts['port']:80,$errno, $errstr, 30);
    
        $out = "POST ".$parts['path']." HTTP/1.1\r\n";
        $out.= "Host: ".$parts['host']."\r\n";
        $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out.= "Content-Length: ".strlen($post_string)."\r\n";
        $out.= "Connection: Close\r\n\r\n";
        if (isset($post_string)) $out.= $post_string;
    
        fwrite($fp, $out);
        fclose($fp);
    }


    // далее идут API`s
    //  Добавление нового платежа в БД
    protected function register() {
        //Проверка на метод
        if ($this->method != 'POST') return $this->_badData("Only POST requests are available for this method");
        //Проверка на то что введенны корректные данные
        if (!$this -> _isDataValid()) return $this->_badData();
        //Проверка на существование необходимых параметров для этого метода
        if (!$this->data['appointment']) {
            $this->errors[]= $this->_determinate("appointment")." - не заполнено";
        }
        if (!$this->data['card_Number']) {
            $this->errors[]= $this->_determinate("card_Number")." - не заполнено";
        }
        if (!$this->data['total']) {
            $this->errors[]= $this->_determinate("total")." - не заполнено";
        }
        //Вызов вывода ошибки если они есть
        if (count($this->errors)>0) return $this->_badData();

        //Проверка по алгоритму Луна
        if (!$this -> _algorithmLuna()) return $this->_badData();

        $query = "INSERT INTO payments (appointment, card_Number, total, date) VALUES ({?},{?},{?},{?})";
        $res = $this->db->query($query, array($this->data['appointment'], $this->data['card_Number'], $this->data['total'], date("Y-m-d H:i:s")));
        //Успешная оплата, добавляем идентификатор для доступа
        if (!$this->_setHashPayment($res)) return $this->_badData("Ошибка оплаты (ошибка запроса в БД)");
        //Отправка асинхронного запроса по переданному URL
        if (strlen($this->data['url'])>0) $this->_sendAsyncRequest($this->data['url'], array("action"=>"payment", 
                                                                                            "appointment"=>$this->data['appointment'], 
                                                                                            "amount"=>$this->data['total'], 
                                                                                            "card_Number"=>$this->data['card_Number']));

        return $this->hash_payment;
    }
    // Получение данных из БД
    protected function getData() {
        //Проверка на метод
        if ($this->method != 'GET') return $this->_badData("Only GET requests are available for this method");
        //Проверка на то что введенны корректные данные
        if (!$this -> _isDataValid()) return $this->_badData();

        $date1 = $this->data['fromDate']?"date >= {?}":"{?}";
        $date2 = $this->data['toDate']?"date <= {?}":"{?}";
        $query = "SELECT `appointment`, `card_Number`, `total`, `date` FROM `payments` WHERE ".$date1." AND ".$date2;
        $res = $this->db->select($query, array($this->data['fromDate']?$this->data['fromDate']:"1",$this->data['toDate']?$this->data['toDate']:"1"));

        return $res;
    }
}
?>