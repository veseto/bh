<?php
  //1 - deposit
  //2 - withdawal
  //3 - bonus
  //4 - tax
  if ($_GET['tab'] === 'dep') {
    $type = 1;
    $heading = "Deposits <small>(in €)</small>";
    $typeH = "Bonus";
  } else if ($_GET['tab'] === 'wit') {
    $type = 2;
    $heading = "Cashouts <small>(in €)</small>";
    $typeH = "Tax";
  }
  $tab = $_GET['tab'];
?>
<!-- deposits -->
            <div class="panel panel-default">
              <div class="panel-body">
                <table class="table table-bordered table-condensed text-center noPaddingMargin" id="deposits">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Date</th>
                      <th>Time</th>
                      <th>Source</th>
                      <th>Amount</th>
                      <th><?=$typeH?></th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                    $i = 1;
                    $res = $mysqli->query("SELECT * FROM money where (type=$type or type=$type+2) and userId=".$_SESSION['uid']." order by tDate desc, tTime desc");
                    while ($dep = $res->fetch_assoc()) {
                  ?>
                    <tr id=<?=$dep['tId']?>>
                      <td><?=$i?></td>
                      <td class="editable"><?=$dep['tDate']?></td>
                      <td class="editable"><?php echo substr($dep['tTime'], 0, -3); ?></td>
                      <td class="editable"><?=$dep['source']?></td>
                      <td class="editable"><?=$dep['amount']?></td>
                      <td><input type="checkbox" class="checkbox" <?php if ($dep['type'] == 3 || $dep['type'] == 4) echo "checked";?>></td>
                    </tr>
                  <?php
                      $i++;
                    }
                  ?>
                  </tbody>
                </table>
              </div>
            </div> <!-- // deposits -->
            <script>
            $(".checkbox").change(function(){
              if (this.checked) {
                $.ajax({ url: 'savemoney.php?tab=<?=$tab?>&run=check',
                   data: {checked: 'true', id: $(this).parent().parent().attr("id")},
                   type: 'post', 
                   success: function(output) {
                  }
              });
              } else {
                $.ajax({ url: 'savemoney.php?tab=<?=$tab?>&run=check',
                   data: {checked: 'false', id: $(this).parent().parent().attr("id")},
                   type: 'post'
                });               
              }
            });

            $(document).ready(function() {  
      /* Init DataTables */
        var oTable = $('#deposits').dataTable({
              "iDisplayLength": 10,
              "bJQueryUI": true,
              "sPaginationType": "full_numbers",
              "sDom": '<"top" lf><"toolbar">irpti<"bottom"pT><"clear">',
              "oTableTools": {
                "sSwfPath": "dt/media/swf/copy_csv_xls_pdf.swf"
              }
      });
        $("div.toolbar").html("<a href='savemoney.php?run=new&tab=<?=$tab?>' class='btn btn-xs btn-primary'>add</a>");
        $("div.toolbar").addClass("text-center");
      
        /* Apply the jEditable handlers to the table */
        oTable.$('td.editable').editable( 'savemoney.php?tab=<?=$tab?>', {
            "callback": function( sValue, y ) {
               // alert(y[0]);
                var aPos = oTable.fnGetPosition( this );
                //var arr = sValue.split("#");
               // alert(sValue+ " " + aPos[0]+ " "+ aPos[1]);
                oTable.fnUpdate( sValue, aPos[0], aPos[1]);
        
                //oTable.fnClearTable();
                //oTable.fnReloadAjax() ;
            },
            "submitdata": function ( value, settings ) {
                return {
                    "row_id": this.parentNode.getAttribute('id'),
                    "column": oTable.fnGetPosition( this )[2]
                };
            },
            "height": "90%",
            "width": "90%"
        } );
    } );

  </script>