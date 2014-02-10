<?php
  if ($_GET['tab'] === 'dep') {
    $type = 1;
    $heading = "Deposits <small>(in €)</small>";
  } else if ($_GET['tab'] === 'wit') {
    $type = 2;
    $heading = "Cashouts <small>(in €)</small>";
  }
  $tab = $_GET['tab'];
?>
<!-- deposits -->
            <div class="panel panel-default">
              <div class="panel-body">
                <table class="table table-bordered table-condensed text-center noPaddingMargin" id="deposits">
                  <thead>
                    <tr>
                      <th style="width: 20%">Date</th>
                      <th style="width: 15%">Time</th>
                      <th style="width: 50%">Source</th>
                      <th style="width: 15%">Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                    $res = $mysqli->query("SELECT * FROM money where type=$type and userId=".$_SESSION['uid']." order by tDate desc, tTime desc");
                    while ($dep = $res->fetch_assoc()) {
                  ?>
                    <tr id=<?=$dep['tId']?>>
                      <td class="editable"><?=$dep['tDate']?></td>
                      <td class="editable"><?php echo substr($dep['tTime'], 0, -3); ?></td>
                      <td class="editable"><?=$dep['source']?></td>
                      <td class="editable"><?=$dep['amount']?></td>
                    </tr>
                  <?php
                    }
                  ?>
                  </tbody>
                </table>
              </div>
            </div> <!-- // deposits -->
            <script>
            $(document).ready(function() {  
      /* Init DataTables */
        var oTable = $('#deposits').dataTable({
              "iDisplayLength": 5,
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