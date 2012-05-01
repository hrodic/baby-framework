<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo _('Feudal-online Editor!'); ?></title>
        <script type="text/javascript" src="/js/lib/jquery.min.js"></script>
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <style language="text/css">
            td{
                border: 1px solid black;
                width: 50px;
                height: 50px;
            }
        </style>
        <script language="javascript">
            $(function(){
                var $map = $('#customMap tbody');
                $('#addColumn').click(function(){
                    $map.find('tr').append('<td>cell</td>');
                });
                $('#addRow').click(function(){
                    var $row = $map.find('tr:last-child').html();
                    $map.append('<tr>'+$row+'</tr>');
                });
                $('#delColumn').click(function(){
                    if($map.find('tr:first-child td').length <= 3)
                        return;
                    $map.find('tr td:last-child').remove();
                });
                $('#delRow').click(function(){
                    if($map.find('tr').length <= 3)
                        return;
                    $map.find('tr:last-child').remove();
                });
            });
        </script>
    </head>
    <body>
        <div id="view-admin-editor">
            <div>
                <button id="addRow">+Row</button>
                <button id="addColumn">+Column</button>
                <button id="delRow">-Row</button>
                <button id="delColumn">-Column</button>
                <select id="tileSelector">
                    <option value="grass">grass</option>
                </select>
            </div>
            <table id="customMap">
                <tbody>    
                    <tr>
                        <td>cell</td><td>cell</td><td>cell</td><td>cell</td>
                    </tr>
                    <tr>
                        <td>cell</td><td>cell</td><td>cell</td><td>cell</td>
                    </tr>
                    <tr>
                        <td>cell</td><td>cell</td><td>cell</td><td>cell</td>
                    </tr>
                    <tr>
                        <td>cell</td><td>cell</td><td>cell</td><td>cell</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>