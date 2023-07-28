<?php
    // receive the data from the form
    $hex = Input::post('hex');
    
    $hex = trim($hex);

    if (empty($hex)) {
        echo json_encode(array('error' => 'Please fill out all fields.'));
    }

    echo json_encode(array('hex' => $hex));
?>