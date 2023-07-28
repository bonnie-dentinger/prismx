<?php

// extend hybrid controller
class Controller_PrismX extends Controller_Hybrid
{

    public $template = 'prismx/template';

    public function action_index()
    {
        $this->template->content = View::forge('prismx/index');
    }

    public function get_index()
    {
        $this->template->content = View::forge('prismx/index');
    }

    public function post_index()
    {
        $this->template->content = View::forge('prismx/index');
    }

    public function action_about()
    {
        $this->template->content = View::forge('prismx/about');
    }

    public function get_colors()
    {
        $this->template->content = View::forge('prismx/colors');
    }

    public function post_colors()
    {
        $rowsColumns = Input::post('rows-columns');
        $colors = Input::post('colors');
        $rowsColumns = trim($rowsColumns);
        $colors = trim($colors);

        if (empty($rowsColumns)) {
            Session::set_flash('error', 'Please fill out all fields.');
            return Response::redirect('prismx/colors');
        }

        if (!is_numeric($rowsColumns)) {
            Session::set_flash('error', 'Please enter a number for rows and columns.');
            return Response::redirect('prismx/colors');
        }

        if ($rowsColumns < 1 || $rowsColumns > 26) {
            Session::set_flash('error', 'Please enter a number between 1 and 26 for rows and columns.');
            return Response::redirect('prismx/colors');
        }

        $data = array(
            'rows' => $rowsColumns,
            'colors' => $colors
        );

        $this->template->content = View::forge('prismx/colors', $data);
    }

    public function post_addNewColor()
    {
        // receive the data from the form
        $hex = Input::post('hex');
        
        $hex = trim($hex);

        if (empty($hex)) {
            echo json_encode(array('error' => 'Please fill out all fields.'));
        }

        $hexNoPound = str_replace('#', '', $hex);
        // send api request to https://api.color.pizza/v1/?values=hex
        $url = 'https://api.color.pizza/v1/?values=' . $hexNoPound;
        $response = file_get_contents($url);
        $response = json_decode($response, true);
        $name = $response['colors'][0]['name'];
        // change any letters with accents to their non-accented version
        $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);

        // add the color to the database
        $exists = DB::select('hex')
            ->from('colors')
            ->where('hex', $hex)
            ->execute()
            ->as_array();

        $nameExists = DB::select('name')
            ->from('colors')
            ->where('name', $name)
            ->execute()
            ->as_array();
        
        if (!empty($exists) || !empty($nameExists)) {
            echo json_encode(array('error' => 'This color already exists.'));
            return;
        }
        
        $response = DB::insert('colors')
            ->set(array(
                'hex' => $hex,
                'name' => $name
            ))
            ->execute();

        echo json_encode(array('name' => $name));
    }

    public function post_deleteColor()
    {
        // receive the data from the form
        $hex = Input::post('hex');
        
        $hex = trim($hex);

        if (empty($hex)) {
            echo json_encode(array('error' => 'Please fill out all fields.'));
        }

        $response = DB::delete('colors')
            ->where('hex', $hex)
            ->execute();

        echo json_encode(array('success' => 'Color deleted.'));
    }

    public function post_editColor()
    {
        $hex = Input::post('hex');
        $oldHex = Input::post('oldHex');
        $hex = trim($hex);
        $oldHex = trim($oldHex);

        if (empty($hex)) {
            echo json_encode(array('error' => 'Please fill out all fields.'));
        }

        $hexNoPound = str_replace('#', '', $hex);
        // send api request to https://api.color.pizza/v1/?values=hex
        $url = 'https://api.color.pizza/v1/?values=' . $hexNoPound;
        $response = file_get_contents($url);
        $response = json_decode($response, true);
        $name = $response['colors'][0]['name'];
        // change any letters with accents to their non-accented version
        $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);

        //  check if the color already exists
        $exists = DB::select('hex')
            ->from('colors')
            ->where('hex', $hex)
            ->execute()
            ->as_array();

        $nameExists = DB::select('name')
            ->from('colors')
            ->where('name', $name)
            ->execute()
            ->as_array();

        if (!empty($exists) || !empty($nameExists)) {
            echo json_encode(array('error' => 'This color already exists.'));
            return;
        }

        // update the color in the database
        $hexQuery = DB::update('colors')
            ->value('hex', $hex)
            ->where('hex', $oldHex)
            ->execute();

        $nameQuery = DB::update('colors')
            ->value('name', $name)
            ->where('hex', $hex)
            ->execute();

        echo json_encode(array('name' => $name));
    }
}

?>