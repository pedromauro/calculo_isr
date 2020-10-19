

<?php 
  include_once "clases/cabecera.html";
  require_once 'PHPExcel/Classes/PHPExcel.php';
  require_once 'clases/functions.php';
  require_once 'clases/scripts.php';
        
  error_reporting(0);

            if (isset($_POST['isr'])){
                echo '<h2 align="center">Cálculo ISR</h2>';
            }else{
                echo '<h2 align="center">Calculo inverso de ISR</h2>';
            }      
        ?>
<!--  Panel Visual -->
        <div class="panel panel-dark">
            <div class="panel-heading bg-dark">
                <h3 class="panel-title" style="color:rgb(255, 255, 255); font-weight:bold;">Resultados de archivo Excel.</h3>
            </div>
            <div class="panel-body ">
                <div>
<!-- Fin de Panel Visual -->

                    <?php
                   /* Carga de archivo */
                    $archivo = $_FILES["archivo"];
                    $url = $archivo["tmp_name"];
                    $year=$_REQUEST['year'];
                    /* extraccion de información */
                
                    $conn = conexion();
                    $table_isr = tableIsr($conn,$year);
                    $dataArray = array();

                    /*Leer Archivo */ 
                    $inputFileType = PHPExcel_IOFactory::identify($url);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($url);
                    $sheet = $objPHPExcel->getSheet(0); 
                    $highestRow = $sheet->getHighestRow(); 
                    $highestColumn = $sheet->getHighestColumn();
                    $num=0;
                    for ($row = 1; $row <= $highestRow; $row++){ 
                        $num++;
                        $dataArray["MIEMBRO"][$row] = $sheet->getCell("A".$row)->getValue(); 
                        $dataArray["NOMBRE"][$row] = $sheet->getCell("B".$row)->getValue(); 
                        $dataArray["IMPORTE"][$row] = $sheet->getCell("C".$row)->getValue();
                    }
                    /* Cerrar archivo */
                    
                    // Calcula todos los datos del Array
                    for($row = 1; $row <=$highestRow; $row++){
                        //var_dump($dataArray["NOMBRE"][1]);
                        if (isset($_POST['isr'])){
                        $dataArray[$row] = calcularIsr($dataArray["MIEMBRO"][$row],$dataArray["NOMBRE"][$row],$dataArray["IMPORTE"][$row],$table_isr);
                        }elseif (isset($_POST['isrI'])){
                        $dataArray[$row] = inversoIsr($dataArray["MIEMBRO"][$row],$dataArray["NOMBRE"][$row],$dataArray["IMPORTE"][$row],$table_isr);
                        }
                    }
                    echo '<div>
                    <table class="table table-hover table-condensed" id="iddataTable" style="text-align:center;">
                    <thead style="background-color:#343A40; color:rgb(255, 255, 255);font-weight:bold;">
                    <tr>
                    <td>#</td>
                    <td>MIEMBRO</td>
                    <td>NOMBRE</td>
                    <td>IMPORTE</td>
                    <td>BASE MENSUAL</td>
                    <td>LIMITE INFERIOR</td>
                    <td>BASE</td>
                    <td>% </td>
                    <td>IMPUESTO MARGINAL </td>
                    <td>CUOTA FIJA </td>
                    <td>ISR </td>
                    <td>NETO </td>
                    </tr>
                    </thead>';

                    $num=0;
                    $importeTotal = 0;
                    $isrTotal =0;
                    $netoTotal=0;
                    
                    for ($row = 1; $row <= $highestRow; $row++){ 
                        $num++;
                        echo '<tr>';
                        echo '<th scope=row>';
                        echo $num;
                        echo '</th>';
                        $dataArray["MIEMBRO"][$row] = $sheet->getCell("A".$row)->getValue();
                        $dataArray["NOMBRE"][$row] = $sheet->getCell("B".$row)->getValue();
                        $dataArray["IMPORTE"][$row] = $sheet->getCell("C".$row)->getValue();

                        $importeTotal=$importeTotal+$dataArray[$row]["IMPORTE"];
                        $isrTotal=$isrTotal+$dataArray[$row]["ISR A RETENER"];
                        $netoTotal=$netoTotal+$dataArray[$row]["NETO"];

                        echo '<td>'.$dataArray[$row]["MIEMBRO"].'</td>';
                        echo '<td>'.$dataArray[$row]["NOMBRE"].'</td>';
                        echo '<td>'.$dataArray[$row]["IMPORTE"].'</td>';
                        echo '<td>'.$dataArray[$row]["BASE MENSUAL"].'</td>';
                        echo '<td>'.$dataArray[$row]["LIMITE INFERIOR"].'</td>';
                        echo '<td>'.$dataArray[$row]["BASE"].'</td>';
                        echo '<td>'.$dataArray[$row]["% SOBRE IMPUESTO"].'</td>';
                        echo '<td>'.$dataArray[$row]["IMPUESTO MARGINAL"].'</td>';
                        echo '<td>'.$dataArray[$row]["CUOTA FIJA"].'</td>';
                        echo '<td>'.$dataArray[$row]["ISR A RETENER"].'</td>';
                        echo '<td>'.$dataArray[$row]["NETO"].'</td>';
                        echo '</tr>';
                    }

                    echo "<h4 align='center'><b>IMPORTE TOTAL: ".number_format($importeTotal, 2, '.', '').'&nbsp;&nbsp;&nbsp;';
                    echo "ISR TOTAL: ".number_format($isrTotal, 2, '.', '').'&nbsp;&nbsp;&nbsp;';
                    echo "NETO TOTAL: ".number_format($netoTotal, 2, '.', '')."</b></h4>";

                ?>
                        </tbody>

                        </table>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#iddataTable').DataTable({
                "scrollY": "250px",
                "scrollCollapse": true,
                "paging": false,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });
    </script>

</body>

<?php include_once "clases/footer.html"; ?>

</html>