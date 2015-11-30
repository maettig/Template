<?php

require_once("template.class.php");

$object = new Template();

$object->_template = '<html>
    <body>
        <h1>{title}</h1>
        <table border="1">
            <!-- BEGIN row -->
                <tr>
                    <td>{id}</td>
                    <td>{name}</td>
                    <!-- IF color -->
                        <td bgcolor="{color}">yea</td>
                    <!-- ELSE -->
                        <td>empty</td>
                    <!-- ENDIF color -->
                </tr>
            <!-- END row -->
        </table>
    </body>
</html>';

$object->_template_assign("title", "I'm a Title");

for ($i = 0; $i < 10; $i++)
{
    $row = array();
    $row['id'] = rand(1, 100);
    $row['name'] = "Thiemo";
    if (rand(1, 3) == 1) $row['color'] = "red";
    $object->_template_append("row", $row);
}

echo $object->_template_toHTML();

?>