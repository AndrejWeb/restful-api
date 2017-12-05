<?php

class Api
{

    private $db, $log_api_calls = true;

    public function __construct()
    {
        $this->db=new DB\SQL(
            'mysql:host=localhost;dbname=restful_api',
            'root',
            ''
        );

        //Uncomment this line if you want to disable logging
        //$this->log_api_calls = false;
    }

    /*
     *  GET /
     *  Get all todos
     */
    public function get_todos()
    {
        $api_data = $this->get_todos_array();

        $data = [
            'total' => count($api_data),
            'completed' => $this->count_completed($api_data),
            'uncompleted' => $this->count_uncompleted($api_data),
            'data' => $api_data,
        ];

        $json_data = json_encode($data);

        if($this->log_api_calls)
            $this->logAPICall('GET', '/', file_get_contents("php://input"), $json_data);

        echo $json_data;
    }

    /*
     *  GET /@id
     *  Get to-do with an id of @id
     */
    public function get_todo($f3, $params)
    {
        $todo = $this->db->exec("SELECT * FROM `todos` WHERE id = :id", array(':id' => $params['id']));
        echo json_encode($todo);
    }

    /*
     * Helper function used by several functions. Get todos rows as an array.
     */
    public function get_todos_array()
    {
        $api_data = $this->db->exec("SELECT * FROM `todos`");
        return $api_data;
    }

    /*
     *  POST /
     *  Add one or more todos
     */
    public function post_todos()
    {
        $errors = array();

        $post_data = file_get_contents("php://input");

        if($post_data != '') {
            if ($this->isJSON($post_data)) {
                $post_data_array = json_decode($post_data, true);

                $contains_array = $this->contains_array($post_data_array);
                if ($contains_array) {
                    $count_post_data = count($post_data_array);
                    if ($count_post_data == 1) {
                        $post_data_array = $post_data_array[0];
                    }
                } else {
                    $count_post_data = 1;
                }

                if ($count_post_data == 1) {
                    if (!isset($post_data_array['completed'])) {
                        $post_data_array['completed'] = false;
                    }
                    $errors = $this->validate_fields($post_data_array);
                } else if ($count_post_data >= 2) {
                    foreach ($post_data_array as $key => $value) {
                        if (!isset($value['completed'])) {
                            $post_data_array[$key]['completed'] = false;
                        }
                        $errors[$key] = $this->validate_fields($post_data_array[$key]);
                    }
                    foreach ($errors as $key => $err_arr) {
                        if (empty($err_arr)) unset($errors[$key]);
                    }
                }

                if (empty($errors)) {

                    $api_data_array = $this->get_todos_array();

                    $api_data_array_ids = $this->get_todo_IDs($api_data_array);
                    $post_data_array_ids = $this->get_todo_IDs($post_data_array);

                    $dups = array_intersect($api_data_array_ids, $post_data_array_ids);

                    if (count($dups) > 0) {
                        $return_data = [
                            "error" => ["Can't insert into already existing ID. Record(s) with existing IDs: " . implode(", ", $dups)]
                        ];
                        $return_code = 400;
                    } else {

                        $this->add_todos($post_data_array);
                        $api_data_array = $this->get_todos_array();

                        $return_data = [
                            'total' => count($api_data_array),
                            'completed' => $this->count_completed($api_data_array),
                            'uncompleted' => $this->count_uncompleted($api_data_array),
                            'data' => $api_data_array
                        ];
                        $return_code = 201;
                    }
                } else {
                    $return_data = [
                        'error' => $errors
                    ];
                    $return_code = 400;
                }
            } else {
                $return_data = [
                    'error' => ['Not a valid JSON data.']
                ];
                $return_code = 400;
            }
        }
        else
        {
            $return_data = [
                'error' => ['Not a valid JSON data.']
            ];
            $return_code = 400;
        }

        $json_data = json_encode($return_data);

        if($this->log_api_calls)
            $this->logAPICall('POST', '/', file_get_contents("php://input"), $json_data);

        http_response_code($return_code);
        echo $json_data;
    }

    /*
    * Helper function used within function post_todos(). It returns an array with to-do IDs.
    */
    private function get_todo_IDs($api_data_array)
    {
        $ids_array = array();

        if($this->contains_array($api_data_array))
        {
            foreach($api_data_array as $key => $value)
            {
                $ids_array[] = $value['id'];
            }
        }
        else
        {
            $ids_array[] = $api_data_array['id'];
        }

        return $ids_array;
    }

    /*
    * Inserts todos in database. Helper function used within function post_todos()
    */
    private function add_todos($todos)
    {
        if($this->contains_array($todos))
        {
            foreach($todos as $todo)
            {
                $this->db->exec("INSERT INTO `todos`(id, userId, title, completed) VALUES(:id, :userId, :title, :completed)", array(
                    ':id' => $todo['id'],
                    ':userId' => $todo['userId'],
                    ':title' => $todo['title'],
                    ':completed' => $todo['completed'],
                ));
            }
        }
        else
        {
            $this->db->exec("INSERT INTO `todos`(id, userId, title, completed) VALUES(:id, :userId, :title, :completed)", array(
                ':id' => $todos['id'],
                ':userId' => $todos['userId'],
                ':title' => $todos['title'],
                ':completed' => $todos['completed'],
            ));
        }
    }

    /*
     *  PUT /@id
     *  Update to-do with an id of @id
     */
    public function update_todo($f3, $params)
    {
        $todo_id = htmlentities((int)$params['id']);

        $post_data = file_get_contents("php://input");

        if($post_data != '')
        {
            if($this->isJSON($post_data))
            {
                    $post_data_array = json_decode($post_data, true);

                    $errors = array();
                    $todo = $this->db->exec("SELECT * FROM `todos` WHERE id = :id", array(':id' => $todo_id));

                    if($todo)
                    {
                        if(isset($post_data_array['title']))
                        {
                            if(trim($post_data_array['title']) == '')
                            {
                                $errors[] = "Title field value can't be empty.";
                            }
                            else
                            {
                                $todo[0]['title'] = $post_data_array['title'];
                            }
                        }

                        if(isset($post_data_array['completed']))
                        {
                            if(is_bool($post_data_array['completed']))
                            {
                                $todo[0]['completed'] = $post_data_array['completed'];
                            }
                            else
                            {
                                $errors[] = 'Completed field value must be a boolean (true/false).';
                            }
                        }

                        if(empty($errors) && count($errors) == 0)
                        {
                            $this->db->exec("UPDATE `todos` SET title = :title, completed = :completed WHERE id = :id", array(
                                ':title' => $todo[0]['title'],
                                ':completed' => $todo[0]['completed'],
                                ':id' => $todo_id,
                            ));

                            $api_data_array = $this->get_todos_array();

                            $return_data = [
                                'total' => count($api_data_array),
                                'completed' => $this->count_completed($api_data_array),
                                'uncompleted' => $this->count_uncompleted($api_data_array),
                                'data' => $api_data_array
                            ];
                            $return_code = 200;
                        }
                        else
                        {
                            $return_data = [
                                'error' => $errors
                            ];
                            $return_code = 400;
                        }
                    }
                    else
                    {
                        $return_data = [
                            'error' => ['Todo with ID ' . $todo_id . ' not found.']
                        ];
                        $return_code = 404;
                    }
            }
            else
            {
                $return_data = [
                    'error' => ['Not a valid JSON data.']
                ];
                $return_code = 400;
            }
        } else {
            $return_data = [
                'error' => ['Not a valid JSON data.']
            ];
            $return_code = 400;
        }

        $json_data = json_encode($return_data);

        if($this->log_api_calls)
            $this->logAPICall('PUT', '/'.$params['id'], file_get_contents("php://input"), $json_data);

        http_response_code($return_code);
        echo $json_data;
    }

    /*
     *  DELETE /@id
     *  DELETE /delete/@id
     *  Delete to-do with an id of @id
     */
    public function delete_todo($f3, $params)
    {
        $delete_id = (int)$params['id'];

        if(is_integer($delete_id) && $delete_id > 0)
        {
                $this->db->exec("DELETE FROM `todos` WHERE id = :id", array(':id' => $delete_id));

                $api_data_array = $this->get_todos_array();

                $return_data = [
                    'total' => count($api_data_array),
                    'completed' => $this->count_completed($api_data_array),
                    'uncompleted' => $this->count_uncompleted($api_data_array),
                    'data'=>$api_data_array
                ];
                $return_code = 200;
        }
        else
        {
            $return_data = [
                "error" => ["The todo ID must be an integer."]
            ];
            $return_code = 400;
        }

        $json_data = json_encode($return_data);

        if($this->log_api_calls)
            $this->logAPICall('DELETE', '/'.$params['id'], file_get_contents("php://input"), $json_data);

        http_response_code($return_code);
        echo $json_data;
    }


    /*
     *  Counts and returns an array of completed todos from a todos array
     */
    private function count_completed($data)
    {
        $completed = 0;
        foreach($data as $item)
        {
            if($item['completed'] == 1) $completed++;
        }

        return $completed;
    }

    /*
     *  Counts and returns an array of uncompleted todos from a todos array
     */
    private function count_uncompleted($data)
    {
        $uncompleted = 0;
        foreach($data as $item)
        {
            if($item['uncompleted'] == 0) $uncompleted++;
        }

        return $uncompleted;
    }

    /*
     * Checks if a string is a valid JSON string.
     */
    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /*
     * Checks and returns boolean value whether an array contains an array
     */
    private function contains_array($array){
        foreach($array as $value){
            if(is_array($value)) {
                return true;
            }
        }
        return false;
    }


    /*
     *  To-do fields validation
     */
    private function validate_fields($array)
    {
        $errors = array();

        if(isset($array[0]))
        {
            $count = count($array);
        }
        else
        {
            $count = 1;
        }

        if($count == 1)
        {
            if (!isset($array['id']))
            {
                $errors[] = 'id is a required field.';
            }
            if (!isset($array['userId']))
            {
                $errors[] = 'userId is a required field.';
            }
            if (!isset($array['title']))
            {
                $errors[] = 'title is a required field.';
            }

            if(isset($array['userId']))
            {
                if(empty($array['userId']))
                {
                    $errors[] = "userId value can't be empty.";
                }
                if(!empty($array['userId']) && !is_int($array['userId']))
                {
                    $errors[] = "userId field value must be be an integer.";
                }
            }

            if(isset($array['id']))
            {
                if(empty($array['id']))
                {
                    $errors[] = "id value can't be empty.";
                }
                if(!empty($array['id']) && !is_int($array['id']))
                {
                    $errors[] = 'id field value must be an integer.';
                }
            }

            if(isset($array['title']))
            {
                if(trim($array['title']) == '')
                {
                    $errors[] = "title field value can't be empty.";
                }
                else
                {
                    if(strlen(trim($array['title'])) < 5)
                    {
                        $errors[] = "title field value minimum length required is 5 characters.";
                    }
                }
            }

            if(isset($array['completed']))
            {
                if(!is_bool($array['completed']))
                {
                    $errors[] = 'completed field value must be a boolean (true/false).';
                }
            }

        }

        return $errors;
    }

    /*
     * DELETE /user/@id
     * Delete todos posted by user with userId of @id
     */
    public function delete_todos_by_user_id($f3, $params)
    {
        $userId = (int)$params['id'];

        if(is_integer($userId) && $userId > 0)
        {
            $user_deleted = $this->db->exec("DELETE FROM `todos` WHERE userId = :userId", array(':userId' => $userId));

            if($user_deleted)
            {
                $api_data_array = $this->get_todos_array();

                $return_data = [
                    'total' => count($api_data_array),
                    'completed' => $this->count_completed($api_data_array),
                    'uncompleted' => $this->count_uncompleted($api_data_array),
                    'data'=>$api_data_array
                ];
                $return_code = 200;
            }
            else
            {
                $return_data = [
                    "error" => ["User with ID ". $userId . " was not found."]
                ];
                $return_code = 404;
            }
        }
        else
        {
            $return_data = [
                "error" => ["The user ID must be an integer."]
            ];
            $return_code = 400;
        }

        $json_data = json_encode($return_data);

        if($this->log_api_calls)
            $this->logAPICall('DELETE', '/user/'.$params['id'], file_get_contents("php://input"), $json_data);

        http_response_code($return_code);
        echo $json_data;
    }

    /*
     * DELETE /delete/all
     * Delete all todos
     */
    public function delete_all()
    {
        $this->db->exec("DELETE FROM `todos`");

        $api_data_array = $this->get_todos_array();

        $return_data = [
            'total' => count($api_data_array),
            'completed' => $this->count_completed($api_data_array),
            'uncompleted' => $this->count_uncompleted($api_data_array),
            'data' => $api_data_array
        ];

        $json_data = json_encode($return_data);

        if($this->log_api_calls)
            $this->logAPICall('DELETE', '/delete/all', file_get_contents("php://input"), $json_data);

        echo $json_data;
    }

    /*
     * GET /completed
     * Get completed todos
     */
    public function get_completed()
    {
        $completed = $this->db->exec("SELECT * FROM `todos` WHERE completed = 1");

        $json_data = json_encode(array('total' => count($completed), 'data' => $completed));

        if($this->log_api_calls)
            $this->logAPICall('GET', '/completed', file_get_contents("php://input"), $json_data);

        echo $json_data;
    }

    /*
    * GET /uncompleted
    * Get uncompleted todos
    */
    public function get_uncompleted()
    {
        $completed = $this->db->exec("SELECT * FROM `todos` WHERE completed = 0");

        $json_data = json_encode(array('total' => count($completed), 'data' => $completed));

        if($this->log_api_calls)
            $this->logAPICall('GET', '/uncompleted', file_get_contents("php://input"), $json_data);

        echo $json_data;
    }

    /*
     * This function logs the sent requests to API endpoints
     */
    private function logAPICall($method = null, $endpoint = '/', $data_submitted = '', $data_returned = '')
    {
        $this->db->exec("INSERT INTO `logs`(method, endpoint, data_submitted, data_returned, created_at) VALUES(:method, :endpoint, :data_submitted, :data_returned, NOW())", array(
            ':method' => $method,
            ':endpoint' => $endpoint,
            ':data_submitted' => $data_submitted,
            ':data_returned' => $data_returned,
        ));
    }

}
