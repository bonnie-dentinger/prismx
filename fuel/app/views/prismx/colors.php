<?php

    if (Input::method() == 'GET') {
        $query = DB::select('*')->from('colors')->execute();
        $numColors = count($query);
        echo "<form action='colors' method='post'>
                <div class='container'>
                    <div style='width: 80%;margin: 0 auto;'>
                        <h1><strong>Choose a number of colors and rows/columns</strong></h1><br>
                        <label for='rows-columns' style='margin-top:1rem;'>Number of Rows/Columns</label>
                        <input type='number' class='form-control' min='1' max='26' name='rows-columns' id='rows-columns' placeholder='Enter a value between 1 and 26' style='width:50%;' onkeyup='validate();' /> <br />
                        <div id='alert-rowscols'></div>
                        <label for='colors' style='margin-top:1rem;'>Number of Colors</label>
                        <input type='number' class='form-control' min='1' max='" . $numColors . "' name='colors' id='colors' placeholder='Enter a value between 1 and " . $numColors . "' style='width:50%' onkeyup='validate();' /> <br />
                        <div id='alert-colors'></div>
                        <button type='submit' class='btn btn-primary' id='submit' onclick='validate();' disabled>Submit</button>
                    </div>
                </div>
            </form>";
        echo View::forge('prismx/footer');
        echo "<script>
                function createAlert(alert_type, alert_message) {
                    var alert = '<div class=\"alert alert-' + alert_type + ' vibrate-1\" role=\"alert\" id=\"alert-rowscols\">';
                        alert += alert_message;
                        alert += '<button type=\"button\" class=\"close\" onclick=\"closeAlert();\" aria-label=\"Close\">';
                        alert += '<span aria-hidden=\"true\">&times;</span>';
                        alert += '</button>';
                        alert += '</div>';

                    return alert;
                }

                function validate() {
                    var rowscols = $('#rows-columns').val();
                    var colors = $('#colors').val();
                    if (rowscols == '' || colors == '') {
                        $('#submit').prop('disabled', true);
                    }
                    if (rowscols.length > 0 && (rowscols > 26 || rowscols < 1)) {
                        var alert = createAlert('danger', '<strong>Error!</strong> Please enter a value between 1 and 26 for rows/columns.');
                        $('#alert-rowscols').empty();
                        $('#alert-rowscols').append(alert);
                        $('#submit').prop('disabled', true);
                    } else if (rowscols.length > 0 && (rowscols <= 26 && rowscols >= 1)) {
                        $('#alert-rowscols').empty();
                    }
                    if (colors.length > 0 && (colors > " . $numColors . " || colors < 1)) {
                        var alert = createAlert('danger', '<strong>Error!</strong> Please enter a value between 1 and " . $numColors . " for colors.');
                        $('#alert-colors').empty();
                        $('#alert-colors').append(alert);
                        $('#submit').prop('disabled', true);
                    } else if (colors.length > 0 && (colors <= " . $numColors . " && colors >= 1)) {
                        $('#alert-colors').empty();
                    }
                    if (rowscols.length > 0 && colors.length > 0 && (rowscols <= 26 && rowscols >= 1) && (colors <= " . $numColors . " && colors >= 1)) {
                        $('#submit').prop('disabled', false);
                    }
                }
                function closeAlert() {
                    $('.alert').remove();
                }
        </script>";
    }
    else {
        $rows = $rows;
        $colors = $colors;
        $query = DB::select('*')->from('colors')->execute();
        $numColors = count($query);

        if ($rows > 26 || $rows < 1 || $rows == '' || !is_numeric($rows)) {
            echo "<div class='container'>
                    <div style='width: 80%;margin: 0 auto;'>
                        <div class='alert alert-danger' role='alert'>
                            <strong>Error!</strong> You must have between 1 and 26 rows/columns.
                        </div>
                    </div>
                </div>
                <div class='container'>
                    <a href='index' class='btn btn-primary'>Go Back</a>";
            echo View::forge('prismx/footer');
            echo "</div>";
            return;
        } else if ($colors > $numColors || $colors < 0 || $colors == '' || !is_numeric($colors)) {
            echo "<div class='container'>
                    <div style='width: 80%;margin: 0 auto;'>
                        <div class='alert alert-danger' role='alert'>
                            <strong>Error!</strong> You must have between 0 and " . $numColors . "colors.
                        </div>
                    </div>
                </div>
                <div class='container'>
                    <a href='index' class='btn btn-primary'>Go Back</a>
                </div>";
            echo View::forge('prismx/footer');
            return;
        }
        $availableColors = DB::select('*')->from('colors')->execute();
        $availableColors = $availableColors->as_array();
        $availableColorsLength = count($availableColors);
        shuffle($availableColors);
        $currentlySelectedColors = array();
        
        // first table has two columns and x rows depending on number of colors
        // left column has 20% width and right has 80%
        // no header row
        // each left column cells has a drop-down with 10 color names (red, orange, yellow, green, blue, purple, grey, brown, black, and teal)
        // no two dropdowns can have the same color at the same time
        // if the same color is selected in two different dropdowns, the second dropdown will revert to the previous color selected and alert the user

        echo "<div class='container' style='margin-top: 100px;'>
                <div id='table1_div' style='width: 80%;margin: 0 auto;'>
                    <table id='table1' class='table table-bordered' style='border: none;'>";
        for ($i = 0; $i < $colors; $i++) {
            $name = $availableColors[$i]['name'];
            $hex = $availableColors[$i]['hex'];
            echo "<tr>
                    <td style='width:2%; border: none; vertical-align: middle;'>
                        <input type='radio' name='color' value='" . $hex . "' data-name='" . $name . "' id='radio_" . $i . "'>
                    </td>
                    <td style='width:19%; background-color:" . $hex . "'>
                        <select class='form-control' id='color_" . $i . "' onchange=\"updateRadio('" . $i . "');\">";
            for ($j = 0; $j < $availableColorsLength; $j++) {
                $name2 = $availableColors[$j]['name'];
                $hex2 = $availableColors[$j]['hex'];
                if ($i == $j) {
                    echo "<option selected='selected' value='" . $hex2 . "'>" . $name2 . "</option>";
                }
                else {
                    echo "<option value='" . $hex2 . "'>" . $name2 . "</option>";
                }
            }
            echo "</select>
                </td>
                <td class='cell-list' id='cellList_" . $i . "' style='width:79%; background-color:" . $hex . ";'></td>
                </tr>";
            // append name to currentlySelectedColors array
            array_push($currentlySelectedColors, $hex);
        }
        echo "</table>
            </div>
            <div style='width: 80%;margin: 0 auto;'>
                <div id='alert'></div>
                <p style='text-align: center;'>Current Color: <span id='currentColor'></span><br>
                <i class='fa-light fa-plus'></i>
                <i class='fa-light fa-pencil' onclick='editColor();'></i>
                <i class='fa-light fa-trash-can' onclick='removeColor();'></i>
            </div>
            <div class='modal fade-in' id='newColorModal'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title'>Add New Color</h5>
                        <i class='fa-light fa-x' onclick='closeNewColorModal();'></i>
                    </div>
                    <div class='modal-body'>
                        <label for='newColorHex' class='form-label' style='color: black;'>Hex Code</label>
                        <input type='text' class='form-control' id='newColorHex' placeholder='#000000' maxlength='7'>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' onclick='closeNewColorModal();'>Cancel</button>
                        <button type='button' class='btn btn-primary' onclick='addNewColor();'>Add Color</button>
                    </div>
                </div>
            </div>
        </div>
        <div class='modal fade-in' id='editColorModal'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title'>Edit Current Color</h5>
                        <i class='fa-light fa-x' onclick='closeEditColorModal();'></i>
                    </div>
                    <div class='modal-body'>
                        <label for='currentHex' class='form-label' style='color: black;'>Current Hex Code</label>
                        <input type='text' class='form-control' id='currentHex' placeholder='#000000' maxlength='7' style='margin-bottom: 5px;' disabled>
                        <label for='editColorHex' class='form-label' style='color: black;'>New Hex Code</label>
                        <input type='text' class='form-control' id='editColorHex' placeholder='#000000' maxlength='7'>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' onclick='closeEditColorModal();'>Cancel</button>
                        <button type='button' class='btn btn-primary' onclick='editColor();'>Edit Color</button>
                    </div>
                </div>
            </div>
        </div>
        <div class='modal fade-in' id='deleteColorModal'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title'>Delete Current Color</h5>
                        <i class='fa-light fa-x' onclick='closeDeleteColorModal();'></i>
                    </div>
                    <div class='modal-body'>
                        <p class='form-label' style='color: black;'>Are you sure you want to delete this color?</p>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' onclick='closeDeleteColorModal();'>Cancel</button>
                        <button type='button' class='btn btn-danger' onclick='deleteColor();'>Delete Color</button>
                    </div>
                </div>
            </div>
        </div>
        </div>";

        // create javascript to handle the color selection
        // if the same color is selected in two different dropdowns, the second dropdown will revert to the previous color selected and alert the user
        echo "<script>
            var currentlySelectedColors = " . json_encode($currentlySelectedColors) . ";
            function createAlert(alert_type, alert_message) {
                var alert = '<div class=\"alert alert-' + alert_type + ' vibrate-1\" role=\"alert\" id=\"alert-rowscols\">';
                    alert += alert_message;
                    alert += '<button type=\"button\" class=\"close\" onclick=\"closeAlert();\" aria-label=\"Close\">';
                    alert += '<span aria-hidden=\"true\">&times;</span>';
                    alert += '</button>';
                    alert += '</div>';

                return alert;
            }

            // update text color to contrast with background color
            function updateTextColor() {
                var cell_list = $('.cell-list');
                var cell_list_length = cell_list.length;
                for (var i = 0; i < cell_list_length; i++) {
                    var cell = cell_list[i];
                    var rgb = cell.style.backgroundColor;
                    var rgb_array = rgb.substring(4, rgb.length - 1).replace(/ /g, '').split(',');
                    var r = rgb_array[0];
                    var g = rgb_array[1];
                    var b = rgb_array[2];
                    var contrast = getContrast(r, g, b);
                    if (contrast > 4.5) {
                        cell.style.color = 'white';
                    } else {
                        cell.style.color = 'black';
                    }
                }
            }

            // calculate contrast between two colors
            function getContrast(r1, g1, b1) {
                var r2 = 255;
                var g2 = 255;
                var b2 = 255;
                var luminance1 = 0.2126 * (r1 / 255) + 0.7152 * (g1 / 255) + 0.0722 * (b1 / 255);
                var luminance2 = 0.2126 * (r2 / 255) + 0.7152 * (g2 / 255) + 0.0722 * (b2 / 255);
                if (luminance1 > luminance2) {
                    return (luminance1 + 0.05) / (luminance2 + 0.05);
                } else {
                    return (luminance2 + 0.05) / (luminance1 + 0.05);
                }
            }

            function colorOptions() {
                var options = $('select option');
                var optionsLength = options.length;
                for (var i = 0; i < optionsLength; i++) {
                    var option = options[i];
                    var hex = option.value;
                    var css = 'color: ' + hex + ';';
                    option.setAttribute('style', css);
                }
            }

            function updateRadio(index) {
                if ($('#alert').length) {
                    $('#alert').empty();
                }

                var previouslySelectedColor = currentlySelectedColors[index];
                var select = $('#color_' + index);
                var selectedColor = select.val();
                var radio = $('#radio_' + index);
                if (currentlySelectedColors.includes(selectedColor)) {
                    // insert alert into the html
                    var alert = createAlert('danger', 'This color is already selected. Please select a different color.');
                    $('#alert').empty();
                    $('#alert').append(alert);
                    // revert the select to the previously selected color
                    select.val(previouslySelectedColor);
                    select.parent().css('background-color', previouslySelectedColor);
                    select.parent().next().css('background-color', previouslySelectedColor);
                }
                else {
                    currentlySelectedColors.forEach(function(color, i) {
                        if (color == previouslySelectedColor) {
                            currentlySelectedColors[i] = selectedColor;
                        }
                    });
                    select.parent().css('background-color', selectedColor);
                    select.parent().next().css('background-color', selectedColor);
                    updateCurrentColor(index);
                }

                updateTextColor();
            }

            $(document).ready(function() {
                var plusIcon = $('.fa-plus');
                var pencilIcon = $('.fa-pencil');
                var trashIcon = $('.fa-trash-can');
                var newColorModal = $('#newColorModal');

                checkForAvailableColors();
                colorOptions();

                $('.fa-plus').click(function() {
                    $('#newColorModal').removeClass('fade-out');
                    $('#newColorModal').css('display', 'block');
                });

                $('.fa-pencil').click(function() {
                    $('#editColorModal').removeClass('fade-out');
                    $('#editColorModal').css('display', 'block');
                    // seleted input
                    var input = $('input[name=color]:checked');
                    $('#currentHex').val(input.val());
                });

                $('.fa-trash-can').click(function() {
                    $('#deleteColorModal').removeClass('fade-out');
                    $('#deleteColorModal').css('display', 'block');
                });

                $('input[type=text]').on('input', function() {
                    var hex = $(this).val();

                    if (hex.length == 0) {
                        $(this).val('#');
                    }
                    if (hex.length == 6) {
                        if (hex.charAt(0) != '#') {
                            $(this).val('#' + hex);
                        }
                    }
                });
            });

            function closeAlert() {
                $('#alert').remove();
            }

            function closeNewColorModal() {
                $('#newColorModal').addClass('fade-out');
                setTimeout(function() {
                    $('#newColorModal').css('display', 'none');
                }, 500);
            }

            function closeEditColorModal() {
                $('#editColorModal').addClass('fade-out');
                setTimeout(function() {
                    $('#editColorModal').css('display', 'none');
                }, 500);
            }

            function closeDeleteColorModal() {
                $('#deleteColorModal').addClass('fade-out');
                setTimeout(function() {
                    $('#deleteColorModal').css('display', 'none');
                }, 500);
            }

            function addNewColor() {
                var hex = $('#newColorHex').val();

                if (hex.length != 7 || hex.charAt(0) != '#') {
                    var alert = createAlert('danger', 'Invalid hex code. Please enter a valid hex code.');
                    $('#newColorModal .modal-body').append(alert);
                    return;
                } else {
                    $.ajax({
                        url: 'addNewColor',
                        type: 'POST',
                        data: {
                            hex: hex
                        },
                        success: function(data) {
                            data = JSON.parse(data);

                            if (data.error) {
                                var alert = createAlert('danger', data.error);
                                $('#newColorModal .modal-body').append(alert);
                                return;
                            }

                            $('#newColorModal').addClass('fade-out');
                            setTimeout(function() {
                                $('#newColorModal').css('display', 'none');
                            }, 500);

                            var option = '<option value=\"' + hex + '\" style=\"color: ' + hex + '\">' + data.name + '</option>';
                            $('select').append(option);
                        },
                        error: function(data) {
                            var alert = createAlert('danger', 'An error occurred. Please try again.');
                            $('#newColorModal .modal-body').append(alert);
                        }
                    });
                }
            }

            function deleteColor() {
                // delete the color that is currently selected from radio inputs
                var color = $('input[name=color]:checked').val();
                var colorName = $('input[name=color]:checked').attr('data-name');
                var input = $('input[name=color]:checked');
                var inputIndex = input.parent().index();
                
                $.ajax({
                    url: 'deleteColor',
                    type: 'POST',
                    data: {
                        hex: color
                    },
                    success: function(data) {
                        data = JSON.parse(data);

                        if (data.error) {
                            var alert = createAlert('danger', data.error);
                            $('#deleteColorModal .modal-body').append(alert);
                            return;
                        }

                        $('#deleteColorModal').addClass('fade-out');
                        setTimeout(function() {
                            $('#deleteColorModal').css('display', 'none');
                        }, 500);

                        $('select option[value=\"' + color + '\"]').remove();
                        updateTextColor();
                        availableColors = checkForAvailableColors();
                        if (availableColors.length == 0) {
                            var row = input.parent().parent();
                            row.remove();
                            // select the first radio input
                            $('input[name=color]:first').prop('checked', true);
                        } else {
                            // assign the first available color to the radio input
                            input.val(availableColors[0]);
                            input.attr('data-name', data.name);
                            input.parent().next().css('background-color', availableColors[0]);
                            input.parent().next().next().css('background-color', availableColors[0]);
                            var select = $('color_' + inputIndex);
                            select.val(availableColors[0]).change();
                            var options = select.find('option');
                            options.each(function() {
                                if ($(this).val() == availableColors[0]) {
                                    $(this).attr('selected', 'selected');
                                }
                            });
                            var cellListTd = $('#cellList_' + inputIndex);
                            cellListTd.empty();
                        }
                        deleteCellColors(colorName);
                        updateCurrentColor(inputIndex);
                    },
                    error: function(data) {
                        var alert = createAlert('danger', 'An error occurred. Please try again.');
                        $('#deleteColorModal .modal-body').append(alert);
                    }
                });
            }

            function editColor() {
                var hex = $('#editColorHex').val();
                var color = $('input[name=color]:checked').val();
                var colorName = $('input[name=color]:checked').attr('data-name');
                var input = $('input[name=color]:checked');
                var inputIndex = input.parent().index();

                if (hex.length != 7 || hex.charAt(0) != '#') {
                    $('#editColorHex').css('border-color', 'red');
                    return;
                } else {
                    $.ajax({
                        url: 'editColor',
                        type: 'POST',
                        data: {
                            hex: hex,
                            oldHex: color
                        },
                        success: function(data) {
                            data = JSON.parse(data);

                            if (data.error) {
                                var alert = createAlert('danger', data.error);
                                $('#editColorModal .modal-body').append(alert);
                                return;
                            }

                            $('#editColorModal').addClass('fade-out');
                            setTimeout(function() {
                                $('#editColorModal').css('display', 'none');
                            }, 500);

                            $('select option[value=\"' + color + '\"]').remove();
                            var option = '<option value=\"' + hex + '\" style=\"color: ' + hex + '\">' + data.name + '</option>';
                            $('select').append(option);
                            updateTextColor();
                            input.val(hex);
                            input.attr('data-name', data.name);
                            input.parent().next().css('background-color', hex);
                            input.parent().next().next().css('background-color', hex);
                            var select = $('color_' + inputIndex);
                            select.val(hex).change();
                            var options = select.find('option');
                            options.each(function() {
                                if ($(this).val() == hex) {
                                    $(this).attr('selected', 'selected');
                                }
                            });
                            changeCellColorNames(colorName, data.name);
                            updateCurrentColor(inputIndex);
                        },
                        error: function(data) {
                            var alert = createAlert('danger', 'An error occurred. Please try again.');
                            $('#editColorModal .modal-body').append(alert);
                        }
                    });
                }
            }

            function changeCellColorNames(oldName, newName) {
                var cells = $('.colors');
                cells.each(function() {
                    if ($(this).attr('data-color') == oldName) {
                        $(this).attr('data-color', newName);
                    }
                });
            }

            function checkForAvailableColors() {
                var colors = $('select option');
                var availableColors = [];

                colors.each(function() {
                    if (currentlySelectedColors.indexOf($(this).val()) == -1 && availableColors.indexOf($(this).val()) == -1) {
                        availableColors.push($(this).val());
                    }
                });
                return availableColors;
            }
        </script>";
        
        // second table is n+1 x n+1 where n is the indicated row/column size
        // table is always square
        // upper-leftmost cell is empty
        // remaining cells across the top are lettered with capital letters in alphabetical order starting with A and going to Z
        // the cells in the left most column are numbered starting with the second row with 1 and going to n

        echo "<div class='container'>
                <div style='width: 80%;margin: 0 auto;'>
                    <table id='table2' class='table table-bordered' style=\"table-layout:fixed\">
                        <tr>
                            <td class=\"square\"></td>";
        for ($i = 0; $i < $rows; $i++) {
            echo "<td class=\"square\">" . chr($i + 65) . "</td>";
        }
        echo "</tr>";
        for ($i = 0; $i < $rows; $i++) {
            echo "<tr>";
            echo "<td class=\"square\">" . ($i + 1) . "</td>";
            for ($j = 0; $j < $rows; $j++) {
                echo "<td class=\"square colors\"></td>";
            }
            echo "</tr>";
        }
        echo "</table>
                <button type='button' class='btn btn-secondary' onclick='goBack();' style='display: none;'><i class='fa fa-arrow-left'></i> Go Back</button>
            </div>
        </div>";
        echo View::forge('prismx/footer');

        echo "<script>
                $(document).ready(function() {
                    $('.square').css('height', $('.square').css('width'));
                    $('input[name=color]').first().prop('checked', true);
                    $('#currentColor').text($('input[name=color]:checked').attr('data-name'));
                    $('#currentColor').css('color', $('input[name=color]:checked').val());

                    $('input[name=color]').change(function() {
                        $('#currentColor').text($('input[name=color]:checked').attr('data-name'));
                        $('#currentColor').css('color', $('input[name=color]:checked').val());
                    });

                    $('#table2 td.colors').click(function() {
                        var currentColor = $('input[name=color]:checked').val();
                        var currentColorName = $('input[name=color]:checked').attr('data-name');
                        $(this).css('background-color', currentColor);
                        $(this).attr('data-color', currentColorName);
                        var index = $('input[name=color]').index($('input[name=color]:checked'));
                        var row = $(this).parent().index();
                        var column = $(this).index();
                        column = String.fromCharCode(column + 64);
                        var table1Row = $('#table1 tr').eq(index);
                        var table1Cell = table1Row.children().eq(2);
                        var table1CellText = table1Cell.text();
                        var newTable1CellText = column + row;

                        if (table1CellText.includes(column + row)) {
                            return;
                        }

                        var table1Rows = $('#table1 tr');
                        for (var i = 0; i < table1Rows.length; i++) {
                            var table1RowContent = table1Rows.eq(i).children().eq(2).text();
                            if (table1RowContent.includes(column + row) && table1Rows[i] != table1Row) {
                                var table1RowContentArray = table1RowContent.split(', ');
                                var index = table1RowContentArray.indexOf(column + row);
                                if (index > -1) {
                                    table1RowContentArray.splice(index, 1);
                                }
                                table1Rows.eq(i).children().eq(2).text(table1RowContentArray.join(', '));
                            }
                        }

                        if (table1CellText.length > 0) {
                            newTable1CellText = table1CellText + ', ' + newTable1CellText;
                            var table1CellTextArray = table1CellText.split(', ');
                            table1CellTextArray.push(column + row);
                            table1CellTextArray.sort();
                            newTable1CellText = table1CellTextArray.join(', ');
                        }
                        table1Cell.text(newTable1CellText);
                        updateTextColor();
                    });
                });

                function hexToRgb(hex) {
                    var r = parseInt(hex.substring(1, 3), 16);
                    var g = parseInt(hex.substring(3, 5), 16);
                    var b = parseInt(hex.substring(5, 7), 16);
                    return [r, g, b];
                }

                function deleteCellColors(colorName) {
                    var cells = $('#table2 td.colors');
                    for (var i = 0; i < cells.length; i++) {
                        if (cells.eq(i).attr('data-color') == colorName) {
                            cells.eq(i).css('background-color', 'transparent');
                            cells.eq(i).attr('data-color', '');
                        }
                    }
                }

                function updateCurrentColor(index) {
                    var radios = document.getElementsByName('color');
                    var selects = document.getElementsByTagName('select');
                    var selectedRadioIndex;

                    for (var i = 0; i < radios.length; i++) {
                        if (radios[i].checked) {
                            selectedRadioIndex = i;
                            break;
                        }
                    }

                    var previouslySelectedColor = radios[index].value;
                    radios[index].value = selects[index].value;
                    radios[index].attributes['data-name'].value = selects[index].options[selects[index].selectedIndex].text;

                    var selectedColor = selects[index].value;
                    var cells = document.getElementById('table2').getElementsByTagName('td');

                    rgb = hexToRgb(previouslySelectedColor);
                    previouslySelectedColor = 'rgb(' + rgb[0] + ', ' + rgb[1] + ', ' + rgb[2] + ')';

                    // convert selectedColor hex to rgb
                    rgb2 = hexToRgb(selectedColor);
                    selectedColor = 'rgb(' + rgb2[0] + ', ' + rgb2[1] + ', ' + rgb2[2] + ')';

                    for (var i = 0; i < cells.length; i++) {
                        if (cells[i].style.backgroundColor == previouslySelectedColor) {
                            cells[i].style.backgroundColor = selects[index].value;
                            cells[i].attributes['data-color'].value = selects[index].options[selects[index].selectedIndex].text;
                        }
                    }

                    if (index == selectedRadioIndex) {
                        $('#currentColor').text($('input[name=color]:checked').attr('data-name'));
                        $('#currentColor').css('color', $('input[name=color]:checked').val());
                    }
                }
        </script>";

        // at the bottom of the page, there is a button that allows the user to go to a printable view of the tables
        // should render in greyscale and include logo and company name as a header
        // selected color names will appear in the cells where the dropdowns were, but as plain text

        echo "<div class='container flex'>
                <a href='#' id='print' class='btn btn-primary center' onclick='printableView();' style='margin-bottom: 50px;'>Printable View</a>
            </div>";
        echo "<script>
                function printableView() {
                    $('body').addClass('printable');
                    var nav_links = document.getElementsByClassName('nav-link');
                    for (var i = 0; i < nav_links.length; i++) {
                        nav_links[i].style.display = 'none';
                    }
                    $('body').css('background-image', 'none');
                    $('.nav-title').show();
                    $('footer').css('display', 'none');
                    $('#print').css('display', 'none');
                    $('input').css('display', 'none');
                    $('#currentColor').parent().css('display', 'none');
                    $('p').css('color', 'black');
                    $('#table1_div').css('margin-top', '50px');
                    $('#table1').children().children().css('height', '10px');
                    $('#table1').children().children().css('max-height', '10px');
                    $('#table1').children().children().children().css('height', '10px');
                    $('#table1').children().children().children().css('max-height', '10px');
                    $('#table1').children().children().children().css('padding', '0px');
                    $('td').css('color', 'black');
                    $('td.colors').css('background-color', 'white');
                    // remove td.colors onclick listener
                    $('td.colors').off('click');
                    var selects = document.getElementsByTagName('select');
                    for (var i = 0; i < selects.length; i++) {
                        var selectedColor = selects[i].children[selects[i].selectedIndex].text;
                        if (selectedColor != 'Orange' && selectedColor != 'Yellow') {
                            selects[i].parentElement.style.color = 'white';
                            selects[i].parentElement.nextElementSibling.style.color = 'white';
                        } else {
                            selects[i].parentElement.style.color = 'black';
                            selects[i].parentElement.nextElementSibling.style.color = 'black';
                        }
                        selects[i].parentElement.innerHTML = selectedColor;
                        i--;
                    }
                    var squares = document.getElementsByClassName('square');
                    for (var i = 0; i < squares.length; i++) {
                        squares[i].style.height = 'auto';
                        squares[i].style.maxHeight = 'auto';
                        squares[i].style.padding = '0px';
                    }

                    $('#table2').css('width', $('#table2').css('height'));
                    $('#table2').css('margin', '0 auto');

                    $('.container').css('margin-bottom', '0px');
                    $('.btn-secondary').css('display', 'block');
                };

                function goBack() {
                    window.location.reload();
                }
        </script>";
    }
?>