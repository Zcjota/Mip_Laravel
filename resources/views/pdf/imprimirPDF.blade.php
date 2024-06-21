
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>

</head>

<body >
<script>

</script>
    <table rules="none" width="792" border="0" style="font-family: Arial; font-size: 10pt; position:absolute; 
           left:29px; top:6px; height: 85px;";>
        <tr width="99">
            <td height = "50" align = "left" valign = "top" style="font-family: Arial; font-size: 7pt; width: 209px;"> 
                <IMG SRC="img/logoimpresion.jpg" height="65" width="100">
                <br><b>CASA MATRIZ SANTA CRUZ</b>
                <br>Plan 12 Hamacas c/ 1 Oeste N#17 
                <br>(591)3-341-6001 / (591)3-341-6544
                
            </td>			 
            <div class="row">
            <table>
                <thead>
                    <th>id</th>
                </thead>
            <body>
             @foreach($item as $data)
                <tr>
                    <td>{{$item->estado}}</td>
                </tr>
             @endforeach
            </body>
            </table>
            </div>

            
        </tr>


     </table>
         
         
  
          

     
 </body>
 </html>';
